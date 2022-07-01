<?php

namespace App\Http\Controllers\Back\File;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

use Storage;

// config内のapp.phpに定義
use Common;

/**
 * 表示・登録、編集、削除
 */
class BackFileController extends Controller
{   
    /**
     *  一覧(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backFileInit(Request $request){   
        Log::debug('start:' .__FUNCTION__);

        try {

            // 一覧取得
            $file_list = $this->getFileList($request);

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
        return view('back.backFile', $file_list, compact('paginate_params', 'free_word'));

    }

    /**
     * 一覧(sql)
     *
     * @return $ret(部屋一覧)
     */
    private function getFileList(Request $request){

        Log::debug('log_start:'.__FUNCTION__);

        try{

            // フリーワード
            $free_word = $request->input('free_word');
            Log::debug('$free_word:' .$free_word);

            // session_id
            $session_id = $request->session()->get('create_user_id');
            Log::debug('$session_id:' .$session_id);

            $str = "select "
            ."files.file_id "
            .",files.file_name "
            .",files.file_type_id "
            .",file_types.file_type_name "
            .",files.file_path "
            .",files.file_memo "
            .",files.entry_user_id "
            .",files.entry_date "
            .",files.update_user_id "
            .",files.update_date "
            ."from "
            ."files "
            ."left join file_types on "
            ."file_types.file_type_id = files.file_type_id "
            ."where "
            ."1 = 1 ";

            // where句
            $where = "";

            // フリーワード
            if($free_word !== null){
                $where = $where ."and ifnull(file_name,'') like '%$free_word%'";
                $where = $where ."or ifnull(file_memo,'') like '%$free_word%'";
            };

            $str = $str .$where;
            Log::debug('$sql:' .$str);

            // query
            $alias = DB::raw("({$str}) as alias");

            // columnの設定、表示件数
            $res = DB::table($alias)->selectRaw("*")->orderByRaw("file_id desc")->paginate(30)->onEachSide(1);

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
     * 新着情報種別リスト
     *
     * @param Request $request
     * @return void
     */
    private function getInformationTypeList(Request $request){
        Log::debug('start:' .__FUNCTION__);

        try{
            // 値設定
            $create_user_id = $request->input('create_user_id');

            // sql
            $str = "select "
            ."* "
            ."from "
            ."information_types ";

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
    public function backInformationEditEntry(Request $request){
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
        if($request->input('information_id') == ""){

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
        $rules['information_title'] = "required|max:100";
        $rules['information_contents'] = "required|max:500";

        /**
         * messages
         */
        $messages = [];
        $messages['information_title.required'] = "タイトルは必須です。";
        $messages['information_title.max'] = "タイトルの文字数が超過しています。100字以内で入力してください。";
        $messages['information_contents.required'] = "内容は必須です。";
        $messages['information_contents.max'] = "内容の文字数が超過しています。500文字以内で入力してください。";
    
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

            // 戻値の配列作成
            $ret = [];
            // 戻値の初期値
            $ret['status'] = true;

            /**
             * status:OK=1 NG=0
             */
            $information_info = $this->insertInformation($request);

            // returnのステータスにtrueを設定
            $ret['status'] = $information_info['status'];

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
    private function insertInformation(Request $request){
        
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $information_title = $request->input('information_title');
            $information_type = $request->input('information_type');
            $information_contents = $request->input('information_contents');
            $information_id = $request->input('information_id');

            // 現在の日付取得
            $date = now() .'.000';
    
            // タイトル
            if($information_title == null){
                $information_title ='';
            }

            // 種別
            if($information_type == null){
                $information_type =0;
            }

            // 内容
            if($information_contents == null){
                $information_contents ='';
            }

            // 登録
            $str = "insert "
            ."into informations( "
            ."information_name "
            .",information_type_id "
            .",information_contents "
            .",entry_user_id "
            .",entry_date "
            .",update_user_id "
            .",update_date "
            .") values ( "
            ."'$information_title' "
            .",$information_type "
            .",'$information_contents' "
            .",$session_id "
            .",'$date' "
            .",$session_id "
            .",'$date' "
            .") ";
            
            Log::debug('str:'.$str);

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
            $room_info = $this->updateUser($request);

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
    private function updateUser(Request $request){
        Log::debug('log_start:' .__FUNCTION__);

        try {
            // returnの初期値
            $ret=[];

            // 値取得
            $session_id = $request->session()->get('create_user_id');
            $information_id = $request->input('information_id');
            $information_title = $request->input('information_title');
            $information_type = $request->input('information_type');
            $information_contents = $request->input('information_contents');

            // 現在の日付取得
            $date = now() .'.000';
    
            // タイトル
            if($information_title == null){
                $information_title ='';
            }

            // 種別
            if($information_type == null){
                $information_type =0;
            }

            // 内容
            if($information_contents == null){
                $information_contents ='';
            }

            $str = "update informations "
            ."set "
            ."information_name = '$information_title' "
            .",information_type_id = $information_type "
            .",information_contents = '$information_contents' "
            .",update_user_id = $session_id "
            .",update_date = '$date' "
            ."where "
            ."information_id = $information_id ";
            
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
     *  編集(表示)
     *
     * @param Request $request(フォームデータ)
     * @return
     */
    public function backInformationEditInit(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        $information_id = $request->input('information_id');
        
        // return初期値
        $response = [];

        $str = "select "
        ."informations.information_id "
        .",informations.information_name "
        .",informations.information_type_id "
        .",information_types.information_type_name "
        .",information_contents "
        .",informations.entry_user_id "
        .",informations.entry_date "
        .",informations.update_user_id "
        .",informations.update_date "
        ."from "
        ."informations "
        ."left join information_types on "
        ."information_types.information_type_id = informations.information_type_id "
        ."where "
        ."information_id = $information_id ";
        Log::debug('str:' .$str);

        $response['information_list'] = DB::select($str);

        Log::debug('log_end:' .__FUNCTION__);
        return response()->json($response);
    }

    /**
     * 削除
     *
     * @param Request $request
     * @return void
     */
    public function backInformationDeleteEntry(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{

            // return初期値
            $response = [];

            $user_info = $this->deleteInformation($request);

            // js側での判定のステータス(true:OK/false:NG)
            $response['status'] = $user_info['status'];

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
     * 削除(sql)
     *
     * @param Request $request
     * @return void
     */
    private function deleteInformation(Request $request){
        Log::debug('log_start:'.__FUNCTION__);

        try{
            // return初期値
            $ret = [];

            // 値取得
            $information_id = $request->input('information_id');

            $str = "delete "
            ."from "
            ."cost_management.informations "
            ."where "
            ."information_id = $information_id ";
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