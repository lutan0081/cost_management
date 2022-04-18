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

            // 管理者フラグ取得
            $admin_user_flag = $request->session()->get('admin_user_flag');

            // admin_user_flag=1:管理ユーザ/admin_user_flag=0:一般ユーザ
            if($admin_user_flag == 1){
                Log::debug('管理ユーザの場合の処理');
                return redirect('adminInit');
            }else{
                Log::debug('一般ユーザの場合の処理');
                return redirect('backHomeInit');
            }
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
            $password = $request->input('password_request');
            
            // メールアドレス
            $mail = $request->input('mail_request');
            
            // 自動ログインのチェック有無
            $auto_login_flag = $request->input('auto_login_flag');

            // retrunの配列作成
            $response = [];

            // ログインユーザデータ取得
            $str = "select * from create_users "
            ."where create_user_password = "
            ."'$password' "
            ."and "
            ."create_user_mail = "
            ."'$mail'";
            Log::debug('sql:' .$str);
            $data = DB::select($str);

            /**
             * count=1:ture(管理ユーザ)
             * count=0:false(一般ユーザ)
             */
            // データ数が1以上が存在する場合、ログイン処理
            if(count($data) > 0){

                Log::debug('ログインデータが存在する場合の処理');

                // 管理者フラグ:true=管理者/false=一般
                $request->session()->put('admin_user_flag',$data[0]->admin_user_flag);

                // kasegu_auth=trueに設定(ログインしていない場合falseの為、frontHomeに強制遷移)
                // $request->session()->put('kasegu_auth',true);

                // session_id
                $request->session()->put('create_user_id',$data[0]->create_user_id);
                
                // アカウント名
                $request->session()->put('create_user_name',$data[0]->create_user_name);

                // ture=自動フラグ設定
                if($auto_login_flag == "true"){

                    $request->session()->put('auto_login_flag',$auto_login_flag);

                    // 自動ログインフラグをセッションに設定
                    $auto_login_flag = $request->session()->get('auto_login_flag');
                    Log::debug('自動ログインフラグ:' .$auto_login_flag);
                }

                // sessionにユーザ名取得
                $create_user_name = $request->session()->get('create_user_name');

                // session_id取得
                $session_id = $request->session()->get('create_user_id');
                Log::debug('session_id:' .$session_id);
                
                // 管理者フラグ
                $admin_user_flag = $data[0]->admin_user_flag;
                Log::debug('admin_user_flag:' .$admin_user_flag);

                // admin_user_flag=1:管理ユーザ/admin_user_flag=0:一般ユーザ
                if($admin_user_flag == 1){
                    Log::debug('管理ユーザの場合の処理');

                    $response["admin"] = true;

                    $response["status"] = false;  
                }else{
                    Log::debug('一般ユーザの場合の処理');

                    $response["admin"] = false;

                    $response["status"] = true;  
                }

            // データ数が存在しない場合の処理 
            }else{
                // ログイン判定フラグ
                $response["admin"] = false;

                $response["status"] = false;  
            }

        // 例外処理(falseを返却しエラーメッセージ表示)
        } catch (\Exception $e) {

            Log::debug('error:'.$e);

            $response["admin"] = false;

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
        ."create_date)values( "
        ."'$ip', "
        ."now() "
        ."); ";
        DB::insert($str);

        Log::debug('log_end:' .__FUNCTION__);
    }
}