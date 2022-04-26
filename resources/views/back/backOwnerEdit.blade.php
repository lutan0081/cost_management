<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>家主詳細/KASEGU</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_owner_edit.css') }}">  
		
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
                            <i class="fas fa-key icon_blue me-2"></i>家主詳細
                        </div>

                        <!-- 境界線 -->
                        <hr>

                        <!-- 家主名 -->
                        <div class="col-12 col-md-10 col-lg-6 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>家主名
                            <input type="text" class="form-control" name="owner_name" id="owner_name" placeholder="例：株式会社〇〇〇〇" value="{{ $owner_list->owner_name }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="owner_name_error">
                                家主名は必須です。
                            </div>
                        </div>
                        <!-- 家主名 -->

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- 郵便番号 -->
                        <div class="col-7 col-md-4 col-lg-2 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>郵便番号
                            <div class="input-group">
                                <input type="number" class="form-control" name="owner_post_number" id="owner_post_number" value="{{ $owner_list->owner_post_number }}" placeholder="例：1111111" required>
                                <button id="owner-btn-zip" class="btn btn-outline-primary btn_zip"><i class="fas fa-search"></i></button>
                                <div class="invalid-feedback" id="owner_post_number_error">
                                    郵便番号は必須です。
                                </div>
                            </div>
                        </div>
                        
                        <!-- 住所 -->
                        <div class="col-12 col-md-12 col-lg-8 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>住所
                            <input type="text" class="form-control" name="owner_address" id="owner_address" value="{{ $owner_list->owner_address }}" placeholder="例：大阪府大阪市梅田1丁目xx-yy" required>
                            <div class="real_estate-tab invalid-feedback" id ="owner_address_error">
                                住所は必須です。
                            </div>       
                        </div>

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- TEL -->
                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>TEL
                            <input type="text" class="form-control" name="owner_tel" id="owner_tel" value="{{ $owner_list->owner_tel }}" placeholder="例：06-1234-5678" required>
                            <div class="invalid-feedback" id ="owner_tel_error">
                                TELは必須です。
                            </div>
                        </div>

                        <!-- FAX -->
                        <div class="col-12 col-md-12 col-lg-3 mt-3 pb-3">
                            <label class="label_any mb-2" for="textBox"></label>FAX
                            <input type="text" class="form-control" name="owner_fax" id="owner_fax" value="{{ $owner_list->owner_fax }}" placeholder="例：06-1234-5678" required>
                            <div class="invalid-feedback" id ="owner_fax_error">
                                FAXは必須です。
                            </div>
                        </div>

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

                        <!-- 家主id -->
                        <input type="hidden" name="owner_id" id="owner_id" value="{{ $owner_list->owner_id }}">

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
		<script src="{{ asset('back/js/back_owner_edit.js') }}"></script>
	</body>
	
</html>