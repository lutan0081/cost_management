<?php

namespace App\Http\Controllers\Back\User;

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
 * 表示・登録、編集、削除
 */
class BackUserController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backUserInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 経費一覧取得
            $user_list = $this->getUserList($request);

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
        return view('back.backUser', $user_list, compact('paginate_params', 'free_word'));

    }

    /**
     * 一覧(sql)
     *
     * @return $ret(部屋一覧)
     */
    private function getUserList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            $str = "select "
            ."create_users.create_user_id, "
            ."create_users.create_user_name, "
            ."create_users.create_user_mail, "
            ."create_users.create_user_password, "
            ."create_users.permission_type_id, "
            ."permission_types.permission_type_name, "
            ."create_users.active_flag, "
            ."create_users.entry_date, "
            ."create_users.update_user_id, "
            ."create_users.update_date "
            ."from "
            ."create_users "
            ."left join permission_types on "
            ."permission_types.permission_type_id = create_users.permission_type_id "
            ."where 1 = 1 ";

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){
                $where = $where ."and ifnull(cost_memo,'') like '%$free_word%'";
                $where = $where ."or ifnull(financial_summary,'') like '%$free_word%'";
            };

            $str = $str .$where;
            Log::debug('$sql:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("create_user_id desc")->paginate(10)->onEachSide(1);

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
    public function backUserNewInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $create_user_list = $this->getNewList($request);

            $common = new Common();

            // 家主一覧
            // $real_estate_list = $common->getRealEstateList();
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backUserEdit' ,compact('create_user_list'));
    }

    /**
     * 新規(ダミー値取得)
     *
     * @return $ret(空の配列)
     */
    private function getNewList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        
        $obj = new \stdClass();
        
        $obj->create_user_name  = '';
        $obj->create_user_mail = '';
        $obj->create_user_password = '';
        $obj->permission_type_id = '';
        $obj->create_user_id = '';

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
    public function backRoomEditInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $room_info = $this->getEditList($request);
            $room_list = $room_info[0];

            $common = new Common();

            // 家主一覧
            $real_estate_list = $common->getRealEstateList();

            // 部屋種別
            $room_type_list = $common->getRoomTypeList();

        // 例外処理
        } catch (\Throwable $e) {
            Log::debug('error:'.$e);
        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backRoomEdit' ,compact('room_list','real_estate_list' ,'room_type_list'));
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
            $room_id = $request->input('room_id');

            // sql
            $str = "select "
            ."rooms.room_id, "
            ."rooms.room_name, "
            ."rooms.room_size, "
            ."rooms.real_estate_id, "
            ."real_estates.real_estate_name, "
            ."rooms.room_type_id, "
            ."room_types.room_type_name, "
            ."rooms.entry_user_id, "
            ."rooms.entry_date, "
            ."rooms.update_user_id, "
            ."rooms.update_date "
            ."from rooms "
            ."left join room_types on "
            ."room_types.room_type_id = rooms.room_type_id "
            ."left join real_estates on "
            ."real_estates.real_estate_id = rooms.real_estate_id "
            ."where rooms.room_id = $room_id; ";

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
    public function backUserEditEntry(Request $request){
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
        if($request->input('room_id') == ""){

            Log::debug('新規の処理');

            // $responseの値設定
            $ret = $this->insertData($request);

        // 編集登録
        }else{

            Log::debug('編集の処理');

            // // $responseの値設定
            // $ret = $this->updateData($request);

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
        $rules['create_user_name'] = "required|max:50";
        $rules['create_user_mail'] = "required|max:50";
        $rules['create_user_password'] = "required|max:10";

        /**
         * messages
         */
        $messages = [];
        $messages['create_user_name.required'] = "ユーザ名は必須です。";
        $messages['create_user_name.max'] = "ユーザ名の文字数が超過しています。";
        $messages['create_user_mail.required'] = "ユーザIDが必須です。";
        $messages['create_user_mail.max'] = "ユーザIDの文字数が超過しています。";
        $messages['create_user_password.required'] = "パスワードは必須です。";
        $messages['create_user_password.max'] = "パスワードは10文字以内で設定してください。";
    
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
            $bank_info = $this->insertRoom($request);

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
    private function insertRoom(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $create_user_name = $request->input('create_user_name');
            $create_user_mail = $request->input('create_user_mail');
            $create_user_password = $request->input('create_user_password');
            $permission_type_id = $request->input('permission_type_id');
            $create_user_id = $request->input('create_user_id');

            // 現在の日付取得
            $date = now() .'.000';
    
            // ユーザ名
            if($create_user_name == null){
                
                $create_user_name ='';
            }

            // ユーザID
            if($create_user_mail == null){
                $create_user_mail ='';
            }

            // パスワード
            if($create_user_password == null){
                $create_user_password ='';
            }

            // 登録
            $str = "insert "
            ."into create_users( "
            ."create_user_name "
            .",create_user_mail "
            .",create_user_password "
            .",permission_type_id "
            .",active_flag "
            .",entry_date "
            .",update_user_id "
            .",update_date "
            .") "
            ."values( "
            ."'$create_user_name' "
            .",'$create_user_mail' "
            .",'$create_user_password' "
            .",$permission_type_id "
            .",0 "
            .",'$date' "
            .",$session_id "
            .",'$date' "
            .") ";
            
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
            $room_info = $this->updateRoom($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $room_info['status'];

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
    private function updateRoom(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $room_id = $request->input('room_id');
            $real_estate_id = $request->input('real_estate_name');
            $roon_name = $request->input('roon_name');
            $room_type_id = $request->input('room_type_id');
            $room_size = $request->input('room_size');

            // 現在の日付取得
            $date = now() .'.000';

            // 物件名
            if($real_estate_id == null){
                $real_estate_id =0;
            }

            // 号室
            if($roon_name == null){
                $roon_name ='';
            }

            // 種別
            if($room_type_id == null){
                $room_type_id =0;
            }

            // 専有面積
            if($room_size == null){
                $room_size = '';
            }

            $str = "update "
            ."rooms "
            ."set "
            ."real_estate_id = $real_estate_id, "
            ."room_name = '$roon_name', "
            ."room_size = '$room_size', "
            ."room_type_id = $room_type_id, "
            ."entry_user_id = $session_id, "
            ."entry_date = '$date', "
            ."update_user_id = $session_id, "
            ."update_date = '$date' "
            ."where "
            ."room_id = $room_id ";
            
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
    public function backRoomDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            $room_info = $this->deleteRoom($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $room_info['status'];

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
    private function deleteRoom(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $room_id = $request->input('room_id');

            $str = "delete "
            ."from "
            ."rooms "
            ."where "
            ."room_id = $room_id; ";
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