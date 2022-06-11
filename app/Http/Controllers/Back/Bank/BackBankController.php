<?php

namespace App\Http\Controllers\Back\Bank;

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

/**
 * 表示・登録、編集、削除
 */
class BackBankController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backBankInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {
            // 集金口座一覧
            $bank_list = $this->getList($request);

            /**
             * フォームに値を保持させるためにそのまま返す
             */
            // フリーワード
            $free_word = $request->input('free_word');

            // ★リクエストパラメータをページネーション用の連想配列に格納★
            $paginate_params = [

                'free_word' => $free_word,

            ];
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backBank' ,$bank_list, compact('paginate_params', 'free_word'));
    }

    /**
     * 一覧(sql)
     *
     * @return $ret(銀行一覧)
     */
    private function getList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            $str = "select "
            ."banks.bank_id as bank_id, "
            ."banks.bank_name as bank_name, "
            ."banks.bank_branch_name as bank_branch_name, "
            ."banks.bank_type_id as bank_type_id, "
            ."bank_types.bank_type_name as bank_type_name, "
            ."bank_number as bank_number, "
            ."bank_account_name as bank_account_name, "
            ."banks.entry_user_id as entry_user_id, "
            ."banks.entry_date as entry_date, "
            ."banks.update_user_id as update_user_id, "
            ."banks.updated as updated "
            ."from "
            ."banks "
            ."left join bank_types on "
            ."banks.bank_type_id = bank_types.bank_type_id ";

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){
                $where = $where ."and ifnull(cost_memo,'') like '%$free_word%'";
                $where = $where ."or ifnull(financial_summary,'') like '%$free_word%'";
            };

            $str = $str .$where;
            Log::debug('$sql:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("bank_id desc")->paginate(30)->onEachSide(1);

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
     *  新規(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backBankNewInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 集金口座一覧
            $bank_list = $this->getNewList($request);
            
            // 集金種別
            $bank_type_list = $this->getBankType($request);
            
        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backBankEdit' ,compact('bank_list' ,'bank_type_list'));
    }

    /**
     * 新規(ダミー値取得)
     *
     * @return $ret(空の配列)
     */
    private function getNewList(Request $request){
        Log::debug('log_start:'.__FUNCTION__);
        $obj = new \stdClass();
        
        // 募集要項
        $obj->bank_id  = '';
        $obj->bank_name = '';
        $obj->bank_branch_name = '';
        $obj->bank_type_id = '';
        $obj->bank_number = '';
        $obj->bank_account_name = '';

        $ret = [];
        $ret = $obj;

        Log::debug('log_end:'.__FUNCTION__);
        return $ret;
    }

    /**
     * 種別(表示)
     *
     * @return void
     */
    private function getBankType(Request $request){
        Log::debug('start:' .__FUNCTION__);

        try {
            $ret = [];

            // sql
            $str = "select * "
            ."from bank_types ";
            Log::debug('sql:' .$str);

            $ret = DB::select($str);

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

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
    public function backBankEditInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 集金口座一覧
            $bank_info = $this->getEditList($request);
            $bank_list = $bank_info[0];
            
            // 集金種別
            $bank_type_list = $this->getBankType($request);

        // 例外処理
        } catch (\Throwable $e) {

            Log::debug('error:'.$e);

        } finally {

        }

        Log::debug('end:' .__FUNCTION__);
        return view('back.backBankEdit' ,compact('bank_list' ,'bank_type_list'));
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
            $bank_id = $request->input('bank_id');

            // sql
            $str = "select "
            ."banks.bank_id as bank_id, "
            ."banks.bank_name as bank_name, "
            ."banks.bank_branch_name as bank_branch_name, "
            ."banks.bank_type_id as bank_type_id, "
            ."bank_types.bank_type_name as bank_type_name, "
            ."bank_number as bank_number, "
            ."bank_account_name as bank_account_name, "
            ."banks.entry_user_id as entry_user_id, "
            ."banks.entry_date as entry_date, "
            ."banks.update_user_id as update_user_id, "
            ."banks.updated as updated "
            ."from "
            ."banks "
            ."left join bank_types on "
            ."banks.bank_type_id = bank_types.bank_type_id "
            ."where "
            ."banks.bank_id = $bank_id ";
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
    public function backBankEditEntry(Request $request){
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
        $bank_id = $request->input('bank_id');

        // 新規登録
        if($request->input('bank_id') == ""){

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
        $rules['bank_name'] = "required|max:50";
        $rules['bank_branch_name'] = "required|max:50";
        $rules['bank_number'] = "required|numeric";
        $rules['bank_account_name'] = "required|max:50";

        /**
         * messages
         */
        $messages = [];
        $messages['bank_name.required'] = "名前は必須です。";
        $messages['bank_name.max'] = "名前の文字数が超過しています。";
        $messages['bank_branch_name.required'] = "支店名は必須です。";
        $messages['bank_branch_name.max'] = "支店名の文字数が超過しています。";
        $messages['bank_number.required'] = "口座番号は必須です。";
        $messages['bank_number.numeric'] = "口座番号の形式が不正です。";
        $messages['bank_account_name.required'] = "名義人は必須です。";
        $messages['bank_account_name.max'] = "名義人の文字数が超過しています。";
    
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
             * 不動産業者(status:OK=1 NG=0/application_id:新規登録のid)
             */
            $bank_info = $this->insertBackBank($request);

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
     * 集金口座(新規登録)
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function insertBackBank(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $bank_id = $request->input('bank_id');
            $bank_name = $request->input('bank_name');
            $bank_branch_name = $request->input('bank_branch_name');
            $bank_type_id = $request->input('bank_type_id');
            $bank_number = $request->input('bank_number');
            $bank_account_name = $request->input('bank_account_name');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 集金口座id
            if($bank_id == null){
                $bank_id = 0;
            }

            // 銀行名
            if($bank_name == null){
                $bank_name ='';
            }

            // 支店名
            if($bank_branch_name == null){
                $bank_branch_name ='';
            }

            // 種別id
            if($bank_type_id == null){
                $bank_type_id ='';
            }

            // 口座番号
            if($bank_number == null){
                $bank_number = '';
            }

            // 名義人
            if($bank_account_name == null){
                $bank_account_name = '';
            }

            // 登録
            $str = "insert "
            ."into "
            ."banks "
            ."( "
            ."bank_name, "
            ."bank_branch_name, "
            ."bank_type_id, "
            ."bank_number, "
            ."bank_account_name, "
            ."entry_user_id, "
            ."entry_date, "
            ."update_user_id, "
            ."updated "
            .")values( "
            ."'$bank_name', "
            ."'$bank_branch_name', "
            ."$bank_type_id, "
            ."'$bank_number', "
            ."'$bank_account_name', "
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
            $legal_place_info = $this->updateBackBank($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $legal_place_info['status'];

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
     * 集金口座(編集)
     * 
     * @param Request $request
     * @return $ret['application_id(登録のapplication_id)']['status:1=OK/0=NG']''
     */
    private function updateBackBank(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $bank_id = $request->input('bank_id');
            $bank_name = $request->input('bank_name');
            $bank_branch_name = $request->input('bank_branch_name');
            $bank_type_id = $request->input('bank_type_id');
            $bank_number = $request->input('bank_number');
            $bank_account_name = $request->input('bank_account_name');

            // 現在の日付取得
            $date = now() .'.000';
    
            // 現在の日付取得
            $date = now() .'.000';
    
            // 集金口座id
            if($bank_id == null){
                $bank_id = 0;
            }

            // 銀行名
            if($bank_name == null){
                $bank_name ='';
            }

            // 支店名
            if($bank_branch_name == null){
                $bank_branch_name ='';
            }

            // 種別id
            if($bank_type_id == null){
                $bank_type_id ='';
            }

            // 口座番号
            if($bank_number == null){
                $bank_number = '';
            }

            // 名義人
            if($bank_account_name == null){
                $bank_account_name = '';
            }

            $str = "update "
            ."banks "
            ."set "
            ."bank_name = '$bank_name', "
            ."bank_branch_name = '$bank_branch_name', "
            ."bank_type_id = $bank_type_id, "
            ."bank_number = '$bank_number', "
            ."bank_account_name = '$bank_account_name', "
            ."entry_user_id = $session_id, "
            ."entry_date = '$date', "
            ."update_user_id = $session_id, "
            ."updated = '$date' "
            ."where "
            ."bank_id = $bank_id; ";

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
    public function backBankDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            /**
             * 不動産業者
             */
            $bank_info = $this->deleteBank($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $bank_info['status'];

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
     * 削除(集金口座)
     *
     * @param Request $request
     * @return void
     */
    private function deleteBank(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $bank_id = $request->input('bank_id');

            $str = "delete "
            ."from "
            ."banks "
            ."where "
            ."bank_id = $bank_id; ";

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