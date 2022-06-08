<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>ユーザ詳細/COST</title>

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
                            <i class="far fa-gem icon_blue me-2"></i></i>ユーザ詳細
                        </div>

                        <!-- 境界線 -->
                        <hr>

                        <!-- ユーザ名 -->
                        <div class="col-12 col-md-10 col-lg-6 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>ユーザ名
                            <input type="text" class="form-control" name="create_user_name" id="create_user_name" placeholder="例：長谷　亘" value="{{ $create_user_list->create_user_name }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="create_user_name_error">
                                ユーザ名は必須です。
                            </div>
                        </div>

                        <!-- 改行 -->
                        <div class="w-100"></div>

                        <!-- パスワード -->
                        <div class="col-12 col-md-10 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>ユーザID
                            <input type="text" class="form-control" name="create_user_mail" id="create_user_mail" placeholder="例：lutan0081" value="{{ $create_user_list->create_user_mail }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="create_user_mail_error">
                                ユーザIDは必須です。
                            </div>
                        </div>
                        
                        <!--  -->
                        <div class="col-12 col-md-10 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>パスワード
                            <input type="text" class="form-control" name="create_user_password" id="create_user_password" placeholder="例：lutan0081" value="{{ $create_user_list->create_user_password }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="create_user_password_error">
                                パスワードは必須です。
                            </div>
                        </div>
                        
                        <!-- 権限 -->
                        <div class="col-6 col-md-6 col-lg-3 mt-3 mb-4">
                            <label class="label_required mb-2" for="textBox"></label>権限                           
                            <select class="form-select " name="permission_type_id" id="permission_type_id" required>
                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                <option></option>
                            </select>
                            <div class="invalid-feedback" id ="permission_type_id_error">
                                権限は必須です。
                            </div>
                        </div>

                        <!-- 改行 -->
                        <div class="w-100"></div>

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

                        <!-- id -->
                        <input type="text" name="create_user_id" id="create_user_id" value="{{ $create_user_list->create_user_id }}">

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