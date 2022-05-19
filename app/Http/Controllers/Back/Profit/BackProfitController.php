<?php

namespace App\Http\Controllers\Back\Profit;

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
class BackProfitController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backProfitInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {
            // 売上一覧取得
            $profit_list = $this->getProfitList($request);

            // 売上合計値取得
            $profit_fee_sum_list = $this->getProfitSumList($request);
            // dd($profit_fee_sum_list);

            $common = new Common();

            // 不動産一覧
            $real_estate_list = $common->getRealEstateList();

            // 勘定科目id
            $profit_account_list = $common->getProfitAccounts();

            // アカウント一覧
            $create_user_list = $common->getCreateUsers();
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backProfit' ,$profit_list ,compact('real_estate_list' ,'profit_account_list' ,'create_user_list' ,'profit_fee_sum_list'));
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
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);
            
            // 勘定科目id
            $profit_account_id = $request->input('profit_account_id');
            Log::debug('$profit_account_id:' .$profit_account_id);

            // 担当者id
            $create_user_id = $request->input('create_user_id');
            Log::debug('$create_user_id:' .$create_user_id);

            // 不動産id
            $real_estate_id = $request->input('real_estate_id');
            Log::debug('$real_estate_id:' .$real_estate_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('$start_date:' .$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('$end_date:' .$end_date);
            
            $str = "select "
            ."profits.profit_id, "
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
            ."create_users.create_user_id = profits.entry_user_id "
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

            $str = $str .$where .$order_by;
            Log::debug('$str:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->paginate(10)->onEachSide(1);

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
     * 合計値取得(sql)
     *
     * @param Request $request
     * @return void
     */
    private function getProfitSumList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);
            
            // 勘定科目id
            $profit_account_id = $request->input('profit_account_id');
            Log::debug('$profit_account_id:' .$profit_account_id);

            // 担当者id
            $create_user_id = $request->input('create_user_id');
            Log::debug('$create_user_id:' .$create_user_id);

            // 不動産id
            $real_estate_id = $request->input('real_estate_id');
            Log::debug('$real_estate_id:' .$real_estate_id);

            // 始期
            $start_date = $request->input('start_date');
            Log::debug('$start_date:' .$start_date);

            // 終期
            $end_date = $request->input('end_date');
            Log::debug('$end_date:' .$end_date);

            // where句
            $where = "";

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

            $str = "select "
            ."count(*) as row_count, "
            ."sum(profit_fee) as profit_fee "
            ."from ( "
            ."select * "
            ."from profits "
            ."where 1 = 1 "
            ."$where "
            .") as t; ";

            // query
            Log::debug('str:'.$str);
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
    public function backProfitNewInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 売上一覧
            $profit_list = $this->getNewList($request);

            $common = new Common();

            // 不動産一覧
            $real_estate_list = $common->getRealEstateList();

            // 勘定科目id
            $profit_account_list = $common->getProfitAccounts();

            // アカウント一覧
            $create_user_list = $common->getCreateUsers();

            // 新規表示の場合、号室不要の為から配列を渡す
            $room_list = [];
    
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backProfitEdit' ,compact('profit_list', 'real_estate_list' ,'create_user_list' ,'profit_account_list', 'room_list'));
    }

    /**
     * 新規(ダミー値取得)
     *
     * @return $ret(空の配列)
     */
    private function getNewList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        
        $obj = new \stdClass();
        
        $obj->profit_id  = '';
        $obj->profit_person_id = '';
        $obj->room_id = '';
        $obj->profit_account_id = '';
        $obj->profit_date  = '';
        $obj->profit_fee = '';
        $obj->profit_memo = '';
        $obj->real_estate_id = '';

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
            $profit_info = $this->getEditList($request);
            $profit_list = $profit_info[0];

            $room_id = $profit_list->room_id;
            Log::debug('room_id:'.$room_id);

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
            $profit_id = $request->input('profit_id');

            // sql
            $str = "select "
            ."profits.profit_id, "
            ."profits.profit_person_id, "
            ."profits.room_id, "
            ."rooms.room_name, "
            ."profits.profit_account_id, "
            ."profits.profit_date, "
            ."profits.profit_fee, "
            ."profits.profit_memo, "
            ."profits.entry_user_id, "
            ."profits.entry_date, "
            ."profits.update_user_id, "
            ."profits.update_date, "
            ."rooms.real_estate_id, "
            ."real_estates.real_estate_name "
            ."from profits "
            ."left join rooms on "
            ."rooms.room_id = profits.room_id "
            ."left join real_estates on "
            ."real_estates.real_estate_id = rooms.real_estate_id "
            ."where profit_id = $profit_id ";

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
    public function backRoomEditEntry(Request $request){
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
        if($request->input('room_id') == ""){

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

        // returnの出力値
        $response = [];

        // 初期値
        $response["status"] = true;

        /**
         * rules
         */
        $rules = [];
        $rules['roon_name'] = "required|max:10";
        $rules['room_size'] = "nullable|max:100";

        /**
         * messages
         */
        $messages = [];
        $messages['roon_name.required'] = "号室は必須です。";
        $messages['roon_name.max'] = "号室の文字数が超過しています。";
        $messages['room_size.max'] = "専有面積の文字数が超過しています。";
    
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
            $bank_info = $this->insertRoom($request);

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
    private function insertRoom(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $room_id = $request->input('room_id');
            $real_estate_id = $request->input('real_estate_name');
            $roon_name = $request->input('roon_name');
            $room_type_id = $request->input('room_type_id');
            $room_size = $request->input('room_size');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 物件名
            if($real_estate_id == null){
                $real_estate_id =0;
            }

            // 号室
            if($roon_name == null){
                $roon_name ='';
            }

            // 種別
            if($room_type_id == null){
                $room_type_id =0;
            }

            // 専有面積
            if($room_size == null){
                $room_size = '';
            }

            // 登録
            $str = "insert "
            ."into "
            ."rooms "
            ."( "
            ."real_estate_id, "
            ."room_name, "
            ."room_size, "
            ."room_type_id, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$real_estate_id, "
            ."'$roon_name', "
            ."'$room_size', "
            ."$room_type_id, "
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
            $room_info = $this->updateRoom($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $room_info['status'];

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
    private function updateRoom(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $room_id = $request->input('room_id');
            $real_estate_id = $request->input('real_estate_name');
            $roon_name = $request->input('roon_name');
            $room_type_id = $request->input('room_type_id');
            $room_size = $request->input('room_size');

            // 現在の日付取得
            $date = now() .'.000';

            // 物件名
            if($real_estate_id == null){
                $real_estate_id =0;
            }

            // 号室
            if($roon_name == null){
                $roon_name ='';
            }

            // 種別
            if($room_type_id == null){
                $room_type_id =0;
            }

            // 専有面積
            if($room_size == null){
                $room_size = '';
            }

            $str = "update "
            ."rooms "
            ."set "
            ."real_estate_id = $real_estate_id, "
            ."room_name = '$roon_name', "
            ."room_size = '$room_size', "
            ."room_type_id = $room_type_id, "
            ."entry_user_id = $session_id, "
            ."entry_date = '$date', "
            ."update_user_id = $session_id, "
            ."update_date = '$date' "
            ."where "
            ."room_id = $room_id ";
            
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
    public function backRoomDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            $room_info = $this->deleteRoom($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $room_info['status'];

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
    private function deleteRoom(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $room_id = $request->input('room_id');

            $str = "delete "
            ."from "
            ."rooms "
            ."where "
            ."room_id = $room_id; ";
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