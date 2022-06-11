<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

/**
 * ログインの処理
 */
class LoginController extends Controller
{     
    /**
     * ログイン画面(表示)
     *
     * @return(空配列)
     */
    public function loginInit(Request $request)
    {   
        Log::debug('log_start:' .__FUNCTION__);

        $this->ipInsert($request);

        // session_idを取得
        $create_user_id = $request->session()->get('create_user_id');

        // auto_login_flag=true:自動ログイン
        $auto_login_flag = $request->session()->get('auto_login_flag');

        // メールアドレス取得
        $create_user_mail = $request->session()->get('create_user_mail');

        // パスワード取得
        $password = $request->session()->get('password');
        
        // 自動ログインフラグ:True=自動ログイン
        if($auto_login_flag == "true"){
            Log::debug("自動ログインの処理");

            return redirect('backHomeInit');
        }

        Log::debug('log_end:' .__FUNCTION__);
        return view('login.login', compact('create_user_id'));
    }

    /**
     * ログイン判定
     * 管理者でログインの場合 admin=ture
     * 管理者でログインができなかった場合、一般ユーザで判定 true=OK false=NG 
     * ログインOKの場合、セッションにidを設定
     *
     * @param Request $request(パスワード、メールアドレス)
     * @return void(admin=true:ログインOK/false:ログインNG status=true:ログインOK/false:ログインNG)
     */
    public function loginEntry(Request $request)
    {
        Log::debug('start:' .__FUNCTION__);

        try {
            
            /**
             * 値取得
             */
            // パスワード
            $password = $request->input('password_request');
            
            // ユーザid
            $mail = $request->input('mail_request');
            
            /**
             * 自動ログイン
             * check = true
             * check = false
             */
            $auto_login_flag = $request->input('auto_login_flag');

            // retrunの配列作成
            $response = [];

            // ログインユーザデータ取得
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
            ."where "
            ."create_user_password = '$password' "
            ."and "
            ."create_user_mail = '$mail' ";

            Log::debug('login_sql:' .$str);
            $data = DB::select($str);

            /**
             * count=1:ture(管理ユーザ)
             * count=0:false(一般ユーザ)
             */
            // データ数が1以上が存在する場合、ログイン処理
            if(count($data) > 0){

                Log::debug('ログインデータが存在する場合の処理');

                // 権限フラグ:1=全機能操作可能
                // 権限フラグ:2 = 質問登録、承諾、CSVのみ出力可能
                $request->session()->put('permission_type_id',$data[0]->permission_type_id);
                $request->session()->put('permission_type_name',$data[0]->permission_type_name);

                // cost_auth=trueに設定(ログインしていない場合falseの為、frontHomeに強制遷移)
                $request->session()->put('cost_auth',true);

                // session_id設定
                $request->session()->put('create_user_id',$data[0]->create_user_id);
                
                // アカウント名設定
                $request->session()->put('create_user_name',$data[0]->create_user_name);

                // ture=自動フラグ設定
                if($auto_login_flag == "true"){
                    // 自動ログインフラグをセッションに設定
                    $request->session()->put('auto_login_flag',$auto_login_flag);

                    // 自動ログインフラグをセッションに取得
                    $auto_login_flag = $request->session()->get('auto_login_flag');
                    Log::debug('自動ログインフラグ:' .$auto_login_flag);
                }

                $response["status"] = true;

            // データ数が存在しない場合の処理 
            }else{

                $response["status"] = false;  
            }

        // 例外処理(falseを返却しエラーメッセージ表示)
        } catch (\Exception $e) {

            Log::debug('error:'.$e);

            $response['status'] = false;

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * IPをDBに記録
     */
    private function ipInsert(Request $request){

        Log::debug('log_start:' .__FUNCTION__);

        // ipアドレス取得
        $ip = $request->ip();
        Log::debug('ip:' .$ip);

        // sql
        $str = "insert "
        ."into "
        ."accesses( "
        ."ip_address, "
        ."entry_date "
        .")values( "
        ."'$ip', "
        ."now() "
        ."); ";
        DB::insert($str);

        Log::debug('log_end:' .__FUNCTION__);
    }
}