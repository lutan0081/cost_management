<?php

namespace App\Http\Controllers\Back\ReissuePassword;

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

use Illuminate\Support\Str;

// メール
use Illuminate\Support\Facades\Mail;

/**
 * 表示・登録、編集、削除
 */
class BackReissuePasswordController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backReissuePasswordInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            $bank_list = [];
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backReissuePassword', $bank_list);
    }

    /**
     * パスワード再発行
     * メール送信、新規パスワードからDB登録
     *
     * @param Request $request
     * @return void
     */
    public function backReissuePasswordEntry(Request $request){
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

            // ランダム文字列生成（パスワード用）
            $new_password = Str::random(8);
            Log::debug('new_password:' .$new_password);

            /**
             * 値取得
             */
            // ユーザid
            $create_user_id = $request->input('create_user_id');

            // mail
            $create_user_mail = $request->input('create_user_mail');

            /**
             * メールの設定
             */
            // 送信先のアドレス
            $to_mail = $create_user_mail;

            // 件名
            $subject_title = '【COST】パスワード再発行のお知らせ';

            // 現在の日付取得
            $date = now() .'.000';

            //　自身のメールアドレス(cost0081.h@gmail.com)をconfigファイルから取得(key:address)
            $from = config('mail.from');
            $from = $from['address'];
            Log::debug('from:' .$from);

            // 本文設定
            $mail_text = "☆───────────────────────────────────────────☆\n"
            ."パスワード再発行のお知らせ\n"
            ."☆───────────────────────────────────────────☆\n\n"
            ."パスワードは以下の通りです。\n"
            ."----------------------------------------------------------\n\n"
            ."新規パスワード：$new_password\n\n"
            ."※本メールは送信専用メールアドレスから配信されています。\n"
            ."ご返信いただいても回答いたしかねます。ご了承ください。\n\n"
            ."☆───────────────────────────────────────────☆\n"
            ."ログイン：https://cost-m.com/\n"
            ."☆───────────────────────────────────────────☆n";

            /**
             * ユーザ(仮データ)の登録
             */
            $str = "update create_users "
            ."set "
            ."create_user_password = '$new_password' "
            ."where "
            ."create_user_mail = '$create_user_id' ";            

            Log::debug('update_sql:'.$str);
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
        // $rules['create_user_id'] = "required|min:8|noneuser";
        $rules['create_user_id'] = "required|noneuser";
        $rules['create_user_mail'] = "required|email";

        /**
         * messages
         */
        $messages = [];
        $messages['create_user_id.required'] = "ユーザIDは必須です。";
        // $messages['create_user_id.min'] = "ユーザIDは8文字以上です。";
        $messages['create_user_id.noneuser'] = "指定されたユーザIDは存在しません。";
        $messages['create_user_mail.required'] = "E-mailは必須です。";
        $messages['create_user_mail.email'] = "E-mailの形式が不正です。";
    
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