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
     *  csvエクスポート（売上）
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
            $arr[] = $create_user_name;
            $arr[] = $customer_name;
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
    
    /**
     * csvインポート
     *
     * @param Request $request
     * @return void
     */
    public function csvImport(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try {

            // return初期値
            $response = [];

            // バリデーション:OK=true NG=false
            $response = $this->editValidation($request);

            if($response["status"] == false){

                Log::debug('validator_status:falseのif文通過');
                return response()->json($response);

            }

            /**
             * 値取得
             */
            // 照会口座id
            $modal_bank_id = $request->input('modal_bank_id');
            Log::debug('modal_bank_id:' .$modal_bank_id);

            /**
             * 分岐
             */
            switch($modal_bank_id){

                case 2:
                    Log::debug('香川銀行の処理');
                    $response = $this->importKagawaCsv($request);
                    break;

                case 3:
                    Log::debug('池田泉州銀行の処理');
                    $response = $this->importIkedasenshuCsv($request);
                    break;

                default:
                Log::debug('その他の処理');
            }

        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $response['status'] = 0;

        } finally {

            if($response['status'] == 1){

                Log::debug('status:trueの処理');

                $response['status'] = true;


            }else{

                Log::debug('status:falseの処理');

                $response['status'] = false;

            }

            // 配列デバック
            $arrString = print_r($response , true);
            Log::debug('importKagawaCsv:'.$arrString);

            Log::debug('log_end:' .__FUNCTION__);
            return response()->json($response);
        }
    }
    
    /**
     * バリデーション(csvインポート)
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
        $rules['modal_img_file'] = "nullable|mimes:csv,txt";
        /**
         * messages
         */
        $messages = [];
        $messages['modal_img_file.mimes'] = "ファイル(csv)でアップロードしてください。";
    
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
     * 香川銀行
     * csv読取->dbにinsert
     *
     * @param Request $request
     * @return void
     */
    private function importKagawaCsv(Request $request){

        Log::debug('log_start:' .__FUNCTION__);

        try {
            // トランザクション
            DB::beginTransaction();

            // returnの初期値
            $ret = [];

            // returnの初期値
            $ret['status'] = 1;
            
            // 照会口座id
            $modal_bank_id = $request->input('modal_bank_id');
            Log::debug('modal_bank_id:' .$modal_bank_id);

            // csvファイル取得
            $file_path  = $request->file('modal_img_file');
            Log::debug('file_path :' .$file_path );

            /**
             * csv読取
             */
            $file = new \SplFileObject($file_path);

            $file->setFlags(
                // CSVとして行を読み込み
                \SplFileObject::READ_CSV |
                // 先読み／巻き戻しで読み込み
                \SplFileObject::READ_AHEAD |
                // 空行を読み飛ばす
                \SplFileObject::SKIP_EMPTY |
                // 行末の改行を読み飛ばす
                \SplFileObject::DROP_NEW_LINE            
            );

            // messageの配列作成
            $message = [];

            // カウント初期値
            $count = 0;

            // csvの配列の件数を取得
            $file->seek(PHP_INT_MAX);
            // 最終行が改行されている為、-1すると最終行をスキップする
            $count_file = $file->key() - 1;
            Log::debug('count_file:'. $count_file);

            // エラーチェックのforeach
            foreach($file as $line){
            
                // 一行目はcontinue
                if ($count == 0) {
                    Log::debug('一行目continueの処理');
                    $count++;
                    continue;
                }

                // // 最終行はstop
                // if ($count == $count_file) {
                //     Log::debug('最終行はstopの処理');
                //     break;
                // }
                
                // スペースを削除する
                // id
                $id = trim(mb_convert_encoding($line[0], 'UTF-8', 'SJIS'));
                
                // 勘定日
                $account_date = trim(mb_convert_encoding($line[1], 'UTF-8', 'SJIS'));
                
                // 摘要・振込名義人
                $financial_summary = trim(mb_convert_encoding($line[2], 'UTF-8', 'SJIS'));
                
                // 入金額
                $income_fee = Common::format_csv_colmun($line[3]);
                Log::debug('income_fee:' .$income_fee);
                
                // 出金額
                $outgo_fee = Common::format_csv_colmun($line[4]);
                Log::debug('income_fee:' .$income_fee);
                
                // 残高
                $balance_fee = Common::format_csv_colmun($line[5]);
                Log::debug('income_fee:' .$income_fee);

                /**
                 * id
                 */
                // 空白の場合
                if($id == null){
                    $message[] = $count. '行目のidが空白です';
                }

                /**
                 * 日付チェック
                 */
                // 空白の場合
                if($account_date == null){
                    $message[] = $count. '行目の日付が空白です';
                }

                // 日付の形式でない場合
                if(!preg_match('/^[1-9]{1}[0-9]{0,3}\/[0-9]{1,2}\/[0-9]{1,2}$/', $account_date)){
                    $message[] = $count. '行目の日付の値が不正です';
                }

                /**
                 * 摘要が空白の場合
                 */
                // 空白の場合
                if($financial_summary == null){
                    $message[] = $count. '行目の摘要が空白です';
                }

                /**
                 * 入金額
                 */
                // 空白でなく、数値に変換できない場合
                if($income_fee !== ''){
                    Log::debug('入金額が空白でない場合の処理');

                    if(is_numeric($income_fee) == false){
                        Log::debug('数値に変換できない場合の処理');
                        $message[] = $count. '行目の入金額の値が不正です';
                    }
                }

                /**
                 * 出金額
                 */
                // 空白でなく、数値に変換できない場合
                if($outgo_fee !== ''){
                    Log::debug('出金額が空白でない場合の処理');

                    if(is_numeric($outgo_fee) == false){
                        Log::debug('数値に変換できない場合の処理');
                        $message[] = $count. '行目の出金額の値が不正です';
                    }

                }

                /**
                 * 出金額
                 */
                // 空白でなく、数値に変換できない場合
                if(is_numeric($balance_fee) == false){
                    Log::debug('数値に変換できない場合の処理');
                    $message[] = $count. '行目の残高の値が不正です';
                }

                // 行数のカウントを加算する
                $count++;
            }

            // エラーメッセージのカウント
            $message_count = count($message);
            Log::debug('message_count:'. $message_count);

            // エラーがある場合の処理
            if($message_count >= 1 ){

                Log::debug('CSVエラーが1個以上ある場合の処理');

                $ret['status'] = 0;

                $ret['message'] = $message;

                return $ret;
                
            }

            // session_id取得
            $session_id = $request->session()->get('create_user_id');

            // カウント初期化
            $count = 0;

            // insertのforeach
            foreach($file as $line){

                // 一行目はcontinue
                if ($count == 0) {
                    Log::debug('一行目continueの処理');
                    $count++;
                    continue;
                }

                // // 最終行はstop
                // if ($count == $count_file) {
                //     Log::debug('最終行はstopの処理');
                //     break;
                // }

                /**
                 * csv値取得
                 */
                $id = trim(mb_convert_encoding($line[0], 'UTF-8', 'SJIS'));
                
                // 勘定日
                $account_date = trim(mb_convert_encoding($line[1], 'UTF-8', 'SJIS'));
                
                // 摘要
                $financial_summary = trim(mb_convert_encoding($line[2], 'UTF-8', 'SJIS'));
                
                // 入金額
                $income_fee = Common::format_csv_colmun($line[3]);
                Log::debug('income_fee:' .$income_fee);
                
                // 出金額
                $outgo_fee = Common::format_csv_colmun($line[4]);
                Log::debug('income_fee:' .$income_fee);
                
                // 残高
                $balance_fee = Common::format_csv_colmun($line[5]);
                Log::debug('income_fee:' .$income_fee);

                // 日付
                $date = now() .'.000';

                // 勘定日
                if($account_date == ''){
                    $account_date ='';
                }

                // 摘要
                if($financial_summary == ''){
                    $financial_summary ='';
                }

                // 入金額
                if($income_fee == ''){
                    $income_fee =0;
                }

                // 出金額
                if($outgo_fee == ''){
                    $outgo_fee =0;
                }

                // 残高
                if($balance_fee == ''){
                    $balance_fee =0;
                }

                /**
                 * DBと重複チェック
                 */
                $str = "select * from costs "
                ."where "
                ."(account_date = '$account_date') "
                ."and "
                ."(income_fee = $income_fee) "
                ."and "
                ."(outgo_fee = $outgo_fee) "
                ."and "
                ."(balance_fee = $balance_fee) "
                ."and "
                ."(financial_summary = '$financial_summary') ";

                Log::debug('$str:' .$str);
                $cost_list = DB::select($str);

                // 重複がある場合は、カウントが1以上になる
                $cost_list_count = count($cost_list);
                Log::debug('cost_list_count:'. $cost_list_count);

                // 重複がある場合は、次に行く
                if($cost_list_count >= 1){
                    Log::debug('DBに登録で重複がある場合の処理');
                    $message[] = $count. '行目が重複しています。';
                    $count++;
                    continue;
                }

                /**
                 * insert
                 */
                $str = "insert "
                ."into "
                ."costs "
                ."( "
                ."private_or_bank_id, "
                ."bank_id, "
                ."account_date, "
                ."income_fee, "
                ."outgo_fee, "
                ."balance_fee, "
                ."cost_type, "
                ."cost_account_id, "
                ."cost_memo, "
                ."financial_name, "
                ."financial_branch, "
                ."financial_summary, "
                ."read_flag, "
                ."question_contents, "
                ."answer_contents, "
                ."entry_user_id, "
                ."entry_date, "
                ."update_user_id, "
                ."update_date "
                .")values( "
                ."2, "
                ."$modal_bank_id, "
                ."'$account_date', "
                ."$income_fee, "
                ."$outgo_fee, "
                ."$balance_fee, "
                ."'出金', "
                ."0, "
                ."'', "
                ."'', "
                ."'', "
                ."'$financial_summary', "
                ."0, "
                ."'', "
                ."'', "
                ."$session_id, "
                ."'$date', "
                ."$session_id, "
                ."'$date' "
                ."); ";

                Log::debug('sql_insert:'. $str);
                $ret['status'] = DB::insert($str);

                $count++;
            }

            $ret['message'] = $message;

            DB::commit();

            // スキップの件数、登録件数をmessageで表示する->messageで返す
            
        } catch (\Throwable  $e) {

            // ロールバック
            DB::rollback();

            Log::debug(__FUNCTION__ .':' .$e);

            throw $e;

        } finally {

            Log::debug('log_end:' .__FUNCTION__);
    
            return $ret;
        }
        
    }

    /**
     * 池田泉州銀行
     * csv読取->dbにinsert
     *
     * @param Request $request
     * @return void
     */
    private function importIkedasenshuCsv(Request $request){
        try {
            // トランザクション
            DB::beginTransaction();

            // returnの初期値
            $ret = [];

            // returnの初期値
            $ret['status'] = 1;
            
            // 照会口座id
            $modal_bank_id = $request->input('modal_bank_id');
            Log::debug('modal_bank_id:' .$modal_bank_id);

            // csvファイル取得
            $file_path  = $request->file('modal_img_file');
            Log::debug('file_path :' .$file_path );

            /**
             * csv読取
             */
            $file = new \SplFileObject($file_path);

            $file->setFlags(
                // CSVとして行を読み込み
                \SplFileObject::READ_CSV |
                // 先読み／巻き戻しで読み込み
                \SplFileObject::READ_AHEAD |
                // 空行を読み飛ばす
                \SplFileObject::SKIP_EMPTY |
                // 行末の改行を読み飛ばす
                \SplFileObject::DROP_NEW_LINE            
            );

            // messageの配列作成
            $message = [];

            // カウント初期値
            $count = 0;

            // csvの配列の件数を取得
            $file->seek(PHP_INT_MAX);
            // 最終行が改行されている為、-1すると最終行をスキップする
            $count_file = $file->key() - 1;
            Log::debug('count_file:'. $count_file);

            // エラーチェックのforeach
            foreach($file as $line){
            
                // 一行目はcontinue
                if ($count == 0) {
                    Log::debug('一行目continueの処理');
                    $count++;
                    continue;
                }

                // // 最終行はstop
                // if ($count == $count_file) {
                //     Log::debug('最終行はstopの処理');
                //     break;
                // }
                
                // スペースを削除する
                // id
                $id = trim(mb_convert_encoding($line[0], 'UTF-8', 'SJIS'));
                Log::debug('id:' .$id);

                // 出金額
                $outgo_fee = Common::format_csv_colmun($line[4]);
                Log::debug('outgo_fee:' .$outgo_fee);

                // 入金額
                $income_fee = Common::format_csv_colmun($line[5]);
                Log::debug('income_fee:' .$income_fee);
                
                // 残高
                $balance_fee = Common::format_csv_colmun($line[7]);
                Log::debug('balance_fee:' .$balance_fee);

                // 勘定日
                $account_date = Common::format_csv_date($line[2]);
                Log::debug('account_date:' .$account_date);
                
                // 金融機関名
                $financial_name = trim(mb_convert_encoding($line[10], 'UTF-8', 'SJIS'));
                Log::debug('financial_name:' .$financial_name);

                // 支店名
                $financial_branch = trim(mb_convert_encoding($line[11], 'UTF-8', 'SJIS'));
                Log::debug('financial_branch:' .$financial_branch);

                // 摘要・振込名義人
                $financial_summary = trim(mb_convert_encoding($line[12], 'UTF-8', 'SJIS'));
                Log::debug('financial_summary:' .$financial_summary);

                /**
                 * id
                 */
                // 空白の場合
                if($id == null){
                    $message[] = $count. '行目のidが空白です';
                }

                /**
                 * 日付チェック
                 */
                // 空白の場合
                if($account_date == null){
                    $message[] = $count. '行目の日付が空白です';
                }

                // 日付の形式でない場合
                if(!preg_match('/^[1-9]{1}[0-9]{0,3}\/[0-9]{1,2}\/[0-9]{1,2}$/', $account_date)){
                    $message[] = $count. '行目の日付の値が不正です';
                }

                /**
                 * 出金額
                 */
                // 空白でなく、数値に変換できない場合
                if($outgo_fee !== ''){
                    Log::debug('出金額が空白でない場合の処理');

                    if(is_numeric($outgo_fee) == false){
                        Log::debug('出金額が数値に変換できない場合の処理');
                        $message[] = $count. '行目の出金額の値が不正です';
                    }

                }

                /**
                 * 入金額
                 */
                // 空白でなく、数値に変換できない場合
                if($income_fee !== ''){
                    Log::debug('入金額が空白でない場合の処理');

                    if(is_numeric($income_fee) == false){
                        Log::debug('入金額が数値に変換できない場合の処理');
                        $message[] = $count. '行目の入金額の値が不正です';
                    }
                }


                /**
                 * 残高
                 */
                // 空白でなく、数値に変換できない場合
                if(is_numeric($balance_fee) == false){
                    Log::debug('残高が数値に変換できない場合の処理');
                    $message[] = $count. '行目の残高の値が不正です';
                }

                /**
                 * 摘要が空白の場合
                 */
                // 空白の場合
                if($financial_summary == null){
                    $message[] = $count. '摘要が空白です';
                }
                

                // 行数のカウントを加算する
                $count++;
            }

            // エラーメッセージのカウント
            $message_count = count($message);
            Log::debug('message_count:'. $message_count);

            // エラーがある場合の処理
            if($message_count >= 1 ){

                Log::debug('CSVエラーが1個以上ある場合の処理');

                $ret['status'] = 0;

                $ret['message'] = $message;

                return $ret;
                
            }

            // session_id取得
            $session_id = $request->session()->get('create_user_id');

            // カウント初期化
            $count = 0;

            // insertのforeach
            foreach($file as $line){

                // 一行目はcontinue
                if ($count == 0) {
                    Log::debug('一行目continueの処理');
                    $count++;
                    continue;
                }

                // 最終行はstop
                // if ($count == $count_file) {
                //     Log::debug('最終行はstopの処理');
                //     break;
                // }

                /**
                 * csv値取得
                 */
               // スペースを削除する
                // id
                $id = trim(mb_convert_encoding($line[0], 'UTF-8', 'SJIS'));
                Log::debug('id:' .$id);

                // 出金額
                $outgo_fee = Common::format_csv_colmun($line[4]);
                Log::debug('outgo_fee:' .$outgo_fee);

                // 入金額
                $income_fee = Common::format_csv_colmun($line[5]);
                Log::debug('income_fee:' .$income_fee);
                
                // 残高
                $balance_fee = Common::format_csv_colmun($line[7]);
                Log::debug('balance_fee:' .$balance_fee);

                // 勘定日
                $account_date = Common::format_csv_date($line[2]);
                Log::debug('account_date:' .$account_date);
                
                // 金融機関名
                $financial_name = trim(mb_convert_encoding($line[10], 'UTF-8', 'SJIS'));
                Log::debug('financial_name:' .$financial_name);

                // 支店名
                $financial_branch = trim(mb_convert_encoding($line[11], 'UTF-8', 'SJIS'));
                Log::debug('financial_branch:' .$financial_branch);

                // 摘要・振込名義人
                $financial_summary = trim(mb_convert_encoding($line[12], 'UTF-8', 'SJIS'));
                Log::debug('financial_summary:' .$financial_summary);

                // 日付
                $date = now() .'.000';

                // 勘定日
                if($account_date == ''){
                    $account_date ='';
                }

                // 金融機関名
                if($financial_name == ''){
                    $financial_name ='';
                }

                // 支店名
                if($financial_branch == ''){
                    $financial_branch ='';
                }
    
                // 摘要
                if($financial_summary == ''){
                    $financial_summary ='';
                }

                // 入金額
                if($income_fee == ''){
                    $income_fee = 0;
                }

                // 出金額
                if($outgo_fee == ''){
                    $outgo_fee = 0;
                }

                // 残高
                if($balance_fee == ''){
                    $balance_fee = 0;
                }

                /**
                 * DBと重複チェック
                 */
                $str = "select * from costs "
                ."where "
                ."(account_date = '$account_date') "
                ."and "
                ."(income_fee = $income_fee) "
                ."and "
                ."(outgo_fee = $outgo_fee) "
                ."and "
                ."(balance_fee = $balance_fee) "
                ."and "
                ."(financial_name = '$financial_name') "
                ."and "
                ."(financial_branch = '$financial_branch') "
                ."and "
                ."(financial_summary = '$financial_summary') ";
                
                Log::debug('$str:' .$str);
                $cost_list = DB::select($str);

                // 重複がある場合は、カウントが1以上になる
                $cost_list_count = count($cost_list);
                Log::debug('cost_list_count:'. $cost_list_count);

                // 重複がある場合は、次に行く
                if($cost_list_count >= 1){
                    Log::debug('DBに登録で重複がある場合の処理');
                    $message[] = $count. '行目が重複しています。';
                    $count++;
                    continue;
                }

                /**
                 * insert
                 */
                $str = "insert "
                ."into "
                ."costs "
                ."( "
                ."private_or_bank_id, "
                ."bank_id, "
                ."account_date, "
                ."income_fee, "
                ."outgo_fee, "
                ."balance_fee, "
                ."cost_type, "
                ."cost_account_id, "
                ."cost_memo, "
                ."financial_name, "
                ."financial_branch, "
                ."financial_summary, "
                ."read_flag, "
                ."question_contents, "
                ."answer_contents, "
                ."entry_user_id, "
                ."entry_date, "
                ."update_user_id, "
                ."update_date "
                .")values( "
                ."2, "
                ."$modal_bank_id, "
                ."'$account_date', "
                ."$income_fee, "
                ."$outgo_fee, "
                ."$balance_fee, "
                ."'出金', "
                ."0, "
                ."'', "
                ."'$financial_name', "
                ."'$financial_branch', "
                ."'$financial_summary', "
                ."0, "
                ."'', "
                ."'', "
                ."$session_id, "
                ."'$date', "
                ."$session_id, "
                ."'$date' "
                ."); ";

                Log::debug('sql_insert:'. $str);
                $ret['status'] = DB::insert($str);

                $count++;
            }

            $ret['message'] = $message;

            DB::commit();

            // スキップの件数、登録件数をmessageで表示する->messageで返す
            
        } catch (\Throwable  $e) {

            // ロールバック
            DB::rollback();

            Log::debug(__FUNCTION__ .':' .$e);

            throw $e;

        } finally {

            Log::debug('log_end:' .__FUNCTION__);
    
            return $ret;
        }
    }

    /**
     * エラーメッセージエクスポート
     *
     * @return void
     */
    public function csvMessageExport(Request $request){

        Log::debug('log_start:' .__FUNCTION__);

        // import時のエラーメッセージ
        $messages = $request->input('message');

        // ','で分割->配列で格納
        $message_list = explode(",", $messages);

        /**
         * ファイル生成、書込み
         */
        // 保存先のパス生成
        $file_path = storage_path('backup/import_backup.txt');

        // メッセージ格納
        foreach ($message_list as $message => $value) {

            Log::debug('value:'.$value);

            // ファイルがなければ作成、あれば上書きする
            \File::append($file_path, $value. "\n");
            
        }
        
        // ファイルの種類宣言
        $headers = ['Content-Type' => 'text/plain'];

        // ファイル名
        $filename = 'import_backup.txt';

        Log::debug('log_end:' .__FUNCTION__);

        // ダウンロード
        return response()->download($file_path, $filename, $headers)->deleteFileAfterSend(true);
    }
} 