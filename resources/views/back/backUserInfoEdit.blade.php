<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>ユーザ情報/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_user_info_edit.css') }}">  
		
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

                                <div class="info_title mt-2">
                                    <i class="bi bi-gear-fill icon_blue me-2"></i>ユーザ情報
                                </div>

                                <!-- 境界線 -->
                                <hr>

                                <!-- カード -->
                                <div class="card border border-0">

                                    @include('component.formUser')

                                    <!-- ボタン -->
                                    <div class="row row-cols-2 mb-5">

                                        <!-- ユーザ申請 -->
                                        <div class="col-6 col-md-6 col-lg-6 mt-3">
                                            <button type="button" class="btn btn-outline-primary btn-default float-start" data-bs-toggle="modal" data-bs-target="#addUseModal">追加申請</button>
                                        </div>
                                        
                                        <!-- 登録、帳票 -->
                                        <div class="col-6 col-md-6 col-lg-6 mt-3">
                                            <button id="btn_edit" class="btn btn-outline-primary btn-default float-end">登録</button>
                                        </div>

                                    </div>     
                                    <!-- ボタン -->

                                    <!-- id -->
                                    <input type="hidden" name="create_user_id" id="create_user_id" value="{{ $create_user_list->create_user_id }}">
                                    
                                    <!-- 権限id sessionから取得 idが重複するため、変数名の末尾にidを省略-->
                                    <input type="hidden" name="permission_type" id="permission_type" value="{{ Session::get('permission_type_id') }}">

                                </div>
                                <!-- カード -->
                            </form>
                        </div>
                    </div>
                </div>
                <!-- 入力項目 -->

                <!-- ユーザ追加モーダル -->
                <div class="modal fade" id="addUseModal" tabindex="-1" aria-labelledby="addUseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <!-- モーダルヘッダー -->
                            <div class="modal-header">
                                <div class="info_title mt-2">
                                    <i class="bi bi-gear-fill icon_blue me-2"></i>ユーザ情報
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <!-- モーダルボディ -->
                            <div class="modal-body">
                                <form autocomplete="off">
                                    <div class="container mt-3">
                                        <div class="row">
                                            <!-- 入力フォーム -->
                                            <div class="col-12 col-md-12 col-lg-12 mb-3">

                                                <div class="row row-cols-2">

                                                    <!-- ユーザ名 -->
                                                    <div class="col-12 col-md-10 col-lg-10">
                                                        <label class="label_required mb-2" for="textBox"></label>ユーザ名
                                                        <input type="text" class="form-control" name="modal_create_user_name" id="modal_create_user_name" placeholder="例：長谷　亘" required>
                                                        <!-- エラーメッセージ -->
                                                        <div class="invalid-feedback" id ="modal_create_user_name_error">
                                                            ユーザ名は必須です。
                                                        </div>
                                                    </div>

                                                    <div class="w-100"></div>

                                                    <!-- ユーザid -->
                                                    <div class="col-12 col-md-10 col-lg-10 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>ユーザID
                                                        <input type="text" class="form-control" name="modal_create_user_mail" id="modal_create_user_mail" placeholder="例：lutan0081.h@gmail.com" style="ime-inactive;" required>
                                                        <!-- エラーメッセージ -->
                                                        <div class="invalid-feedback" id ="modal_create_user_mail_error">
                                                            ユーザIDは必須です。
                                                        </div>
                                                    </div>

                                                    <div class="w-100"></div>

                                                    <!-- パスワード -->
                                                    <div class="col-12 col-md-10 col-lg-10 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>パスワード
                                                        <input type="password" class="form-control" name="modal_create_user_password" id="modal_create_user_password" placeholder="例：lutan0081" required>
                                                        <!-- エラーメッセージ -->
                                                        <div class="invalid-feedback" id ="modal_create_user_password_error">
                                                            パスワードは必須です。
                                                        </div>
                                                    </div>

                                                    <!-- パスワード再入力 -->
                                                    <div class="col-12 col-md-10 col-lg-10 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>パスワード(確認用)
                                                        <input type="password" class="form-control" name="modal_create_user_password_confirm" id="modal_create_user_password_confirm" placeholder="例：lutan0081" required>
                                                        <!-- エラーメッセージ -->
                                                        <div class="invalid-feedback" id ="modal_create_user_password_confirm_error">
                                                            パスワードは必須です。
                                                        </div>
                                                    </div>
                                                    
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- ボタン -->
                            <div class="modal-footer">
                                <button type="button" id="btn_modal_back" class="btn btn-outline-secondary btn-default" data-bs-dismiss="modal">閉じる</button>
                                <button type="button" id="btn_modal_add_user" class="btn btn-outline-primary btn-default">申請</button>
                            </div>

                        </div>
                    </div>
                </div>

            </main>
            <!-- page-content" -->

		</div>
		<!-- page-wrapper -->

        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_user_info_edit.js') }}"></script>
	</body>
	
</html>