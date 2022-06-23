$(function(){
    /**
     * 権限IDで判断し、一般ユーザは操作不可にする
     */
    let permission_type_id = $("#permission_type_id").val();
    console.log('permission_type_id:' + permission_type_id);

    // OK=質問欄入力・承諾・CSV出力/NG = 編集(質問欄除く)
    if(permission_type_id != 1){
        $(".disabled_class").prop('disabled', true);
    }
    
    /**
     * 登録
     */
    $("#btn_edit").on('click', function(e) {

        console.log("btn_editクリックされています");

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);

        // バリデーション
        // formの値数を取得
        let forms = $('.needs-validation');
        console.log('forms.length:' + forms[0].length);

        // validationフラグ初期値
        let v_check = true;

        // formの項目数ループ処理
        for (let i = 0; i < forms[0].length; i++) {

            // タグ名、Id名取得
            let form = forms[0][i];
            console.log('from:'+ form);

            // タグ名を取得 input or button
            let tag = $(form).prop("tagName");
            console.log('tag:'+ tag);

            // 各項目のid取得
            let f_id = $(form).prop("id");
            console.log('id:'+ f_id);
            
            // form内のbuttonは通過
            if (tag == 'BUTTON') {
                continue;
            }

            // 必須ではない場合、以降を処理せず次のレコードに行く
            let required = $(form).attr("required");

            console.log('required:' + required);

            if (required !== 'required') {

                continue;
            }

            // formの値を取得->クラス付与
            let val = $(form).val();

            console.log('value:'+ val);

            if (val === '') {

                // blade側のformタグにwas-validatedを追加
                $(forms).addClass("was-validated");
                v_check = false;

            }
        }

        // チェック=falseの場合プログラム終了
        console.log(v_check);

        if (v_check === false) {

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            return false;
        }

        // ユーザ名
        let create_user_name = $("#create_user_name").val();

        // ユーザID
        let create_user_mail = $("#create_user_mail").val();

        // パスワード
        let create_user_password = $("#create_user_password").val();

        // 権限
        let permission_type_id = $("#permission_type_id").val();

        // create_user_id
        let create_user_id = $("#create_user_id").val();

        // 送信データインスタンス化
        var sendData = new FormData();
        
        sendData.append('create_user_name', create_user_name);
        sendData.append('create_user_mail', create_user_mail);
        sendData.append('create_user_password', create_user_password);
        sendData.append('permission_type_id', permission_type_id);
        sendData.append('create_user_id', create_user_id);
        
        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backUserEditEntry',
            dataType: 'json',
            data: sendData,
            cache:false,
            processData : false,
            contentType : false,

        // 接続が出来た場合の処理
        }).done(function(data) {

            // trueの処理->申込一覧に遷移
            if(data.status == true){

                console.log("status:" + data.status);

                // alertの設定
                var options = {
                    title: "登録が完了しました。",
                    icon: "success",
                    buttons: {
                        OK: true
                    }
                };
                
                // then() OKを押した時の処理
                swal(options)
                    .then(function(val) {
                    if (val) {

                        location.href = 'backUserInit';
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
                    text: '※誤入力箇所を赤文字で表示しています。',
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
                        };
                    };

                    return false;
                });
            }

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

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

    /**
     * 削除
     */
    $("#btn_delete").on('click', function(e) {

        console.log('削除の処理');

        e.preventDefault();

        // alertの設定
        var options = {
            title: "削除しますか？",
            text: "※一度削除したデータは復元出来ません。",
            icon: 'warning',
            buttons: {
                Cancel: "Cancel", // キャンセルボタン
                OK: true
            }
        };

        // 値取得
        let create_user_id = $("#create_user_id").val();
        console.log(create_user_id);
        
        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {

            if(val == null){

                console.log('キャンセルの処理');

                return false;
            }

            if (val == "OK") {

                console.log('OKの処理');

                // 送信用データ
                let sendData = {

                    "create_user_id": create_user_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backUserDeleteEntry',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                    console.log('status:' + data.status)

                    var options = {
                        title: "削除が完了しました。",
                        icon: "success",
                        buttons: {
                            ok: true
                        }
                    };

                    // then() OKを押した時の処理
                    swal(options)
                        .then(function(val) {
                        if (val) {

                            location.href = 'backUserInit';
                            
                        }
                    });

                // ajax接続失敗の時の処理
                }).fail(function(jqXHR, textStatus, errorThrown) {

                    setTimeout(function(){
                        $("#overlay").fadeOut(300);
                    },500);

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            };
            // sweetalert
        });
    });

    /**
     * 権限付与
     */
    $("#btn_set_authority").on('click', function(e) {

        console.log('権限付与の処理');

        e.preventDefault();

        // alertの設定
        var options = {
            title: "ユーザ権限を付与しますか？",
            icon: 'warning',
            buttons: {
                Cancel: "Cancel", // キャンセルボタン
                OK: true
            }
        };

        // 値取得
        let create_user_id = $("#create_user_id").val();
        console.log(create_user_id);
        
        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {

            if(val == null){

                console.log('キャンセルの処理');

                return false;
            }

            if (val == "OK") {

                console.log('OKの処理');

                // 送信用データ
                let sendData = {

                    "create_user_id": create_user_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backSetAuthorityEntry',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                    console.log('status:' + data.status)

                    var options = {
                        title: "権限付与が完了しました。",
                        icon: "success",
                        buttons: {
                            ok: true
                        }
                    };

                    // then() OKを押した時の処理
                    swal(options)
                        .then(function(val) {
                        if (val) {

                            location.href = 'backUserInit';
                            
                        }
                    });

                // ajax接続失敗の時の処理
                }).fail(function(jqXHR, textStatus, errorThrown) {

                    setTimeout(function(){
                        $("#overlay").fadeOut(300);
                    },500);

                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            };
            // sweetalert
        });
    }); 

});