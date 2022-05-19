<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>売上詳細/COSTS</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_profit_edit.css') }}">  
		
        <style>

            /* ボタンデフォルト値 */
            .btn-default{
                width: 6rem;
            }

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
		<main class="page-content">

                <!-- 入力項目 -->
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">

                            <form id="editForm" class="needs-validation" novalidate>

                                <div class="info_title mt-3">
                                    <i class="fas bi bi-piggy-bank-fill icon_blue me-2"></i>売上詳細
                                </div>

                                <!-- 境界線 -->
                                <hr>

                                <!-- カード -->
                                <div class="card border border-0">

                                    <!-- タブ内のコンテンツ -->
                                    <div class="row row-cols-3">
                                        <div class="col-12 col-md-12 col-lg-12 mb-3">
                                            <!-- 内容 -->
                                            <div class="tab-content" id="nav-tabContent">
                                            
                                                <div class="tab-pane fade show active" id="nav-contract_progress" role="tabpanel" aria-labelledby="nav-contract_progress-tab">
                                                    
                                                    <div class="row row-cols-2">

                                                        <!-- 売上担当 -->
                                                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                            <label class="label_required mb-2" for="textBox"></label>売上担当
                                                            <select class="form-select" name="profit_person_id" id="profit_person_id" required>
                                                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                                <option></option>
                                                                @foreach($create_user_list as $create_users)
                                                                    <option value="{{$create_users->create_user_id}}" @if($profit_list->profit_person_id == $create_users->create_user_id) selected @endif>{{ $create_users->create_user_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback" id =profit_person_id_error">
                                                                売上担当は必須です。
                                                            </div>
                                                        </div>
                                                        <!-- 売上担当 -->

                                                        <!-- 勘定科目 -->
                                                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                            <label class="label_required mb-2" for="textBox"></label>勘定科目
                                                            <select class="form-select" name="profit_account_id" id="profit_account_id" value="{{ $profit_list->profit_account_id }}" required>
                                                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                                <option></option>
                                                                @foreach($profit_account_list as $profit_accounts)
                                                                    <option value="{{ $profit_accounts->profit_account_id }}" @if($profit_list->profit_account_id == $profit_accounts->profit_account_id) selected @endif>{{ $profit_accounts->profit_account_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback" id ="profit_account_id_error">
                                                                勘定科目は必須です。
                                                            </div>
                                                        </div>
                                                        <!-- 勘定科目 -->

                                                        <div class="w-100"></div>
    
                                                        <!-- 勘定日 -->
                                                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                            <label class="label_required mb-2" for="textBox"></label>勘定日
                                                            <input type="text" class="form-control" name="profit_account_date" id="profit_account_date" placeholder="例：2022/05/17" value="{{ $profit_list->profit_date }}" required>
                                                            <!-- エラーメッセージ -->
                                                            <div class="invalid-feedback" id ="profit_account_date_error">
                                                                勘定日は必須です。
                                                            </div>
                                                        </div>
                                                        <!-- 勘定日 -->
                                                        
                                                        <!-- 金額 -->
                                                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                            <label class="label_required mb-2" for="textBox"></label>利益額
                                                            <input type="text" class="form-control" name="profit_fee" id="profit_fee" placeholder="例：3000000" value="{{ $profit_list->profit_fee }}" style="text-align:right" required>
                                                            <!-- エラーメッセージ -->
                                                            <div class="invalid-feedback" id ="profit_fee_error">
                                                                利益額は必須です。
                                                            </div>
                                                        </div>
                                                        <!-- 金額 -->

                                                        <div class="col-6 col-md-8 col-lg-12 mt-3">
                                                            <hr>
                                                        </div>

                                                        <!-- 物件名 -->
                                                        <div class="col-12 col-md-8 col-lg-6 mt-3">
                                                            <label class="label_any" for="textBox"></label>物件名
                                                            <div class="input-group">
                                                                <select class="form-select" name="real_estate_id" id="real_estate_id" class="real_estate_id">
                                                                    <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                                    <option></option>
                                                                    @foreach($real_estate_list as $real_estates)
                                                                        <option value="{{$real_estates->real_estate_id}}" @if($profit_list->real_estate_id == $real_estates->real_estate_id) selected @endif>{{ $real_estates->real_estate_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button id="real_estate-zip" class="btn btn-outline-primary btn_zip"><i class="fas fa-search"></i></button>
                                                            </div>
                                                            <div class="invalid-feedback" id ="real_estate_id_error">
                                                            </div>
                                                        </div>
                                                        <!-- 物件名 -->

                                                        <div class="w-100"></div>

                                                        <!-- 部屋番号 -->
                                                        <div class="col-12 col-md-8 col-lg-2 mt-3">
                                                            <label class="label_any" for="textBox"></label>号室
                                                            
                                                            <select class="form-select" name="room_id" id="room_id">
                                                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                                <option></option>
                                                                @foreach($room_list as $rooms)
                                                                    <option value="{{$rooms->room_id}}" @if($profit_list->room_id == $rooms->room_id) selected @endif>{{ $rooms->room_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback" id ="room_id_error">
                                                            </div>
                                                        </div>
                                                        <!-- 部屋番号 -->

                                                        <div class="col-6 col-md-8 col-lg-12 mt-3">
                                                            <hr>
                                                        </div>
                                                                                    
                                                        <!-- 備考 -->
                                                        <div class="col-6 col-md-8 col-lg-12 mt-3">
                                                            <label class="label_any mb-2" for="textBox"></label>備考
                                                            <textarea class="form-control" name="profit_memo" id="profit_memo" rows="5" placeholder="自由に入力">{{ $profit_list->profit_memo }}</textarea>
                                                            <div class="invalid-feedback" id ="profit_memo_error">
                                                            </div> 
                                                        </div>
                                                        <!-- 備考 -->

                                                    </div>

                                                </div>
                                    
                                            </div>
                                            <!-- 内容 -->
                                        </div>
                                    </div>
                                    <!-- タブ内のコンテンツ -->
                                    
                                    <!-- 境界線 -->
                                    <hr>

                                    <!-- ボタン -->
                                    <div class="row row-cols-2 mb-5">

                                        <!-- 削除 -->
                                        <div class="col-6 col-md-6 col-lg-6 mt-3">
                                            <button id="btn_delete" class="btn btn-outline-danger btn-default">削除</button>
                                        </div>
                                        
                                        <!-- 登録、帳票 -->
                                        <div class="col-6 col-md-6 col-lg-6 mt-3">
                                            <!-- 契約詳細id='':帳票ボタン非表示 -->
                                            <button id="btn_edit" class="btn btn-outline-primary btn-default float-end">登録</button>
                                        </div>

                                    </div>     
                                    <!-- ボタン -->

                                <!-- id -->
                                <input type="text" name="profit_id" id="profit_id" value="{{ $profit_list->profit_id }}">

                                </div>
                                <!-- カード -->
                            </form>
                        </div>
                    </div>
                </div>
                <!-- 入力項目 -->


		</main>
		<!-- page-content" -->

		</div>
		<!-- page-wrapper -->

        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_profit_edit.js') }}"></script>
    
        <!-- bootstrap-datepickerのjavascriptコード -->
        <script>
            
            $('#profit_account_date').datepicker({
                language:'ja'
            });

        </script>

	</body>
	
</html>