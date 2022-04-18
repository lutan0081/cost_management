/**
 * ログインボタン
 */
$(function() {
    $(".btn").on('click', function(e) {

        e.preventDefault();
        
        // Mailを取得
        let mail_request = $("#mail_request").val();

        // パスワード取得
        let password_request = $("#password_request").val();

        // 自動ログイン(tru=OK false=NG)
        let auto_login_flag = $("#auto_login_flag").prop('checked')
        console.log("auto_login_flag:" + auto_login_flag);

        // 送信用データ設定
        let sendData = {
            "auto_login_flag": auto_login_flag,
            "mail_request": mail_request,
            "password_request": password_request
        };

        console.log(sendData);
        
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            
            type: 'post',
            url: 'loginEntry',
            dataType: 'json',
            data: sendData,
        
        // 接続が出来た場合の処理
        }).done(function(data) {
            // trueの場合ログイン
            console.log(data);
            
            /**
             * admin = trueの場合、管理画面に遷移
             */
            if(data.admin == true){

                location.href = 'adminInit'
                return false;

            }else{
                /**
                 * status = trueの場合、一般画面に遷移
                 */
                if(data.status == true){

                    location.href = 'backHomeInit';
                    return false;

                }else{

                    /**
                     * falseの処理
                     */
                    $('.msg').addClass('error_text');
                    $('.msg').text('E-mailまたはPasswordが正しくありません。').show();
                
                }

            }
        // ajax接続が出来なかった場合の処理
        }).fail(function(jqXHR, textStatus, errorThrown) {
            
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        
    });
});

