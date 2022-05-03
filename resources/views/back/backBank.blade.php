<!DOCTYPE html>
<html lang="ja">

	<head>
		<title>銀行一覧/COSTS</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_bank.css') }}">  
		
        <style>

            /* ボタンデフォルト値 */
            .btn-default{
                width: 6rem;
            }

            /* 一覧の左右に余白が出来るため、0に設定 */
            .card-body {
                padding: 0rem;
            }

		</style>
        
	</head>

	<body>
		<!-- page-wrapper -->
		<div class="page-wrapper chiller-theme toggled">

            <!-- ローディング画面の表示 -->
            <div id="overlay">
                <div class="cv-spinner">
                    <span class="spinner"></span>
                </div>
            </div>
        
            <!-- sidebar-wrapper  -->
            @component('component.backSidebar')
            @endcomponent
            <!-- sidebar-wrapper  -->
            
            <!-- page-content" -->
            <main class="page-content mb-3">

                <!-- 上段検索 -->
                <div class="container">

                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 mt-2">

                            <!-- タイトル -->
                            <div class="row">
                                <div class="col-12 col-md-12 col-lg-12 mb-2">
                                    <div class="info_title mt-3">
                                        <i class="fas fa-key icon_blue me-2"></i>銀行一覧
                                    </div>
                                    <!-- 境界線 -->
                                    <hr>
                                </div>
                            </div>
                            <!-- タイトル -->
                            
                            <div class="row">
                                <form action="backBankInit" method="post">
                                    {{ csrf_field() }}
                                    <div class="col-sm-12">
                                        <div class="card border border-0">
                                            <div class="row align-items-end">

                                                <!-- フリーワード -->
                                                <div class="col-7 col-md-8 col-lg-4">
                                                    <label for="">フリーワード</label>
                                                    <input type="text" class="form-control" name="free_word" id="free_word" value="">
                                                </div>


                                                <!-- 検索ボタン -->
                                                <div class="col-5 col-md-4 col-lg-8">
                                                    <input type="submit" class="btn btn-default btn-outline-primary float-end" value="検索">
                                                </div>
                                                <!-- 検索ボタン -->

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- 上段検索 -->

                <!-- 一覧 -->
                <div class="container">
                    
                    <div class="row">
                            
                        <!-- テーブルcard -->
                        <div class="col-12 col-md-12 col-lg-12">

                            <div class="card">
                        
                                <!-- カードボディ -->
                                <div class="card-body">
                                    <!-- スクロール -->
                                    <div class="overflow-auto" style="height:35rem;">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-condensed table-striped">
                                                <!-- テーブルヘッド -->
                                                <thead>
                                                    <tr>
                                                        <th scope="col" id="create_user_id" style="display:none">id</th>
                                                        <th>選択</th>
                                                        <th scope="col" id="legal_place_name">銀行名</th>
                                                        <th scope="col" id="legal_place_post_number">支店名</th>
                                                        <th scope="col" id="legal_place_address">種別</th>
                                                        <th scope="col" id="legal_place_address">口座番号</th>
                                                        <th scope="col" id="legal_place_address">名義</th>
                                                    </tr>
                                                </thead>

                                                <!-- テーブルボディ -->
                                                <tbody>
                                                    @foreach($res as $bank_list)
                                                        <tr>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class" style="display:none"></td>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class"><input id="{{ $bank_list->bank_id }}" type="radio" class="align-middle" name="flexRadioDisabled"></td>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class">{{ $bank_list->bank_name }}</td>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class">{{ $bank_list->bank_branch_name }}</td>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class">{{ $bank_list->bank_type_name }}</td>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class">{{ $bank_list->bank_number }}</td>
                                                            <td id="{{ $bank_list->bank_id }}" class="click_class">{{ $bank_list->bank_account_name }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <!-- テーブルボディ -->
                                            </table>
                                        </div>
                                    </div>
                                    <!-- スクロール -->
                                <!-- カードボディ -->
                                </div>

                            </div>

                            <!-- ぺージネーション -->   
                            <div id="links" style="display:none;" class="mt-3">
                                {{ $res->links() }}
                            </div>

                        </div>
                        <!-- テーブルcard -->

                        <!-- ボタン -->
                        <div class="col-sm-12 mt-4 pt-3">
                            <div class="card border border-0">
                                <!-- row -->
                                <div class="row">
                                    <!-- 新規、編集 -->
                                    <div class="col-12">
                                        <div class="btn-group float-end" role="group">
                                            <button type="button" onclick="location.href='backBankNewInit'" class="btn btn-outline-primary float-end btn-default">新規登録</button>
                                            <button type="button" id="btn_edit" class="btn btn-outline-primary float-end btn-default">編集</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- row -->
                            </div>
                        </div>
                        <!-- ボタン -->

                    </div>
                </div>
                <!-- 一覧 --> 

            </main>
            <!-- page-content" -->

		</div>
		<!-- page-wrapper -->
        
        <!-- js -->
        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_bank.js') }}"></script>
	</body>
	
</html>