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

            // フリーワード
            $free_word = $request->input('free_word');
            
            // 勘定科目id
            $profit_account_id = $request->input('profit_account_id');
            
            // 担当者id
            $create_user_id = $request->input('create_user_id');
            Log::debug('end:' .__FUNCTION__);

            // 不動産id
            $real_estate_id = $request->input('real_estate_id');

            // 始期
            $start_date = $request->input('start_date');

            // 終期
            $end_date = $request->input('end_date');
            
            // ページネーションにキーワード
            $paginate_params = [

                'free_word' => $free_word,
                'profit_account_id' => $profit_account_id,
                'create_user_id' => $create_user_id,
                'real_estate_id' => $real_estate_id,
                'start_date' => $start_date,
                'end_date' => $end_date,

            ];
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backProfit', $profit_list, compact('paginate_params' ,'real_estate_list' ,'profit_account_list' ,'create_user_list' ,'profit_fee_sum_list', 'free_word', 'profit_account_id', 'create_user_id', 'real_estate_id', 'start_date', 'end_date'));
    }

    /**
     * 一覧(sql)
     *
     * @return $ret
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
            ."profits.customer_name, "
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
            ."profit_approval_id, "
            ."profit_approval_date, "
            ."profit_question_contents, "
            ."profit_answer_contents, "
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
                $where = $where ."or ifnull(customer_name,'') like '%$free_word%'";
            };

            // 勘定項目id
            if($profit_account_id !== null){

                $where = $where ."and profits.profit_account_id = '$profit_account_id' ";
            
            };

            // 担当者id
            if($create_user_id !== null){

                $where = $where ."and profits.profit_person_id = '$create_user_id' ";
            
            };

            // 不動産id
            if($real_estate_id !== null){

                $where = $where ."and rooms.real_estate_id = '$real_estate_id' ";
            
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
            $res = DB::table($alias)->selectRaw("*")->paginate(20)->onEachSide(1);

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

            // 不動産id
            if($real_estate_id !== null){

                $where = $where ."and rooms.real_estate_id = '$real_estate_id' ";
            
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
            ."select "
            ."profits.profit_id, "
            ."profits.profit_person_id, "
            ."profits.customer_name, "
            ."profits.room_id, "
            ."profits.profit_account_id, "
            ."profits.profit_date, "
            ."profits.profit_fee, "
            ."profits.profit_memo, "
            ."profits.entry_user_id, "
            ."profits.entry_date, "
            ."profits.update_user_id, "
            ."profits.update_date "
            ."from profits "
            ."left join rooms on "
            ."rooms.room_id = profits.room_id "
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

            // 画像一覧
            $profit_img_list = [];

            $common = new Common();

            // 不動産一覧
            $real_estate_list = $common->getRealEstateList();

            // 勘定科目id
            $profit_account_list = $common->getProfitAccounts();

            // アカウント一覧
            $create_user_list = $common->getCreateUsers();

            // 画像種別
            $profit_img_type_list = $common->getProfitImgTypes();

            // 銀行名
            $profit_bank_list = $common->getBanks();

            // 新規表示の場合、号室不要の為から配列を渡す
            $room_list = [];
    
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backProfitEdit' ,compact('profit_list', 'real_estate_list' ,'create_user_list' ,'profit_account_list', 'room_list', 'profit_img_type_list', 'profit_img_list', 'profit_bank_list'));
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
        $obj->customer_name = '';
        $obj->profit_approval_id = '';
        $obj->profit_approval_date = '';
        $obj->profit_question_contents = '';
        $obj->profit_answer_contents = '';
        $obj->create_user_name = '';
        $obj->bank_id = '';
        
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
     * 編集(画像一覧取得)
     *
     * @param Request $request
     * @return void
     */
    private function getImgList(Request $request){

        Log::debug('start:' .__FUNCTION__);

        try{
            // 値設定
            $profit_id = $request->input('profit_id');

            $str = "select "
            ."profit_imgs.profit_img_id, "
            ."profit_imgs.profit_id, "
            ."profit_imgs.profit_img_type_id, "
            ."profit_img_types.profit_img_type_name, "
            ."profit_imgs.profit_img_path, "
            ."profit_imgs.profit_img_memo, "
            ."profit_imgs.entry_user_id, "
            ."profit_imgs.entry_date, "
            ."profit_imgs.update_user_id, "
            ."profit_imgs.update_date "
            ."from "
            ."profit_imgs "
            ."left join profit_img_types on "
            ."profit_img_types.profit_img_type_id = profit_imgs.profit_img_type_id "
            ."where profit_imgs.profit_id = $profit_id ";
            Log::debug('sql:' .$str);

            $ret = DB::select($str);

        } catch (\Throwable $e) {

            throw $e;

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return $ret;
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

            // 部屋一覧
            $room_id = $profit_list->room_id;

            // 画像パス取得
            $profit_img_list = $this->getImgList($request);

            $common = new Common();

            // 不動産一覧
            $real_estate_list = $common->getRealEstateList();

            // 勘定科目id
            $profit_account_list = $common->getProfitAccounts();

            // アカウント一覧
            $create_user_list = $common->getCreateUsers();

            // 号室
            $room_list = $common->getRoomList($room_id);

            // 画像種別
            $profit_img_type_list = $common->getProfitImgTypes();

            // 銀行名
            $profit_bank_list = $common->getBanks();

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backProfitEdit' ,compact('profit_list', 'real_estate_list' ,'create_user_list' ,'profit_account_list', 'room_list', 'profit_img_type_list', 'profit_img_list', 'profit_bank_list'));
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
            ."profits.bank_id, "
            ."profits.profit_person_id, "
            ."profits.customer_name, "
            ."profits.room_id, "
            ."rooms.room_name, "
            ."profits.profit_account_id, "
            ."profits.profit_date, "
            ."profits.profit_fee, "
            ."profits.profit_memo, "
            ."profits.profit_approval_id, "
            ."create_users.create_user_name, "
            ."profits.profit_approval_date, "
            ."profits.profit_question_contents, "
            ."profits.profit_answer_contents, "
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
            ."left join create_users on "
            ."create_users.create_user_id = profits.profit_approval_id "
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
        $rules['question_contents'] = "nullable|max:500";
        $rules['answer_contents'] = "nullable|max:500";

        $img_file = $request->file('img_file');
        Log::debug('バリデーション_img_file:' .$img_file);

        if($img_file !== null){

            Log::debug('画像が添付されています');
            $rules['img_file'] = "nullable|mimes:jpeg,png,jpg,pdf";

        }
    
        $rules['img_text'] = "nullable|max:20";

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
        $messages['question_contents.max'] = "質問の文字数が超過しています。";
        $messages['answer_contents.max'] = "回答の文字数が超過しています。";

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

            // retrun初期値
            $ret = [];
            $ret['status'] = true;

            /**
             * status:OK=1 NG=0
             */
            $profit_info = $this->insertProfit($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $profit_info['status'];

            // 登録時のidを取得
            $profit_id = $profit_info['profit_id'];
            Log::debug('profit_id:'.$profit_id);

            /**
             * 画像
             */            
            $profit_img_info = $this->insertImg($request, $profit_id);

            $ret['status'] = $profit_img_info['status'];

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
            $bank_id = $request->input('bank_id');
            $profit_person_id = $request->input('profit_person_id');
            $profit_account_id = $request->input('profit_account_id');
            $profit_account_date = $request->input('profit_account_date');
            $profit_fee = $request->input('profit_fee');
            $real_estate_id = $request->input('real_estate_id');
            $room_id = $request->input('room_id');
            $profit_memo = $request->input('profit_memo');
            $customer_name = $request->input('customer_name');
            $profit_question_contents = $request->input('question_contents');
            $profit_answer_contents = $request->input('answer_contents');

            // 現在の日付取得
            $date = now() .'.000';
    
            
            // 銀行id
            if($bank_id == null){
                $bank_id = 0;
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

            // 取引先
            if($customer_name == null){
                $customer_name = '';
            }

            // 質問
            if($profit_question_contents == null){
                $profit_question_contents = '';
            }

            // 回答
            if($profit_answer_contents == null){
                $profit_answer_contents = '';
            }

            $str = "insert "
            ."into "
            ."cost_management.profits "
            ."( "
            ."bank_id, "
            ."profit_person_id, "
            ."customer_name, "
            ."room_id, "
            ."profit_account_id, "
            ."profit_date, "
            ."profit_fee, "
            ."profit_memo, "
            ."profit_approval_id, "
            ."profit_approval_date, "
            ."profit_question_contents, "
            ."profit_answer_contents, "
            ."profit_deadline_flag, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$bank_id, "
            ."$profit_person_id, "
            ."'$customer_name', "
            ."$room_id, "
            ."$profit_account_id, "
            ."'$profit_account_date', "
            ."'$profit_fee', "
            ."'$profit_memo', "
            ."0, "
            ."'', "
            ."'$profit_question_contents', "
            ."'$profit_answer_contents', "
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
            $str = "select * from profits "
            ."where "
            ."(profit_account_id = $profit_account_id) "
            ."and "
            ."(profit_fee = $profit_fee) "
            ."and "
            ."(entry_date = '$date') ";

            Log::debug('select_profit:'.$str);
            $profit_info = DB::select($str);

            $arrString = print_r($profit_info , true);
            Log::debug('profit_info:'.$arrString);

            // id
            $ret['profit_id'] = $profit_info[0]->profit_id;

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
    private function insertImg(Request $request, $profit_id){
        Log::debug('log_start:'.__FUNCTION__);

        try {
            Log::debug('insertImg_profit_id:'.$profit_id);

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

            // 拡張子取得
            $file_extension = $img_file->getClientOriginalExtension();
            Log::debug('file_extension:'.$file_extension);

            // 種別
            $img_type = $request->input('img_type');
            Log::debug('img_type:'.$img_type);

            // 備考
            $img_text = $request->input('img_text');
            Log::debug('img_text:'.$img_text);

            // 現在の日付取得
            $date = now() .'.000';
        
            // idごとのフォルダ作成のためのパス生成
            $dir ='img/profit/' .$profit_id;
            
            // 任意のフォルダ作成
            // ※appを入れるとエラーになる
            Storage::makeDirectory('/public/' .$dir);

            /**
             * 画像登録処理
             */
            // ファイル名変更
            $file_name = time() .'.' .$file_extension;
            Log::debug('ファイル名:'.$file_name);

            // ファイルパス+ファイル名
            $tmp_file_path = $dir .'/' .$file_name;
            Log::debug('tmp_file_path :'.$tmp_file_path);

            // pdfの場合、通常の保存をする
            if($file_extension == 'pdf'){

                // 第一引数=dir,第二引数=ファイル名
                Log::debug('PDFの処理');
                $img_file->storeAs('/public/'. $dir, $file_name);

            }else{

                // pdf以外は、リサイズし、保存する
                Log::debug('jpg,pngの処理');
                InterventionImage::make($img_file)->resize(380, null,
                function ($constraint) {
                    $constraint->aspectRatio();
                })->save(storage_path('app/public/' .$tmp_file_path));

            }

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
            ."profit_imgs "
            ."( "
            ."profit_id, "
            ."profit_img_type_id, "
            ."profit_img_path, "
            ."profit_img_memo, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."update_date "
            .")values( "
            ."$profit_id, "
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
             * status:OK=1 NG=0
             */
            $profit_info = $this->updateProfit($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $profit_info['status'];

            $profit_id = $request->input('profit_id');

            $profit_img_info = $this->insertImg($request, $profit_id);

            $ret['status'] = $profit_img_info['status'];

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
            $bank_id = $request->input('bank_id');
            $profit_person_id = $request->input('profit_person_id');
            $profit_account_id = $request->input('profit_account_id');
            $profit_account_date = $request->input('profit_account_date');
            $profit_fee = $request->input('profit_fee');
            $real_estate_id = $request->input('real_estate_id');
            $room_id = $request->input('room_id');
            $profit_memo = $request->input('profit_memo');
            $customer_name = $request->input('customer_name');
            $profit_question_contents = $request->input('question_contents');
            $profit_answer_contents = $request->input('answer_contents');

            // 現在の日付取得
            $date = now() .'.000';

            // 銀行id
            if($bank_id == null){
                $bank_id = 0;
            }

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

            // 質問
            if($profit_question_contents == null){
                $profit_question_contents = '';
            }

            // 回答
            if($profit_answer_contents == null){
                $profit_answer_contents = '';
            }

            $str = "update "
            ."profits "
            ."set "
            ."bank_id = $bank_id, "
            ."profit_person_id = $profit_person_id, "
            ."customer_name = '$customer_name', "
            ."room_id = $room_id, "
            ."profit_account_id = $profit_account_id, "
            ."profit_date = '$profit_account_date', "
            ."profit_fee = $profit_fee, "
            ."profit_memo = '$profit_memo', "
            ."profit_question_contents = '$profit_question_contents', "
            ."profit_answer_contents = '$profit_answer_contents', "
            ."profit_deadline_flag = 0, "
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

            DB::beginTransaction();

            // return初期値
            $response = [];

            $profit_info = $this->deleteProfit($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $profit_info['status'];

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

    /**
     * 削除(画像)
     *
     * @param Request $request
     * @return void
     */
    private function deleteEntryImg(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            $ret = [];

            // 値取得
            $profit_id = $request->input('profit_id');

            /**
             * 画像削除
             * 1.契約者Idごとの画像データ取得
             * 2.パス取得
             * 3.フォルダ削除
             * 4.データ(DB)削除
             */
            $str = "select * from profit_imgs "
            ."where profit_id = '$profit_id' ";
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
            $arr = explode('/', $img_list[0]->profit_img_path);

            // appを除外し文字結合(public/img/214)
            $img_dir_path = $arr[0] ."/" .$arr[1]."/" .$arr[2];

            // フォルダ削除
            Storage::deleteDirectory('/public/' .$img_dir_path);

            // 画像データ削除(DB)
            $str = "delete from profit_imgs "
            ."where profit_id = '$profit_id' ";
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
    public function backProfitDeleteEntryImgDetail(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // トランザクション
            DB::beginTransaction();

            $response = [];

            // 値取得
            $profit_img_id = $request->input('img_id');

            /**
             * 画像削除
             * 1.契約者Idごとの画像データ取得
             * 2.パス取得
             * 3.フォルダ削除
             * 4.データ(DB)削除
             */
            $str = "select * from profit_imgs "
            ."where profit_img_id = '$profit_img_id' ";
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
            $img_name_path = $img_list[0]->profit_img_path;
            Log::debug('img_name_path:'.$img_name_path);

            // ファイル削除(例:Storage::delete('public/img/214/1637578613.jpg');
            Storage::delete('/public/' .$img_name_path);

            /**
             * 画像フォルダ削除
             */
             // 画像パスを"/"で分解->配列化
            $arr = explode('/', $img_list[0]->profit_img_path);
            $img_dir_path = $arr[0] ."/" .$arr[1]."/" .$arr[2];

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
            $str = "delete from profit_imgs "
            ."where profit_img_id = '$profit_img_id' ";
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
    public function backProfitApprovalEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $response = [];

            /**
             * 売上概要
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
            // 承認on,offフラグ
            $approval_flag = $request->input('approval_flag');
            Log::debug('approval_flag:'.$approval_flag);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('session_id:'.$session_id);

            /**
             * true:approval_id=登録者(session_id)
             * false:approval_id=空白
             */
            if($approval_flag == 'true'){

                $approval_id = $session_id;
                $approval_date = now() .'.000';

            }else{

                $approval_id = 0;
                $approval_date = '';

            }

            // id
            $profit_id = $request->input('profit_id');

            // 日付
            $date = now() .'.000';

            $str = "update profits "
            ."set "
            ."profit_approval_id=$approval_id "
            .",profit_approval_date='$approval_date' "
            .",update_user_id=$session_id "
            .",update_date='$date' "
            ."where "
            ."profit_id=$profit_id ";
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