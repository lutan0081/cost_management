$(function() {

    let guarantor_flag = $('#guarantor_flag').val();

    // 1 = 連帯保証人有以外の場合、連帯保証人箇所を編集不可にする
    if(guarantor_flag !== 1){

        console.log('guarantor_flag:' + guarantor_flag);

        $('.guarantor_disable_flag').prop('disabled', true);
        
    }

    /**
     * 住所検索
     */
    $(".btn_zip").on('click', function(e) {
        console.log("btn_zipクリックされています");
        
        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);　

        // 住所検索ボタンのid取得
        var post_number_id = $(this).attr('id');
        console.log(post_number_id);
        
        // 郵便番号初期値:空白
        let post_number = '';

        // 仲介業者
        if(post_number_id == 'real_estate_agent-btn-zip'){
            post_number = $('#post_number').val();
            console.log('不動産の検索ボタンだよ');
        }

        // 賃借人
        if(post_number_id == 'contract-btn-zip'){
            post_number = $('#entry_contract_post_number').val();
            console.log('賃借人の検索ボタンだよ');
        }

        // 賃借人(勤務先)
        if(post_number_id == 'contract_business-btn-zip'){
            post_number = $('#entry_contract_business_post_number').val();
            console.log('賃借人の勤務先の郵便番号の検索ボタンだよ');
        }

        // 同居人
        if(post_number_id == 'housemates-btn-zip'){
            post_number = $('#housemate_post_number').val();
            console.log('同居人の検索ボタンだよ');
        }

        // 緊急連絡先
        if(post_number_id == 'emergency-btn-zip'){
            post_number = $('#emergency_post_number').val();
            console.log('緊急連絡先の検索ボタンだよ');
        }

        // 連帯保証人
        if(post_number_id == 'guarantors-btn-zip'){
            post_number = $('#guarantor_post_number').val();
            console.log('連帯保証人の検索ボタンだよ');
        }

        // 連帯保証人(勤務先)
        if(post_number_id == 'guarantor_business-btn-zip'){
            post_number = $('#guarantor_business_post_number').val();
            console.log('連帯保証人勤務先の検索ボタンだよ');
        }

        // 郵便番号が空白の場合のプログラム終了
        if(post_number==""){

            // ローディング画面停止
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            return false;
        }

        // url指定
        let zipUrl = "https://zipcloud.ibsnet.co.jp/api/search?zipcode="+ post_number;

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'get',
            url: zipUrl,
            // 自身のサイトの場合json,他のサイトの場合jsonp
            dataType: 'jsonp'
        
        // 接続が出来た場合の処理
        }).done(function(data) {
            console.log(data);
            
            // ローディング画面終了の処理
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

            //取得結果がNGの場合、メッセージを追加する
            if(data.results == null){
                console.log(data.message);
            }

            // 取得結果がOKの場合、ifで入力データをもとに分岐し住所を設定する
            if(data.results !== null){

                // 不動産業者
                if(post_number_id == 'real_estate_agent-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#address').val(address);
                    console.log('不動産の検索ボタン');
                }

                // 契約者
                if(post_number_id == 'contract-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#entry_contract_address').val(address);
                    console.log('賃貸借契約の検索ボタン');
                }

                // 契約者(勤務先)
                if(post_number_id == 'contract_business-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#entry_contract_business_address').val(address);
                    console.log('賃貸借契約郵便番号の検索ボタン');
                }

                // 同居人
                if(post_number_id == 'housemates-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#housemate_address').val(address);
                    console.log('同居人の検索ボタン');
                }

                // 緊急連絡先
                if(post_number_id == 'emergency-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#emergency_address').val(address);
                    console.log('緊急連絡先の検索ボタン');
                }

                // 連帯保証人
                if(post_number_id == 'guarantors-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#guarantor_address').val(address);
                    console.log('連帯保証人の検索ボタン');
                }

                // 連帯保証人(勤務先)
                if(post_number_id == 'guarantor_business-btn-zip'){
                    let address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                    console.log(address);
                    $('#guarantor_business_address').val(address);
                    console.log('連帯保証人の検索ボタン');
                }

            }
        // ajax接続が出来なかった場合の処理
        }).fail(function(jqXHR, textStatus, errorThrown) {

            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);

        });
        
    });

    /**
     * 登録
     */
    $("#btn_edit").on('click', function(e) {

        console.log("btn_editクリックされています");

        e.preventDefault();

        $('#nav-user-tab').removeClass('bg_tab_error');
        $('#nav-trade-tab').removeClass('bg_tab_error');
        $('#nav-contract-tab').removeClass('bg_tab_error');
        $('#nav-housemate-tab').removeClass('bg_tab_error');
        $('#nav-emergency-tab').removeClass('bg_tab_error');
        $('#nav-guarantor-tab').removeClass('bg_tab_error');
        $('#nav-document-tab').removeClass('bg_tab_error');

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
         * 不動産業者
         */
        // 進捗状況
        let contract_progress_id = $("#contract_progress_id").val();
        console.log('contract_progress_id:' + contract_progress_id);

        // 個人又は法人
        let private_or_company_id = $("#private_or_company_id").val();
        console.log('private_or_company_id:' + private_or_company_id);

        // 仲介業者名
        let broker_company_name = $("#broker_company_name").val();

        // 仲介業者Tel
        let broker_tel = $("#broker_tel").val();

        // 仲介業者mail
        let broker_mail = $("#broker_mail").val();

        // 担当者
        let broker_name = $("#broker_name").val();

        // 申込区分id
        let application_type_id = $("#application_type_id").val();

        // 物件用途
        let application_use_id = $("#application_use_id").val();

        // 入居予定
        let contract_start_date = $("#contract_start_date").val();

        // 物件名
        let real_estate_name = $("#real_estate_name").val();

        // 物件カナ
        let real_estate_ruby = $("#real_estate_ruby").val();

        // 号室
        let room_name = $("#room_name").val();

        // 郵便番号
        let post_number = $("#post_number").val();

        // 住所
        let address = $("#address").val();

        // ペット飼育数
        let pet_bleed = $("#pet_bleed").val();

        // 飼育頭数
        let pet_kind = $("#pet_kind").val();

        // 駐車台数
        let car_number_number = $("#car_number_number").val();

        // 駐輪台数
        let bicycle_number = $("#bicycle_number").val();

        // 保証金
        let deposit_fee = $("#deposit_fee").val();

        // 解約引き
        let refund_fee = $("#refund_fee").val();
        
        // 敷金
        let security_fee = $("#security_fee").val();

        // 礼金
        let key_fee = $("#key_fee").val();

        // 家賃
        let rent_fee = $("#rent_fee").val()

        // 共益費
        let service_fee = $("#service_fee").val();

        // 水道代
        let water_fee = $("#water_fee").val();

        // その他
        let ohter_fee = $("#ohter_fee").val();

        // 総賃料
        let total_fee = $("#total_fee").val();

        // 連帯保証人有無
        let guarantor_flag = $("#guarantor_flag").val();

        /**
         * 契約者名
         */
        // 契約者名
        let entry_contract_name = $("#entry_contract_name").val();

        // 契約者カナ
        let entry_contract_ruby = $("#entry_contract_ruby").val();

        // 郵便番号
        let entry_contract_post_number = $("#entry_contract_post_number").val();

        // 住所
        let entry_contract_address = $("#entry_contract_address").val();

        // 性別
        let entry_contract_sex_id = $("#entry_contract_sex_id").val();

        // 生年月日
        let entry_contract_birthday = $("#entry_contract_birthday").val();

        // 年齢
        let entry_contract_age = $("#entry_contract_age").val();

        // 自宅電話番号
        let entry_contract_home_tel = $("#entry_contract_home_tel").val();

        // 携帯電話番号
        let entry_contract_mobile_tel = $("#entry_contract_mobile_tel").val();

        // 勤務先名
        let entry_contract_business_name = $("#entry_contract_business_name").val();
        
        // 勤務先カナ
        let entry_contract_business_ruby = $("#entry_contract_business_ruby").val();
        
        // 勤務先郵便番号
        let entry_contract_business_post_number = $("#entry_contract_business_post_number").val();

        // 勤務先住所
        let entry_contract_business_address = $("#entry_contract_business_address").val();

        // 勤務先電話番号
        let entry_contract_business_tel = $("#entry_contract_business_tel").val();

        // 業種
        let entry_contract_business_type = $("#entry_contract_business_type").val();
        
        // 職種
        let entry_contract_business_line = $("#entry_contract_business_line").val();
        
        // 雇用形態
        let entry_contract_business_status = $("#entry_contract_business_status").val();
        
        // 勤続年数
        let entry_contract_business_year = $("#entry_contract_business_year").val();

        // 年収
        let entry_contract_income = $("#entry_contract_income").val();
        
        // 健康保険
        let entry_contract_insurance_type_id = $("#entry_contract_insurance_type_id").val();

        /**
         * 同居人
         */
        // 同居人名
        let housemate_name = $("#housemate_name").val();

        // 同居人カナ
        let housemate_ruby = $("#housemate_ruby").val();

        // 性別
        let housemate_sex_id = $("#housemate_sex_id").val();

        // 続柄
        let housemate_link_id = $("#housemate_link_id").val()
        
        // 生年月日
        let housemate_birthday = $("#housemate_birthday").val()
        
        // 年齢
        let housemate_age = $("#housemate_age").val()
        
        // 自宅電話番号
        let housemate_home_tel = $("#housemate_home_tel").val()
        
        // 携帯電話
        let housemate_mobile_tel = $("#housemate_mobile_tel").val()

        /**
         * 緊急連絡先
         */
        // 緊急連絡先名
        let emergency_name = $("#emergency_name").val();

        // 緊急連絡先カナ
        let emergency_ruby = $("#emergency_ruby").val();

        // 性別
        let emergency_sex_id = $("#emergency_sex_id").val();

        // 続柄
        let emergency_link_id = $("#emergency_link_id").val();
        
        // 生年月日
        let emergency_birthday = $("#emergency_birthday").val();
        
        // 年齢
        let emergency_age = $("#emergency_age").val();
        
        // 郵便番号
        let emergency_post_number = $("#emergency_post_number").val();
        
        // 住所
        let emergency_address = $("#emergency_address").val();
        
        // 自宅電話番号
        let emergency_home_tel = $("#emergency_home_tel").val();
        
        // 携帯電話番号
        let emergency_mobile_tel = $("#emergency_mobile_tel").val();

        /**
         * 連帯保証人
         */
        // 連帯保証人名
        let guarantor_name = $("#guarantor_name").val();

        // 連帯保証人カナ
        let guarantor_ruby = $("#guarantor_ruby").val();

        // 郵便番号
        let guarantor_post_number = $("#guarantor_post_number").val();

        // 住所
        let guarantor_address = $("#guarantor_address").val();

        // 性別
        let guarantor_sex_id = $("#guarantor_sex_id").val();

        // 生年月日
        let guarantor_birthday = $("#guarantor_birthday").val();

        // 年齢
        let guarantor_age = $("#guarantor_age").val();

        // 続柄
        let guarantor_link_id = $("#guarantor_link_id").val();

        // 自宅電話番号
        let guarantor_home_tel = $("#guarantor_home_tel").val();

        // 携帯電話番号
        let guarantor_mobile_tel = $("#guarantor_mobile_tel").val();

        // 勤務先名
        let guarantor_business_name = $("#guarantor_business_name").val();

        // 勤務先フリガナ
        let guarantor_business_ruby = $("#guarantor_business_ruby").val();

        // 勤務先郵便番号
        let guarantor_business_post_number = $("#guarantor_business_post_number").val();

        // 勤務先住所
        let guarantor_business_address = $("#guarantor_business_address").val();

        // 勤務先電話番号
        let guarantor_business_tel = $("#guarantor_business_tel").val();
        
        // 業種
        let guarantor_business_type = $("#guarantor_business_type").val();
        
        // 職種
        let guarantor_business_line = $("#guarantor_business_line").val();
        
        // 雇用形態
        let guarantor_business_status = $("#guarantor_business_status").val();
        
        // 勤続年数
        let guarantor_business_years = $("#guarantor_business_years").val();
        
        // 年収
        let guarantor_income = $("#guarantor_income").val();
        
        // 健康保険
        let guarantor_insurance_type_id = $("#guarantor_insurance_type_id").val();

        // 画像ファイル取得
        let img_file = $('#img_file').prop('files')[0];
        console.log("img_file:" + img_file);

        // 種別
        let img_type = $("#img_type").val();
        console.log("img_type:" + img_type);

        // 備考
        let img_text = $("#img_text").val();
        console.log("img_text:" + img_text);

        //業者id
        let application_id = $("#application_id").val();

        //同居人id
        let housemate_id = $("#housemate_id").val();

        // 同居人追加フラグ(追加=true 追加無=false)
        let housemate_add_flag = $('#housemate_add_flag').val();

        // 送信データインスタンス化
        var sendData = new FormData();

        /**
         * 画像
         */
        sendData.append('img_file', img_file);
        sendData.append('img_type', img_type);
        sendData.append('img_text', img_text);
        
        /**
         * 不動産業者
         */
        sendData.append('contract_progress_id', contract_progress_id);
        sendData.append('broker_company_name', broker_company_name);
        sendData.append('broker_tel', broker_tel);
        sendData.append('broker_mail', broker_mail);
        sendData.append('broker_name', broker_name);
        sendData.append('application_type_id', application_type_id);
        sendData.append('application_use_id', application_use_id);
        sendData.append('contract_start_date', contract_start_date);
        sendData.append('real_estate_name', real_estate_name);
        sendData.append('real_estate_ruby', real_estate_ruby);
        sendData.append('room_name', room_name);
        sendData.append('post_number', post_number);
        sendData.append('address', address);
        sendData.append('pet_bleed', pet_bleed);
        sendData.append('pet_kind', pet_kind);
        sendData.append('bicycle_number', bicycle_number);
        sendData.append('car_number_number', car_number_number);
        sendData.append('deposit_fee', deposit_fee);
        sendData.append('refund_fee', refund_fee);
        sendData.append('security_fee', security_fee);
        sendData.append('key_fee', key_fee);
        sendData.append('rent_fee', rent_fee);
        sendData.append('service_fee', service_fee);
        sendData.append('water_fee', water_fee);
        sendData.append('ohter_fee', ohter_fee);
        sendData.append('total_fee', total_fee);
        sendData.append('private_or_company_id', private_or_company_id);
        sendData.append('guarantor_flag', guarantor_flag);
        
        /**
         * 賃借人
         */
        sendData.append('entry_contract_name', entry_contract_name);
        sendData.append('entry_contract_ruby', entry_contract_ruby);
        sendData.append('entry_contract_post_number', entry_contract_post_number);
        sendData.append('entry_contract_address', entry_contract_address);
        sendData.append('entry_contract_sex_id', entry_contract_sex_id);
        sendData.append('entry_contract_birthday', entry_contract_birthday);
        sendData.append('entry_contract_age', entry_contract_age);
        sendData.append('entry_contract_home_tel', entry_contract_home_tel);
        sendData.append('entry_contract_mobile_tel', entry_contract_mobile_tel);
        sendData.append('entry_contract_business_name', entry_contract_business_name);
        sendData.append('entry_contract_business_ruby', entry_contract_business_ruby);
        sendData.append('entry_contract_business_post_number', entry_contract_business_post_number);
        sendData.append('entry_contract_business_address', entry_contract_business_address);
        sendData.append('entry_contract_business_tel', entry_contract_business_tel);
        sendData.append('entry_contract_business_type', entry_contract_business_type);
        sendData.append('entry_contract_business_line', entry_contract_business_line);
        sendData.append('entry_contract_business_status', entry_contract_business_status);
        sendData.append('entry_contract_business_year', entry_contract_business_year);
        sendData.append('entry_contract_income', entry_contract_income);
        sendData.append('entry_contract_insurance_type_id', entry_contract_insurance_type_id);

        /**
         * 同居人
         */
        sendData.append('housemate_name', housemate_name);
        sendData.append('housemate_ruby', housemate_ruby);
        sendData.append('housemate_sex_id', housemate_sex_id);
        sendData.append('housemate_link_id', housemate_link_id);
        sendData.append('housemate_age', housemate_age);
        sendData.append('housemate_birthday', housemate_birthday);
        sendData.append('housemate_home_tel', housemate_home_tel);
        sendData.append('housemate_mobile_tel', housemate_mobile_tel);

        /**
         * 緊急連絡先
         */
        // sendData.append('emergency_contact_id', emergency_contact_id);
        sendData.append('emergency_name', emergency_name);
        sendData.append('emergency_ruby', emergency_ruby);
        sendData.append('emergency_sex_id', emergency_sex_id);
        sendData.append('emergency_link_id', emergency_link_id);
        sendData.append('emergency_birthday', emergency_birthday);
        sendData.append('emergency_age', emergency_age);
        sendData.append('emergency_post_number', emergency_post_number);
        sendData.append('emergency_address', emergency_address);
        sendData.append('emergency_home_tel', emergency_home_tel);
        sendData.append('emergency_mobile_tel', emergency_mobile_tel);
        
        /**
         * 連帯保証人
         */
        // sendData.append('guarantor_contracts_id', guarantor_contracts_id);
        sendData.append('guarantor_name', guarantor_name);
        sendData.append('guarantor_ruby', guarantor_ruby);
        sendData.append('guarantor_post_number', guarantor_post_number);
        sendData.append('guarantor_address', guarantor_address);
        sendData.append('guarantor_sex_id', guarantor_sex_id);
        sendData.append('guarantor_birthday', guarantor_birthday);
        sendData.append('guarantor_age', guarantor_age);
        sendData.append('guarantor_link_id', guarantor_link_id);
        sendData.append('guarantor_home_tel', guarantor_home_tel);
        sendData.append('guarantor_mobile_tel', guarantor_mobile_tel);
        sendData.append('guarantor_business_name', guarantor_business_name);
        sendData.append('guarantor_business_ruby', guarantor_business_ruby);
        sendData.append('guarantor_business_post_number', guarantor_business_post_number);
        sendData.append('guarantor_business_address', guarantor_business_address);
        sendData.append('guarantor_business_tel', guarantor_business_tel);
        sendData.append('guarantor_business_type', guarantor_business_type);
        sendData.append('guarantor_business_line', guarantor_business_line);
        sendData.append('guarantor_business_status', guarantor_business_status);
        sendData.append('guarantor_business_years', guarantor_business_years);
        sendData.append('guarantor_income', guarantor_income);
        sendData.append('guarantor_insurance_type_id', guarantor_insurance_type_id);

        // 不動産業者id
        sendData.append('application_id', application_id);

        // 同居人id
        sendData.append('housemate_id', housemate_id);

        // 同居人追加フラグ(追加=true 追加無=false)
        sendData.append('housemate_add_flag', housemate_add_flag);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backAppEditEntry',
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

                        location.href = 'backAppInit';
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

                            // ローディング画面停止
                            setTimeout(function(){
                                $("#overlay").fadeOut(300);
                            },500);
                        };

                        return false;
                    };
                });
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
     * エラー時のタブ背景色の設定
     */
    function tabError(error_message_id){

        // error_message_idがある場合の処理
        if(error_message_id !== undefined){

            // error_message_id内にあるclassのtab名を取得
            let tab_class = error_message_id.split(' ')[0];
            console.log('tab_class:' + tab_class);
            
            // アカウント
            if(tab_class == 'user-tab'){
                                
                console.log('ユーザタブの処理');

                $('#nav-user-tab').addClass('bg_tab_error');
                
            } 

            // 募集要項
            if(tab_class == 'trade-tab'){
                                
                console.log('募集要項の処理');

                $('#nav-trade-tab').addClass('bg_tab_error');
                
            }

            // 契約者
            if(tab_class == 'contract-tab'){
                                
                console.log('契約者の処理');

                $('#nav-contract-tab').addClass('bg_tab_error');
                
            }

            // 同居人
            if(tab_class == 'housemate-tab'){
                                
                console.log('同居人の処理');

                $('#nav-housemate-tab').addClass('bg_tab_error');
                
            }

            // 緊急連絡先
            if(tab_class == 'emergency-tab'){
                                
                console.log('緊急連絡先の処理');

                $('#nav-emergency-tab').addClass('bg_tab_error');
                
            }

            // 連帯保証人
            if(tab_class == 'guarantor-tab'){
                                
                console.log('連帯保証人の処理');

                $('#nav-guarantor-tab').addClass('bg_tab_error');
                
            }

            // 画像
            if(tab_class == 'document-tab'){
                                
                console.log('画像の処理');

                $('#nav-document-tab').addClass('bg_tab_error');
                
            }
        }
    }

    /**
     * ページネーションセンター
     */
    $(".pagination").addClass("justify-content-center");
    $("#links").show();

    /**
     * 同居人ダブルクリックの処理
     */
    $(".click_class").on('dblclick', function(e) {
        console.log("ダブルクリックの処理.");

        e.preventDefault();

		// ローディング画面
		$("#overlay").fadeIn(300);

        // id取得
        var housemate_id = $(this).attr("id");
        console.log(housemate_id);

        // 送信データ
        let sendData = {
			"housemate_id": housemate_id,
        };

		console.log(sendData);

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'post',
            url: 'backAppHouseMateInit',
            dataType: 'json',
            data: sendData,
        
        // 接続処理
        }).done(function(data) {

            console.log("data:" + data.list[0]['housemate_name']);
            
            /**
             * 値代入
             */
            // 同居人id
            $("#housemate_id").val(data.list[0]['housemate_id']);

            // 同居人名
            $("#housemate_name").val(data.list[0]['housemate_name']);
			$('#housemate_name').prop('disabled', false);
            
            // 同居人カナ
            $("#housemate_ruby").val(data.list[0]['housemate_ruby']);
            $('#housemate_ruby').prop('disabled', false);

            // 性別
            $("#housemate_sex_id").val(data.list[0]['housemate_sex_id']);
            $('#housemate_sex_id').prop('disabled', false);

            // 続柄
            $("#housemate_link_id").val(data.list[0]['housemate_link_id']);
            $('#housemate_link_id').prop('disabled', false);

            // 生年月日
            $("#housemate_birthday").val(data.list[0]['housemate_birthday']);
            $('#housemate_birthday').prop('disabled', false);

            // 年齢
            $("#housemate_age").val(data.list[0]['housemate_age']);
            $('#housemate_age').prop('disabled', false);

            // 自宅Tel
            $("#housemate_home_tel").val(data.list[0]['housemate_home_tel']);
            $('#housemate_home_tel').prop('disabled', false);

            // 携帯Tel
            $("#housemate_mobile_tel").val(data.list[0]['housemate_mobile_tel']);
            $('#housemate_mobile_tel').prop('disabled', false);
            
            $('#housemate_add_flag').val(true);

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
     * 総額賃料の計算
     */
    $('.fee_text').keyup(function() {
        console.log("fee_textを押されました");

        // 家賃
        let rent_fee = $('#rent_fee').val();

        // 共益費
        let service_fee = $('#service_fee').val();
        
        // 水道代
        let water_fee = $('#water_fee').val();

        // その他
        let ohter_fee = $('#ohter_fee').val();

        // 総賃料
        let total_fee = Number(rent_fee) + Number(service_fee) + Number(water_fee) + Number(ohter_fee);
        console.log(total_fee);

        $("#total_fee").val(total_fee);
    });

    /**
     * 同居人削除の処理
     */
    $(".hm_btn_delete").on('click', function(e) {

        console.log('同居人の削除処理');

        e.preventDefault();

        // id取得
        var housemate_id = $(this).attr("id");
        console.log(housemate_id);

        // alertの設定
        var options = {
            title: '削除しますか？',
            text: "※一度削除したデータは復元出来ません。",
            icon: "warning",
            buttons: {
                cancel: "Cancel", // キャンセルボタン
                ok: true
            }
        };
        
        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {
            
            if(val == null){

                console.log("キャンセルの処理");

                return false;
            }

            if (val == "ok") {

                console.log('OKの処理');
                
                // ローディング画面
                $("#overlay").fadeIn(300);

                // 送信用データ
                let sendData = {

                    "housemate_id": housemate_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backAppHouseMateDeleteEntry',
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
                            // 画面更新
                            window.location.reload();
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
     * 同居人追加の処理
     * text、housemate_idを初期化
     */
    $("#housemate_add").on('click', function(e) {
        
        console.log('同居人追加の処理');

        $('#housemate_name').val('');
        $('#housemate_name').prop('disabled', false);

        $('#housemate_ruby').val('');
        $('#housemate_ruby').prop('disabled', false);

        $('#housemate_sex_id').val('');
        $('#housemate_sex_id').prop('disabled', false);

        $('#housemate_link_id').val('');
        $('#housemate_link_id').prop('disabled', false);

        $('#housemate_birthday').val('');
        $('#housemate_birthday').prop('disabled', false);

        $('#housemate_age').val('');
        $('#housemate_age').prop('disabled', false);

        $('#housemate_home_tel').val('');
        $('#housemate_home_tel').prop('disabled', false);

        $('#housemate_mobile_tel').val('');
        $('#housemate_mobile_tel').prop('disabled', false);

        $('#housemate_id').val('');

        // 同居人追加フラグ(追加=true 追加無=false)
        $('#housemate_add_flag').val(true);
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
     * 削除(申込)
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
        let application_id = $("#application_id").val();
        console.log(application_id);
        
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

                    "application_id": application_id,
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backAppDeleteEntry',
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

                            location.href="backAppInit"
                            
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
     * 一時登録
     */
    $("#btn_temporarily").on('click', function(e) {

        console.log("btn_editクリックされています");

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);

        /**
         * 一時登録フラグ
         */
        let temporarily_flag = true;

        /**
         * 不動産業者
         */
        // 進捗状況
        let contract_progress_id = $("#contract_progress_id").val();
        console.log('contract_progress_id:' + contract_progress_id);

        // 仲介業者名
        let broker_company_name = $("#broker_company_name").val();

        // 仲介業者Tel
        let broker_tel = $("#broker_tel").val();

        // 仲介業者mail
        let broker_mail = $("#broker_mail").val();

        // 担当者
        let broker_name = $("#broker_name").val();

        // 申込区分id
        let application_type_id = $("#application_type_id").val();

        // 物件用途
        let application_use_id = $("#application_use_id").val();

        // 入居予定
        let contract_start_date = $("#contract_start_date").val();

        // 物件名
        let real_estate_name = $("#real_estate_name").val();

        // 物件カナ
        let real_estate_ruby = $("#real_estate_ruby").val();

        // 号室
        let room_name = $("#room_name").val();

        // 郵便番号
        let post_number = $("#post_number").val();

        // 住所
        let address = $("#address").val();

        // ペット飼育数
        let pet_bleed = $("#pet_bleed").val();

        // 飼育頭数
        let pet_kind = $("#pet_kind").val();

        // 駐車台数
        let car_number_number = $("#car_number_number").val();

        // 駐輪台数
        let bicycle_number = $("#bicycle_number").val();

        // 保証金
        let deposit_fee = $("#deposit_fee").val();

        // 解約引き
        let refund_fee = $("#refund_fee").val();
        
        // 敷金
        let security_fee = $("#security_fee").val();

        // 礼金
        let key_fee = $("#key_fee").val();

        // 家賃
        let rent_fee = $("#rent_fee").val()

        // 共益費
        let service_fee = $("#service_fee").val();

        // 水道代
        let water_fee = $("#water_fee").val();

        // その他
        let ohter_fee = $("#ohter_fee").val();

        // 総賃料
        let total_fee = $("#total_fee").val();

        // 連帯保証人有無
        let guarantor_flag = $("#guarantor_flag").val();

        /**
         * 契約者名
         */
        // 契約者名
        let entry_contract_name = $("#entry_contract_name").val();

        // 契約者カナ
        let entry_contract_ruby = $("#entry_contract_ruby").val();

        // 郵便番号
        let entry_contract_post_number = $("#entry_contract_post_number").val();

        // 住所
        let entry_contract_address = $("#entry_contract_address").val();

        // 性別
        let entry_contract_sex_id = $("#entry_contract_sex_id").val();

        // 生年月日
        let entry_contract_birthday = $("#entry_contract_birthday").val();

        // 年齢
        let entry_contract_age = $("#entry_contract_age").val();

        // 自宅電話番号
        let entry_contract_home_tel = $("#entry_contract_home_tel").val();

        // 携帯電話番号
        let entry_contract_mobile_tel = $("#entry_contract_mobile_tel").val();

        // 勤務先名
        let entry_contract_business_name = $("#entry_contract_business_name").val();
        
        // 勤務先カナ
        let entry_contract_business_ruby = $("#entry_contract_business_ruby").val();
        
        // 勤務先郵便番号
        let entry_contract_business_post_number = $("#entry_contract_business_post_number").val();

        // 勤務先住所
        let entry_contract_business_address = $("#entry_contract_business_address").val();

        // 勤務先電話番号
        let entry_contract_business_tel = $("#entry_contract_business_tel").val();

        // 業種
        let entry_contract_business_type = $("#entry_contract_business_type").val();
        
        // 職種
        let entry_contract_business_line = $("#entry_contract_business_line").val();
        
        // 雇用形態
        let entry_contract_business_status = $("#entry_contract_business_status").val();
        
        // 勤続年数
        let entry_contract_business_year = $("#entry_contract_business_year").val();

        // 年収
        let entry_contract_income = $("#entry_contract_income").val();
        
        // 健康保険
        let entry_contract_insurance_type_id = $("#entry_contract_insurance_type_id").val();

        /**
         * 同居人
         */
        // 同居人名
        let housemate_name = $("#housemate_name").val();

        // 同居人カナ
        let housemate_ruby = $("#housemate_ruby").val();

        // 性別
        let housemate_sex_id = $("#housemate_sex_id").val();

        // 続柄
        let housemate_link_id = $("#housemate_link_id").val()
        
        // 生年月日
        let housemate_birthday = $("#housemate_birthday").val()
        
        // 年齢
        let housemate_age = $("#housemate_age").val()
        
        // 自宅電話番号
        let housemate_home_tel = $("#housemate_home_tel").val()
        
        // 携帯電話
        let housemate_mobile_tel = $("#housemate_mobile_tel").val()

        /**
         * 緊急連絡先
         */
        // 緊急連絡先名
        let emergency_name = $("#emergency_name").val();

        // 緊急連絡先カナ
        let emergency_ruby = $("#emergency_ruby").val();

        // 性別
        let emergency_sex_id = $("#emergency_sex_id").val();

        // 続柄
        let emergency_link_id = $("#emergency_link_id").val();
        
        // 生年月日
        let emergency_birthday = $("#emergency_birthday").val();
        
        // 年齢
        let emergency_age = $("#emergency_age").val();
        
        // 郵便番号
        let emergency_post_number = $("#emergency_post_number").val();
        
        // 住所
        let emergency_address = $("#emergency_address").val();
        
        // 自宅電話番号
        let emergency_home_tel = $("#emergency_home_tel").val();
        
        // 携帯電話番号
        let emergency_mobile_tel = $("#emergency_mobile_tel").val();

        /**
         * 連帯保証人
         */
        // 連帯保証人名
        let guarantor_name = $("#guarantor_name").val();

        // 連帯保証人カナ
        let guarantor_ruby = $("#guarantor_ruby").val();

        // 郵便番号
        let guarantor_post_number = $("#guarantor_post_number").val();

        // 住所
        let guarantor_address = $("#guarantor_address").val();

        // 性別
        let guarantor_sex_id = $("#guarantor_sex_id").val();

        // 生年月日
        let guarantor_birthday = $("#guarantor_birthday").val();

        // 年齢
        let guarantor_age = $("#guarantor_age").val();

        // 続柄
        let guarantor_link_id = $("#guarantor_link_id").val();

        // 自宅電話番号
        let guarantor_home_tel = $("#guarantor_home_tel").val();

        // 携帯電話番号
        let guarantor_mobile_tel = $("#guarantor_mobile_tel").val();

        // 勤務先名
        let guarantor_business_name = $("#guarantor_business_name").val();

        // 勤務先フリガナ
        let guarantor_business_ruby = $("#guarantor_business_ruby").val();

        // 勤務先郵便番号
        let guarantor_business_post_number = $("#guarantor_business_post_number").val();

        // 勤務先住所
        let guarantor_business_address = $("#guarantor_business_address").val();

        // 勤務先電話番号
        let guarantor_business_tel = $("#guarantor_business_tel").val();
        
        // 業種
        let guarantor_business_type = $("#guarantor_business_type").val();
        
        // 職種
        let guarantor_business_line = $("#guarantor_business_line").val();
        
        // 雇用形態
        let guarantor_business_status = $("#guarantor_business_status").val();
        
        // 勤続年数
        let guarantor_business_years = $("#guarantor_business_years").val();
        
        // 年収
        let guarantor_income = $("#guarantor_income").val();
        
        // 健康保険
        let guarantor_insurance_type_id = $("#guarantor_insurance_type_id").val();

        // 画像ファイル取得
        let img_file = $('#img_file').prop('files')[0];
        console.log("img_file:" + img_file);

        // 種別
        let img_type = $("#img_type").val();
        console.log("img_type:" + img_type);

        // 備考
        let img_text = $("#img_text").val();
        console.log("img_text:" + img_text);

        //業者id
        let application_id = $("#application_id").val();

        //同居人id
        let housemate_id = $("#housemate_id").val();

        // 同居人追加フラグ(追加=true 追加無=false)
        let housemate_add_flag = $('#housemate_add_flag').val();

        // 送信データインスタンス化
        var sendData = new FormData();

        /**
         * 一時登録フラグ
         */
        sendData.append('temporarily_flag', temporarily_flag);

        /**
         * 画像
         */
        sendData.append('img_file', img_file);
        sendData.append('img_type', img_type);
        sendData.append('img_text', img_text);
        
        /**
         * 不動産業者
         */
        sendData.append('contract_progress_id', contract_progress_id);
        sendData.append('broker_company_name', broker_company_name);
        sendData.append('broker_tel', broker_tel);
        sendData.append('broker_mail', broker_mail);
        sendData.append('broker_name', broker_name);
        sendData.append('application_type_id', application_type_id);
        sendData.append('application_use_id', application_use_id);
        sendData.append('contract_start_date', contract_start_date);
        sendData.append('real_estate_name', real_estate_name);
        sendData.append('real_estate_ruby', real_estate_ruby);
        sendData.append('room_name', room_name);
        sendData.append('post_number', post_number);
        sendData.append('address', address);
        sendData.append('pet_bleed', pet_bleed);
        sendData.append('pet_kind', pet_kind);
        sendData.append('bicycle_number', bicycle_number);
        sendData.append('car_number_number', car_number_number);
        sendData.append('deposit_fee', deposit_fee);
        sendData.append('refund_fee', refund_fee);
        sendData.append('security_fee', security_fee);
        sendData.append('key_fee', key_fee);
        sendData.append('rent_fee', rent_fee);
        sendData.append('service_fee', service_fee);
        sendData.append('water_fee', water_fee);
        sendData.append('ohter_fee', ohter_fee);
        sendData.append('total_fee', total_fee);
        sendData.append('guarantor_flag', guarantor_flag);
        
        /**
         * 賃借人
         */
        sendData.append('entry_contract_name', entry_contract_name);
        sendData.append('entry_contract_ruby', entry_contract_ruby);
        sendData.append('entry_contract_post_number', entry_contract_post_number);
        sendData.append('entry_contract_address', entry_contract_address);
        sendData.append('entry_contract_sex_id', entry_contract_sex_id);
        sendData.append('entry_contract_birthday', entry_contract_birthday);
        sendData.append('entry_contract_age', entry_contract_age);
        sendData.append('entry_contract_home_tel', entry_contract_home_tel);
        sendData.append('entry_contract_mobile_tel', entry_contract_mobile_tel);
        sendData.append('entry_contract_business_name', entry_contract_business_name);
        sendData.append('entry_contract_business_ruby', entry_contract_business_ruby);
        sendData.append('entry_contract_business_post_number', entry_contract_business_post_number);
        sendData.append('entry_contract_business_address', entry_contract_business_address);
        sendData.append('entry_contract_business_tel', entry_contract_business_tel);
        sendData.append('entry_contract_business_type', entry_contract_business_type);
        sendData.append('entry_contract_business_line', entry_contract_business_line);
        sendData.append('entry_contract_business_status', entry_contract_business_status);
        sendData.append('entry_contract_business_year', entry_contract_business_year);
        sendData.append('entry_contract_income', entry_contract_income);
        sendData.append('entry_contract_insurance_type_id', entry_contract_insurance_type_id);

        /**
         * 同居人
         */
        sendData.append('housemate_name', housemate_name);
        sendData.append('housemate_ruby', housemate_ruby);
        sendData.append('housemate_sex_id', housemate_sex_id);
        sendData.append('housemate_link_id', housemate_link_id);
        sendData.append('housemate_age', housemate_age);
        sendData.append('housemate_birthday', housemate_birthday);
        sendData.append('housemate_home_tel', housemate_home_tel);
        sendData.append('housemate_mobile_tel', housemate_mobile_tel);

        /**
         * 緊急連絡先
         */
        // sendData.append('emergency_contact_id', emergency_contact_id);
        sendData.append('emergency_name', emergency_name);
        sendData.append('emergency_ruby', emergency_ruby);
        sendData.append('emergency_sex_id', emergency_sex_id);
        sendData.append('emergency_link_id', emergency_link_id);
        sendData.append('emergency_birthday', emergency_birthday);
        sendData.append('emergency_age', emergency_age);
        sendData.append('emergency_post_number', emergency_post_number);
        sendData.append('emergency_address', emergency_address);
        sendData.append('emergency_home_tel', emergency_home_tel);
        sendData.append('emergency_mobile_tel', emergency_mobile_tel);
        
        /**
         * 連帯保証人
         */
        // sendData.append('guarantor_contracts_id', guarantor_contracts_id);
        sendData.append('guarantor_name', guarantor_name);
        sendData.append('guarantor_ruby', guarantor_ruby);
        sendData.append('guarantor_post_number', guarantor_post_number);
        sendData.append('guarantor_address', guarantor_address);
        sendData.append('guarantor_sex_id', guarantor_sex_id);
        sendData.append('guarantor_birthday', guarantor_birthday);
        sendData.append('guarantor_age', guarantor_age);
        sendData.append('guarantor_link_id', guarantor_link_id);
        sendData.append('guarantor_home_tel', guarantor_home_tel);
        sendData.append('guarantor_mobile_tel', guarantor_mobile_tel);
        sendData.append('guarantor_business_name', guarantor_business_name);
        sendData.append('guarantor_business_ruby', guarantor_business_ruby);
        sendData.append('guarantor_business_post_number', guarantor_business_post_number);
        sendData.append('guarantor_business_address', guarantor_business_address);
        sendData.append('guarantor_business_tel', guarantor_business_tel);
        sendData.append('guarantor_business_type', guarantor_business_type);
        sendData.append('guarantor_business_line', guarantor_business_line);
        sendData.append('guarantor_business_status', guarantor_business_status);
        sendData.append('guarantor_business_years', guarantor_business_years);
        sendData.append('guarantor_income', guarantor_income);
        sendData.append('guarantor_insurance_type_id', guarantor_insurance_type_id);

        // 不動産業者id
        sendData.append('application_id', application_id);

        // 同居人id
        sendData.append('housemate_id', housemate_id);

        // 同居人追加フラグ(追加=true 追加無=false)
        sendData.append('housemate_add_flag', housemate_add_flag);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backAppEditEntry',
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

                        location.href = 'backAppInit';
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

                            // ローディング画面停止
                            setTimeout(function(){
                                $("#overlay").fadeOut(300);
                            },500);
                        };

                        return false;
                    };
                });
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
     * 契約に進む
     */
    $("#btn_app").on('click', function(e) {

        console.log('入居審査の処理');

        e.preventDefault();

        // alertの設定
        var options = {
            title: "契約に進みますか？",
            text: "※一度入居審査に進めると、編集することができません。",
            icon: 'warning',
            buttons: {
                Cancel: "Cancel", // キャンセルボタン
                OK: true
            }
        };

        // then() OKを押した時の処理
        swal(options)
            .then(function(val) {
            
            // キャンセルの処理
            if(val == null){

                console.log('キャンセルの処理');

            }

            // OKの処理
            if (val == "OK") {

                console.log('OKの処理');

                /**
                 * 空白箇所のerrorチェック
                 */
                $('#nav-user-tab').removeClass('bg_tab_error');
                $('#nav-trade-tab').removeClass('bg_tab_error');
                $('#nav-contract-tab').removeClass('bg_tab_error');
                $('#nav-housemate-tab').removeClass('bg_tab_error');
                $('#nav-emergency-tab').removeClass('bg_tab_error');
                $('#nav-guarantor-tab').removeClass('bg_tab_error');
                $('#nav-document-tab').removeClass('bg_tab_error');
        
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
                 * 値取得
                 */
                // 申込id
                let application_id = $("#application_id").val();
                console.log(application_id);

                /**
                 * 送信用データ
                 */
                let sendData = {

                    "application_id": application_id,
                    
                };

                console.log(sendData);

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({

                    type: 'post',
                    url: 'backAppNextStageEntry',
                    dataType: 'json',
                    data: sendData,
                
                // 接続処理
                }).done(function(data) {

                setTimeout(function(){
                    $("#overlay").fadeOut(300);
                },500);

                // trueの処理->申込一覧に遷移
                if(data.status == true){

                    console.log("status:" + data.status);

                    // alertの設定
                    var options = {
                        title: "契約管理に連動しました。",
                        text: "契約一覧から編集して下さい。",
                        icon: "success",
                        buttons: {
                            ok: true
                        }
                    };
                    
                    // then() OKを押した時の処理
                    swal(options)
                        .then(function(val) {
                        if (val) {

                            // location.href = 'backAppInit';
                        };
                    });
                    
                    return false;
                };

                // falseの処理->アラートでエラーメッセージを表示
                if(data.status == false){

                    console.log("status:" + data.status);

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
     * 帳票作成
     */
    $("#btn_modal_report").on('click', function(e) {
            
        console.log('帳票作成');

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);

        // idをパラメーターでControllerに渡す
        // location.href = "backApplicationExcelEntry?application_id=" + id;

        // --------------------
        // ★ボタン非活性
        // --------------------
        $("#btn_modal_report").prop("disabled", true);

        /**
         * 値取得
         */
        // application_id
        var application_id = $('#application_id').val();
        console.log('application_id:'+ application_id);

        // 保証会社id
        var guarantee_company_id = $('#guarantee_company_id').val();
        console.log('guarantee_company_id:'+ guarantee_company_id);

        // 連帯保証人の有無
        var guarantor_need_id = $('#guarantor_need_id').val();
        console.log('guarantor_need_id:'+ guarantor_need_id);

        let sendData = {
        };

        console.log(sendData);

        // ajaxセットアップ
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: 'get',
            url: 'excelEntryDummy',
            dataType: 'json',
            data: sendData,

        // 接続が出来た場合の処理
        }).done(function(data) {
            console.log(data);

            // --------------------
            // ★エクセルダウンロード処理
            // --------------------
            window.open('backApplicationExcelEntry?application_id=' + application_id + '&&guarantee_company_id=' + guarantee_company_id + '&&guarantor_need_id=' + guarantor_need_id, '_self');

            // ★3秒間固定でスリープ(ここは要調整で)
            sleep(2000);

            // -------------------
            // ★ボタン活性
            // --------------------
            $("#btn_modal_report").prop("disabled", false);

            setTimeout(function(){
                $("#overlay").fadeOut(300);
            },500);

        // ajax接続が出来なかった場合の処理
        }).fail(function(jqXHR, textStatus, errorThrown) {

            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);

        });


    });

    // スリープ処理
    function sleep(waitMsec) {
        var startMsec = new Date();
        
        // 指定ミリ秒間だけループさせる（CPUは常にビジー状態）
        while (new Date() - startMsec < waitMsec);
    }

    // 保証人の有無の変更
    $("#guarantor_flag").change(function(e) {

        let guarantor_flag = $("#guarantor_flag").val();
        console.log(guarantor_flag);

        /**
         * 連帯保証人有りの場合 = 編集可能
         */
        if(guarantor_flag == 1){

            $("#guarantor_name").prop("disabled", false);

            $("#guarantor_ruby").prop("disabled", false);

            $("#guarantor_sex_id").prop("disabled", false);

            $("#guarantor_link_id").prop("disabled", false);

            $("#guarantor_birthday").prop("disabled", false);

            $("#guarantor_age").prop("disabled", false);

            $("#guarantor_post_number").prop("disabled", false);

            $("#guarantor_address").prop("disabled", false);

            $("#guarantor_home_tel").prop("disabled", false);

            $("#guarantor_mobile_tel").prop("disabled", false);

            $("#guarantor_business_name").prop("disabled", false);

            $("#guarantor_business_ruby").prop("disabled", false);

            $("#guarantor_business_post_number").prop("disabled", false);

            $("#guarantor_business_address").prop("disabled", false);

            $("#guarantor_business_tel").prop("disabled", false);

            $("#guarantor_business_type").prop("disabled", false);

            $("#guarantor_business_line").prop("disabled", false);

            $("#guarantor_business_status").prop("disabled", false);

            $("#guarantor_business_years").prop("disabled", false);

            $("#guarantor_income").prop("disabled", false);

            $("#guarantor_insurance_type_id").prop("disabled", false);

            $("#cb_emergency").prop("disabled", false);
        }

        /**
         * 連帯保証人無しの場合 = 編集不可
         */
        if(guarantor_flag == 2){

            $("#guarantor_name").prop("disabled", true);
            $("#guarantor_name").val('');

            $("#guarantor_ruby").prop("disabled", true);
            $("#guarantor_ruby").val('');

            $("#guarantor_sex_id").prop("disabled", true);
            $("#guarantor_sex_id").val('');

            $("#guarantor_link_id").prop("disabled", true);
            $("#guarantor_link_id").val('');

            $("#guarantor_birthday").prop("disabled", true);
            $("#guarantor_birthday").val('');

            $("#guarantor_age").prop("disabled", true);
            $("#guarantor_age").val('');

            $("#guarantor_post_number").prop("disabled", true);
            $("#guarantor_post_number").val('');

            $("#guarantor_address").prop("disabled", true);
            $("#guarantor_address").val('');

            $("#guarantor_home_tel").prop("disabled", true);
            $("#guarantor_home_tel").val('');

            $("#guarantor_mobile_tel").prop("disabled", true);
            $("#guarantor_mobile_tel").val('');

            $("#guarantor_business_name").prop("disabled", true);
            $("#guarantor_business_name").val('');

            $("#guarantor_business_ruby").prop("disabled", true);
            $("#guarantor_business_ruby").val('');

            $("#guarantor_business_post_number").prop("disabled", true);
            $("#guarantor_business_post_number").val('');

            $("#guarantor_business_address").prop("disabled", true);
            $("#guarantor_business_address").val('');

            $("#guarantor_business_tel").prop("disabled", true);
            $("#guarantor_business_tel").val('');

            $("#guarantor_business_type").prop("disabled", true);
            $("#guarantor_business_type").val('');

            $("#guarantor_business_line").prop("disabled", true);
            $("#guarantor_business_line").val('');

            $("#guarantor_business_status").prop("disabled", true);
            $("#guarantor_business_status").val('');

            $("#guarantor_business_years").prop("disabled", true);
            $("#guarantor_business_years").val('');

            $("#guarantor_income").prop("disabled", true);
            $("#guarantor_income").val('');

            $("#guarantor_insurance_type_id").prop("disabled", true);
            $("#guarantor_insurance_type_id").val('');

            $("#cb_emergency").prop("disabled", true);
            $("#cb_emergency").val('');

        }
    });

    /**
     * 緊急連絡先と同一の処理
     */
    $('#cb_emergency').on('change', function(){
        
        console.log('緊急連絡先と同一の処理');

        let guarantor_flag = $('#guarantor_flag').val();
        console.log(guarantor_flag);

        if(guarantor_flag == 1){

            let emergency_name = $('#emergency_name').val();
            $('#guarantor_name').val(emergency_name);
    
            let emergency_ruby = $('#emergency_ruby').val();
            $('#guarantor_ruby').val(emergency_ruby);
    
            let emergency_sex_id = $('#emergency_sex_id').val();
            $('#guarantor_sex_id').val(emergency_sex_id);
    
            let emergency_link_id = $('#emergency_link_id').val();
            $('#guarantor_link_id').val(emergency_link_id);
    
            let emergency_birthday = $('#emergency_birthday').val();
            $('#guarantor_birthday').val(emergency_birthday);
    
            let emergency_age = $('#emergency_age').val();
            $('#guarantor_age').val(emergency_age);
            
            let emergency_post_number = $('#emergency_post_number').val();
            $('#guarantor_post_number').val(emergency_post_number);
    
            let emergency_address = $('#emergency_address').val();
            $('#guarantor_address').val(emergency_address);
    
            let emergency_home_tel = $('#emergency_home_tel').val();
            $('#guarantor_home_tel').val(emergency_home_tel);
    
            let emergency_mobile_tel = $('#emergency_mobile_tel').val();
            $('#guarantor_mobile_tel').val(emergency_mobile_tel);
        }

    });

    /**
     * モーダル表示の処理
     * 表示後、URL生成し、備考に記載
     */
    $('#btn_url_again').on('click', function (e) {

        e.preventDefault();

        console.log('URL発行モーダルが表示の処理');

        // ローディング画面
        $("#overlay").fadeIn(300);

        /**
         * 値取得
         */
        // 不動産id
        let application_id = $("#application_id").val();
        console.log('application_id:' + application_id);

        /**
         * 送信データ設定
         */
        // 送信データインスタンス化
        var sendData = new FormData();

        // 不動産業者id
        sendData.append('application_id', application_id);

        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backAppModalInit',
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

                console.log("url:" + data.url);

                $("#url_text").val(data.url);
                
            };

             // falseの処理->アラートでエラーメッセージを表示
            if(data.status == false){

                console.log("status:" + data.status);
                console.log("messages:" + data.messages);
                console.log("errorkeys:" + data.errkeys);

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
     * 本文をコピー
     */
    $('#btn_copy').on('click', function(){

        console.log('本文コピーの処理');

        //　テキストエリアを選択
        $('#url_text').select();
        
        // コピー
        document.execCommand('copy');

        swal({
            title: "本文をコピーしました！",
            text: "【control(Ctrl)+C】で貼付け可能です。",
            icon: "success",
            button: "OK!",
        });

    });

    /**
     * 送信(申込URL発行)
     */
    $("#btn_modal_url_send").on('click', function(e) {

        console.log('申込URL発行の処理');

        e.preventDefault();

        // ローディング画面
        $("#overlay").fadeIn(300);        

        /**
         * 値取得
         */
        // 不動産id
        let application_id = $("#application_id").val();
        console.log('application_id:' + application_id);

        // 不動産名
        let real_estate_name = $("#real_estate_name").val();
        console.log('real_estate_name:' + real_estate_name);

        // 部屋名
        let room_name = $("#room_name").val();
        console.log('room_name:' + room_name);

        // 宛名
        let application_name = $("#application_name").val();
        console.log('application_name:' + application_name);

        // E-mail
        let application_mail = $("#application_mail").val();
        console.log('application_mail:' + application_mail);

        // 件名
        let subject_text = $("#subject_text").val();
        console.log('subject_text:' + subject_text);

        // 本文
        let url_text = $("#url_text").val();
        console.log('url_text:' + subject_text);

        // validationフラグ初期値
        let v_check = true;
        
        /**
         * v_checkフラグがfalseの場合、下段のバリデーションに引っ掛かり
         * modalFormにwas-validatedを付与、エラー文字の表示
         */
        if(application_name == ''){

            v_check = false;
        }

        if(application_mail == ''){

            v_check = false;
        }

        if(subject_text == ''){

            v_check = false;
        }

        // チェック=falseの場合プログラム終了
        console.log(v_check);
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


        sendData.append('application_id', application_id);
        sendData.append('real_estate_name', real_estate_name);
        sendData.append('room_name', room_name);
        sendData.append('application_name', application_name);
        sendData.append('application_mail', application_mail);
        sendData.append('subject_text', subject_text);
        sendData.append('url_text', url_text);


        // ajaxヘッダー
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({

            type: 'post',
            url: 'backAppMailEntry',
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
                    title: "送信が完了しました！",
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
                            
                            let error_message_id = $(msg_key).attr('class');

                            tabError(error_message_id);

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

});