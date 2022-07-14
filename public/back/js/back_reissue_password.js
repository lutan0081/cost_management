// ページネーションセンター
$(".pagination").addClass("justify-content-center");
$("#links").show();

/**
 * 再発行の処理(ダブルクリックの処理)
 */
$(function(){

    $("#btn_edit").on('click', function(e) {

        console.log("editの処理");

        // ローディング画面
        $("#overlay").fadeIn(300);

        e.preventDefault();

        /**
         * 値取得
         */
        // ユーザ名
        let create_user_id = $("#create_user_id").val();
        console.log('create_user_id:' + create_user_id);

        // mail
        let create_user_mail = $("#create_user_mail").val();
        console.log('create_user_mail:' + create_user_mail);

        // validationフラグ初期値
        let v_check = true;
        
        /**
         * v_checkフラグがfalseの場合、下段のバリデーションに引っ掛かり
         * modalFormにwas-validatedを付与、エラー文字の表示
         */
        if(create_user_id == ''){

            v_check = false;
        }

        if(create_user_mail == ''){

            v_check = false;
        }

        // チェック=falseの場合プログラム終了
        console.log(v_check);

        if (v_check === false) {

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            // エラーメッセージ表示
            $('#passwordForm').addClass("was-validated");

            // ボックスの大きさ調整
            $('.box_container').addClass("box_container_after");

            return false;
        }

        /**
         * 送信データ設定
         */
        // 送信データインスタンス化
        var sendData = new FormData();

        sendData.append('create_user_id', create_user_id);
        sendData.append('create_user_mail', create_user_mail);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backReissuePasswordEntry',
            dataType: 'json',
            data: sendData,
            /**
             * 画像送信設定
             */
            //ajaxのキャッシュの削除
            cache:false,
            /**
             * dataに指定したオブジェクトをクエリ文字列に変換するかどうかを設定します。
             * 初期値はtrue、自動的に "application/x-www-form-urlencoded" 形式に変換します。
             */
            processData : false,
            contentType : false,

        // 接続が出来た場合の処理
        }).done(function(data) {

            // trueの処理->申込一覧に遷移
            if(data.status == true){

                console.log("status:" + data.status);

                // alertの設定
                var options = {
                    title: "メールを送信しました。",
                    icon: "success",
                    buttons: {
                        OK: true
                    }
                };
                
                // then() OKを押した時の処理
                swal(options)
                    .then(function(val) {
                    if (val == 'OK' || val == null) {

                        // location.reload();
                    };
                });

                // ローディング画面終了の処理
                setTimeout(function(){
                    $("#overlay").fadeOut(300);
                },500);
                
                return false;
            };

             // falseの処理->アラートでエラーメッセージを表示
            if(data.status == false){

                console.log("status:" + data.status);
                console.log("messages:" + data.messages);
                console.log("errorkeys:" + data.errkeys);

                // アラートボタン設定
                var options = {
                    title: '入力箇所をご確認ください。',
                    text: '※赤表示の箇所を修正後、再登録をしてください。',
                    icon: 'error',
                    buttons: {
                        OK: 'OK'
                    }
                };
                
                // then() OKを押した時の処理
                swal(options)
                    .then(function(val) {
                    /**
                     * ダイアログ外をクリックされた場合、nullを返す為
                     * ok,nullの場合の処理を記載
                     */
                    if (val == 'OK' || val == null) {

                        console.log(val);

                        /**
                         * formの全要素をerror_Messageを表示に変更
                         * error数だけループ処理
                         */
                        for (let i = 0; i < data.errkeys.length; i++) {
                            
                            // bladeの各divにclass指定
                            let id_key = "#" + data.errkeys[i];
                            $(id_key).addClass('is-invalid');
                            console.log(id_key);

                            // 表示箇所のMessageのkey取得
                            let msg_key = "#" + data.errkeys[i] + "_error"

                            // error_messageテキスト追加
                            $(msg_key).text(data.messages[i]);
                            $(msg_key).show();
                            console.log(msg_key);

                            // ローディング画面停止
                            setTimeout(function(){
                                $("#overlay").fadeOut(300);
                            },500);
                        };

                        // エラーメッセージ挿入後のボックスの大きさ調整
                        $('.box_container').addClass("box_container_after");
                        
                        return false;
                    };
                });
            }

            // ローディング画面終了の処理
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);
            
            return false;

        // ajax接続が出来なかった場合の処理
        }).fail(function(jqXHR, textStatus, errorThrown) {
            
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);

            // ローディング画面終了の処理
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);
            
        });

        
    });

});