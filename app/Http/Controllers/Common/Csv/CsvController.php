<?php

namespace App\Http\Controllers\Common\Csv;

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

use Response;

/**
 * Csv帳票出力、取込
 */
class CsvController extends Controller
{   
    /**
     *  帳票出力
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function csvDownload(Request $request){

        // 始期
        $start_date = $request->input('start_date');

        // 終期
        $end_date = $request->input('end_date');

        // 売上一覧取得(オブジェクトで受けている)
        $profit_list = $this->getProfitList($request);

        // 配列デバック
        $arrString = print_r($profit_list , true);
        Log::debug('messages:'.$arrString);
        
        // ファイル作成
        $stream = fopen('php://output', 'w');

        // Excelで開いた時、文字化けするためBOMを付ける
        fwrite($stream, pack('C*',0xEF,0xBB,0xBF)); // BOM をつける

        // ヘッダー
        $arr = [];
        $arr[] = 'ID';
        $arr[] = '勘定日';
        $arr[] = '勘定科目';
        $arr[] = '担当者';
        $arr[] = '取引先';
        $arr[] = '物件名';
        $arr[] = '号室';
        $arr[] = '利益額';
        $arr[] = '備考';


        // 書き込み（第2引数が連想配列にする）
        fputcsv($stream, $arr);

        // 利益合計値の初期値
        $profit_sum = 0;

        // 各値ループ->連想配列にする->CSVに書き込み
        foreach ($profit_list as $profit) {

            // profit_listがオフジェクトの為、連想配列にする
            $profit_id = $profit->profit_id;

            $profit_date = $profit->profit_date;

            $profit_account_name = $profit->profit_account_name;

            $create_user_name = $profit->create_user_name;

            $customer_name = $profit->customer_name;
            
            $profit_fee = $profit->profit_fee;

            $real_estate_name = $profit->real_estate_name;

            $room_name = $profit->room_name;

            $profit_memo = $profit->profit_memo;

            // 配列内に格納
            $arr = [];
            $arr[] = $profit_id;
            $arr[] = $profit_date;
            $arr[] = $profit_account_name;
            $arr[] = $customer_name;
            $arr[] = $create_user_name;
            $arr[] = $real_estate_name;
            $arr[] = $room_name;
            $arr[] = $profit_fee;
            $arr[] = $profit_memo;


            // CSVファイル書き込み
            fputcsv($stream, $arr);

            // 利益額を加算していく
            $profit_sum = $profit_sum + $profit_fee; 

        }

        // 最終行に合計値を書き込み
        $arr_total = [];
        $arr_total[] = '';
        $arr_total[] = '';
        $arr_total[] = '';
        $arr_total[] = '';
        $arr_total[] = '';
        $arr_total[] = '';
        $arr_total[] = '合計値';
        $arr_total[] = $profit_sum;

        fputcsv($stream, $arr_total);

        // phpの改行コードを\r\nに変換
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        
        // 文字形式指定
        $csv = mb_convert_encoding($csv, 'SJIS', 'UTF-8');

        $file_name = 'profit_'. $start_date. '_'. $end_date. '.csv';

        $headers = array(

            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='. "$file_name",

        );

        return Response::make($csv, 200, $headers);
        
    }

    /**
     * 一覧(sql)
     *
     * @return $ret(部屋一覧)
     */
    private function getProfitList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('free_word:'.$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            
            // 勘定科目id
            $profit_account_id = $request->input('profit_account_id');
            Log::debug('profit_account_id:'.$profit_account_id);

            // 担当者id
            $create_user_id = $request->input('create_user_id');
            Log::debug('create_user_id:'.$create_user_id);

            // 不動産id
            $real_estate_id = $request->input('real_estate_id');
            Log::debug('real_estate_id:'.$real_estate_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('start_date:'.$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('end_date:'.$end_date);
            
            $str = "select "
            ."profits.profit_id, "
            ."profits.customer_name, "
            ."profits.profit_person_id, "
            ."create_users.create_user_name, "
            ."rooms.real_estate_id, "
            ."real_estates.real_estate_name, "
            ."profits.room_id, "
            ."rooms.room_name, "
            ."profits.profit_account_id, "
            ."profit_accounts.profit_account_name, "
            ."profits.profit_date, "
            ."profits.profit_fee, "
            ."profits.profit_memo, "
            ."profits.entry_user_id, "
            ."profits.entry_date, "
            ."profits.update_user_id, "
            ."profits.update_date "
            ."from profits "
            ."left join create_users on "
            ."create_users.create_user_id = profits.profit_person_id "
            ."left join rooms on "
            ."rooms.room_id = profits.room_id "
            ."left join real_estates on "
            ."real_estates.real_estate_id = rooms.real_estate_id "
            ."left join profit_accounts on "
            ."profit_accounts.profit_account_id = profits.profit_account_id "
            ."where 1 = 1 ";
            
            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){

                $where = $where ."and ifnull(real_estate_name,'') like '%$free_word%'";
                $where = $where ."or ifnull(profit_memo,'') like '%$free_word%'";

            };

            // 勘定項目id
            if($profit_account_id !== null){

                $where = $where ."and profits.profit_account_id = '$profit_account_id' ";
            
            };

            // 担当者id
            if($create_user_id !== null){

                $where = $where ."and profits.profit_person_id = '$create_user_id' ";
            
            };
    
            // 勘定日
            // 始期・終期がnullでない場合
            if(($start_date !== null) && ($end_date !== null)) {

                Log::debug('始期・終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(profit_date >= '$start_date') "
                ."and" 
                ."(profit_date <= '$end_date')";
            };

            // 始期がnullでない場合の処理
            if(($start_date !== null) && ($end_date == null)) {

                Log::debug('始期がnullでない場合の処理');

                $where = $where ."and " 
                ."(profit_date >= '$start_date') "
                ."and" 
                ."(profit_date <= '9999/12/31')";
                
            };

            // 終期がnullでない場合の処理
            if(($start_date == null) && ($end_date !== null)) {

                Log::debug('終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(profit_date >= '1900/01/01') "
                ."and" 
                ."(profit_date <= '$end_date')";

            };

            // order by句
            $order_by = "order by profit_id desc";

            // $str = $str .$where .$order_by;
            $str = $str. $where. $order_by;
            Log::debug('$str:' .$str);

            // resの中に値が代入されている
            $ret = [];
            $ret = DB::select($str);

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }
    

} 