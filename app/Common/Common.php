<?php
namespace App\Common;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

class Common
{   

    /**
     * 家主一覧
     *
     * @return $ret
     */
    public function getOwnerList(){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from owners "
        ."order by owner_id asc ";

        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }

    /**
     * 不動産一覧
     *
     * @return $ret
     */
    public function getRealEstateList(){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from real_estates "
        ."order by real_estate_id asc ";

        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }

    /**
     * 部屋種別
     *
     * @return $ret
     */
    public function getRoomTypeList(){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from room_types "
        ."order by room_type_id asc ";

        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }

    /**
     * 号室
     *
     * @return $ret
     */
    public function getRoomList($room_id){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from rooms "
        ."where rooms.room_id = $room_id "
        ."order by room_id asc ";
        
        Log::debug('str:'.$str);

        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }


    /**
     * 勘定科目
     *
     * @return void
     */
    public function getProfitAccounts(){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from profit_accounts "
        ."order by profit_account_id asc ";

        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }

    /**
     * アカウント一覧
     *
     * @return void
     */
    public function getCreateUsers(){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from create_users "
        ."order by create_user_id asc ";

        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }



    /**
     * 月度
     *
     * @return void
     */
    public function getMonth() {
        Log::debug('log_start:'.__FUNCTION__);
        
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = (int) $i;
        }

        Log::debug('log_end:'.__FUNCTION__);
        return $months; 
    }

    /**
     * 年度(前後5年取得)
     *
     * @return void
     */
    public function getFiveYear() {
        Log::debug('log_start:'.__FUNCTION__);
        
        $days = [];
        $start = date('Y', strtotime('-2 year'));
        $end = date('Y', strtotime('+2 year'));

        for ($i = $start; $i <= $end; $i++) {
            $days[] = (int) $i;
        }

        Log::debug('log_end:'.__FUNCTION__);
        return $days; 
    }

    /**
     * 有無リスト作成
     *
     * @return $ret(健康保険リスト)
     */
    public function getNeeds() {
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select * from needs "
        ."order by sort_id asc ";
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret; 
    }

    /**
     * 日付fフォーマット(年月日)
     * {{ Common::format_date($update->create_date,'Y年m月d日') }}
     * @return return date('Y/m/d', strtotime($date));
     */
    public static function format_date($date, $format='Y/m/d'){
        return date($format, strtotime($date));
    }

    // 年月日
    public static function format_date_jp($date){
        return self::format_date($date,'Y年m月d日');
    }

    // 年月日時分
    public static function format_date_min($date){
        return self::format_date($date,'Y年m月d日H時i分');
    }

    // 年-月-日
    public static function format_date_hy($date){
        return self::format_date($date,'Y-m-d');
    }

    // 数値を三桁区切り
    public static function format_three_digit_separator($money){
        return number_format($money);
    }
}