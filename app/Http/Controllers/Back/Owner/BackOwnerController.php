<?php

namespace App\Http\Controllers\Back\Owner;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

use Storage;

// データ縮小
use InterventionImage;

// 暗号化
use Illuminate\Support\Facades\Crypt;

use Common;

/**
 * 家主マスタ(表示・登録、編集、削除)
 */
class BackOwnerController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backOwnerInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {
            // 家主一覧取得
            $owner_list = $this->getList($request);
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backOwner' ,$owner_list);
    }

    /**
     * 一覧(sql)
     *
     * @return $ret(銀行一覧)
     */
    private function getList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            $str = "select * from owners ";

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){

                if($where == ""){

                    $where = "where ";

                }else{

                    $where = "and ";
                }

                $where = $where ."ifnull(owner_name,'') like '%$free_word%'";
            };

            // id
            if($where == ""){

                $where = $where ."where "
                ."entry_user_id = '$session_id' ";

            }else{

                $where = $where ."and "
                ."entry_user_id = '$session_id' ";
            }

            // order by句
            $order_by = "order by owner_id ";

            $str = $str .$where .$order_by;
            Log::debug('$sql:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->paginate(10)->onEachSide(1);

            // resの中に値が代入されている
            $ret = [];
            $ret['res'] = $res;

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

    /**
     *  新規(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backOwnerNewInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 家主一覧
            $owner_list = $this->getNewList($request);
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backOwnerEdit' ,compact('owner_list'));
    }

    /**
     * 新規(ダミー値取得)
     *
     * @return $ret(空の配列)
     */
    private function getNewList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        $obj = new \stdClass();
        
        // 募集要項
        $obj->owner_id  = '';
        $obj->owner_name = '';
        $obj->owner_post_number = '';
        $obj->owner_address = '';
        $obj->owner_tel = '';
        $obj->owner_fax = '';

        $ret = [];
        $ret = $obj;

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     *  編集(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backOwnerEditInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $owner_info = $this->getEditList($request);
            $owner_list = $owner_info[0];

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backOwnerEdit' ,compact('owner_list'));
    }

    /**
     * 編集(表示:sql)
     *
     * @return void
     */
    private function getEditList(Request $request){
        Log::debug('start:' .__FUNCTION__);

        try{
            // 値設定
            $owner_id = $request->input('owner_id');

            // sql
            $str = "select * "
            ."from owners "
            ."where "
            ."owners.owner_id = $owner_id ";
            Log::debug('sql:' .$str);
            
            $ret = DB::select($str);

        // 例外処理
        } catch (\Exception $e) {

            throw $e;

        } finally {

        }
        
        Log::debug('start:' .__FUNCTION__);
        return $ret;
    }


    /**
     * 登録分岐(新規/編集)
     *
     * @param $request(edit.blade.phpの各項目)
     * @return $response(status:true=OK/false=NG)
     */
    public function backOwnerEditEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        
        // return初期値
        $response = [];

        // バリデーション:OK=true NG=false
        $response = $this->editValidation($request);

        if($response["status"] == false){

            Log::debug('validator_status:falseのif文通過');
            return response()->json($response);

        }

        /**
         * id=無:insert
         * id=有:update
         */
        // 新規登録
        if($request->input('owner_id') == ""){

            Log::debug('新規の処理');

            // $responseの値設定
            $ret = $this->insertData($request);

        // 編集登録
        }else{

            Log::debug('編集の処理');

            // $responseの値設定
            $ret = $this->updateData($request);

        }

        // js側での判定のステータス(true:OK/false:NG)
        $response["status"] = $ret['status'];

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * バリデーション
     *
     * @param Request $request(bladeの項目)
     * @return response(status=NG/msg="入力を確認して下さい/messages=$msgs/$errkeys=$keys)
     */
    private function editValidation(Request $request){

        // returnの出力値
        $response = [];

        // 初期値
        $response["status"] = true;

        /**
         * rules
         */
        $rules = [];
        $rules['owner_name'] = "required|max:50";
        $rules['owner_post_number'] = "required|zip";
        $rules['owner_address'] = "required|max:100";
        $rules['owner_tel'] = "required|jptel";
        $rules['owner_fax'] = "nullable|jptel";

        /**
         * messages
         */
        $messages = [];
        $messages['owner_name.required'] = "家主名は必須です。";
        $messages['owner_name.max'] = "家主名の文字数が超過しています。";
        $messages['owner_post_number.required'] = "郵便番号は必須です。";
        $messages['owner_post_number.zip'] = "郵便番号の形式が不正です。";
        $messages['owner_address.required'] = "住所は必須です。";
        $messages['owner_address.max:100'] = "住所の文字数が超過しています。";
        $messages['owner_tel.required'] = "TELは必須です。";
        $messages['owner_tel.jptel'] = "TELの形式が不正です。";
        $messages['owner_fax.jptel'] = "FAXの形式が不正です。";
    
        // validation判定
        $validator = Validator::make($request->all(), $rules, $messages);

        // エラーがある場合処理
        if ($validator->fails()) {
            Log::debug('validator:失敗');

            // response初期値
            $keys = [];
            $msgs = [];

            // errorsをjson形式に変換(true=連想配列)
            $ary = json_decode($validator->errors(), true);
            
            // ループ&値をvalueに設定
            foreach ($ary as $key => $value) {
                // キーを配列に設定
                $keys[] = $key;
                // 値(メッセージ)を設定
                $msgs[] = $value;
            }

            // keyデバック
            $arrKeys = print_r($keys , true);
            Log::debug('keys:'.$arrKeys);

            // msgsデバック
            $arrMsgs = print_r($msgs , true);
            Log::debug('msgs:'.$arrMsgs);

            // response値設定
            // status = falseの場合js側でerrorメッセージ表示
            $response["status"] = false;
            $response['msg'] = "入力を確認して下さい。";
            $response["messages"] = $msgs;
            $response["errkeys"] = $keys;
            
            Log::debug('log_end:' .__FUNCTION__);
        }
        return $response;
    }

    /**
     * 新規登録(各テーブルに分岐)
     *
     * @param Request $request(edit.blade.phpの各項目)
     * @return ret(true:登録OK/false:登録NG、maxId(contract_id)、session_id(create_user_id))
     */
    private function insertData(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {

            // retrun初期値
            $ret = [];
            $ret['status'] = true;

            /**
             * status:OK=1 NG=0
             */
            $bank_info = $this->insertBackOwner($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $bank_info['status'];

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $ret['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($ret['status'] == 1){

                Log::debug('status:trueの処理');
                $ret['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $ret['status'] = false;
            }

            Log::debug('log_end:'.__FUNCTION__);
            return $ret;
        }
    }

    /**
     * 新規登録
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function insertBackOwner(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $owner_name = $request->input('owner_name');
            $owner_post_number = $request->input('owner_post_number');
            $owner_address = $request->input('owner_address');
            $owner_tel = $request->input('owner_tel');
            $owner_fax = $request->input('owner_fax');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 家主名
            if($owner_name == null){
                $owner_name ='';
            }

            // 郵便番号
            if($owner_post_number == null){
                $owner_post_number ='';
            }

            // 住所
            if($owner_address == null){
                $owner_address ='';
            }

            // TEL
            if($owner_tel == null){
                $owner_tel = '';
            }

            // FAX
            if($owner_fax == null){
                $owner_fax = '';
            }

            // 登録
            $str = "insert "
            ."into "
            ."owners "
            ."( "
            ."owner_name, "
            ."owner_post_number, "
            ."owner_address, "
            ."owner_tel, "
            ."owner_fax, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."'$owner_name', "
            ."'$owner_post_number', "
            ."'$owner_address', "
            ."'$owner_tel', "
            ."'$owner_fax', "
            ."$session_id, "
            ."'$date', "
            ."$session_id, "
            ."'$date' "
            ."); ";
            
            Log::debug('sql:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::insert($str);

        // 例外処理
        } catch (\Throwable  $e) {

            throw $e;

        // status:OK=1/NG=0
        } finally {
            
        }

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 編集登録(各テーブルに分岐)
     *
     * @param Request $request(edit.blade.phpの各項目)
     * @return ret(true:登録OK/false:登録NG)
     */
    private function updateData(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            
            // retrun初期値
            $ret = [];
            $ret['status'] = true;

            /**
             * status:OK=1 NG=0
             */
            $legal_place_info = $this->updateBackOwner($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $legal_place_info['status'];

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $ret['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($ret['status'] == 1){

                Log::debug('status:trueの処理');
                $ret['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $ret['status'] = false;
            }

            Log::debug('log_end:'.__FUNCTION__);
            return $ret;
        }
    }

    /**
     * 編集
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function updateBackOwner(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $owner_id = $request->input('owner_id');
            $owner_name = $request->input('owner_name');
            $owner_post_number = $request->input('owner_post_number');
            $owner_address = $request->input('owner_address');
            $owner_tel = $request->input('owner_tel');
            $owner_fax = $request->input('owner_fax');

            // 現在の日付取得
            $date = now() .'.000';
    
            // id
            if($owner_id == null){
                $owner_id = 0;
            }

            // 家主名
            if($owner_name == null){
                $owner_name ='';
            }

            // 郵便番号
            if($owner_post_number == null){
                $owner_post_number ='';
            }

            // 住所
            if($owner_address == null){
                $owner_address ='';
            }

            // TEL
            if($owner_tel == null){
                $owner_tel = '';
            }

            // FAX
            if($owner_fax == null){
                $owner_fax = '';
            }

            $str = "update "
            ."owners "
            ."set "
            ."owner_name = '$owner_name', "
            ."owner_post_number = '$owner_post_number', "
            ."owner_address = '$owner_address', "
            ."owner_tel = '$owner_tel', "
            ."owner_fax = '$owner_fax', "
            ."entry_user_id = $session_id, "
            ."entry_date = '$date', "
            ."update_user_id = $session_id, "
            ."update_date = '$date' "
            ."where "
            ."owner_id = $owner_id; ";
            
            Log::debug('sql:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::update($str);

        // 例外処理
        } catch (\Throwable  $e) {

            throw $e;

        // status:OK=1/NG=0
        } finally {
            
        }

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 削除
     *
     * @param Request $request
     * @return void
     */
    public function backOwnerDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            $owner_info = $this->deleteOwner($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $owner_info['status'];

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $response['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($response['status'] == 1){

                Log::debug('status:trueの処理');
                $response['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $response['status'] = false;
            }

        }

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * 削除
     *
     * @param Request $request
     * @return void
     */
    private function deleteOwner(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $owner_id = $request->input('owner_id');

            $str = "delete "
            ."from "
            ."owners "
            ."where "
            ."owner_id = $owner_id; ";
            Log::debug('str:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::delete($str);

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            throw $e;

        // status:OK=1/NG=0
        } finally {

        }

        Log::debug('log_end:' .__FUNCTION__);
        return $ret;
    }
} 