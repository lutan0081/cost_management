<?php

namespace App\Http\Controllers\Back\UserInfo;

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

// use App\Config;

// メール
use Illuminate\Support\Facades\Mail;

/**
 * 表示・編集
 */
class BackUserInfoController extends Controller
{   
    /**
     *  表示
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backUserInfoInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $create_user_info = $this->getEditList($request);
            $create_user_list = $create_user_info[0];

            $common = new Common();

            $permission_type_list = $common->getPermissionTypeList();

        // 例外処理
        } catch (\Throwable $e) {
            Log::debug('error:'.$e);
        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backUserInfoEdit' ,compact('create_user_list', 'permission_type_list'));
    }

    /**
     * 表示(sql)
     *
     * @return void
     */
    private function getEditList(Request $request){

        Log::debug('start:' .__FUNCTION__);

        try{
            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            // sql
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
            ."where create_user_id = $session_id ";

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
    public function backUserInfoEditEntry(Request $request){
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
        if($request->input('create_user_id') == ""){

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
            $room_info = $this->updateUser($request);

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
    private function updateUser(Request $request){
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

            // 権限
            if($permission_type_id == null){
                $permission_type_id =0;
            }

            $str = "update create_users "
            ."set "
            ."create_user_name = '$create_user_name' "
            .",create_user_mail = '$create_user_mail' "
            .",create_user_password = '$create_user_password' "
            .",permission_type_id = $permission_type_id "
            .",active_flag = 0 "
            .",update_user_id = $session_id "
            .",update_date = '$date'"
            ."where "
            ."create_user_id = $session_id ";            
            
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
     * ユーザ追加申請
     *
     * @param Request $request
     * @return void
     */
    public function backUserInfoMailEntry(Request $request){
        Log::debug('start:' .__FUNCTION__);

        try {

            // トランザクション
            DB::beginTransaction();

            // 出力値
            $response = [];

            // バリデーション:OK=true NG=false
            $response = $this->addUserValidation($request);

            if($response["status"] == false){

                Log::debug('validator_status:falseのif文通過');
                return response()->json($response);

            }

            /**
             * 値取得
             */
            // ログインセッションid取得
            $session_id = $request->session()->get('create_user_id');

            /**
             * session_idからデータ申請者を取得する
             */
            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            // sql
            $str = "select "
            ."* "
            ."from "
            ."create_users "
            ."where create_user_id = $session_id ";
            Log::debug('sql:' .$str);
            $create_user_info = DB::select($str);

            // 申請者
            $create_user_name = $create_user_info[0]->create_user_name;

            // ユーザ名
            $modal_create_user_name = $request->input('modal_create_user_name');

            // ユーザid
            $modal_create_user_mail = $request->input('modal_create_user_mail');

            // パスワード
            $modal_create_user_password = $request->input('modal_create_user_password');

            // パスワード確認
            $modal_create_user_password_confirm = $request->input('modal_create_user_password_confirm');

            // 送信先のアドレス
            $to_mail = 'lutan0081.h@gmail.com';

            // 件名
            $subject_title = '【COST】ユーザ申請のお知らせ';

            // 現在の日付取得
            $date = now() .'.000';

            //　自身のメールアドレス(cost0081.h@gmail.com)をconfigファイルから取得(key:address)
            $from = config('mail.from');
            $from = $from['address'];
            Log::debug('from:' .$from);

            // 本文設定
            $mail_text = "☆───────────────────────────────────────────☆\n"
            ."下記ユーザからユーザ追加申請のお知らせ。\n"
            ."☆───────────────────────────────────────────☆\n\n"
            ."ユーザ追加申請は以下の通りです。\n"
            ."----------------------------------------------------------\n\n"
            ."申請者：$modal_create_user_name\n"
            ."ユーザ名：$modal_create_user_name\n"
            ."ユーザID：$modal_create_user_mail\n"
            ."パスワード：$modal_create_user_password\n\n"
            ."※本メールは送信専用メールアドレスから配信されています。\n"
            ."ご返信いただいても回答いたしかねます。ご了承ください。\n\n"
            ."☆───────────────────────────────────────────☆\n"
            ."ユーザ一覧より、追加承認を行ってください。\n"
            ."ログイン：https://cost-m.com/\n"
            ."☆───────────────────────────────────────────☆n";

            /**
             * ユーザ(仮データ)の登録
             */
            $str = "insert into "
            ."create_users( "
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
            ."'$modal_create_user_name' "
            .",'$modal_create_user_mail' "
            .",'$modal_create_user_password' "
            .",2 "
            .",1 "
            .",'$date' "
            .",$session_id "
            .",'$date' "
            .") ";

            Log::debug('insert_sql:'.$str);
            $ret['status'] = DB::insert($str);
            
            /**
             * 仮登録のお知らせメールの送信
             */
            Mail::raw($mail_text, function($message) use($to_mail,$from,$subject_title){
                $message->to($to_mail)
                ->from($from)
                ->subject($subject_title);
            });

            $response['status'] = 1;

            // コミット
            DB::commit();

        // 例外処理
        } catch (\Exception $e) { 

            // ログ
            Log::debug('error:'.$e);

            DB::rollback();

            // 失敗の場合falseを返す
            $response['status'] = 0;
            
            
        // status=1の場合、true/status=1以外の場合、false
        } finally {

            if($response['status'] == 1){

                $response['status'] = true;
                
            }else{
                
                $response['status'] = false;
            }

        }

        Log::debug('end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * バリデーション(ユーザ申請)
     *
     * @param Request $request(bladeの項目)
     * @return response(status=NG/msg="入力を確認して下さい/messages=$msgs/$errkeys=$keys)
     */
    private function addUserValidation(Request $request){

        // returnの出力値
        $response = [];

        // 初期値
        $response["status"] = true;

        /**
         * rules
         */
        $rules = [];
        $rules['modal_create_user_name'] = "required|max:50";
        $rules['modal_create_user_mail'] = "required|max:50|userid";
        $rules['modal_create_user_password'] = "required|max:10";

        /**
         * messages
         */
        $messages = [];
        $messages['modal_create_user_name.required'] = "ユーザ名は必須です。";
        $messages['modal_create_user_name.max'] = "ユーザ名の文字数が超過しています。";
        $messages['modal_create_user_mail.required'] = "ユーザIDが必須です。";
        $messages['modal_create_user_mail.max'] = "ユーザIDの文字数が超過しています。";
        $messages['modal_create_user_mail.userid'] = "ユーザIDが重複しています。";
        $messages['modal_create_user_password.required'] = "パスワードは必須です。";
        $messages['modal_create_user_password.max'] = "パスワードは10文字以内で設定してください。";
    
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

} 