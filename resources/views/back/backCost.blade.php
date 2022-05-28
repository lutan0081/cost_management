<!DOCTYPE html>
<html lang="ja">

	<head>
		<title>経費一覧/COSTS</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_cost.css') }}">  
		
        <style>
            /* ボタンデフォルト値 */
            .btn-default{
                width: 6rem;
            }

            /* 一覧の左右に余白が出来るため、0に設定 */
            .card-body {
                padding: 0rem;
            }
		</style>
        
	</head>

	<body>
		<!-- page-wrapper -->
		<div class="page-wrapper chiller-theme toggled">

            <!-- ローディング画面の表示 -->
            <div id="overlay">
                <div class="cv-spinner">
                    <span class="spinner"></span>
                </div>
            </div>
        
            <!-- sidebar-wrapper  -->
            @component('component.backSidebar')
            @endcomponent
            <!-- sidebar-wrapper  -->
            
            <!-- page-content" -->
            <main class="page-content mb-3">

                <!-- 上段検索 -->
                <div class="container">

                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 mt-2">

                            <!-- タイトル -->
                            <div class="row">
                                <div class="col-12 col-md-12 col-lg-12 mb-2">
                                    <div class="info_title mt-3">
                                        <i class="fas bi bi-piggy-bank-fill icon_blue me-2"></i>経費一覧
                                    </div>
                                    <!-- 境界線 -->
                                    <hr>
                                </div>
                            </div>
                            <!-- タイトル -->
                            
                            <div class="row">
                                <form action="backCostInit" method="post">
                                    {{ csrf_field() }}
                                    <div class="col-sm-12">
                                        <div class="card border border-0">
                                            <div class="row align-items-end">

                                                <!-- フリーワード -->
                                                <div class="col-12 col-md-8 col-lg-4 mt-1">
                                                    <label for="">フリーワード</label>
                                                    <input type="text" class="form-control" name="free_word" id="free_word" value="{{ $free_word }}">
                                                </div>
                                
                                                <!-- 照会口座 -->
                                                <div class="col-12 col-md-8 col-lg-4 mt-1">
                                                    <label class="" for="textBox"></label>照会口座
                                                    
                                                    <select class="form-select" name="bank_id" id="bank_id">
                                                        <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                        <option></option>
                                                        @foreach($bank_list as $bank)
                                                            <option value="{{ $bank->bank_id }}" @if( $bank_id == $bank->bank_id) selected @endif>{{ $bank->bank_name. '_'. $bank->bank_number }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 照会口座 -->

                                                <!-- 勘定科目 -->
                                                <div class="col-12 col-md-8 col-lg-3 mt-1">
                                                    <label class="" for="textBox"></label>勘定科目
                                                    
                                                    <select class="form-select" name="cost_account_id" id="cost_account_id">
                                                        <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                        <option></option>
                                                        @foreach($cost_account_list as $cost_account)
                                                            <option value="{{ $cost_account->cost_account_id }}" @if( $cost_account_id == $cost_account->cost_account_id) selected @endif>{{ $cost_account->cost_account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 勘定科目 -->

                                                <!-- 改行 -->
                                                <div class="w-100"></div>

                                                <!-- 日付始期 -->
                                                <div class="col-6 col-md-6 col-lg-2 mt-2">
                                                    <label for="">日付始期</label>
                                                    <input type="text" class="form-control" id="start_date" name="start_date" autocomplete="off" value=" {{ $start_date }}">
                                                </div>

                                                <!-- 日付終期 -->
                                                <div class="col-6 col-md-6 col-lg-2 mt-2">
                                                    <label for="">日付終期</label>
                                                    <input type="text" class="form-control" id="end_date" name="end_date" autocomplete="off" value="{{ $end_date }}">
                                                </div>

                                                <!-- 出金区分 -->
                                                <div class="col-12 col-md-8 col-lg-3 mt-1">
                                                    <label class="" for="textBox"></label>出金区分
                                                    
                                                    <select class="form-select" name="private_or_bank_id" id="private_or_bank_id">
                                                        <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                        <option></option>
                                                        @foreach($private_or_bank_list as $private_or_bank)
                                                            <option value="{{ $private_or_bank->private_or_bank_id }}" @if( $private_or_bank_id == $private_or_bank->private_or_bank_id) selected @endif>{{ $private_or_bank->private_or_bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 出金区分 -->

                                                <!-- 検索ボタン -->
                                                <div class="col-5 col-md-4 col-lg-5 mt-2">
                                                    <input type="submit" class="btn btn-default btn-outline-primary float-end" value="検索">
                                                </div>
                                                <!-- 検索ボタン -->

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- 上段検索 -->

                <!-- 一覧 -->
                <div class="container">
                    
                    <div class="row">
                            
                        <!-- テーブルcard -->
                        <div class="col-12 col-md-12 col-lg-12 mt-2">

                            <div class="card">
                        
                                <!-- カードボディ -->
                                <div class="card-body">
                                    <!-- スクロール -->
                                    <div class="overflow-auto" style="height:30rem;">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-condensed table-striped">

                                                <!-- テーブルヘッド -->
                                                <thead>
                                                    <tr>
                                                        <th scope="col" id="create_user_id" style="display:none">id</th>
                                                        <th>選択</th>
                                                        <th scope="col" id="bank_name">照会口座</th>
                                                        <th scope="col" id="account_date">勘定日</th>
                                                        <th scope="col" id="cost_type">出金区分</th>
                                                        <th scope="col" id="cost_account_name">勘定科目</th>
                                                        <th scope="col" id="outgo_fee">出金額</th>
                                                        <th scope="col" id="income_fee">入金額</th>
                                                        <th scope="col" id="balance_fee">残高</th>
                                                        <th scope="col" id="cost_type">取引区分</th>
                                                        <th scope="col" id="financial_name">金融機関名</th>
                                                        <th scope="col" id="financial_branch">支店名</th>
                                                        <th scope="col" id="financial_summary">摘要</th>
                                                        <th scope="col" id="cost_memo">備考</th>
                                                    </tr>
                                                </thead>

                                                <!-- テーブルボディ -->
                                                <tbody>
                                                    @foreach($res as $cost_list)
                                                        <tr>
                                                            <td id="id_{{ $cost_list->cost_id }}" class="click_class" style="display:none"></td>
                                                            <td id="select_{{ $cost_list->cost_id }}" class="click_class"><input id="{{ $cost_list->cost_id }}" type="radio" class="align-middle" name="flexRadioDisabled"></td>
                                                            <td id="bankName_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->bank_name. '_' .$cost_list->bank_number}}</td>
                                                            <td id="accountDate_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->account_date }}</td>
                                                            <td id="privateOrBankName{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->private_or_bank_name }}</td>
                                                            <td id="costAccountName_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->cost_account_name }}</td>
                                                            <td id="outgoFee_{{ $cost_list->cost_id }}" class="click_class">{{ Common::format_three_digit_separator($cost_list->outgo_fee) }}</td>
                                                            <td id="incomeFee_{{ $cost_list->cost_id }}" class="click_class">{{ Common::format_three_digit_separator($cost_list->income_fee) }}</td>
                                                            <td id="balanceFee_{{ $cost_list->cost_id }}" class="click_class">{{ Common::format_three_digit_separator($cost_list->balance_fee) }}</td>
                                                            <td id="costType_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->cost_type }}</td>
                                                            <td id="financialName_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->financial_name }}</td>
                                                            <td id="financialBranch_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->financial_branch }}</td>
                                                            <td id="financialSummary_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->financial_summary }}</td>
                                                            <td id="costMemo_{{ $cost_list->cost_id }}" class="click_class">{{ $cost_list->cost_memo }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <!-- テーブルボディ -->

                                            </table>
                                        </div>
                                    </div>
                                    <!-- スクロール -->
                                <!-- カードボディ -->
                                </div>

                            </div>

                            <!-- ぺージネーション -->   
                            <div id="links" style="display:none;" class="mt-3">
                                {{ $res->appends($paginate_params)->links() }}
                            </div>

                        </div>
                        <!-- テーブルcard -->

                        <div class="col-12 col-md-6 col-lg-3 mt-3">
                            <div class="row">

                                <div class="col-12 col-md-8 col-lg-12">
                                    <div class="form-group">
                                        <label for="">合計値</label>
                                        <input type="text" class="form-control" name="money" id="money" value=" {{ Common::format_three_digit_separator($outgo_fee_sum_list->outgo_fee) }}" style="text-align:right" disabled>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ボタン -->
                        <div class="col-12 col-md-6 col-lg-12 mt-3 pt-3 mb-3">
                            <div class="card border border-0">
                            <!-- row -->
                            <div class="row">

                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="btn-group" role="group">
                                        <button type="button" id="btn_csv_output" class="btn btn-outline-primary float-start btn-default">CSV出力</button>
                                        <button type="button" id="btn_csv_capture" class="btn btn-outline-primary float-start btn-default" data-bs-toggle="modal" data-bs-target="#csvModal">CSV取込</button>
                                    </div>
                                </div>
                                
                                <!-- 新規、編集 -->
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="btn-group float-xl-end" role="group">
                                        <button type="button" onclick="location.href='backProfitNewInit'" id="btn_csv" class="btn btn-outline-primary float-end btn-default">新規登録</button>
                                        <button type="button" id="btn_edit" class="btn btn-outline-primary float-end btn-default">編集</button>
                                    </div>
                                </div>
                            </div>
                            <!-- row -->
                            </div>
                        </div>
                        <!-- ボタン -->

                    </div>
                </div>
                <!-- 一覧 --> 

            </main>
            <!-- page-content" -->

		</div>
		<!-- page-wrapper -->

        <!-- CSV読込 -->
        <div class="modal fade" id="csvModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <!-- ヘッダー -->
                    <div class="modal-header">
                        <div class="modal-title info_title" id="exampleModalLabel">
                            <i class="fas fa-file-csv icon_blue me-2"></i>CSV読込
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- ボディ -->
                    <div class="modal-body px-4">
                        <form id="modalForm" class="needs-validation" novalidate>

                            <div class="col-12 col-md-6 col-lg-12">
                                <div class="row">

                                    <!-- 照会口座 -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-1">
                                        <label class="" for="textBox"></label>照会口座
                                        
                                        <select class="form-select" name="modal_bank_id" id="modal_bank_id">
                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                            <option></option>
                                            @foreach($bank_list as $bank)
                                                <option value="{{ $bank->bank_id }}" @if( $bank_id == $bank->bank_id) selected @endif>{{ $bank->bank_name. '_'. $bank->bank_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- 照会口座 -->

                                    <!-- ファイル -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-3 mb-3">
                                        <label class="mb-2">アップロード</label>
                                        <input class="form-control" type="file" id="modal_img_file">
                                        <!-- エラーメッセージ -->
                                        <div class="invalid-feedback" id ="modal_img_file_error"></div>
                                    </div>
                                    <!-- ファイル -->

                                </div>  
                            </div>

                        </form>
                    </div>
                    <!-- ボディ -->

                    <!-- フッター -->
                    <div class="modal-footer">

                        <div class="col my-3">

                            <!-- 戻る -->
                            <button type="button" id="btn_modal_csv_back" class="btn btn-outline-primary btn-default" data-bs-dismiss="modal">戻る</button>

                            <!-- 送信 -->
                            <button type="button" id="btn_modal_csv_import" class="btn btn-outline-primary btn-default float-end">Import</button>
                            
                        </div>

                    </div>
                    <!-- フッター -->
                    
                </div>
            </div>
        </div>
        <!-- CSV読込 -->
        
        <!-- js -->
        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_cost.js') }}"></script>

        <!-- bootstrap-datepickerのjavascriptコード -->
        <script>
            
            $('#start_date').datepicker({
                language:'ja'
            });

            $('#end_date').datepicker({
                language:'ja'
            });

        </script>

	</body>
	
</html>