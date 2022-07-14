<!DOCTYPE html>
<html lang="ja">

	<head>
		<title>パスワード再発行/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_reissue_password.css') }}">  
		
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
            <!-- ローディング画面の表示 -->
        
            <!-- page-content" -->
            

                <!-- 一覧 -->
                <div class="container-fluid">
                    
                    <div class="row">
                            
                        <!-- テーブルcard -->
                        <div class="col-12 col-md-12 col-lg-12">

                            <!-- テーブルcard -->
                            <div class="main_container">
                                <div class="box_container">

                                    <div class="row">

                                        <div class="col-12 col-md-12 col-lg-12 box_title mt-3">
                                            <i class="bi bi-key"></i>パスワード再発行
                                        </div>

                                        <div class="col-12 col-md-12 col-lg-12 mt-3 px-5">
                                            パスワードの再発行をご希望の場合は、以下にユーザIDを入力のうえ「再発行ボタン」をクリックしてください。<br>
                                            Eメールにて、新たなパスワードのお送りします。<br>
                                            なお、パスワードを再発行しますと現在ご使用のパスワードではログインができなくなりますのでご注意ください。
                                        </div>

                                        <form id="passwordForm" class="needs-validation" novalidate>

                                            <div class="col-12 col-md-12 col-lg-8 mt-3 px-5">
                                                <label class="label_required mb-2" for="textBox"></label>ユーザID
                                                <input type="text" class="form-control" name="create_user_id" id="create_user_id" placeholder="例：osaka0001" value="" required>
                                                <!-- エラーメッセージ -->
                                                <div class="invalid-feedback" id ="create_user_id_error">
                                                    ユーザIDは必須です。
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-8 mt-3 px-5">
                                                <label class="label_required mb-2" for="textBox"></label>E-mail
                                                <input type="text" class="form-control" name="create_user_mail" id="create_user_mail" placeholder="例：lutan0081.h@gmail.com" value="" required>
                                                <!-- エラーメッセージ -->
                                                <div class="invalid-feedback" id ="create_user_mail_error">
                                                    E-mailは必須です。
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-12 mt-3 px-5">
                                                <button id="btn_edit" class="btn btn-outline-primary float-end btn-default">再発行</button>
                                            </div>

                                        </form>
                                        
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                        <!-- テーブルcard -->

                    </div>
                </div>
                <!-- 一覧 --> 

		</div>
		<!-- page-wrapper -->
        
        <!-- js -->
        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_reissue_password.js') }}"></script>
	</body>
	
</html>