$(function(){

    // csv取込モーダル画面表示
    $('#csvModal').on('show.bs.modal', function (e) {
        console.log('show');//showメソッドが実行されたら実行される。
        $('#modal_bank_id').val('');
        $('#modal_bank_format_type_id').val('');
        $('#modal_img_file').val('');
    });

    /**
     * ページネーションセンター
     */
    $(".pagination").addClass("justify-content-center");
    $("#links").show();
    
    /**
     * cvsインポート時のエラーメッセージエクスポート
     */
    function message_export (data) {

        window.open('csvMessageExport?message=' + data.message, '_self');
    }

    // スリープ処理
    function sleep(waitMsec) {
        var startMsec = new Date();
        
        // 指定ミリ秒間だけループさせる（CPUは常にビジー状態）
        while (new Date() - startMsec < waitMsec);
    }

    /**
     * 編集(ダブルクリックの処理)
     */
    $(".click_class").on('dblclick', function(e) {

        console.log("ダブルクリックの処理.");

        // ローディング画面
        $("#overlay").fadeIn(300);

        // tdのidを配列に分解
        var id_info = $(this).attr("id");
        id = id_info.split('_')[1];
        console.log(id);

        setTimeout(function(){
            $("#overlay").fadeOut(300);
        },500);

        // idをパラメーターでControllerに渡す
        location.href = "backCostEditInit?cost_id=" + id;

    });

    /**
     * 編集(ラジオボタンの処理)
     */
    $("#btn_edit").on('click', function(e) {
        console.log("編集ボタンの処理");

        // ローディング画面
        $("#overlay").fadeIn(300);

        e.preventDefault();

        /**
         * ラジオボタンにチェックがない場合、プログラム終了
         * ラジオボタンに値がない場合 = 0
         * ラジオボタンに値がある場合 = 1
         */
        // チェックがない場合終了
        if ($('input[name=flexRadioDisabled]:checked').length <= 0) {

            console.log("チェックがない場合の処理");

            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);
            
            exit;
        }    

        // id取得
        var id = $('input[name=flexRadioDisabled]:checked').attr('id');
        
        console.log(id);

        // idをパラメーターでControllerに渡す
        location.href = "backProfitEditInit?profit_id=" + id;

        setTimeout(function(){
            $("#overlay").fadeOut(300);
        },300);
        
    });

    /**
     * CSVインポート
     */
    $("#btn_modal_csv_import").on('click', function(e) {

        console.log("csv読込ボタンの処理");

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);

        /**
         * CSV読込
         */
        // 銀行id
        let modal_bank_id = $("#modal_bank_id").val();
        console.log('modal_bank_id:' + modal_bank_id);

        // 銀行formatid
        let modal_bank_format_type_id = $("#modal_bank_format_type_id").val();
        console.log('modal_bank_format_type_id:' + modal_bank_format_type_id);

        // 画像ファイル取得
        let modal_img_file = $('#modal_img_file').prop('files')[0];
        console.log("modal_img_file:" + modal_img_file);

        /**
         * バリデーション
         */
        // validationフラグ初期値
        let v_check = true;

        /**
         * v_checkフラグがfalseの場合、下段のバリデーションに引っ掛かり
         * modalFormにwas-validatedを付与、エラー文字の表示
         */
        if(modal_bank_id == ''){

            v_check = false;
        }

        if(modal_bank_format_type_id == ''){

            v_check = false;
        }

        if(modal_img_file == ''){

            v_check = false;
        }

        // チェック=falseの場合プログラム終了
        console.log(v_check);

        if (v_check === false) {

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            $('#modalForm').addClass("was-validated");;

            return false;
        }

        // 送信データインスタンス化
        var sendData = new FormData();

        // 送信データ
        sendData.append('modal_bank_id', modal_bank_id);
        sendData.append('modal_bank_format_type_id', modal_bank_format_type_id);
        sendData.append('modal_img_file', modal_img_file);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'csvImport',
            dataType: 'json',
            data: sendData,
            
            // 画像送信設定
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
            
            console.log("status:" + data.status);
            console.log("message:" + data.message);
            console.log("typeof:" + typeof(data.message));

            // trueの処理->申込一覧に遷移
            if(data.status == true){

                // alertの設定
                var options = {
                    title: "CSVのインポートが完了しました。",
                    icon: "success",
                    buttons: {
                        OK: true
                    }
                };
                
                // then() OKを押した時の処理
                swal(options)
                    .then(function(val) {
                    if (val == 'OK' || val == null) {

                        if (data.message != ''){

                            console.log('messegeがある場合の処理');

                            // エラーメッセージが有る場合Excelに出力
                            message_export(data); 
                            
                            sleep(1000);
                        }

                        // 一覧に画面遷移
                        location.href = 'backCostInit';
                    };
                });
            };

             // falseの処理->アラートでエラーメッセージを表示
            if(data.status == false){

                // アラートボタン設定
                var options = {
                    title: '入力箇所をご確認ください。',
                    text: "メッセージ:" + data.message,
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

                        // エラーメッセージをExcelに出力
                        message_export(data);
                    };
                });
            }

            // ローディング画面終了の処理
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
     * csvエクスポート
     */
    $("#btn_csv_output").on('click', function(e) {

        console.log("csvボタンの処理");

        // ローディング画面
        $("#overlay").fadeIn(300);

        e.preventDefault();

        // フリーワード
        let free_word = $('#free_word').val();
        console.log('free_word：' + free_word);
        
        let bank_id = $('#bank_id').val();
        console.log('bank_id：' + bank_id);

        let cost_account_id = $('#cost_account_id').val();
        console.log('cost_account_id：' + cost_account_id);

        let private_or_bank_id = $('#private_or_bank_id').val();
        console.log('private_or_bank_id：' + private_or_bank_id);

        let cost_flag_id = $('#cost_flag_id').prop('checked');
        console.log('cost_flag_id：' + cost_flag_id);

        let approval_id = $('#approval_id').prop('checked');
        console.log('approval_id：' + approval_id);

        let question_contents = $('#question_contents').prop('checked');
        console.log('question_contents：' + question_contents);

        let start_date = $('#start_date').val();

        let end_date = $('#end_date').val();

        // 5秒遅延
        setTimeout(function(){
            $("#overlay").fadeOut(300);
        },3000);

        // csvDownload
        location.href = "csvCostDownload?free_word=" + free_word + "&bank_id=" + bank_id + "&cost_account_id=" + cost_account_id+ "&private_or_bank_id=" + private_or_bank_id + "&cost_flag_id=" + cost_flag_id + "&approval_id=" + approval_id + "&question_contents=" + question_contents + "&start_date=" + start_date + "&end_date=" + end_date;

    });

    /**
     * 対象口座変更の際、csv形式取得の処理
     */
    $("#modal_bank_id").change(function(e) {

        console.log('対象口座の変更の処理');

        // ローディング画面
        $("#overlay").fadeIn(300);

        // バリデーション
        // formの値数を取得
        let forms = $('.needs-validation');
        console.log('forms.length:' + forms[0].length);

        // 銀行id
        let modal_bank_id = $("#modal_bank_id").val();

        // 送信データインスタンス化
        var sendData = new FormData();
        
        sendData.append('modal_bank_id', modal_bank_id);
        
        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backCostModalBankChange',
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
                console.log("bank_format_type_list:" + data.bank_format_type_list);

                // 値を空にする
                $('#modal_bank_format_type_id').empty();

                // 1列目に空のボックスを作る
                $('#modal_bank_format_type_id').append("<option value=''></option>");

                // roomリストの数だけループする
                for (let i = 0; i < data.bank_format_type_list.length; i++) {
                    
                    let bank_format_type_id = data.bank_format_type_list[i]['bank_format_type_id'];

                    let bank_format_type_name = data.bank_format_type_list[i]['bank_format_type_name'];

                    // roomリストの分だけコンボボックスに値を入れる
                    $('#modal_bank_format_type_id').append("<option value='" + bank_format_type_id + "'>" + bank_format_type_name + "</option>");
                }

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

                // ローディング画面停止
                setTimeout(function(){
                    $("#overlay").fadeOut(300);
                },500);
            }
            
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