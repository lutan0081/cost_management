<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>銀行詳細/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_user_edit.css') }}">  
		
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
                    
                    <form id="editForm" class="needs-validation" novalidate>

                        <div class="info_title mt-3">
                            <i class="fas fa-key icon_blue me-2"></i>銀行詳細
                        </div>

                        <!-- 境界線 -->
                        <hr>

                        <!-- 銀行名 -->
                        <div class="col-12 col-md-10 col-lg-6 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>銀行名
                            <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="例：〇〇〇〇銀行" value="{{ $bank_list->bank_name }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="bank_name_error">
                                銀行名は必須です。
                            </div>
                        </div>
                        <!-- 銀行名 -->

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- 支店名 -->
                        <div class="col-12 col-md-10 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>支店名
                            <input type="text" class="form-control" name="bank_branch_name" id="bank_branch_name" placeholder="例：〇〇支店" value="{{ $bank_list->bank_branch_name }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="user_license_ruby_error">
                                支店名は必須です。
                            </div>
                        </div>
                        <!-- 支店名 -->
                        
                        <!-- 集金口座 -->
                        <div class="col-6 col-md-6 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>種別                           
                            <select class="form-select " name="bank_type_id" id="bank_type_id" required>
                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                <option></option>
                                @foreach($bank_type_list as $bank_types)
                                    <option value="{{$bank_types->bank_type_id}}" @if($bank_list->bank_type_id == $bank_types->bank_type_id) selected @endif>{{$bank_types->bank_type_name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id ="bank_number_error">
                                種別は必須です。
                            </div>
                        </div>
                        <!-- 集金口座 -->

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- 口座番号 -->
                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>口座番号
                            <input type="text" class="form-control" name="bank_number" id="bank_number" value="{{ $bank_list->bank_number }}" placeholder="例：3054295" required>
                            <div class="invalid-feedback" id ="bank_number_error">
                                口座番号は必須です。
                            </div>
                        </div>
                        <!-- 口座番号 -->

                        <!-- 口座名義人 -->
                        <div class="col-12 col-md-12 col-lg-6 mt-3 pb-2">
                            <label class="label_required mb-2" for="textBox"></label>名義人
                            <input type="text" class="form-control" name="bank_account_name" id="bank_account_name" value="{{ $bank_list->bank_account_name }}" placeholder="例：カブシキガイシャ〇〇〇〇" required>
                            <div class="invalid-feedback" id ="bank_account_name_error">
                                名義人は必須です。
                            </div>
                        </div>
                        <!-- 口座名義人 -->

                        <hr>
        
                        <!-- ボタン -->
                        <div class="row row-cols-2 mb-5">

                            <!-- 削除 -->
                            <div class="col-6 col-md-6 col-lg-6 mt-3">
                                <button id="btn_delete" class="btn btn-outline-danger btn-default">削除</button>
                            </div>
                            
                            <!-- 登録 -->
                            <div class="col-6 col-md-6 col-lg-6 mt-3">
                                <button id="btn_edit" class="btn btn-outline-primary float-end btn-default">登録</button>
                            </div>

                        </div>     
                        <!-- ボタン -->

                        <!-- 集金口座id -->
                        <input type="hidden" name="bank_id" id="bank_id" value="{{ $bank_list->bank_id }}">

                    </form>

                </div>
            </div>
            <!-- コンテンツ -->

		</main>
		<!-- page-content" -->

		</div>
		<!-- page-wrapper -->

        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_user_edit.js') }}"></script>
	</body>
	
</html>