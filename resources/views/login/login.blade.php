<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>ログイン/COSTS</title>

        <!-- css -->
        @component('component.front_head')
        @endcomponent

        <!-- login -->
        <link rel="stylesheet" href="{{ asset('login/css/login.css') }}">

    </head>

    <body>

        <div class="wrapper fadeInDown">
            <div id="formContent">
                <!-- Tabs Titles -->

                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="./login/images/kasegu_logo.png" id="icon" alt="User Icon" />
                </div>

                <!-- Login Form -->
                <form>

                    <!-- エラーメッセージ -->
                    <div style="display:none" class="msg"></div>

                    <!-- Id -->
                    <label for="">E-mail<br></label>
                    <input type="text" id="mail_request" class="fadeIn second" name="login" required>
                    
                    <!-- パスワード -->
                    <label for="">Password<br></label>
                    <input type="password" id="password_request" class="fadeIn third" name="login" placeholder="" required>
                
                    <!-- 登録ボタン -->
                    <input type="button" class="fadeIn fourth btn" value="Log In">
                    
                    <!-- 自動ログイン -->
                    <div class="fadeIn fifth">
                        <input class="form-check-input" type="checkbox" name="autoLogin" id="auto_login_flag">
                        <span class="form-check-label" for="autoLogin">次回から自動的にログイン</span>
                    </div>

                </form>

                <!-- アカウント作成 -->
                <div id="formFooter">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-6">
                                <a class="underlineHover float-lg-start" href="createUserInit">パスワードを忘れた</a>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                                <a class="underlineHover float-lg-end" href="createUserInit">ユーザ登録</a>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- ユーザ登録後、ログイン画面に遷移の為のパス設定 -->
        <input type="hidden" id="top_url" value="{{ url('/') }}" />

        <!-- js -->
        @component('component.front_js')
        @endcomponent
        
        <!-- login.js -->
        <script src="{{ asset('login/js/login.js') }}"></script>
        
    </body>

</html>