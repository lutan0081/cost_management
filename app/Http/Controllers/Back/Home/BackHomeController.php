<?php

namespace App\Http\Controllers\Back\Home;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Crypt;

use DateTime;

use Common;

/**
 * ホーム(バックエンド)
 */
class BackHomeController extends Controller
{   
    /**
     *  ホーム(表示)
     *
     * @param Request $request
     * @return view('application.application','list_user_count','list_app_count','list_picture_count','list_contacts_count','list_access_total','list_access_today');
     */
    public function backHomeInit(Request $request)
    {   
        Log::debug('start:' .__FUNCTION__);

        try {
            // 現在の日付から月初を取得
            $start_date = new DateTime('first day of this month');
            $start_date = $start_date->format('Y/m/d');
            Log::debug('start_date:' .$start_date);

            // 現在の日付から来月の月初を取得
            $end_date = new DateTime('last day of this month');
            $end_date = $end_date->format('Y/m/d');
            Log::debug('end_date:' .$end_date);

            // 当月売上
            $thisMonthProfit_info = $this->getThisMonthProfit($request, $start_date, $end_date);
            $thisMonthProfit_list = $thisMonthProfit_info[0];

            // 年間売上
            $thisYearProfit_info = $this->getThisYearProfit($request, $start_date, $end_date);
            $thisYearProfit_list = $thisYearProfit_info[0];

            // 当月経費
            $thisMonthCost_info = $this->getThisMonthCost($request, $start_date, $end_date);
            $thisMonthCost_list = $thisMonthCost_info[0];

            // 年間経費
            $thisYearCost_info = $this->getThisYearCost($request, $start_date, $end_date);
            $thisYearCost_list = $thisYearCost_info[0];

            // 承諾数(売上)
            $profitApproval_info = $this->getProfitApproval($request);
            $profitApproval_list = $profitApproval_info[0];

            // 承諾数(経費)
            $costApproval_info = $this->getCostApproval($request);
            $costApproval_list = $costApproval_info[0];

            // 質問件数(経費)
            $cost_quetion_info = $this->getCostQuestionContents($request);
            $cost_quetion_list = $cost_quetion_info[0];

            // 質問件数(売上)
            $profit_quetion_info = $this->getProfitQuestionContents($request);
            $profit_quetion_list = $profit_quetion_info[0];

            // 新着情報


        // 例外処理
        } catch (\Exception $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);

        return view('back.backHome', compact('thisMonthProfit_list', 'thisYearProfit_list', 'thisMonthCost_list', 'thisYearCost_list', 'profitApproval_list', 'costApproval_list', 'cost_quetion_list', 'profit_quetion_list'));
    }

    /**
     *  当月の売上取得
     *
     * @return $ret(real_estate_agentの件数)
     */
    private function getThisMonthProfit(Request $request, $start_date, $end_date){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select "
        ."count(*) as row_count "
        .",sum(profit_fee) as profit_fee "
        ."from "
        ."profits "
        ."where "
        ."profits.profit_date between '$start_date' and '$end_date' ";

        // 実行
        Log::debug('sql:'.$str);
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 年度の売上取得
     * @param Request $request
     * @param [type] $start_date
     * @param [type] $end_date
     * @return void
     */
    private function getThisYearProfit(Request $request, $start_date, $end_date){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // ====================仕様====================
            // 例：
            // 2022/01/01
            // 2021/08/01 <= 2022/01/01(本日) and 2022/07/31 >= 2022/01/01(本日)
            
            // 2021/12/31
            // 2021/08/01 <= 2021/12/31(本日) and 2022/07/31 >=2021/12/31(本日)
            
            // 2021/07/31
            // 2021/08/01 <= 2021/07/31(本日) and 2022/07/31 >=2021/07/31(本日)
            
            // 本日の日付が8月から12月の場合、
            // 始期のそのまま
            // 終期の年度+1
            
            // 本日の日付が1月から7月の場合
            // 始期の年度-1
            // 終期そのまま
            // ===========================================

            // 本日の日付を取得
            $nowDate = date("Y/m/d");
            Log::debug('nowDate:'.$nowDate);

            // 本日の年度だけを取得
            $nowYear = explode("/",$nowDate)[0];
            Log::debug('nowYear:'.$nowYear);

            // 本日の日付を分割
            $explode_date = explode("/",$nowDate);
            
            // 月日を変数に格納
            $month = $explode_date[1];
            $date = $explode_date[2];

            // 現在の月日を生成
            $nowMonthDate = $month. '/'. $date;
            Log::debug('nowMonthDate:'.$nowMonthDate);


            // 本日の日付が8月から12月の場合、終期が来年の07/31から参照する為、年度を+する
            // 始期のそのまま
            // 終期の年度+1
            if($nowMonthDate >= '08/01' && $nowMonthDate <= '12/31'){
                $start_year = $nowYear;
                Log::debug('$start_year:'. $start_year);

                $end_year = $nowYear + 1;
                Log::debug('$end_year:'. $end_year);
            }

            // 本日の日付が1月から7月の場合、始期が去年の08/01から参照する為、年度を-する
            // 始期の年度-1
            // 終期そのまま
            elseif($nowMonthDate >= '01/01' && $nowMonthDate <= '07/31'){
                $start_year = $nowYear - 1;
                Log::debug('$start_year:'. $start_year);

                $end_year = $nowYear;
                Log::debug('$end_year:'. $end_year);
            }

            /**
             * グローバル変数(config/local.php)から会社決済日取得
             */
            // 始期
            $start_settlement_of_account = $start_year. '/'.  config('local.start_settlement_of_account');
            Log::debug('start_settlement_of_account:' .$start_settlement_of_account);

            // 終期
            $end_settlement_of_account = $end_year. '/'.  config('local.end_settlement_of_account');
            Log::debug('end_settlement_of_account:' .$end_settlement_of_account);

            $str = "select "
            ."count(*) as row_count "
            .",sum(profit_fee) as profit_fee "
            ."from "
            ."profits "
            ."where "
            ."profits.profit_date between '$start_settlement_of_account' and '$end_settlement_of_account' ";

            // 実行
            Log::debug('sql:'.$str);
            $ret = DB::select($str);

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

    /**
     *  当月の経費取得
     *
     * @return $ret(real_estate_agentの件数)
     */
    private function getThisMonthCost(Request $request, $start_date, $end_date){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select "
        ."count(*) as row_count "
        .",sum(outgo_fee) as outgo_fee "
        ."from "
        ."costs "
        ."where "
        ."cost_flag_id = 1 "
        ."and "
        ."costs.account_date between '$start_date' and '$end_date' ";

        // 実行
        Log::debug('sql:'.$str);
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 年度の売上取得
     * @param Request $request
     * @param [type] $start_date
     * @param [type] $end_date
     * @return void
     */
    private function getThisYearCost(Request $request, $start_date, $end_date){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // ====================仕様====================
            // 例：
            // 2022/01/01
            // 2021/08/01 <= 2022/01/01(本日) and 2022/07/31 >= 2022/01/01(本日)
            
            // 2021/12/31
            // 2021/08/01 <= 2021/12/31(本日) and 2022/07/31 >=2021/12/31(本日)
            
            // 2021/07/31
            // 2021/08/01 <= 2021/07/31(本日) and 2022/07/31 >=2021/07/31(本日)
            
            // 本日の日付が8月から12月の場合、
            // 始期のそのまま
            // 終期の年度+1
            
            // 本日の日付が1月から7月の場合
            // 始期の年度-1
            // 終期そのまま
            // ===========================================

            // 本日の日付を取得
            $nowDate = date("Y/m/d");
            Log::debug('nowDate:'.$nowDate);

            // 本日の年度だけを取得
            $nowYear = explode("/",$nowDate)[0];
            Log::debug('nowYear:'.$nowYear);

            // 本日の日付を分割
            $explode_date = explode("/",$nowDate);
            
            // 月日を変数に格納
            $month = $explode_date[1];
            $date = $explode_date[2];

            // 現在の月日を生成
            $nowMonthDate = $month. '/'. $date;
            Log::debug('nowMonthDate:'.$nowMonthDate);


            // 本日の日付が8月から12月の場合、終期が来年の07/31から参照する為、年度を+する
            // 始期のそのまま
            // 終期の年度+1
            if($nowMonthDate >= '08/01' && $nowMonthDate <= '12/31'){
                $start_year = $nowYear;
                Log::debug('$start_year:'. $start_year);

                $end_year = $nowYear + 1;
                Log::debug('$end_year:'. $end_year);
            }

            // 本日の日付が1月から7月の場合、始期が去年の08/01から参照する為、年度を-する
            // 始期の年度-1
            // 終期そのまま
            elseif($nowMonthDate >= '01/01' && $nowMonthDate <= '07/31'){
                $start_year = $nowYear - 1;
                Log::debug('$start_year:'. $start_year);

                $end_year = $nowYear;
                Log::debug('$end_year:'. $end_year);
            }

            /**
             * グローバル変数(config/local.php)から会社決済日取得
             */
            // 始期
            $start_settlement_of_account = $start_year. '/'.  config('local.start_settlement_of_account');
            Log::debug('start_settlement_of_account:' .$start_settlement_of_account);

            // 終期
            $end_settlement_of_account = $end_year. '/'.  config('local.end_settlement_of_account');
            Log::debug('end_settlement_of_account:' .$end_settlement_of_account);

            $str = "select "
            ."count(*) as row_count "
            .",sum(outgo_fee) as outgo_fee "
            ."from "
            ."costs "
            ."where "
            ."cost_flag_id = 1 "
            ."and "
            ."costs.account_date between '$start_settlement_of_account' and '$end_settlement_of_account' ";

            // 実行
            Log::debug('sql:'.$str);
            $ret = DB::select($str);

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

    /**
     *  未承諾件数（売上）
     *
     * @return $ret(real_estate_agentの件数)
     */
    private function getProfitApproval(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select "
        ."count(*) as row_count "
        ."from "
        ."profits "
        ."where "
        ."profits.profit_approval_id = 0";

        // 実行
        Log::debug('sql:'.$str);
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     *  未承諾件数（経費）
     *
     * @return $ret(real_estate_agentの件数)
     */
    private function getCostApproval(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select "
        ."count(*) as row_count "
        ."from "
        ."costs "
        ."where "
        ."costs.approval_id = 0";

        // 実行
        Log::debug('sql:'.$str);
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     *  Q&A（経費）
     *
     * @return $ret(real_estate_agentの件数)
     */
    private function getCostQuestionContents(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select "
        ."count(*) as row_count "
        ."from "
        ."costs "
        ."where "
        ."costs.question_contents != ''";

        // 実行
        Log::debug('sql:'.$str);
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     *  Q&A（売上）
     *
     * @return $ret(real_estate_agentの件数)
     */
    private function getProfitQuestionContents(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        $str = "select "
        ."count(*) as row_count "
        ."from "
        ."profits "
        ."where "
        ."profits.profit_question_contents != ''";

        // 実行
        Log::debug('sql:'.$str);
        $ret = DB::select($str);

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

}