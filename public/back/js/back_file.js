$(function(){

    /**
     * ページネーションセンター
     */
    $(".pagination").addClass("justify-content-center");
    $("#links").show();
    
    /**
     * 新着情報の投稿フォームの初期化
     */
    function clearProc(){
        console.log('モーダル初期化の処理')

        $("#information_title").val("");
    
        $("#information_type").val("");
    
        $("#information_contents").val("");
    };

    /**
     * 新規表示
     */
    $("#btn_new").on('click', function(e) {

        // モーダル初期化
        clearProc();

        // モーダルを開く処理
        $('#fileEditModal').modal('show');

    });

    /**
     * 編集表示(ダブルクリックの処理:ajax)
     */
    $(".click_class").on('dblclick', function(e) {

        console.log("ダブルクリックの処理");

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);

        // tdのidを配列に分解
        var id = $(this).attr("id");

        var information_id = id.split('_')[1];
        console.log(information_id);

        // 送信データ
        let sendData = {

			"information_id": information_id,

        };

        $.ajaxSetup({

            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}

        });

        $.ajax({

            type: 'post',
            url: 'backInformationEditInit',
            dataType: 'json',
            data: sendData,
        
        // 接続処理
        }).done(function(data) {

            /**
             * 値取得
             */
            information_name = data.information_list[0]['information_name'];

            information_type_id = data.information_list[0]['information_type_id'];

            information_contents = data.information_list[0]['information_contents'];

            information_id = data.information_list[0]['information_id'];

            /**
             * 値代入
             */
            // id
            $("#information_id").val(information_id);

            // タイトル名
            $("#information_title").val(information_name);

            // 種別
            $("#information_type").val(information_type_id);

            // 内容
            $("#information_contents").val(information_contents);

            // モーダル開く
            $('#informaitonModal').modal('show');

            // ローディング画面停止
			setTimeout(function(){
				$("#overlay").fadeOut(300);
			},500);

            return false;
    
        // ajax接続失敗の時の処理
        }).fail(function(jqXHR, textStatus, errorThrown) {

            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown); 

        });
    });

    /**
     * 登録
     */
    $("#btn_modal_edit").on('click', function(e) {

        console.log('新着情報登録の処理');

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);        

        /**
         * 値取得
         */
        // id
        let information_id = $("#information_id").val();
        console.log('information_id:' + information_id);

        // タイトル
        let information_title = $("#information_title").val();
        console.log('information_title:' + information_title);

        // 種別
        let information_type = $("#information_type").val();
        console.log('information_type:' + information_type);

        // 内容
        let information_contents = $("#information_contents").val();
        console.log('information_contents:' + information_contents);

        // validationフラグ初期値
        let v_check = true;
        
        /**
         * v_checkフラグがfalseの場合、下段のバリデーションに引っ掛かり
         * modalFormにwas-validatedを付与、エラー文字の表示
         */
        if(information_title == ''){

            v_check = false;
        }

        if(information_type == ''){

            v_check = false;
        }

        if(information_contents == ''){

            v_check = false;
        }
        
        // チェック=falseの場合プログラム終了
        console.log('v_check' + v_check);

        if (v_check === false) {

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            $('#informaitonModal').addClass("was-validated");

            return false;
        }

        /**
         * 送信データ設定
         */
        // 送信データインスタンス化
        var sendData = new FormData();

        sendData.append('information_id', information_id);
        sendData.append('information_title', information_title);
        sendData.append('information_type', information_type);
        sendData.append('information_contents', information_contents);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backInformationEditEntry',
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
                    title: "登録が完了しました。",
                    icon: "success",
                    buttons: {
                        OK: true
                    }
                };
                
                // then() OKを押した時の処理
                swal(options)
                    .then(function(val) {
                    if (val == 'OK' || val == null) {

                        location.reload();
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

    /**
     * 削除
     */
    $("#btn_modal_delete").on('click', function(e) {

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
        let information_id = $("#information_id").val();
        console.log(information_id);
        
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

                    "information_id": information_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backInformationDeleteEntry',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                    console.log('status:' + data.status)

                    var options = {
                        title: "削除が完了しました。",
                        icon: "success",
                        buttons: {
                            OK: true
                        }
                    };

                    // then() OKを押した時の処理
                    swal(options)
                        .then(function(val) {
                        if (val) {
                            location.reload();
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




