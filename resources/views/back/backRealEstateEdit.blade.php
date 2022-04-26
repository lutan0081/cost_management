<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>物件詳細/COSTS</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_owner_edit.css') }}">  
		
        <style>

            /* ボタンデフォルト値 */
            .btn-default{
                width: 7rem;
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
                            <i class="fas fa-key icon_blue me-2"></i>物件詳細
                        </div>

                        <!-- 境界線 -->
                        <hr>

                        <!-- 物件名 -->
                        <div class="col-12 col-md-10 col-lg-8 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>物件名
                            <input type="text" class="form-control" name="real_estate_name" id="real_estate_name" placeholder="例：〇〇〇〇マンション" value="{{ $real_estate_list->real_estate_name }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="real_estate_name_error">
                                物件名は必須です。
                            </div>
                        </div>
                        <!-- 物件名 -->

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- 郵便番号 -->
                        <div class="col-7 col-md-4 col-lg-2 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>郵便番号
                            <div class="input-group">
                                <input type="number" class="form-control" name="real_estate_post_number_error" id="real_estate_post_number_error" value="{{ $real_estate_list->real_estate_post_number }}" placeholder="例：1111111" required>
                                <button id="owner-btn-zip" class="btn btn-outline-primary btn_zip"><i class="fas fa-search"></i></button>
                                <div class="invalid-feedback" id="real_estate_post_number_error">
                                    郵便番号は必須です。
                                </div>
                            </div>
                        </div>
                        
                        <!-- 住所 -->
                        <div class="col-12 col-md-12 col-lg-8 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>住所
                            <input type="text" class="form-control" name="real_estate_address" id="real_estate_address" value="{{ $real_estate_list->real_estate_address }}" placeholder="例：大阪府大阪市梅田1丁目xx-yy" required>
                            <div class="invalid-feedback" id ="real_estate_address_error">
                                住所は必須です。
                            </div>       
                        </div>


                        <div class="col-6 col-md-8 col-lg-12 mt-4">
                            <hr>
                        </div>

                        <!-- 家主名 -->
                        <div class="col-6 col-md-8 col-lg-5 mt-3">
                            <label class="label_required mb-2"></label>家主名
                            
                            <select class="form-select" name="owner_name" id="owner_name">
                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                <option></option>
                                @foreach($owner_list as $owners)
                                    <option value="{{ $owners->owner_id }}" @if($real_estate_list->owner_id == $owners->owner_id) selected @endif>{{ $owners->owner_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 郵便番号 -->
                        <div class="col-7 col-md-4 col-lg-2 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>郵便番号
                            <div class="input-group">
                                <input type="number" class="form-control" name="owner_post_number" id="owner_post_number" value="{{ $real_estate_list->real_estate_name }}" placeholder="例：1111111" disabled>
                                <div class="invalid-feedback" id="owner_post_number_error">
                                    郵便番号は必須です。
                                </div>
                            </div>
                        </div>
                        
                        <!-- 住所 -->
                        <div class="col-12 col-md-12 col-lg-8 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>住所
                            <input type="text" class="form-control" name="owner_address" id="owner_address" value="{{ $real_estate_list->real_estate_name }}" placeholder="例：大阪府大阪市梅田1丁目xx-yy" disabled>
                            <div class="real_estate-tab invalid-feedback" id ="owner_address_error">
                                住所は必須です。
                            </div>       
                        </div>

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- TEL -->
                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>TEL
                            <input type="text" class="form-control" name="owner_tel" id="owner_tel" value="{{ $real_estate_list->real_estate_name }}" placeholder="例：06-1234-5678" disabled>
                            <div class="invalid-feedback" id ="owner_tel_error">
                                TELは必須です。
                            </div>
                        </div>

                        <!-- FAX -->
                        <div class="col-12 col-md-12 col-lg-3 mt-3 pb-2">
                            <label class="label_any mb-2" for="textBox"></label>FAX
                            <input type="text" class="form-control" name="owner_fax" id="owner_fax" value="{{ $real_estate_list->real_estate_name }}" placeholder="例：06-1234-5678" disabled>
                            <div class="invalid-feedback" id ="owner_fax_error">
                                FAXは必須です。
                            </div>
                        </div>

                        <div class="col-6 col-md-8 col-lg-12 mt-4">
                            <hr>
                        </div>
        
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

                        <!-- 不動産id -->
                        <input type="hidden" name="owner_id" id="owner_id" value="{{ $real_estate_list->real_estate_id }}">

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