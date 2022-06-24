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
            $start_date = $start_date->format('Y-m-d'). ' 00:00:00.000';
            Log::debug('start_date:' .$start_date);

            // 現在の日付から来月の月初を取得
            $end_date = new DateTime('first day of next month');
            $end_date = $end_date->format('Y-m-d'). ' 00:00:00.000';
            Log::debug('end_date:' .$end_date);

            // 当月売上
            $thisMonthProfit_list = $this->getThisMonthProfit($request, $start_date, $end_date);
            // dd($thisMonthProfit_list);

            // 年間売上
            $thisYearProfit_list = $this->getThisYearProfit($request, $start_date, $end_date);

        // 例外処理
        } catch (\Exception $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);

        return view('back.backHome', compact('thisMonthProfit_list'));
    }

    /**
     * 当月の売上習得
     * @param Request $request
     * @param [type] $start_date
     * @param [type] $end_date
     * @return void
     */
    private function getThisMonthProfit(Request $request, $start_date, $end_date){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            $str = "select "
            ."count(*) as row_count, "
            ."sum(profit_fee) as profit_fee "
            ."from profits "
            ."where "
            ."profits.profit_date between '2022/06/01' and '2022/06/30' ";

            // 実行
            Log::debug('sql:'.$str);
            $ret = DB::select($str)[0];

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

    /**
     * 年月の売上習得
     * @param Request $request
     * @param [type] $start_date
     * @param [type] $end_date
     * @return void
     */
    private function getThisYearProfit(Request $request, $start_date, $end_date){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            $str = "select "
            ."count(*) as row_count, "
            ."sum(profit_fee) as profit_fee "
            ."from profits "
            ."where "
            ."profits.profit_date between '2022/06/01' and '2022/06/30' ";

            // 実行
            Log::debug('sql:'.$str);
            $ret = DB::select($str)[0];

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

}