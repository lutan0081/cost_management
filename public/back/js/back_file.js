$(function(){

    /**
     * ページネーションセンター
     */
    $(".pagination").addClass("justify-content-center");
    $("#links").show();
    
    /**
     * 投稿フォームの初期化
     */
    function clearProc(){
        console.log('モーダル初期化の処理')

        $("#file_name").val("");
    
        $("#file_type_id").val("");

        $("#file_memo").val("");
    
        $("#file_upload").val("");

        $("#file_id").val("");

        $(".remove_class").remove();

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

        var file_id = id.split('_')[1];
        console.log(file_id);

        // 送信データ
        let sendData = {
			"file_id": file_id,
        };

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backFileEditInit',
            dataType: 'json',
            data: sendData,
        
        // 接続処理
        }).done(function(data) {

            console.log("file_list:" + data.file_list);

            $('.remove_class').remove();
            
            /**
             * 値取得
             */
            // id
            file_id = data.file_list[0]['file_id'];
            console.log(file_id);

            // ファイル名
            file_name = data.file_list[0]['file_name'];
            console.log(file_name);

            // 種別
            file_type_id = data.file_list[0]['file_type_id'];
            console.log(file_type_id);

            // 備考
            file_memo = data.file_list[0]['file_memo'];
            console.log(file_memo);

            // パス名
            file_path = data.file_list[0]['file_path'];
            console.log(file_path);

            // 拡張子
            file_extension = data.file_list[0]['file_extension'];
            console.log(file_extension);

            // ファイルのパスの生成（ファイルパス+ファイル名+拡張子)
            flle_path = file_path + '/' + file_name + '.' + file_extension;
            console.log(flle_path);

            /**
             * 値代入
             */
            // id
            $("#file_id").val(file_id);

            // タイトル名
            $("#file_name").val(file_name);

            // 種別
            $("#file_type_id").val(file_type_id);

            // 内容
            $("#file_memo").val(file_memo);

            /**
             * 画像指定
             * パス指定・removeクラス（前回のhtmlを削除)
             */
            // var img_link =  $("<img src='./back/img/pdf_icon.jpeg' class='pdf_icon_size remove_class' />");
            // var a_link = $("<a href='../storage/app/public/" + flle_path + "' target='_blank'></a>");

            // iconサイズの変数
            let file_icon = "";

            // imgタグのアイコンのサイズ
            let file_class=""

            /**
             * パスの生成
             * pdfの場合
             */
            if(file_extension == 'pdf'){
                file_icon = "./back/img/pdf_icon.jpeg"
                file_class = "pdf_icon_size remove_class"
            }

            // pngの場合
            if(file_extension == 'png'){
                file_icon = "storage/" + flle_path
                file_class = "img_icon_size remove_class"
            }

            // jpegの場合
            if(file_extension == 'jpeg'){
                file_icon = "storage/" + flle_path
                file_class = "img_icon_size remove_class"
            }

            /**
             * タグの生成
             */
            // imgタグの生成
            let img_link = $('<img>', {
                src:file_icon,
                class:file_class,
            });

            // aタグの生成
            let a_link = $('<a>', {
                href:"../storage/app/public/" + flle_path,
                target:"_blank",
            });
            
            // aタグにimgタグを追加
            a_link.append(img_link);

            // file_boxにaタグ（img）を追加
            $('#file_box').append(a_link);

            // モーダルを開く
            $('#fileEditModal').modal('show');

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
        let file_id = $("#file_id").val();
        console.log('file_id:' + file_id);

        // タイトル
        let file_name = $("#file_name").val();
        console.log('file_name:' + file_name);

        // 種別
        let file_type_id = $("#file_type_id").val();
        console.log('file_type_id:' + file_type_id);

        // 内容
        let file_memo = $("#file_memo").val();
        console.log('file_memo:' + file_memo);

        // 画像ファイル取得
        let file_upload = $('#file_upload').prop('files')[0];
        console.log("file_upload:" + file_upload);

        // validationフラグ初期値
        let v_check = true;
        
        /**
         * v_checkフラグがfalseの場合、下段のバリデーションに引っ掛かり
         * modalFormにwas-validatedを付与、エラー文字の表示
         */
        if(file_name == ''){

            v_check = false;
        }

        if(file_type_id == ''){

            v_check = false;
        }

        if(file_upload == ''){

            v_check = false;
        }
        
        // チェック=falseの場合プログラム終了
        console.log('v_check' + v_check);

        if (v_check === false) {

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            $('#modalForm').addClass("was-validated");

            return false;
        }

        /**
         * 送信データ設定
         */
        // 送信データインスタンス化
        var sendData = new FormData();

        sendData.append('file_id', file_id);
        sendData.append('file_name', file_name);
        sendData.append('file_type_id', file_type_id);
        sendData.append('file_memo', file_memo);
        sendData.append('file_upload', file_upload);
        
        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backFileEditEntry',
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

        console.log('file_deleteの処理');

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
        let file_id = $("#file_id").val();
        console.log(file_id);
        
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

                    "file_id": file_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backFileDeleteEntry',
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




