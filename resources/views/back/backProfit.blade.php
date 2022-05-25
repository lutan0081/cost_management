<!DOCTYPE html>
<html lang="ja">

	<head>
		<title>売上詳細/COSTS</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_profit.css') }}">  
		
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
                                        <i class="fas bi bi-piggy-bank-fill icon_blue me-2"></i>売上一覧
                                    </div>
                                    <!-- 境界線 -->
                                    <hr>
                                </div>
                            </div>
                            <!-- タイトル -->
                            
                            <div class="row">
                                <form action="backProfitInit" method="post">
                                    {{ csrf_field() }}
                                    <div class="col-sm-12">
                                        <div class="card border border-0">
                                            <div class="row align-items-end">

                                                <!-- フリーワード -->
                                                <div class="col-12 col-md-8 col-lg-4 mt-1">
                                                    <label for="">フリーワード</label>
                                                    <input type="text" class="form-control" name="free_word" id="free_word" value="{{ $free_word }}">
                                                </div>
                                
                                                <!-- 物件名 -->
                                                <div class="col-12 col-md-8 col-lg-3 mt-1">
                                                    <label class="" for="textBox"></label>物件名
                                                    
                                                    <select class="form-select" name="real_estate_id" id="real_estate_id">
                                                        <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                        <option></option>
                                                        @foreach($real_estate_list as $real_estates)
                                                            <option value="{{ $real_estates->real_estate_id }}" @if( $real_estate_id == $real_estates->real_estate_id) selected @endif>{{ $real_estates->real_estate_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 物件名 -->

                                                <!-- 売上担当 -->
                                                <div class="col-12 col-md-8 col-lg-3 mt-1">
                                                    <label class="" for="textBox"></label>担当
                                                    
                                                    <select class="form-select" name="create_user_id" id="create_user_id">
                                                        <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                        <option></option>
                                                        @foreach($create_user_list as $create_users)
                                                            <option value="{{ $create_users->create_user_id }}" @if( $create_user_id == $create_users->create_user_id) selected @endif>{{ $create_users->create_user_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 売上担当 -->

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

                                                <!-- 勘定項目 -->
                                                <div class="col-12 col-md-8 col-lg-3 mt-1">
                                                    <label class="" for="textBox"></label>勘定項目
                                                    
                                                    <select class="form-select" name="profit_account_id" id="profit_account_id">
                                                        <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                        <option></option>
                                                        @foreach($profit_account_list as $profit_accounts)
                                                            <option value="{{ $profit_accounts->profit_account_id }}" @if( $profit_account_id == $profit_accounts->profit_account_id) selected @endif>{{ $profit_accounts->profit_account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 勘定項目 -->

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
                                                        <th scope="col" id="legal_place_address">勘定日</th>
                                                        <th scope="col" id="legal_place_post_number">勘定科目</th>
                                                        <th scope="col" id="legal_place_name">取引先</th>
                                                        <th scope="col" id="legal_place_name">担当</th>
                                                        <th scope="col" id="legal_place_address">物件名</th>
                                                        <th scope="col" id="legal_place_address">号室</th>
                                                        <th scope="col" id="legal_place_address">利益額</th>
                                                        <th scope="col" id="legal_place_address">備考</th>
                                                    </tr>
                                                </thead>

                                                <!-- テーブルボディ -->
                                                <tbody>
                                                    @foreach($res as $profit_list)
                                                        <tr>
                                                            <td id="id_{{ $profit_list->profit_id }}" class="click_class" style="display:none"></td>
                                                            <td id="select_{{ $profit_list->profit_id }}" class="click_class"><input id="{{ $profit_list->profit_id }}" type="radio" class="align-middle" name="flexRadioDisabled"></td>
                                                            <td id="date_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->profit_date }}</td>
                                                            <td id="account_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->profit_account_name }}</td>
                                                            <td id="customer_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->customer_name }}</td>
                                                            <td id="user_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->create_user_name }}</td>
                                                            <td id="realEstateName_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->real_estate_name }}</td>
                                                            <td id="roomName_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->room_name }}</td>
                                                            <td id="profitFee_{{ $profit_list->profit_id }}" class="click_class">{{ Common::format_three_digit_separator($profit_list->profit_fee) }}</td>
                                                            <td id="profitMemo_{{ $profit_list->profit_id }}" class="click_class">{{ $profit_list->profit_memo }}</td>
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
                                {{ $res->links() }}
                            </div>

                        </div>
                        <!-- テーブルcard -->

                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="row">

                                <div class="col-12 col-md-8 col-lg-12">
                                    <div class="form-group">
                                        <label for="">利益額</label>
                                        <input type="text" class="form-control" name="money" id="money" value=" {{ Common::format_three_digit_separator($profit_fee_sum_list->profit_fee) }}" style="text-align:right" disabled>
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
                                        <button type="button" id="btn_csv" class="btn btn-outline-primary float-start btn-default">CSV</button>
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
        
        <!-- js -->
        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_profit.js') }}"></script>

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