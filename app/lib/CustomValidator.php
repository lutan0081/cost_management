<?php
namespace app\lib;

use Illuminate\Http\Request;

use \Illuminate\Validation\Validator;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

/**
 * 自作のバリデーションクラス
 *
 */
class CustomValidator extends Validator {
    /**
     * メールの重複確認
     * Controller側のrules.massageの設定は小文字でやるvalidateMaildb->maildb
     * valueにController側の$requestの中のkeyの値がくる
     * retrun true|falseのみ
     */
    public function validateMaildb($attribute, $value, $parameters) {
        Log::debug('log_start:' .__FUNCTION__);

        $str = "select * from create_users "
        ."where create_user_mail = "
        ."'$value'";
        Log::debug('sql_common:' .$str);
        $data = DB::select($str);

        // 該当データが存在する場合row=1:false
        if(count($data) > 0){
            Log::debug('E-mail重複確認');
            return false;
        }

        Log::debug('log_end:' .__FUNCTION__);
        return true;
    }

    /**
     * 電話番号のバリデーション
     *
     * @param [type] $attribute
     * @param [type] $value バリデーションの値
     * @param [type] $parameters
     * @return bool true:バリデーションOK、false:バリデーションNG
     */
    public function validateJptel($attribute, $value, $parameters) {

        if(preg_match("/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/", $value)){
            return true;
        }
        return false;
    }

    /**
     * 郵便番号のバリデーション
     *
     * @param [type] $attribute
     * @param [type] $value バリデーションの値
     * @param [type] $parameters
     * @return bool true:バリデーションOK、false:バリデーションNG
     */
    public function validateZip($attribute, $value, $parameters)
    {
        if($value){
            return preg_match('/^(([0-9]{3}-[0-9]{4})|([0-9]{7}))$/', $value);
        } else {
            return true;
        } 
    }
}