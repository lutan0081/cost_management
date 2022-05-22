$(function(){

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
        location.href = "backProfitEditInit?profit_id=" + id;

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
     * csv出力
     */
    $("#btn_csv").on('click', function(e) {

        console.log("csvボタンの処理");

        // ローディング画面
        $("#overlay").fadeIn(300);

        e.preventDefault();

        // フリーワード
        let free_word = $('#free_word').val();
        
        let real_estate_id = $('#real_estate_id').val();

        let create_user_id = $('#create_user_id').val();

        let start_date = $('#start_date').val();

        let end_date = $('#end_date').val();

        let profit_account_id = $('#profit_account_id').val();

        // 5秒遅延
        setTimeout(function(){
            $("#overlay").fadeOut(300);
        },3000);

        // csvDownload
        location.href = "csvDownload?free_word=" + free_word + "&real_estate_id=" + real_estate_id + "&profit_account_id=" + profit_account_id+ "&create_user_id=" + create_user_id + "&start_date=" + start_date + "&end_date=" + end_date+ "&profit_account_id=" + profit_account_id;

    });

});