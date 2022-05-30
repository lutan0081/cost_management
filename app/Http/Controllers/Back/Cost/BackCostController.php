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

            // 出金合計
            $outgo_fee_sum_list = $this->getOutgoFeeSumList($request);

            // 入金合計
            $income_fee_sum_list = $this->getIncomeFeeSumList($request);
            // dd($income_fee_sum_list);
            
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

            // ★リクエストパラメータをページネーション用の連想配列に格納★
            $paginate_params = [

                'free_word' => $free_word,
                'bank_id' => $bank_id,
                'cost_account_id' => $cost_account_id,
                'private_or_bank_id' => $private_or_bank_id,
                'start_date' => $start_date,
                'end_date' => $end_date,

            ];
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backCost', $cost_list, compact('paginate_params' ,'outgo_fee_sum_list', 'income_fee_sum_list' ,'bank_list' ,'cost_account_list' ,'private_or_bank_list', 'free_word', 'bank_id', 'cost_account_id', 'private_or_bank_id', 'start_date', 'end_date'));
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

            $str = $str .$where;
            Log::debug('$str:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("cost_id desc")->paginate(20)->onEachSide(1);

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

            // 共通クラスインスタンス化
            $common = new Common();

            // 銀行一覧
            $bank_list = $common->getBanks();

            // 出金区分
            $private_or_bank_list = $common->getPrivateOrBanks();
            
            // 勘定科目
            $cost_account_list = $common->getCostAccounts();


            // 取引区分

            // // 不動産一覧
            // $real_estate_list = $common->getRealEstateList();

            // // 勘定科目id
            // $profit_account_list = $common->getProfitAccounts();

            // // アカウント一覧
            // $create_user_list = $common->getCreateUsers();

            // // 新規表示の場合、号室不要の為から配列を渡す
            // $room_list = [];
    
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backCostEdit' ,compact('cost_list', 'bank_list', 'private_or_bank_list', 'cost_account_list'));
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
        $obj->approval_date= '';
        $obj->question_contents= '';
        $obj->answer_contents= '';
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
    public function backProfitEditInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $cost_info = $this->getEditList($request);
            $cost_list = $cost_info[0];

            $common = new Common();

            // 不動産一覧
            $real_estate_list = $common->getRealEstateList();

            // 勘定科目id
            $profit_account_list = $common->getProfitAccounts();

            // アカウント一覧
            $create_user_list = $common->getCreateUsers();

            // 号室
            $room_list = $common->getRoomList($room_id);
            // 配列デバック
            $arrString = print_r($room_list , true);
            Log::debug('room_list:'.$arrString);

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backProfitEdit' ,compact('profit_list', 'real_estate_list' ,'create_user_list' ,'profit_account_list', 'room_list'));
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
     * 登録分岐(新規/編集)
     *
     * @param $request(edit.blade.phpの各項目)
     * @return $response(status:true=OK/false=NG)
     */
    public function backProfitEditEntry(Request $request){
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
        if($request->input('profit_id') == ""){

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
        $rules['profit_account_date'] = "required|date";
        $rules['profit_fee'] = "required|integer";
        $rules['profit_memo'] = "nullable|max:100";
        $rules['customer_name'] = "nullable|max:50";

        /**
         * messages
         */
        $messages = [];

        $messages['profit_account_date.required'] = "勘定日は必須です。";
        $messages['profit_account_date.date'] = "勘定日の形式が不正です。";

        $messages['profit_fee.required'] = "利益額は必須です。";
        $messages['profit_fee.integer'] = "利益額の形式が不正です。";

        $messages['profit_memo.max'] = "備考の形式が不正です。";

        $messages['customer_name.max'] = "取引先の文字数が超過しています。";
    
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

            // retrun初期値
            $ret = [];
            $ret['status'] = true;

            /**
             * status:OK=1 NG=0
             */
            $bank_info = $this->insertProfit($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $bank_info['status'];

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
     * 新規登録
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function insertProfit(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $profit_person_id = $request->input('profit_person_id');
            $profit_account_id = $request->input('profit_account_id');
            $profit_account_date = $request->input('profit_account_date');
            $profit_fee = $request->input('profit_fee');
            $real_estate_id = $request->input('real_estate_id');
            $room_id = $request->input('room_id');
            $profit_memo = $request->input('profit_memo');
            $customer_name = $request->input('customer_name');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 売上担当id
            if($profit_person_id == null){
                $profit_person_id = 0;
            }

            // 勘定科目id
            if($profit_account_id == null){
                $profit_account_id = 0;
            }

            // 勘定日
            if($profit_account_date == null){
                $profit_account_date = '';
            }

            // 利益額
            if($profit_fee == null){
                $profit_fee = 0;
            }

            // 不動産id
            if($real_estate_id == null){
                $real_estate_id = 0;
            }

            // 号室id
            if($room_id == null){
                $room_id = 0;
            }

            // 備考
            if($profit_memo == null){
                $profit_memo = '';
            }

            // 取引先
            if($customer_name == null){
                $customer_name = '';
            }


            $str = "insert "
            ."into "
            ."cost_management.profits "
            ."( "
            ."profit_person_id, "
            ."customer_name, "
            ."room_id, "
            ."profit_account_id, "
            ."profit_date, "
            ."profit_fee, "
            ."profit_memo, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$profit_person_id, "
            ."'$customer_name', "
            ."$room_id, "
            ."$profit_account_id, "
            ."'$profit_account_date', "
            ."'$profit_fee', "
            ."'$profit_memo', "
            ."$session_id, "
            ."'$date', "
            ."$session_id, "
            ."'$date' "
            ."); ";

            Log::debug('sql:'.$str);

            // OK=1/NG=0
            $ret['status'] = DB::insert($str);

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
             * status:OK=1 NG=0
             */
            $profit_info = $this->updateProfit($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $profit_info['status'];

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
    private function updateProfit(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $profit_id = $request->input('profit_id');
            $profit_person_id = $request->input('profit_person_id');
            $profit_account_id = $request->input('profit_account_id');
            $profit_account_date = $request->input('profit_account_date');
            $profit_fee = $request->input('profit_fee');
            $real_estate_id = $request->input('real_estate_id');
            $room_id = $request->input('room_id');
            $profit_memo = $request->input('profit_memo');
            $customer_name = $request->input('customer_name');

            // 現在の日付取得
            $date = now() .'.000';

            // 取引先
            if($customer_name == null){
                $customer_name = '';
            }

            // 売上担当id
            if($profit_person_id == null){
                $profit_person_id = 0;
            }

            // 勘定科目id
            if($profit_account_id == null){
                $profit_account_id = 0;
            }

            // 勘定日
            if($profit_account_date == null){
                $profit_account_date = '';
            }

            // 利益額
            if($profit_fee == null){
                $profit_fee = 0;
            }

            // 不動産id
            if($real_estate_id == null){
                $real_estate_id = 0;
            }

            // 号室id
            if($room_id == null){
                $room_id = 0;
            }

            // 備考
            if($profit_memo == null){
                $profit_memo = '';
            }

            $str = "update "
            ."cost_management.profits "
            ."set "
            ."profit_person_id = $profit_person_id, "
            ."customer_name = '$customer_name', "
            ."room_id = $room_id, "
            ."profit_account_id = $profit_account_id, "
            ."profit_date = '$profit_account_date', "
            ."profit_fee = $profit_fee, "
            ."profit_memo = '$profit_memo', "
            ."entry_user_id = $session_id, "
            ."entry_date = '$date', "
            ."update_user_id = $session_id, "
            ."update_date = '$date' "
            ."where "
            ."profit_id = $profit_id; ";
            
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
    public function backProfitDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            $profit_info = $this->deleteProfit($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $profit_info['status'];

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
     * 削除
     *
     * @param Request $request
     * @return void
     */
    private function deleteProfit(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $profit_id = $request->input('profit_id');

            $str = "delete "
            ."from "
            ."profits "
            ."where "
            ."profit_id = $profit_id; ";
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
} 