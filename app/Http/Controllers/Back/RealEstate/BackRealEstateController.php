<?php

namespace App\Http\Controllers\Back\RealEstate;

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

// config内のapp.phpに定義
use Common;

/**
 * 不動産マスタ(表示・登録、編集、削除)
 */
class BackRealEstateController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backRealEstateInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {
            // 物件一覧
            $real_estate_list = $this->getRealEstateList($request);
            // dd($real_estate_list);

            $common = new Common();

            /**
             * フォームに値を保持させるためにそのまま返す
             */
            // フリーワード
            $free_word = $request->input('free_word');

            // ★リクエストパラメータをページネーション用の連想配列に格納★
            $paginate_params = [

                'free_word' => $free_word,
            ];
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backRealEstate' ,$real_estate_list ,compact('paginate_params', 'free_word'));
    }

    /**
     * 不動産一覧(sql)
     *
     * @return $ret(銀行一覧)
     */
    private function getRealEstateList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            $str = "select "
            ."real_estates.real_estate_id, "
            ."real_estates.owner_id, "
            ."owners.owner_name, "
            ."owners.owner_tel, "
            ."real_estates.real_estate_name, "
            ."real_estates.real_estate_post_number, "
            ."real_estates.real_estate_address, "
            ."real_estates.entry_user_id, "
            ."real_estates.entry_date, "
            ."real_estates.update_user_id, "
            ."real_estates.update_date "
            ."from real_estates "
            ."left join owners "
            ."on owners.owner_id = real_estates.owner_id "
            ."where 1 = 1 ";

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){
                $where = $where ."and ifnull(real_estate_name,'') like '%$free_word%'";
                $where = $where ."or ifnull(owner_name,'') like '%$free_word%'";
            };

            $str = $str .$where;
            Log::debug('$sql:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("real_estate_id asc")->paginate(30)->onEachSide(1);

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
    public function backRealEstateNewInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 不動産一覧
            $real_estate_list = $this->getNewList($request);

            $common = new Common();

            // 家主一覧
            $owner_list = $common->getOwnerList();
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backRealEstateEdit' ,compact('real_estate_list' ,'owner_list'));
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
        $obj->real_estate_id  = '';
        $obj->real_estate_name = '';
        $obj->real_estate_post_number = '';
        $obj->real_estate_address = '';
        $obj->owner_id = '';
        $obj->owner_name = '';
        $obj->owner_owner_post_number = '';
        $obj->owner_address = '';
        $obj->owner_tel = '';
        $obj->owner_fax = '';

        $ret = [];
        $ret = $obj;

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 家主コンボボックス変更
     *
     * @param Request $request
     * @return void
     */
    public function backOwnerNameChange(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        // 家主id
        $owner_id = $request->input('owner_id');

        $str = "select * "
        ."from "
        ."owners "
        ."where owners.owner_id = $owner_id ";
        Log::debug('sql:' .$str);

        $owner_list = DB::select($str);
        
        // return
        $response = [];
        $response['owner_list'] = $owner_list;

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);

    }
    
    /**
     *  編集(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backRealEstateEditInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $real_estate_info = $this->getEditList($request);
            $real_estate_list = $real_estate_info[0];

            $common = new Common();

            // 家主一覧
            $owner_list = $common->getOwnerList();

        // 例外処理
        } catch (\Throwable $e) {
            Log::debug('error:'.$e);
        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backRealEstateEdit' ,compact('real_estate_list' ,'owner_list'));
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
            $real_estate_id = $request->input('real_estate_id');

            // sql
            $str = "select * "
            ."from real_estates "
            ."where "
            ."real_estate_id = $real_estate_id ";
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
    public function backRealEstateEditEntry(Request $request){
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
        if($request->input('real_estate_id') == ""){

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
        $rules['real_estate_name'] = "required|max:50";
        $rules['real_estate_post_number'] = "required|zip";
        $rules['real_estate_address'] = "required|max:100";

        /**
         * messages
         */
        $messages = [];
        $messages['real_estate_name.required'] = "物件名は必須です。";
        $messages['real_estate_name.max'] = "物件名の文字数が超過しています。";
        $messages['real_estate_post_number.required'] = "郵便番号は必須です。";
        $messages['real_estate_post_number.zip'] = "郵便番号の形式が不正です。";
        $messages['real_estate_address.required'] = "住所は必須です。";
        $messages['real_estate_address.max:100'] = "住所の文字数が超過しています。";
    
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
            $bank_info = $this->insertRealEstate($request);

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
    private function insertRealEstate(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $real_estate_name = $request->input('real_estate_name');
            $real_estate_post_number = $request->input('real_estate_post_number');
            $real_estate_address = $request->input('real_estate_address');
            $owner_id = $request->input('owner_id');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 物件名
            if($real_estate_name == null){
                $real_estate_name ='';
            }

            // 郵便番号
            if($real_estate_post_number == null){
                $real_estate_post_number ='';
            }

            // 住所
            if($real_estate_address == null){
                $real_estate_address ='';
            }

            // 家主id
            if($owner_id == null){
                $owner_id = 0;
            }

            // 登録
            $str = "insert "
            ."into "
            ."real_estates "
            ."( "
            ."owner_id, "
            ."real_estate_name, "
            ."real_estate_post_number, "
            ."real_estate_address, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$owner_id, "
            ."'$real_estate_name', "
            ."'$real_estate_post_number', "
            ."'$real_estate_address', "
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
            $real_estate_id = $request->input('real_estate_id');
            $real_estate_name = $request->input('real_estate_name');
            $real_estate_post_number = $request->input('real_estate_post_number');
            $real_estate_address = $request->input('real_estate_address');
            $owner_id = $request->input('owner_id');

            // 現在の日付取得
            $date = now() .'.000';

            // 不動産id
            if($real_estate_id == null){
                $real_estate_id = 0;
            }
            
            // 物件名
            if($real_estate_name == null){
                $real_estate_name ='';
            }

            // 郵便番号
            if($real_estate_post_number == null){
                $real_estate_post_number ='';
            }

            // 住所
            if($real_estate_address == null){
                $real_estate_address ='';
            }

            // 家主id
            if($owner_id == null){
                $owner_id = 0;
            }

            $str = "update "
            ."real_estates "
            ."set "
            ."owner_id = $owner_id, "
            ."real_estate_name = '$real_estate_name', "
            ."real_estate_post_number = '$real_estate_post_number', "
            ."real_estate_address = '$real_estate_address', "
            ."entry_user_id = $session_id, "
            ."entry_date = '$date', "
            ."update_user_id = $session_id, "
            ."update_date = '$date' "
            ."where "
            ."real_estate_id = $real_estate_id; ";
            
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
    public function backRealEstateDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            $real_estate_info = $this->deleteRealEstate($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $real_estate_info['status'];

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
    private function deleteRealEstate(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $real_estate_id = $request->input('real_estate_id');

            $str = "delete "
            ."from "
            ."real_estates "
            ."where "
            ."real_estate_id = $real_estate_id; ";
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