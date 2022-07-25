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
            $information_info = $this->getInformations($request);
            $information_list = $information_info;

            // ★リクエストパラメータをページネーション用の連想配列に格納★
            $paginate_params = [];

            /**
             * グラフデータ
             */
            $chart_data = $this->getChartData($request);
            // dd($chart_data);


            // // 年月データ(DBから取得を想定)
            // $date_list = [];

            // $d = new \stdClass();
            // $d->ym = '2020/01';
            // $date_list[] = $d;

            // $d = new \stdClass();
            // $d->ym = '2020/02';
            // $date_list[] = $d;

            // $d = new \stdClass();
            // $d->ym = '2020/03';
            // $date_list[] = $d;

            // $d = new \stdClass();
            // $d->ym = '2020/04';
            // $date_list[] = $d;

            // $d = new \stdClass();
            // $d->ym = '2020/05';
            // $date_list[] = $d;

            // // 金額データ(DBから取得を想定)
            // $money_list = [];
            // $m = new \stdClass();
            // $m->money = '20';
            // $money_list[] = $m;

            // $m = new \stdClass();
            // $m->money = '30';
            // $money_list[] = $m;

            // $m = new \stdClass();
            // $m->money = '5';
            // $money_list[] = $m;

            // // 出力値
            // $outPut = [];
            // // 年月データを設定
            // $outPut['date_list'] = $date_list;
            // // 金額データを設定
            // $outPut['money_list'] = $money_list;




            
        // 例外処理
        } catch (\Exception $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);

        // compctは代入名=キーになる
        // キーに名前をつけるときはwith()にする
        return view('back.backHome', $information_list, compact('paginate_params', 'thisMonthProfit_list', 'thisYearProfit_list', 'thisMonthCost_list', 'thisYearCost_list', 'profitApproval_list', 'costApproval_list', 'cost_quetion_list', 'profit_quetion_list'))->with($chart_data);
        // return view('back.backHome', $information_list, compact('paginate_params', 'thisMonthProfit_list', 'thisYearProfit_list', 'thisMonthCost_list', 'thisYearCost_list', 'profitApproval_list', 'costApproval_list', 'cost_quetion_list', 'profit_quetion_list', 'chart_data'));
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
     * 年度の経費取得
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

    /**
     * 新着情報一覧取得
     *
     * @param Request $request
     * @return void
     */
    private function getInformations(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            $str = "select "
            ."informations.information_id "
            .",informations.information_name "
            .",informations.information_type_id "
            .",information_types.information_type_name "
            .",informations.information_contents "
            .",informations.entry_user_id "
            .",informations.entry_date "
            .",informations.update_user_id "
            .",informations.update_date "
            ."from "
            ."informations "
            ."left join information_types on "
            ."information_types.information_type_id = informations.information_type_id ";
            Log::debug('$str:' .$str);

            // 実行
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("information_id desc")->paginate(5)->onEachSide(1);

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
     * グラフデータ
     */
    private function getChartData(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // 初期値
            $ret = [];

            // 本日の日付を取得
            $nowDate = date("Y/m/d");
            Log::debug('nowDate:'.$nowDate);

            // 本日の年度だけを取得
            $nowYear = explode("/",$nowDate)[0];
            Log::debug('nowYear:'.$nowYear);

            // 本日の日付を年月日に分割
            $explode_date = explode("/",$nowDate);
            
            // 月日を変数に格納
            $month = $explode_date[1];
            $date = $explode_date[2];

            // 現在の月日を生成
            $nowMonthDate = $month. '/'. $date;
            Log::debug('nowMonthDate:'.$nowMonthDate);
            
            // 配列初期値（日付）
            $date_list = [];

            // 金額初期値（日付）
            $money_list = [];

            // 現在が8月から12月の場合の処理
            if($nowMonthDate >= '08/01' && $nowMonthDate <= '12/31'){
                Log::debug('現在が8月から12月の場合の処理');


                // 本年度
                $nowYear = $nowYear;
                Log::debug('$nowYear:'. $nowYear);

                // 翌年度
                $nextYear = $nowYear + 1;
                Log::debug('$nextYear:'. $nextYear);

                /**
                 * グラフデータ
                 */               
                $date_list[] = $nowYear. '/08';

                $date_list[] = $nowYear. '/09';

                $date_list[] = $nowYear. '/10';

                $date_list[] = $nowYear. '/11';

                $date_list[] = $nowYear. '/12';
                
                $date_list[] = $nextYear. '/01';

                $date_list[] = $nextYear. '/02';

                $date_list[] = $nextYear. '/03';

                $date_list[] = $nextYear. '/04';

                $date_list[] = $nextYear. '/05';

                $date_list[] = $nextYear. '/06';

                $date_list[] = $nextYear. '/07';
    
                /**
                 * 売上データ
                 */
                /**
                 * 8月～12月までをループ
                 */
                // 月の初期値
                $first_half_month_count = 8;

                for($i = 0; $i < 5; $i++){
                    Log::debug('$i:'. $i);

                    // ループの回数分月を加算
                    $month_count = $first_half_month_count + $i;

                    // 月初・月末を取得
                    $year_month = $nowYear. '-'. $month_count;
                    Log::debug('$year_month:'. $year_month);

                    // 月初取得
                    $first_date = date('Y/m/d', strtotime('first day of ' . $year_month));
                    Log::debug('$first_date:'. $first_date);

                    // 月末取得
                    $last_date = date('Y/m/d', strtotime('last day of ' . $year_month));
                    Log::debug('$last_date:'. $last_date);

                    // 売上データ取得
                    $str = "select "
                    ."count(*) as row_count "
                    .",sum(profit_fee) as profit_fee "
                    ."from "
                    ."profits "
                    ."where "
                    ."profits.profit_date between '$first_date' and '$last_date' ";
                    Log::debug('$str:'. $str);

                    // 実行
                    $profit_fee_info = DB::select($str)[0];

                    // 売上合計値を取得
                    $profit_fee = $profit_fee_info->profit_fee;
                    Log::debug('profit_fee:'.$profit_fee);

                    // 連想配列に売上を設定
                    $money_list[] = $profit_fee;  
                }

                /**
                 * 1月～7月までをループ
                 */
                // 月の初期値
                $second_half_month_count = 1;

                for($i = 0; $i < 7; $i++){
                    Log::debug('$i:'. $i);

                    // ループの回数分月を加算
                    $month_count = $second_half_month_count + $i;

                    // 月初・月末を取得
                    $year_month = $nextYear. '-'. $month_count;
                    Log::debug('$year_month:'. $year_month);

                    // 月初取得
                    $first_date = date('Y/m/d', strtotime('first day of ' . $year_month));
                    Log::debug('$first_date:'. $first_date);

                    // 月末取得
                    $last_date = date('Y/m/d', strtotime('last day of ' . $year_month));
                    Log::debug('$last_date:'. $last_date);

                    // 売上データ取得
                    $str = "select "
                    ."count(*) as row_count "
                    .",sum(profit_fee) as profit_fee "
                    ."from "
                    ."profits "
                    ."where "
                    ."profits.profit_date between '$first_date' and '$last_date' ";
                    Log::debug('$str:'. $str);

                    // 実行
                    $profit_fee_info = DB::select($str)[0];

                    // 売上合計値を取得
                    $profit_fee = $profit_fee_info->profit_fee;
                    Log::debug('profit_fee:'.$profit_fee);

                    // 連想配列に売上を設定
                    $money_list[] = $profit_fee;  
                }

                // 配列デバック
                $arrString = print_r($money_list , true);
                Log::debug('money_list:'.$arrString);

            }

            // 現在が1月から7月の場合の処理
            elseif($nowMonthDate >= '01/01' && $nowMonthDate <= '07/31'){
                Log::debug('現在が1月から7月の場合の処理');

                // 本年度
                $nowYear = $nowYear;
                Log::debug('$nowYear:'. $nowYear);

                // 昨年度
                $last_year = $nowYear - 1;
                Log::debug('$last_year:'. $last_year);

                /**
                 * グラフデータ
                 */               
                $date_list[] = $last_year. '/08';

                $date_list[] = $last_year. '/09';

                $date_list[] = $last_year. '/10';

                $date_list[] = $last_year. '/11';

                $date_list[] = $last_year. '/12';
                
                $date_list[] = $nowYear. '/01';

                $date_list[] = $nowYear. '/02';

                $date_list[] = $nowYear. '/03';

                $date_list[] = $nowYear. '/04';

                $date_list[] = $nowYear. '/05';

                $date_list[] = $nowYear. '/06';

                $date_list[] = $nowYear. '/07';

                /**
                 * 8月～12月までをループ
                 */
                // 月の初期値
                $first_half_month_count = 8;

                for($i = 0; $i < 5; $i++){
                    Log::debug('$i:'. $i);

                    // ループの回数分月を加算
                    $month_count = $first_half_month_count + $i;

                    // 月初・月末を取得
                    $year_month = $last_year. '-'. $month_count;
                    Log::debug('$year_month:'. $year_month);

                    // 月初取得
                    $first_date = date('Y/m/d', strtotime('first day of ' . $year_month));
                    Log::debug('$first_date:'. $first_date);

                    // 月末取得
                    $last_date = date('Y/m/d', strtotime('last day of ' . $year_month));
                    Log::debug('$last_date:'. $last_date);

                    // 売上データ取得
                    $str = "select "
                    ."count(*) as row_count "
                    .",sum(profit_fee) as profit_fee "
                    ."from "
                    ."profits "
                    ."where "
                    ."profits.profit_date between '$first_date' and '$last_date' ";
                    Log::debug('$str:'. $str);

                    // 実行
                    $profit_fee_info = DB::select($str)[0];

                    // 売上合計値を取得
                    $profit_fee = $profit_fee_info->profit_fee;
                    Log::debug('profit_fee:'.$profit_fee);

                    // 連想配列に売上を設定
                    $money_list[] = $profit_fee;  
                }

                /**
                 * 1月～7月までをループ
                 */
                // 月の初期値
                $second_half_month_count = 1;

                for($i = 0; $i < 7; $i++){
                    Log::debug('$i:'. $i);

                    // ループの回数分月を加算
                    $month_count = $second_half_month_count + $i;

                    // 月初・月末を取得
                    $year_month = $nowYear. '-'. $month_count;
                    Log::debug('$year_month:'. $year_month);

                    // 月初取得
                    $first_date = date('Y/m/d', strtotime('first day of ' . $year_month));
                    Log::debug('$first_date:'. $first_date);

                    // 月末取得
                    $last_date = date('Y/m/d', strtotime('last day of ' . $year_month));
                    Log::debug('$last_date:'. $last_date);

                    // 売上データ取得
                    $str = "select "
                    ."count(*) as row_count "
                    .",sum(profit_fee) as profit_fee "
                    ."from "
                    ."profits "
                    ."where "
                    ."profits.profit_date between '$first_date' and '$last_date' ";
                    Log::debug('$str:'. $str);

                    // 実行
                    $profit_fee_info = DB::select($str)[0];

                    // 売上合計値を取得
                    $profit_fee = $profit_fee_info->profit_fee;
                    Log::debug('profit_fee:'.$profit_fee);

                    // 連想配列に売上を設定
                    $money_list[] = $profit_fee;  
                }

                // 配列デバック
                $arrString = print_r($money_list , true);
                Log::debug('money_list:'.$arrString);
            }

            $ret['date_list'] = $date_list;

            // 金額データを設定
            $ret['money_list'] = $money_list;



        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

}