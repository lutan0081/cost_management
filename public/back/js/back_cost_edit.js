$(function() {

    /**
     * 承認ボタンを押した場合、操作不可
     */
    let approval_id = $('[name="approval_id"]').prop('checked')
    console.log('approval_id:' + approval_id);

    // 承認ボタンがtrue = 承諾ボタンを操作・入力項目を操作可能
    if(approval_id == true){

        console.log('trueの処理');
        $(".disabled_class").prop('disabled', true);
        $("#approval_id").prop('disabled', true);

    // 承認ボタンがfalse = 承諾ボタンを操作・入力項目を操作不可能
    }else{

        console.log('falseの処理');
        $(".disabled_class").prop('ssdisabled', false);
        $("#approval_id").prop('disabled', false);
    }
    
    /**
     * 一般ユーザは操作不可にする
     */
    let permission_type_id = $("#permission_type_id").val();
    console.log('permission_type_id:' + permission_type_id);

    // OK=質問欄入力・承諾・CSV出力/NG = 編集(質問欄除く)
    if(permission_type_id != 1){
        $(".disabled_class").prop('disabled', true);
    }

    /**
     * エラー時のタブ背景色の設定
     */
    function tabError(error_message_id){

        // error_message_idがある場合の処理
        if(error_message_id !== undefined){

            // error_message_id内にあるclassのtab名を取得
            let tab_class = error_message_id.split(' ')[0];
            console.log('tab_class:' + tab_class);
            
            // 経費概要
            if(tab_class == 'cost-tab'){
                                
                console.log('経費概要');

                $('#nav-cost-tab').addClass('bg_tab_error');
                
            } 

            // 付属書類
            if(tab_class == 'file-tab'){
                                
                console.log('付属書類');

                $('#nav-file-tab').addClass('bg_tab_error');
                
            }

            // 質問
            if(tab_class == 'other-tab'){
                                
                console.log('質問');

                $('#nav-other-tab').addClass('bg_tab_error');
                
            }

        }
    }

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
     * ページネーションセンター
     */
    $(".pagination").addClass("justify-content-center");
    $("#links").show();

    /**
     * 登録
     */
    $("#btn_edit").on('click', function(e) {

        console.log("btn_editクリックされています");

        e.preventDefault();

        $('#nav-cost-tab').removeClass('bg_tab_error');
        $('#nav-file-tab').removeClass('bg_tab_error');
        $('#nav-other-tab').removeClass('bg_tab_error');

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

            // 必須で値が空白の場合の処理
            let val = $(form).val();

            console.log('value:'+ val);

            if (val === '') {

                // エラーメッセージのidを作成
                let f_id_error = f_id + '_error';

                let error_message_id = $('#' + f_id_error).attr('class');
                
                // タブを赤色に変更する(引数:エラーメッセージのid)
                tabError(error_message_id);

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

        /**
         * 経費詳細
         */
        // 照会口座id
        let bank_id = $("#bank_id").val();

        // 金融機関名
        let financial_name = $("#financial_name").val();

        // 支店名
        let financial_branch = $("#financial_branch").val();

        // 摘要
        let financial_summary = $("#financial_summary").val();

        // 出金区分
        let private_or_bank_id = $("#private_or_bank_id").val();

        // 勘定日
        let account_date = $("#account_date").val();

        // 勘定科目
        let cost_account_id = $("#cost_account_id").val();

        // 出金額
        let outgo_fee = $("#outgo_fee").val();

        // 入金額
        let income_fee = $("#income_fee").val();

        // 残高
        let balance_fee = $("#balance_fee").val();

        // 備考
        let cost_memo = $("#cost_memo").val();

        // 経費か否かtrue/false
        let cost_flag_id = $('#cost_flag_id').prop('checked');

        console.log('cost_flag_id:' + cost_flag_id);

        // 1=経費
        if(cost_flag_id == true){
            
            cost_flag_id = 1
        
        // 0=経費無し
        }else{

            cost_flag_id = 0
        }
        
        /**
         * その他
         */
        // 質問
        let question_contents = $("#question_contents").val();

        // 回答
        let answer_contents = $("#answer_contents").val();

        /**
         * 付属書類
         */
        // 画像ファイル取得
        let img_file = $('#img_file').prop('files')[0];
        console.log("img_file:" + img_file);

        // 種別
        let img_type = $("#img_type").val();
        console.log("img_type:" + img_type);

        // 備考
        let img_text = $("#img_text").val();
        console.log("img_text:" + img_text);

        //経費id
        let cost_id = $("#cost_id").val();

        // 送信データインスタンス化
        var sendData = new FormData();

        /**
         * 画像
         */
        sendData.append('img_file', img_file);
        sendData.append('img_type', img_type);
        sendData.append('img_text', img_text);
        
        /**
         * 経費一覧
         */
        sendData.append('bank_id', bank_id);
        sendData.append('financial_name', financial_name);
        sendData.append('financial_branch', financial_branch);
        sendData.append('financial_summary', financial_summary);
        sendData.append('private_or_bank_id', private_or_bank_id);
        sendData.append('account_date', account_date);
        sendData.append('cost_account_id', cost_account_id);
        sendData.append('outgo_fee', outgo_fee);
        sendData.append('income_fee', income_fee);
        sendData.append('balance_fee', balance_fee);
        sendData.append('cost_memo', cost_memo);
        sendData.append('cost_flag_id', cost_flag_id);
        
        /**
         * その他
         */
        sendData.append('question_contents', question_contents);
        sendData.append('answer_contents', answer_contents);

        // id
        sendData.append('cost_id', cost_id);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backCostEditEntry',
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
                        ok: true
                    }
                };
                
                // then() OKを押した時の処理
                swal(options)
                    .then(function(val) {
                    if (val) {

                        location.href = 'backCostInit';
                    };
                });
            };

             // falseの処理->アラートでエラーメッセージを表示
            if(data.status == false){

                console.log("status:" + data.status);
                console.log("messages:" + data.messages);
                console.log("errorkeys:" + data.errkeys);

                // アラートボタン設定
                var options = {
                    title: '入力箇所をご確認ください。',
                    text: '※赤表示の箇所を修正し、再登録をしてください。',
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
                            
                            let error_message_id = $(msg_key).attr('class');

                            tabError(error_message_id);

                            // error_messageテキスト追加
                            $(msg_key).text(data.messages[i]);
                            $(msg_key).show();
                            console.log(msg_key);
                        };
                    };
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
     * 削除(画像)
     */
    $(".btn_img_delete").on('click', function(e) {

        console.log('画像削除の処理');

        e.preventDefault();

        // id取得
        var img_id = $(this).attr("id");
        console.log(img_id);

        // alertの設定
        var options = {
            title: "削除しますか？",
            text: "※一度削除したデータは復元出来ません。",
            icon: 'warning',
            buttons: {
                cancel: "Cancel", // キャンセルボタン
                ok: true
            }
        };
        
        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {

            if(val == null){

                console.log('キャンセルの処理');

                return false;
            }

            if (val == "ok") {

                console.log('OKの処理');

                // 送信用データ
                let sendData = {

                    "img_id": img_id,
                };

                console.log(sendData);

                // ajaxヘッダー
                $.ajaxSetup({

                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                
                });

                $.ajax({

                    type: 'post',
                    url: 'backDeleteEntryImgDetail',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                    console.log('status:' + data.status)

                    if(data.status == true){

                        var options = {
                            title: "削除が完了しました。",
                            icon: 'success',
                            buttons: {
                                ok: true
                            }
                        };
    
                        // then() OKを押した時の処理
                        swal(options)
                            .then(function(val) {
                            if (val) {
                                // 画面更新
                                window.location.reload();
                            }
                        });
                    }

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
     * 削除(全体)
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
                cancel: "Cancel", // キャンセルボタン
                ok: true
            }
        };

        // 値取得
        let cost_id = $("#cost_id").val();
        console.log(cost_id);
        
        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {

            if(val == null){

                console.log('キャンセルの処理');

                return false;
            }
    
            if (val == "ok") {

                console.log('OKの処理');

                // 送信用データ
                let sendData = {

                    "cost_id": cost_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backCostDeleteEntry',
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

                            location.href="backCostInit"
                            
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
     * 承諾ボタンの処理
     */
    $("#approval_id").click(function(e){

        console.log("承認ボタンの処理");

        e.preventDefault();

        // 承認ボタンonの処理
        if($('[name="approval_id"]').prop('checked')){

            console.log('承認ボタンonの処理');
            approval_checked_on();

        // 承認ボタンoffの処理
        }else{

            console.log('承認ボタンoffの処理');
            approval_checked_off();

        }

    });

    /**
     * 承認ボタンonの処理
     */
    function approval_checked_on(){

        console.log('approvalcheck_onの処理');

        // alertの設定
        var options = {
            title: "承諾しますか？",
            text: "一度承諾をすると編集ができません。\n編集が必要な場合、システム管理者にお問合せください。",
            icon: 'warning',
            buttons: {
                Cancel: "Cancel", // キャンセルボタン
                OK: true
            }
        };

        // 値取得
        let cost_id = $("#cost_id").val();
        console.log(cost_id);
        
        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {
            
            // Cancelの処理
            if(val == null){

                console.log('Cancel');

                return false;
            }
    
            // OKの処理
            if (val == "OK") {

                console.log('OK');

                // ローディング画面
                $("#overlay").fadeIn(300);

                $('#nav-cost-tab').removeClass('bg_tab_error');
                $('#nav-file-tab').removeClass('bg_tab_error');
                $('#nav-other-tab').removeClass('bg_tab_error');
        
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
        
                    // 必須で値が空白の場合の処理
                    let val = $(form).val();
        
                    console.log('value:'+ val);
        
                    if (val === '') {
        
                        // エラーメッセージのidを作成
                        let f_id_error = f_id + '_error';
        
                        let error_message_id = $('#' + f_id_error).attr('class');
                        
                        // タブを赤色に変更する(引数:エラーメッセージのid)
                        tabError(error_message_id);
        
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

                /**
                 * 承認ボタンのon,offの判定
                 */
                // true/false
                let approval_flag = true;
                console.log('approval_flag:' + approval_flag);
        
                // 送信用データ
                let sendData = {
                    "cost_id": cost_id,
                    "approval_flag": approval_flag,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({
                    type: 'post',
                    url: 'backCostApprovalEntry',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                    console.log('status:' + data.status)
                    console.log('承諾ボタンの処理')
                    
                    setTimeout(function(){
                        $("#overlay").fadeOut(300);
                    },500);

                    location.reload();
                    // location.href = 'backCostInit';

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
    }

    /**
     * 承認ボタンoffの処理
     */
    function approval_checked_off(){

        console.log('approvalcheck_onの処理');

        // 値取得
        let cost_id = $("#cost_id").val();
        console.log(cost_id);

        // ローディング画面
        $("#overlay").fadeIn(300);

        $('#nav-cost-tab').removeClass('bg_tab_error');
        $('#nav-file-tab').removeClass('bg_tab_error');
        $('#nav-other-tab').removeClass('bg_tab_error');

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

            // 必須で値が空白の場合の処理
            let val = $(form).val();

            console.log('value:'+ val);

            if (val === '') {

                // エラーメッセージのidを作成
                let f_id_error = f_id + '_error';

                let error_message_id = $('#' + f_id_error).attr('class');
                
                // タブを赤色に変更する(引数:エラーメッセージのid)
                tabError(error_message_id);

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

        /**
         * 承認ボタンのon,offの判定
         */
        // true/false
        let approval_flag = false;
        console.log('approval_flag:' + approval_flag);

        // 送信用データ
        let sendData = {
            
            "cost_id": cost_id,
            "approval_flag": approval_flag,
        };

        console.log(sendData);

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backCostApprovalEntry',
            dataType: 'json',
            data: sendData,
        
        // 接続処理
        }).done(function(data) {

            console.log('status:' + data.status)
            console.log('承諾ボタンの処理')
            
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            location.reload();
            // location.href = 'backCostInit';

        // ajax接続失敗の時の処理
        }).fail(function(jqXHR, textStatus, errorThrown) {

            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        
    }

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
        // 経費id
        let cost_id = $("#cost_id").val();
        console.log('cost_id:' + cost_id);

        // CSVformatid
        let modal_credit_card_format_type_id = $("#modal_credit_card_format_type_id").val();
        console.log('modal_credit_card_format_type_id:' + modal_credit_card_format_type_id);

        // ファイル取得
        let modal_credit_card_file = $('#modal_credit_card_file').prop('files')[0];
        console.log("modal_credit_card_file:" + modal_credit_card_file);

        /**
         * バリデーション
         */
        // validationフラグ初期値
        let v_check = true;

        /**
         * v_checkフラグがfalseの場合、下段のバリデーションに引っ掛かり
         * modalFormにwas-validatedを付与、エラー文字の表示
         */
        if(modal_credit_card_format_type_id == ''){

            v_check = false;
        }

        if(modal_credit_card_file == ''){

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
        
        sendData.append('cost_id', cost_id);
        sendData.append('modal_credit_card_format_type_id', modal_credit_card_format_type_id);
        sendData.append('modal_credit_card_file', modal_credit_card_file);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'csvCreditCardImport',
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
                        // location.href = 'backCostInit';

                        location.reload();
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
     * CSV詳細画面
     */
    $(".click_class").on('dblclick', function(e) {
        
        console.log("クレジット詳細画面の処理");

        // ローディング画面
        $("#overlay").fadeIn(300);

        // tdのidを配列に分解
        var id_info = $(this).attr("id");
        cost_id = id_info.split('_')[1];
        console.log(cost_id);

        // 送信データインスタンス化
        var sendData = new FormData();

        // 送信データ
        sendData.append('cost_id', cost_id);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backCostCreditCardDetailInit',
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



            };

             // falseの処理->アラートでエラーメッセージを表示
            if(data.status == false){

                // アラートボタン設定
                var options = {
                    title: 'データ取得に失敗しました。',
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

});