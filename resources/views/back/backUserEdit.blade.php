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
                    <div class="col-12 col-md-12 col-lg-12">

                        <form id="editForm" class="needs-validation" novalidate>

                            <div class="info_title mt-3">
                                <i class="far fa-gem icon_blue me-2"></i>ユーザ詳細
                            </div>

                            <!-- 境界線 -->
                            <hr>

                            <!-- カード -->
                            <div class="card border border-0">

                                <div class="col-12 col-md-12 col-lg-12 mb-3">
        
                                    <div class="row row-cols-2">

                                        <!-- ユーザ名 -->
                                        <div class="col-12 col-md-10 col-lg-6 mt-3">
                                            <label class="label_required mb-2" for="textBox"></label>ユーザ名
                                            <input type="text" class="form-control" name="create_user_name" id="create_user_name" placeholder="例：長谷　亘" value="{{ $create_user_list->create_user_name }}" required>
                                            <!-- エラーメッセージ -->
                                            <div class="invalid-feedback" id ="create_user_name_error">
                                                ユーザ名は必須です。
                                            </div>
                                        </div>

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

                                        <div class="w-100"></div>

                                        <div class="col-12 col-md-10 col-lg-3 mt-3">
                                            <label class="label_required mb-2" for="textBox"></label>パスワード
                                            <input type="text" class="form-control" name="create_user_password" id="create_user_password" placeholder="例：lutan0081" value="{{ $create_user_list->create_user_password }}" required>
                                            <!-- エラーメッセージ -->
                                            <div class="invalid-feedback" id ="create_user_password_error">
                                                パスワードは必須です。
                                            </div>
                                        </div>

                                        <div class="w-100"></div>

                                        <!-- 権限 -->
                                        <div class="col-6 col-md-6 col-lg-3 mt-3 mb-4">
                                            <label class="label_required mb-2" for="textBox"></label>権限
                                            <select class="form-select " name="permission_type_id" id="permission_type_id" required>
                                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                <option></option>
                                                @foreach($permission_type_list as $permission_type)
                                                    <option value="{{ $permission_type->permission_type_id }}" @if($permission_type->permission_type_id == $create_user_list->permission_type_id) selected @endif>{{ $permission_type->permission_type_name }}</option>
                                                @endforeach
                                            </select>
                                            
                                            <div class="invalid-feedback" id ="permission_type_id_error">
                                                権限は必須です。
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-10 col-lg-5 mt-3 d-flex align-items-center">
                                            <label class="mb-2 pink_line" for="textBox"><i class="bi bi-megaphone icon_blue me-2 "></i>システム管理者：全操作可/一般ユーザ：操作規制有</label>
                                        </div>
                                        
                                    </div>

                                </div>
                            
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
                            <input type="text" name="create_user_id" id="create_user_id" value="{{ $create_user_list->create_user_id }}">

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
		<script src="{{ asset('back/js/back_user_edit.js') }}"></script>
	</body>
	
</html>