<?php

namespace App\Http\Controllers\Back\Cost;

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

// config内のapp.phpに定義
use Common;

use App\Config;

/**
 * 表示・登録、編集、削除
 */
class BackCostController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backCostInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 経費一覧取得
            $cost_list = $this->getCostList($request);
            // dd($cost_list);

            // 出金合計
            $outgo_fee_sum_list = $this->getOutgoFeeSumList($request);

            // 入金合計
            $income_fee_sum_list = $this->getIncomeFeeSumList($request);
            
            // 共通クラス
            $common = new Common();

            // 銀行一覧
            $bank_list = $common->getBanks();

            // 勘定科目id
            $cost_account_list = $common->getCostAccounts();

            // 取引区分
            $private_or_bank_list = $common->getPrivateOrBanks();

            /**
             * フォームに値を保持させるためにそのまま返す
             */
            // フリーワード
            $free_word = $request->input('free_word');
            
            // 照会口座id
            $bank_id = $request->input('bank_id');
            
            // 勘定科目id
            $cost_account_id = $request->input('cost_account_id');

            // 取引区分id
            $private_or_bank_id = $request->input('private_or_bank_id');

            // 始期
            $start_date = $request->input('start_date');

            // 終期
            $end_date = $request->input('end_date');

            // 経費
            $cost_flag_id = $request->input('cost_flag_id');

            // 承認前
            $approval_id = $request->input('approval_id');

            // Q&A
            $question_contents = $request->input('question_contents');

            // ★リクエストパラメータをページネーション用の連想配列に格納★
            $paginate_params = [

                'free_word' => $free_word,
                'bank_id' => $bank_id,
                'cost_account_id' => $cost_account_id,
                'private_or_bank_id' => $private_or_bank_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'cost_flag_id' => $cost_flag_id,
                'approval_id' => $approval_id,
                'question_contents' => $question_contents,

            ];
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backCost', $cost_list, compact('paginate_params' ,'outgo_fee_sum_list', 'income_fee_sum_list' ,'bank_list' ,'cost_account_list' ,'private_or_bank_list', 'free_word', 'bank_id', 'cost_account_id', 'private_or_bank_id', 'start_date', 'end_date' ,'cost_flag_id', 'approval_id', 'question_contents'));
    }

    /**
     * 一覧(sql)
     *
     * @return $ret(部屋一覧)
     */
    private function getCostList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);
            
            // 照会口座id
            $bank_id = $request->input('bank_id');
            Log::debug('$bank_id:' .$bank_id);

            // 勘定科目
            $cost_account_id = $request->input('cost_account_id');
            Log::debug('$cost_account_id:' .$cost_account_id);

            // 取引区分
            $private_or_bank_id = $request->input('private_or_bank_id');
            Log::debug('$private_or_bank_id:' .$private_or_bank_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('$start_date:' .$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('$end_date:' .$end_date);

            // 経費id
            $cost_flag_id = $request->input('cost_flag_id');
            Log::debug('$cost_flag_id:' .$cost_flag_id);

            // 承認前is
            $approval_id = $request->input('approval_id');
            Log::debug('$approval_id:' .$approval_id);

            $question_contents = $request->input('question_contents');
            Log::debug('$question_contents:' .$question_contents);

            $str = "select "
            ."cost_id, "
            ."costs.private_or_bank_id, "
            ."private_or_banks.private_or_bank_name, "
            ."costs.bank_id, "
            ."banks.bank_name, "
            ."banks.bank_number, "
            ."costs.account_date, "
            ."costs.income_fee, "
            ."costs.outgo_fee, "
            ."costs.balance_fee, "
            ."costs.cost_account_id, "
            ."cost_accounts.cost_account_name, "
            ."costs.cost_memo, "
            ."costs.financial_name, "
            ."costs.financial_branch, "
            ."costs.financial_summary, "
            ."costs.approval_id, "
            ."costs.approval_date, "
            ."costs.question_contents, "
            ."costs.answer_contents, "
            ."costs.cost_flag_id, "
            ."costs.deadline_flag, "
            ."costs.entry_user_id, "
            ."costs.entry_date, "
            ."costs.update_user_id, "
            ."costs.update_date "
            ."from costs "
            ."left join private_or_banks on "
            ."private_or_banks.private_or_bank_id = costs.private_or_bank_id "
            ."left join banks on "
            ."banks.bank_id = costs.bank_id "
            ."left join cost_accounts on "
            ."cost_accounts.cost_account_id = costs.cost_account_id "            
            ."where 1 = 1 ";
            
            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){
                $where = $where ."and ifnull(cost_memo,'') like '%$free_word%'";
                $where = $where ."or ifnull(summary,'') like '%$free_word%'";
            };

            // 勘定項目id
            if($cost_account_id !== null){
                $where = $where ."and costs.cost_account_id = '$cost_account_id' ";
            };

            // 照会口座
            if($bank_id !== null){
                $where = $where ."and costs.bank_id = '$bank_id' ";
            };

            // 個人又は預金
            if($private_or_bank_id !== null){
                $where = $where ."and costs.private_or_bank_id = '$private_or_bank_id' ";
            };
    
            // 勘定日
            // 始期・終期がnullでない場合
            if(($start_date !== null) && ($end_date !== null)) {
                Log::debug('始期・終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '$end_date')";
            };

            // 始期がnullでない場合の処理
            if(($start_date !== null) && ($end_date == null)) {
                Log::debug('始期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '9999/12/31')";
            };

            // 終期がnullでない場合の処理
            if(($start_date == null) && ($end_date !== null)) {
                Log::debug('終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '1900/01/01') "
                ."and" 
                ."(account_date <= '$end_date')";
            };

            // 経費か否か
            if($cost_flag_id == 'on'){
                Log::debug('経費にチェックされてる場合の処理');
                $where = $where ."and costs.cost_flag_id = 1 ";
            }

            // 承諾前のみ表示
            if($approval_id == 'on'){
                Log::debug('承認前にチェックされてる場合の処理');
                $where = $where ."and costs.approval_id = 0 ";
            }

            // Q&Aの表示
            if($question_contents == 'on'){
                Log::debug('Q&Aにチェックされてる場合の処理');
                $where = $where ."and costs.question_contents != '' ";
            }
            
            $str = $str .$where;
            Log::debug('$str:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("cost_id desc")->paginate(30)->onEachSide(1);

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
     * 出金合計(sql)
     *
     * @param Request $request
     * @return void
     */
    private function getOutgoFeeSumList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);
            
            // 照会口座id
            $bank_id = $request->input('bank_id');
            Log::debug('$bank_id:' .$bank_id);

            // 勘定科目
            $cost_account_id = $request->input('cost_account_id');
            Log::debug('$cost_account_id:' .$cost_account_id);

            // 取引区分
            $private_or_bank_id = $request->input('private_or_bank_id');
            Log::debug('$private_or_bank_id:' .$private_or_bank_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('$start_date:' .$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('$end_date:' .$end_date);

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){

                $where = $where ."and ifnull(cost_memo,'') like '%$free_word%'";
                $where = $where ."or ifnull(summary,'') like '%$free_word%'";

            };

            // 勘定項目id
            if($cost_account_id !== null){

                $where = $where ."and costs.cost_account_id = '$cost_account_id' ";
            
            };

            // 照会口座
            if($bank_id !== null){

                $where = $where ."and costs.bank_id = '$bank_id' ";
            
            };
    
            // 勘定日
            // 始期・終期がnullでない場合
            if(($start_date !== null) && ($end_date !== null)) {

                Log::debug('始期・終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '$end_date')";
            };

            // 始期がnullでない場合の処理
            if(($start_date !== null) && ($end_date == null)) {

                Log::debug('始期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '9999/12/31')";
                
            };

            // 終期がnullでない場合の処理
            if(($start_date == null) && ($end_date !== null)) {

                Log::debug('終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '1900/01/01') "
                ."and" 
                ."(account_date <= '$end_date')";

            };

            $str = "select "
            ."count(*) as row_count, "
            ."sum(outgo_fee) as outgo_fee "
            ."from "
            ."( "
            ."select "
            ."costs.cost_id, "
            ."costs.private_or_bank_id, "
            ."costs.bank_id, "
            ."costs.account_date, "
            ."costs.income_fee, "
            ."costs.outgo_fee, "
            ."costs.balance_fee, "
            ."costs.cost_account_id, "
            ."costs.cost_memo, "
            ."costs.financial_name, "
            ."costs.financial_branch, "
            ."costs.financial_summary, "
            ."costs.entry_user_id, "
            ."costs.entry_date, "
            ."costs.update_user_id, "
            ."costs.update_date "
            ."from costs "
            ."where 1 = 1 "
            ."$where "
            .")as t ";

            // query
            Log::debug('str_sum:'.$str);
            $ret = DB::select($str)[0];

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

    /**
     * 入金額合計(sql)
     *
     * @param Request $request
     * @return void
     */
    private function getIncomeFeeSumList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);
            
            // 照会口座id
            $bank_id = $request->input('bank_id');
            Log::debug('$bank_id:' .$bank_id);

            // 勘定科目
            $cost_account_id = $request->input('cost_account_id');
            Log::debug('$cost_account_id:' .$cost_account_id);

            // 取引区分
            $private_or_bank_id = $request->input('private_or_bank_id');
            Log::debug('$private_or_bank_id:' .$private_or_bank_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('$start_date:' .$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('$end_date:' .$end_date);

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){

                $where = $where ."and ifnull(cost_memo,'') like '%$free_word%'";
                $where = $where ."or ifnull(summary,'') like '%$free_word%'";

            };

            // 勘定項目id
            if($cost_account_id !== null){

                $where = $where ."and costs.cost_account_id = '$cost_account_id' ";
            
            };

            // 照会口座
            if($bank_id !== null){

                $where = $where ."and costs.bank_id = '$bank_id' ";
            
            };
    
            // 勘定日
            // 始期・終期がnullでない場合
            if(($start_date !== null) && ($end_date !== null)) {

                Log::debug('始期・終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '$end_date')";
            };

            // 始期がnullでない場合の処理
            if(($start_date !== null) && ($end_date == null)) {

                Log::debug('始期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '9999/12/31')";
                
            };

            // 終期がnullでない場合の処理
            if(($start_date == null) && ($end_date !== null)) {

                Log::debug('終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '1900/01/01') "
                ."and" 
                ."(account_date <= '$end_date')";

            };

            $str = "select "
            ."count(*) as row_count, "
            ."sum(income_fee) as income_fee "
            ."from "
            ."( "
            ."select "
            ."costs.cost_id, "
            ."costs.private_or_bank_id, "
            ."costs.bank_id, "
            ."costs.account_date, "
            ."costs.income_fee, "
            ."costs.outgo_fee, "
            ."costs.balance_fee, "
            ."costs.cost_account_id, "
            ."costs.cost_memo, "
            ."costs.financial_name, "
            ."costs.financial_branch, "
            ."costs.financial_summary, "
            ."costs.entry_user_id, "
            ."costs.entry_date, "
            ."costs.update_user_id, "
            ."costs.update_date "
            ."from costs "
            ."where 1 = 1 "
            ."$where "
            .")as t ";

            // query
            Log::debug('str_sum:'.$str);
            $ret = DB::select($str)[0];

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }

    /**
     * 個人経費(sql)
     *
     * @param Request $request
     * @return void
     */
    private function getPrivateFeeSumList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);
            
            // 照会口座id
            $bank_id = $request->input('bank_id');
            Log::debug('$bank_id:' .$bank_id);

            // 勘定科目
            $cost_account_id = $request->input('cost_account_id');
            Log::debug('$cost_account_id:' .$cost_account_id);

            // 取引区分
            $private_or_bank_id = $request->input('private_or_bank_id');
            Log::debug('$private_or_bank_id:' .$private_or_bank_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('$start_date:' .$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('$end_date:' .$end_date);

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){

                $where = $where ."and ifnull(cost_memo,'') like '%$free_word%'";
                $where = $where ."or ifnull(summary,'') like '%$free_word%'";

            };

            // 勘定項目id
            if($cost_account_id !== null){

                $where = $where ."and costs.cost_account_id = '$cost_account_id' ";
            
            };

            // 照会口座
            if($bank_id !== null){

                $where = $where ."and costs.bank_id = '$bank_id' ";
            
            };
    
            // 勘定日
            // 始期・終期がnullでない場合
            if(($start_date !== null) && ($end_date !== null)) {

                Log::debug('始期・終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '$end_date')";
            };

            // 始期がnullでない場合の処理
            if(($start_date !== null) && ($end_date == null)) {

                Log::debug('始期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '$start_date') "
                ."and" 
                ."(account_date <= '9999/12/31')";
                
            };

            // 終期がnullでない場合の処理
            if(($start_date == null) && ($end_date !== null)) {

                Log::debug('終期がnullでない場合の処理');

                $where = $where ."and " 
                ."(account_date >= '1900/01/01') "
                ."and" 
                ."(account_date <= '$end_date')";

            };

            $str = "select "
            ."count(*) as row_count, "
            ."sum(income_fee) as income_fee "
            ."from "
            ."( "
            ."select "
            ."costs.cost_id, "
            ."costs.private_or_bank_id, "
            ."costs.bank_id, "
            ."costs.account_date, "
            ."costs.income_fee, "
            ."costs.outgo_fee, "
            ."costs.balance_fee, "
            ."costs.cost_type, "
            ."costs.cost_account_id, "
            ."costs.cost_memo, "
            ."costs.financial_name, "
            ."costs.financial_branch, "
            ."costs.financial_summary, "
            ."costs.entry_user_id, "
            ."costs.entry_date, "
            ."costs.update_user_id, "
            ."costs.update_date "
            ."from costs "
            ."where 1 = 1 "
            ."$where "
            .")as t ";

            // query
            Log::debug('str_sum:'.$str);
            $ret = DB::select($str)[0];

        }catch(\Throwable $e) {

            throw $e;

        }finally{

        };

        Log::debug('log_end:'.__FUNCTION__);

        return $ret;
    }
    
    /**
     *  新規(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backCostNewInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 経費一覧
            $cost_list = $this->getNewList($request);

            // 写真一覧
            $cost_img_list = [];

            // 共通クラスインスタンス化
            $common = new Common();

            // 銀行一覧
            $bank_list = $common->getBanks();

            // 出金区分
            $private_or_bank_list = $common->getPrivateOrBanks();
            
            // 勘定科目
            $cost_account_list = $common->getCostAccounts();

            // 画像種別
            $cost_img_type_list = $common->getImgTypes();

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backCostEdit' ,compact('cost_list', 'bank_list', 'private_or_bank_list', 'cost_account_list', 'cost_img_type_list', 'cost_img_list'));
    }

    /**
     * 新規(ダミー値取得)
     *
     * @return $ret(空の配列)
     */
    private function getNewList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        
        $obj = new \stdClass();
        
        $obj->cost_id= '';
        $obj->private_or_bank_id= '';
        $obj->private_or_bank_name= '';
        $obj->bank_id= '';
        $obj->bank_name= '';
        $obj->bank_branch_name= '';
        $obj->account_date= '';
        $obj->income_fee= '';
        $obj->outgo_fee= '';
        $obj->balance_fee= '';
        $obj->cost_account_id= '';
        $obj->cost_account_name= '';
        $obj->cost_memo= '';
        $obj->financial_name= '';
        $obj->financial_branch= '';
        $obj->financial_summary= '';
        $obj->approval_id= '';
        $obj->approval_date= '';
        $obj->question_contents= '';
        $obj->create_user_name= '';
        $obj->answer_contents= '';
        $obj->cost_flag_id= '';
        $obj->deadline_flag= '';
        $obj->entry_user_id= '';
        $obj->entry_date= '';
        $obj->update_user_id= '';
        $obj->update_date= '';
        
        $ret = [];
        $ret = $obj;

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 不動産のコンボボックスを変更した場合の号室取得
     *
     * @param Request $request
     * @return void
     */
    public function backRealEstateChangeInit(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        
        // return初期値
        $response = [];

        /**
         * status:OK=1 NG=0
         */
        $room_list = $this->getRoom($request);

        /**
         * returnのステータス
         * room_list,status
         */
        $response['room_list'] = $room_list['room_list'];

        // js側での判定のステータス(true:OK/false:NG)
        $response["status"] = $room_list['status'];

        // 配列デバック
        $arrString = print_r($response , true);
        Log::debug('messages:'.$arrString);

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * 号室取得
     *
     * @param Request $request(edit.blade.phpの各項目)
     * @return ret(true:登録OK/false:登録NG、maxId(contract_id)、session_id(create_user_id))
     */
    private function getRoom(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {

            // retrun初期値
            $ret = [];

            /**
             * 不動産id
             */
            $real_estate_id = $request->input('real_estate_id');

            // sql
            $str = "select * from rooms "
            ."where rooms.real_estate_id = $real_estate_id "
            ."order by room_name asc, room_id desc";

            Log::debug('str:' . $str);

            // 値格納
            $ret['room_list'] = DB::select($str);

            // 戻り値
            $ret['status'] = 1;
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $ret['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($ret['status'] == 1){

                Log::debug('status:trueの処理');
                $ret['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $ret['status'] = false;
            }

            Log::debug('log_end:'.__FUNCTION__);
            return $ret;
        }
    }

    /**
     *  編集(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backCostEditInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $cost_info = $this->getEditList($request);
            $cost_list = $cost_info[0];

            // 画像パス取得
            $cost_img_list = $this->getImgList($request);

            // 共通クラスインスタンス化
            $common = new Common();

            // 銀行一覧
            $bank_list = $common->getBanks();

            // 出金区分
            $private_or_bank_list = $common->getPrivateOrBanks();
            
            // 勘定科目
            $cost_account_list = $common->getCostAccounts();

            // 画像種別
            $cost_img_type_list = $common->getImgTypes();

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backCostEdit' ,compact('cost_list', 'bank_list', 'private_or_bank_list', 'cost_account_list', 'cost_img_type_list', 'cost_img_list'));
    }

    /**
     * 編集(表示:sql)
     *
     * @return void
     */
    private function getEditList(Request $request){

        Log::debug('start:' .__FUNCTION__);

        try{
            // 値設定
            $cost_id = $request->input('cost_id');

            // sql
            $str = "select "
            ."costs.cost_id, "
            ."costs.private_or_bank_id, "
            ."private_or_banks.private_or_bank_name, "
            ."costs.bank_id, "
            ."banks.bank_name, "
            ."banks.bank_branch_name, "
            ."costs.account_date, "
            ."costs.income_fee, "
            ."costs.outgo_fee, "
            ."costs.balance_fee, "
            ."costs.cost_account_id, "
            ."cost_accounts.cost_account_name, "
            ."costs.cost_memo, "
            ."costs.financial_name, "
            ."costs.financial_branch, "
            ."costs.financial_summary, "
            ."costs.approval_id, "
            ."create_users.create_user_name, "
            ."costs.approval_date, "
            ."costs.question_contents, "
            ."costs.answer_contents, "
            ."costs.cost_flag_id, "
            ."costs.deadline_flag, "
            ."costs.entry_user_id, "
            ."costs.entry_date, "
            ."costs.update_user_id, "
            ."costs.update_date "
            ."from "
            ."costs "
            ."left join private_or_banks on "
            ."private_or_banks.private_or_bank_id = costs.private_or_bank_id "
            ."left join banks on "
            ."banks.bank_id = costs.bank_id "
            ."left join create_users on "
            ."create_users.create_user_id = costs.approval_id "
            ."left join cost_accounts on "
            ."cost_accounts.cost_account_id = costs.cost_account_id "
            ."where cost_id = $cost_id ";

            Log::debug('sql:' .$str);
            
            $ret = DB::select($str);

        // 例外処理
        } catch (\Exception $e) {

            throw $e;

        } finally {
        }
        
        Log::debug('start:' .__FUNCTION__);
        return $ret;
    }

    /**
     * 編集(画像一覧取得)
     *
     * @param Request $request
     * @return void
     */
    private function getImgList(Request $request){

        Log::debug('start:' .__FUNCTION__);

        try{
            // 値設定
            $cost_id = $request->input('cost_id');

            $str = "select "
            ."cost_imgs.cost_img_id, "
            ."cost_imgs.cost_id, "
            ."cost_imgs.cost_img_type_id, "
            ."cost_img_types.cost_img_type_name, "
            ."cost_imgs.cost_img_path, "
            ."cost_imgs.cost_img_memo, "
            ."cost_imgs.entry_user_id, "
            ."cost_imgs.entry_date, "
            ."cost_imgs.update_user_id, "
            ."cost_imgs.update_date "
            ."from "
            ."cost_imgs "
            ."left join cost_img_types on "
            ."cost_img_types.cost_img_type_id = cost_imgs.cost_img_type_id "
            ."where cost_imgs.cost_id = $cost_id ";
            
            $ret = DB::select($str);

        } catch (\Throwable $e) {

            throw $e;

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return $ret;
    }

    /**
     * 登録分岐(新規/編集)
     *
     * @param $request(edit.blade.phpの各項目)
     * @return $response(status:true=OK/false=NG)
     */
    public function backCostEditEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        
        // return初期値
        $response = [];

        // バリデーション:OK=true NG=false
        $response = $this->editValidation($request);

        if($response["status"] == false){

            Log::debug('validator_status:falseのif文通過');
            return response()->json($response);

        }

        /**
         * id=無:insert
         * id=有:update
         */
        // 新規登録
        if($request->input('cost_id') == ""){

            Log::debug('新規の処理');

            // $responseの値設定
            $ret = $this->insertData($request);

        // 編集登録
        }else{

            Log::debug('編集の処理');

            // $responseの値設定
            $ret = $this->updateData($request);

        }

        // js側での判定のステータス(true:OK/false:NG)
        $response["status"] = $ret['status'];

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * バリデーション
     *
     * @param Request $request(bladeの項目)
     * @return response(status=NG/msg="入力を確認して下さい/messages=$msgs/$errkeys=$keys)
     */
    private function editValidation(Request $request){

        /**
         * 値取得
         */
        $guarantor_flag = $request->input('guarantor_flag');

        // returnの出力値
        $response = [];

        // 初期値
        $response["status"] = true;

        /**
         * rules
         */
        $rules = [];
        $rules['financial_name'] = "nullable|max:100";
        $rules['financial_branch'] = "nullable|max:100";
        $rules['financial_summary'] = "nullable|max:100";
        $rules['account_date'] = "required|date";
        $rules['outgo_fee'] = "required|integer";
        $rules['income_fee'] = "required|integer";
        $rules['balance_fee'] = "nullable|integer";
        $rules['cost_memo'] = "nullable|max:100";
        $rules['question_contents'] = "nullable|max:500";
        $rules['answer_contents'] = "nullable|max:500";

        /**
         * 画像
         * nullableが効かない為、if文で判定
         */
        $img_file = $request->file('img_file');
        Log::debug('バリデーション_img_file:' .$img_file);

        if($img_file !== null){

            Log::debug('画像が添付されています');
            $rules['img_file'] = "nullable|mimes:jpeg,png,jpg";

        }
    
        $rules['img_text'] = "nullable|max:20";
        /**
         * messages
         */
        $messages = [];

        $messages['financial_name.max'] = "金融機関名の文字数が超過しています。";
        $messages['financial_branch.max'] = "支店名の文字数が超過しています。";
        $messages['financial_summary.max'] = "摘要の文字数が超過しています。";
        $messages['account_date.required'] = "勘定日は必須です。";
        $messages['account_date.date'] = "勘定日の形式が不正です。";
        $messages['outgo_fee.required'] = "出金額は必須です。。";
        $messages['outgo_fee.integer'] = "出金額の値が不正です。";
        $messages['income_fee.required'] = "入金額は必須です。";
        $messages['income_fee.integer'] = "入金額の値が不正です。";
        $messages['balance_fee.integer'] = "残高の値が不正です。";
        $messages['question_contents.max'] = "質問の文字数が超過しています。";
        $messages['answer_contents.max'] = "回答の文字数が超過しています。";
        $messages['cost_memo.max'] = "備考飲み時数が超過しています。";
        
        $img_file = $request->file('img_file');
        Log::debug('バリデーション_img_file:' .$img_file);

        if($img_file !== null){

            Log::debug('画像が添付されています');
            $messages['img_file.mimes'] = "画像ファイル(jpg.jpeg.png)でアップロードして下さい。";

        }
    
        $messages['img_text.max'] = "備考の文字数が超過しています。";
    
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
     * 新規登録(各テーブルに分岐)
     *
     * @param Request $request(edit.blade.phpの各項目)
     * @return ret(true:登録OK/false:登録NG、maxId(contract_id)、session_id(create_user_id))
     */
    private function insertData(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {

            // トランザクション
            DB::beginTransaction();

            // retrun初期値
            $ret = [];
            $ret['status'] = true;

            /**
             * 経費
             */
            $cost_info = $this->insertCost($request);

            $ret['status'] = $cost_info['status'];

            // 登録時のcost_idを取得
            $cost_id = $cost_info['cost_id'];
            Log::debug('cost_id:'.$cost_id);
            
            /**
             * 画像
             */
            $cost_img_info = $this->insertImg($request, $cost_id);

            $ret['status'] = $cost_img_info['status'];
            
            // コミット
            DB::commit();

        // 例外処理
        } catch (\Throwable $e) {

            // ロールバック
            DB::rollback();

            Log::debug(__FUNCTION__ .':' .$e);

            $ret['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($ret['status'] == 1){

                Log::debug('status:trueの処理');
                $ret['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $ret['status'] = false;
            }

            Log::debug('log_end:'.__FUNCTION__);
            return $ret;
        }
    }

    /**
     * 新規登録
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function insertCost(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $bank_id = $request->input('bank_id');
            $financial_name = $request->input('financial_name');
            $financial_branch = $request->input('financial_branch');
            $financial_summary = $request->input('financial_summary');
            $private_or_bank_id = $request->input('private_or_bank_id');
            $account_date = $request->input('account_date');
            $cost_account_id = $request->input('cost_account_id');
            $outgo_fee = $request->input('outgo_fee');
            $income_fee = $request->input('income_fee');
            $balance_fee = $request->input('balance_fee');
            $cost_memo = $request->input('cost_memo');
            $question_contents = $request->input('question_contents');
            $answer_contents = $request->input('answer_contents');
            $cost_flag_id = $request->input('cost_flag_id');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 金融機関名
            if($financial_name == null){
                $financial_name = '';
            }

            // 支店名
            if($financial_branch == null){
                $financial_branch = '';
            }

            // 摘要
            if($financial_summary == null){
                $financial_summary = '';
            }

            // 個人又は預金
            if($private_or_bank_id == null){
                $private_or_bank_id = 0;
            }

            // 勘定日
            if($account_date == null){
                $account_date = '';
            }

            // 勘定科目id
            if($cost_account_id == null){
                $cost_account_id = 0;
            }

            // 出金額
            if($outgo_fee == null){
                $outgo_fee = 0;
            }

            // 入金額
            if($income_fee == null){
                $income_fee = 0;
            }

            // 残高
            if($balance_fee == null){
                $balance_fee = 0;
            }

            // 備考
            if($cost_memo == null){
                $cost_memo = '';
            }

            // 質問
            if($question_contents == null){
                $question_contents = '';
            }

            // 回答
            if($answer_contents == null){
                $answer_contents = '';
            }

            // 銀行id
            if($bank_id == null){
                $bank_id = 0;
            }

            // 経費か否かid
            if($cost_flag_id == null){
                $cost_flag_id = 0;
            }
            
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
            ."cost_account_id, "
            ."cost_memo, "
            ."financial_name, "
            ."financial_branch, "
            ."financial_summary, "
            ."approval_id, "
            ."approval_date, "
            ."question_contents, "
            ."answer_contents, "
            ."cost_flag_id, "
            ."deadline_flag, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$private_or_bank_id, "
            ."$bank_id, "
            ."'$account_date', "
            ."$income_fee, "
            ."$outgo_fee, "
            ."$balance_fee, "
            ."$cost_account_id, "
            ."'$cost_memo', "
            ."'$financial_name', "
            ."'$financial_branch', "
            ."'$financial_summary', "
            ."0, "
            ."'', "
            ."'$question_contents', "
            ."'$answer_contents', "
            ."$cost_flag_id, "
            ."0, "
            ."$session_id, "
            ."'$date', "
            ."$session_id, "
            ."'$date' "
            ."); ";

            Log::debug('sql:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::insert($str);

            // 登録したapplication_id取得
            $str = "select * from costs "
            ."where "
            ."(outgo_fee = $outgo_fee) "
            ."and "
            ."(income_fee = $income_fee) "
            ."and "
            ."(balance_fee = $balance_fee) "
            ."and "
            ."(entry_date = '$date') ";

            Log::debug('select_cost:'.$str);
            $cost_info = DB::select($str);

            // 経費id
            $ret['cost_id'] = $cost_info[0]->cost_id;

        // 例外処理
        } catch (\Throwable  $e) {

            throw $e;

        // status:OK=1/NG=0
        } finally {
            
        }

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }


    /**
     * 付属書類(登録)
     *
     * @param Request $request
     * @return void
     */
    private function insertImg(Request $request, $cost_id){
        Log::debug('log_start:'.__FUNCTION__);

        try {

            /**
             * 値取得
             */
            // session_id
            $session_id = $request->session()->get('create_user_id');

            $img_file = $request->file('img_file');
            Log::debug('img_file:'.$img_file);

            // 付属書類がない場合の処理
            if($img_file == null){

                Log::debug('付属書類がない場合の処理');

                $ret['status'] = 1;

                return $ret;

            }

            // 種別
            $img_type = $request->input('img_type');
            Log::debug('img_type:'.$img_type);

            // 備考
            $img_text = $request->input('img_text');
            Log::debug('img_text:'.$img_text);

            // 現在の日付取得
            $date = now() .'.000';
        
            // idごとのフォルダ作成のためのパス生成
            $dir ='img/' .$cost_id;
            
            // 任意のフォルダ作成
            // ※appを入れるとエラーになる
            Storage::makeDirectory('/public/' .$dir);

            /**
             * 画像登録処理
             */
            // ファイル名変更
            $file_name = time() .'.' .$img_file->getClientOriginalExtension();
            Log::debug('ファイル名:'.$file_name);

            // ファイルパス+ファイル名
            $tmp_file_path = $dir .'/' .$file_name;
            Log::debug('tmp_file_path :'.$tmp_file_path);

            // ※ここにappを入れないとエラーになる
            InterventionImage::make($img_file)->resize(380, null,
            function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path('app/public/' .$tmp_file_path));

            /**
             * 種別idがnullの場合、0を入れる
             */
            if($img_type == null){
                $img_type = 0;
            }

            /**
             * 画像データ(insert)
             */
            $str = "insert "
            ."into "
            ."cost_imgs "
            ."( "
            ."cost_id, "
            ."cost_img_type_id, "
            ."cost_img_path, "
            ."cost_img_memo, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$cost_id, "
            ."$img_type, "
            ."'$tmp_file_path', "
            ."'$img_text', "
            ."$session_id, "
            ."'$date', "
            ."$session_id, "
            ."'$date' "
            ."); ";
            
            Log::debug('sql:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::insert($str);

            Log::debug('status:'.$ret);
            
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

            // storage/app/public/imagesから、画像ファイルを削除する
            Storage::delete($tmp_file_path);

            throw $e;

        }finally{

            Log::debug('log_end:'.__FUNCTION__);
            return $ret;

        }
    }

    /**
     * 編集登録(各テーブルに分岐)
     *
     * @param Request $request(edit.blade.phpの各項目)
     * @return ret(true:登録OK/false:登録NG)
     */
    private function updateData(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            
            // retrun初期値
            $ret = [];
            $ret['status'] = true;

            /**
             * 経費概要
             */
            $cost_info = $this->updateCost($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $cost_info['status'];

            /**
             * 補足資料
             */
            $cost_id = $request->input('cost_id');

            $cost_img_info = $this->insertImg($request, $cost_id);

            $ret['status'] = $cost_img_info['status'];


        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $ret['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($ret['status'] == 1){

                Log::debug('status:trueの処理');
                $ret['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $ret['status'] = false;
            }

            Log::debug('log_end:'.__FUNCTION__);
            return $ret;
        }
    }

    /**
     * 編集
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function updateCost(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $cost_id = $request->input('cost_id');
            $bank_id = $request->input('bank_id');
            $financial_name = $request->input('financial_name');
            $financial_branch = $request->input('financial_branch');
            $financial_summary = $request->input('financial_summary');
            $private_or_bank_id = $request->input('private_or_bank_id');
            $account_date = $request->input('account_date');
            $cost_account_id = $request->input('cost_account_id');
            $outgo_fee = $request->input('outgo_fee');
            $income_fee = $request->input('income_fee');
            $balance_fee = $request->input('balance_fee');
            $cost_memo = $request->input('cost_memo');
            $question_contents = $request->input('question_contents');
            $answer_contents = $request->input('answer_contents');
            $cost_flag_id = $request->input('cost_flag_id');
            
            // 現在の日付取得
            $date = now() .'.000';

            // 金融機関名
            if($financial_name == null){
                $financial_name = '';
            }

            // 支店名
            if($financial_branch == null){
                $financial_branch = '';
            }

            // 摘要
            if($financial_summary == null){
                $financial_summary = '';
            }

            // 個人又は預金
            if($private_or_bank_id == null){
                $private_or_bank_id = 0;
            }

            // 勘定日
            if($account_date == null){
                $account_date = '';
            }

            // 勘定科目id
            if($cost_account_id == null){
                $cost_account_id = 0;
            }

            // 出金額
            if($outgo_fee == null){
                $outgo_fee = 0;
            }

            // 入金額
            if($income_fee == null){
                $income_fee = 0;
            }

            // 残高
            if($balance_fee == null){
                $balance_fee = 0;
            }

            // 備考
            if($cost_memo == null){
                $cost_memo = '';
            }

            // 質問
            if($question_contents == null){
                $question_contents = '';
            }

            // 回答
            if($answer_contents == null){
                $answer_contents = '';
            }

            // 銀行id
            if($bank_id == null){
                $bank_id = 0;
            }

            // 経費か否かid
            if($cost_flag_id == null){
                $cost_flag_id = 0;
            }

            $str = "update "
            ."costs "
            ."set "
            ."private_or_bank_id = $private_or_bank_id, "
            ."bank_id = $bank_id, "
            ."account_date = '$account_date', "
            ."income_fee = $income_fee, "
            ."outgo_fee = $outgo_fee, "
            ."balance_fee = $balance_fee, "
            ."cost_account_id = $cost_account_id, "
            ."cost_memo = '$cost_memo', "
            ."financial_name = '$financial_name', "
            ."financial_branch = '$financial_branch', "
            ."financial_summary = '$financial_summary', "
            ."approval_id = 0, "
            ."approval_date = '', "
            ."question_contents = '$question_contents', "
            ."answer_contents = '$answer_contents', "
            ."cost_flag_id = $cost_flag_id, "
            ."deadline_flag = 0, "
            ."update_user_id = $session_id, "
            ."update_date = '$date' "
            ."where "
            ."cost_id = $cost_id; ";
            
            Log::debug('sql:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::update($str);

        // 例外処理
        } catch (\Throwable  $e) {

            throw $e;

        // status:OK=1/NG=0
        } finally {
            
        }

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 削除
     *
     * @param Request $request
     * @return void
     */
    public function backCostDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            DB::beginTransaction();

            // return初期値
            $response = [];

            /**
             * 経費概要
             */
            $cost_info = $this->deleteCost($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $cost_info['status'];

            /**
             * 補足資料
             */
            $img_info = $this->deleteEntryImg($request);

            // js側での判定のステータス(true:OK/false:NG)
            $ret['status'] = $img_info['status'];

            // js側での判定のステータス(true:OK/false:NG)
            $response["status"] = $ret['status'];

            DB::commit();

        // 例外処理
        } catch (\Throwable $e) {

            DB::rollback();

            Log::debug(__FUNCTION__ .':' .$e);

            $response['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($response['status'] == 1){

                Log::debug('status:trueの処理');
                $response['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $response['status'] = false;
            }

        }

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * 削除(sql)
     *
     * @param Request $request
     * @return void
     */
    private function deleteCost(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $cost_id = $request->input('cost_id');

            $str = "delete "
            ."from "
            ."costs "
            ."where "
            ."cost_id = $cost_id; ";
            Log::debug('str:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::delete($str);

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            throw $e;

        // status:OK=1/NG=0
        } finally {

        }

        Log::debug('log_end:' .__FUNCTION__);
        return $ret;
    }

    /**
     * 削除(画像)
     *
     * @param Request $request
     * @return void
     */
    public function deleteEntryImg(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            $ret = [];

            // 値取得
            $cost_id = $request->input('cost_id');

            /**
             * 画像削除
             * 1.契約者Idごとの画像データ取得
             * 2.パス取得
             * 3.フォルダ削除
             * 4.データ(DB)削除
             */
            $str = "select * from cost_imgs "
            ."where cost_id = '$cost_id' ";
            Log::debug('select_sql:'.$str);
            $img_list = DB::select($str);

            // デバック
            $arrString = print_r($img_list , true);
            Log::debug('log_Imgs:'.$arrString);

            /**
             * 画像データが存在しない場合
             * 削除対象が無のため、return=trueを返却
             */
            if(count($img_list) == 0){

                Log::debug('画像データが存在しない場合の処理');

                $ret['status'] = 1;

                return $ret;
            }

            // 画像パスを"/"で分解->配列化
            $arr = explode('/', $img_list[0]->cost_img_path);

            // appを除外し文字結合(public/img/214)
            $img_dir_path = $arr[0] ."/" .$arr[1];

            // フォルダ削除
            Storage::deleteDirectory('/public/' .$img_dir_path);

            // 画像データ削除(DB)
            $str = "delete from cost_imgs "
            ."where cost_id = '$cost_id' ";
            Log::debug('delete_sql:'.$str);

            $ret['status'] = DB::delete($str);
            Log::debug($ret['status']);
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            throw $e;

        // status:OK=1/NG=0
        } finally {

        }

        Log::debug('log_end:' .__FUNCTION__);
        return $ret;
    }

    /**
     * 削除(画像:詳細)
     *
     * @param Request $request
     * @return $ret['status'] OK=true/NG=false
     */
    public function backDeleteEntryImgDetail(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // トランザクション
            DB::beginTransaction();

            $response = [];

            // 値取得
            $cost_img_id = $request->input('img_id');

            /**
             * 画像削除
             * 1.契約者Idごとの画像データ取得
             * 2.パス取得
             * 3.フォルダ削除
             * 4.データ(DB)削除
             */
            $str = "select * from cost_imgs "
            ."where cost_img_id = '$cost_img_id' ";
            Log::debug('select_sql:'.$str);
            $img_list = DB::select($str);

            // デバック
            $arrString = print_r($img_list , true);
            Log::debug('imgs:'.$arrString);

            // 画像データが存在しない場合、削除対象が無のため、return=trueを返却
            if(count($img_list) == 0){

                Log::debug('画像が存在しない場合の処理');

                $ret['status'] = true;

                // コミット(記載無しの場合、処理が実行されない)
                DB::commit();

                return response()->json($response);

            }
            
            /**
             * 画像ファイル削除
             */
            // 画像パスを"/"で分解->配列化
            $img_name_path = $img_list[0]->cost_img_path;
            Log::debug('img_name_path:'.$img_name_path);

            // ファイル削除(例:Storage::delete('public/img/214/1637578613.jpg');
            Storage::delete('/public/' .$img_name_path);

            /**
             * 画像フォルダ削除
             */
             // 画像パスを"/"で分解->配列化
            $arr = explode('/', $img_list[0]->cost_img_path);
            $img_dir_path = $arr[0] ."/" .$arr[1];

            // フォルダの中身を確認
            $img_arr = Storage::files('/public/' .$img_dir_path);

            // デバック(ファイルの中身を確認)
            Log::debug('img_arr:'.$arrString);
            $arrString = print_r($img_arr , true);

            // 参照の値が空白の場合、フォルダ削除
            if(empty($img_arr)){

                Log::debug('フォルダの中身がない場合の処理');

                // フォルダ削除
                Storage::deleteDirectory('/public/' .$img_dir_path);
            }

            // 画像データ削除(DB)
            $str = "delete from cost_imgs "
            ."where cost_img_id = '$cost_img_id' ";
            Log::debug('delete_sql:'.$str);

            $response['status'] = DB::delete($str);
            Log::debug($response['status']);
            
            // コミット
            DB::commit();

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            DB::rollback();

            $response['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($response['status'] == 1){

                Log::debug('status:trueの処理');
                $response['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $response['status'] = false;
            }

        }

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * 承認の処理
     *
     * @param Request $request
     * @return void
     */
    public function backCostApprovalEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $response = [];

            /**
             * 経費概要
             */
            $approval_info = $this->updateApproval($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $approval_info['status'];

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            $response['status'] = 0;

        // status:OK=1/NG=0
        } finally {

            if($response['status'] == 1){

                Log::debug('status:trueの処理');
                $response['status'] = true;

            }else{

                Log::debug('status:falseの処理');
                $response['status'] = false;
            }

        }

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * 承認の処理(sql)
     *
     * @param Request $request
     * @return void
     */
    private function updateApproval(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            /**
             * 値取得
             */
            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('session_id:'.$session_id);

            // 経費id
            $cost_id = $request->input('cost_id');

            // 日付
            $date = now() .'.000';

            $str = "update costs "
            ."set "
            ."approval_id=$session_id "
            .",approval_date='$date' "
            .",update_user_id=$session_id "
            .",update_date='$date' "
            ."where "
            ."cost_id=$cost_id ";
            Log::debug('str:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::update($str);

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug(__FUNCTION__ .':' .$e);

            throw $e;

        // status:OK=1/NG=0
        } finally {

        }

        Log::debug('log_end:' .__FUNCTION__);
        return $ret;
    }
} 