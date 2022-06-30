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
        $(".disabled_class").prop('disabled', false);
        $("#approval_id").prop('disabled', false);
    }

    /**
     * 登録
     */
    $("#btn_edit").on('click', function(e) {

        console.log("btn_editクリックされています");

        e.preventDefault();

        $('#nav-profit-tab').removeClass('bg_tab_error');
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

            // formの値を取得->クラス付与
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
        // 照会口座id
        let bank_id = $("#bank_id").val();

        // 売上担当id
        let profit_person_id = $("#profit_person_id").val();

        // 勘定科目
        let profit_account_id = $("#profit_account_id").val();

        // 勘定日
        let profit_account_date = $("#profit_account_date").val();

        // 利益額
        let profit_fee = $("#profit_fee").val();

        // 取引先
        let customer_name = $("#customer_name").val();

        // 物件名
        let real_estate_id = $("#real_estate_id").val();

        // 号室
        let room_id = $("#room_id").val();

        // 備考
        let profit_memo = $("#profit_memo").val();
        
        // id
        let profit_id = $("#profit_id").val();

        /**
         * その他
         */
        // 質問
        let question_contents = $("#question_contents").val();
        console.log(question_contents);

        // 回答
        let answer_contents = $("#answer_contents").val();
        console.log(answer_contents);

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
        
        // 送信データインスタンス化
        var sendData = new FormData();
        
        
        sendData.append('bank_id', bank_id);
        sendData.append('profit_person_id', profit_person_id);
        sendData.append('profit_account_id', profit_account_id);
        sendData.append('profit_account_date', profit_account_date);
        sendData.append('profit_fee', profit_fee);
        sendData.append('real_estate_id', real_estate_id);
        sendData.append('room_id', room_id);
        sendData.append('profit_memo', profit_memo);
        sendData.append('profit_id', profit_id);
        sendData.append('customer_name', customer_name);

        /**
         * 画像
         */
        sendData.append('img_file', img_file);
        sendData.append('img_type', img_type);
        sendData.append('img_text', img_text);

        /**
         * その他
         */
        sendData.append('question_contents', question_contents);
        sendData.append('answer_contents', answer_contents);
        
        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backProfitEditEntry',
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
                    if (val == 'OK' || val == null) {
                        location.href = 'backProfitInit';
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
                            
                            let error_message_id = $(msg_key).attr('class');

                            tabError(error_message_id);

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
        let profit_id = $("#profit_id").val();
        console.log(profit_id);
        
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

                    "profit_id": profit_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backProfitDeleteEntry',
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

                            location.href="backProfitInit"
                            
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
     * 不動産コンボボックス変更の際、号室取得の処理
     */
    $("#real_estate_id").change(function(e) {

        console.log('不動産変更の処理');

        // ローディング画面
        $("#overlay").fadeIn(300);

        // バリデーション
        // formの値数を取得
        let forms = $('.needs-validation');
        console.log('forms.length:' + forms[0].length);

        // 不動産id
        let real_estate_id = $("#real_estate_id").val();

        // 送信データインスタンス化
        var sendData = new FormData();
        
        sendData.append('real_estate_id', real_estate_id);
        
        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backRealEstateChangeInit',
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
                console.log("room_list:" + data.room_list);

                // 値を空にする
                $('#room_id').empty();

                // 1列目に空のボックスを作る
                $('#room_id').append("<option value=''></option>");

                // roomリストの数だけループする
                for (let i = 0; i < data.room_list.length; i++) {
                    
                    let room_id = data.room_list[i]['room_id'];
                    console.log(room_id);

                    let room_name = data.room_list[i]['room_name'];
                    console.log(room_name);

                    // roomリストの分だけコンボボックスに値を入れる
                    $('#room_id').append("<option value='" + room_id + "'>" + room_name + "</option>");

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
                OK: true
            }
        };
        
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

                    "img_id": img_id,
                };

                console.log(sendData);

                // ajaxヘッダー
                $.ajaxSetup({

                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                
                });

                $.ajax({

                    type: 'post',
                    url: 'backProfitDeleteEntryImgDetail',
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
        let profit_id = $("#profit_id").val();
        console.log('profit_id:' + profit_id);
        
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

                $('#nav-profit-tab').removeClass('bg_tab_error');
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
                    "profit_id": profit_id,
                    "approval_flag": approval_flag,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({
                    type: 'post',
                    url: 'backProfitApprovalEntry',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                    console.log('status:' + data.status)
                    console.log('承諾ボタンの処理')
                    
                    setTimeout(function(){
                        $("#overlay").fadeOut(300);
                    },500);

                    // location.reload();
                    location.href = 'backProfitInit';

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
        let profit_id = $("#profit_id").val();
        console.log(profit_id);

        // ローディング画面
        $("#overlay").fadeIn(300);

        $('#nav-profit-tab').removeClass('bg_tab_error');
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
            
            "profit_id": profit_id,
            "approval_flag": approval_flag,
        };

        console.log(sendData);

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backProfitApprovalEntry',
            dataType: 'json',
            data: sendData,
        
        // 接続処理
        }).done(function(data) {

            console.log('status:' + data.status)
            console.log('承諾の処理')
            
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
     * エラー時のタブ背景色の設定
     */
    function tabError(error_message_id){

        // error_message_idがある場合の処理
        if(error_message_id !== undefined){

            // error_message_id内にあるclassのtab名を取得
            let tab_class = error_message_id.split(' ')[0];
            console.log('tab_class:' + tab_class);
            
            // 経費概要
            if(tab_class == 'profit-tab'){
                                
                console.log('売上概要');

                $('#nav-profit-tab').addClass('bg_tab_error');
                
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
});