<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>経費詳細/KASEGU</title>

		<!-- head -->
		@component('component.backHead')
		@endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_cost_edit.css') }}">  
		
        <style>

            /* ボタンデフォルト値 */
            .btn-default{
                width: 6rem;
            }

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
            <main class="page-content">

                <div class="container mt-3">
                    <div class="row">

                        <!-- タイトル -->
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="info_title mt-3">
                            <i class="fas bi bi-piggy-bank-fill icon_blue me-2"></i>経費一覧
                            </div>
                            <hr>
                        </div>

                    </div>
                </div>

                <!-- 入力項目 -->
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">

                            <form id="editForm" class="needs-validation" novalidate>
                    
                                <!-- カード -->
                                <div class="card border border-0">

                                    <!-- タブタイトル -->
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-lg-12 mt-2">
                                            <!-- ナビゲーションの設定 -->
                                            <nav>
                                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                    <a class="nav-link active" id="nav-user-tab" data-bs-toggle="tab" href="#nav-user" role="tab" aria-controls="nav-user" aria-selected="true">経費概要</a>
                                                    <a class="nav-link" id="nav-trade-tab" data-bs-toggle="tab" href="#nav-trade" role="tab" aria-controls="nav-trade" aria-selected="false">連絡事項</a>
                                                    <a class="nav-link" id="nav-document-tab" data-bs-toggle="tab" href="#nav-document" role="tab" aria-controls="nav-document" aria-selected="false">付属書類</a>
                                                </div>
                                            </nav>
                                            <!-- ナビゲーションの設定 -->
                                        </div>
                                    </div>
                                    <!-- タブタイトル -->

                                    <!-- タブ内のコンテンツ -->
                                    <div class="row row-cols-3">
                                        <div class="col-12 col-md-12 col-lg-12 mb-3">
                                            <!-- 内容 -->
                                            <div class="tab-content" id="nav-tabContent">
                                            
                                                <!-- 概要 -->
                                                <div class="tab-pane fade show active" id="nav-user" role="tabpanel" aria-labelledby="nav-user-tab">
                                                    
                                                    <div class="row row-cols-2">

                                                        <!-- 照会口座名 -->
                                                        <div class="col-6 col-md-8 col-lg-6 mt-3">
                                                            <label class="label_any mb-2"></label>照会口座名
                                                            
                                                            <select class="form-select" name="contract_progress_id" id="contract_progress_id">
                                                                <option></option>
                                                                @foreach($bank_list as $bank)
                                                                    <option value="{{$bank->bank_id}}" @if($cost_list->bank_id == $bank->bank_id) selected @endif>{{ $bank->bank_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                            <hr>
                                                        </div>

                                                        <!-- 金融機関名 -->
                                                        <div class="col-6 col-md-8 col-lg-6 mt-3">
                                                            <label class="label_any mb-2" for="textBox"></label>金融機関名
                                                            <input type="text" class="form-control" name="financial_name" id="financial_name" value="{{ $cost_list->financial_name }}" placeholder="例：ﾐﾂﾋﾞｼUFJ">
                                                            <!-- バリデーション -->
                                                            <div class="user-tab invalid-feedback" id ="financial_name_error">
                                                                金融機関名は必須です。
                                                            </div>
                                                        </div>

                                                        <!-- 支店名 -->
                                                        <div class="col-12 col-md-12 col-lg-6 mt-3">
                                                            <label class="label_any mb-2" for="textBox"></label>支店名
                                                            <input type="text" class="form-control" name="financial_branch" id="financial_branch" value="{{ $cost_list->financial_branch }}" placeholder="例：ﾔｴｽﾄﾞｵﾘ">
                                                            <!-- バリデーション -->
                                                            <div class="user-tab invalid-feedback" id ="financial_branch_error">
                                                                支店名は必須です。
                                                            </div>
                                                        </div>

                                                        <!-- 改行 -->
                                                        <div class="w-100"></div>

                                                        <!-- 摘要 -->
                                                        <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                            <label class="label_any mb-2" for="textBox"></label>摘要
                                                            <input type="text" class="form-control" name="financial_summary" id="financial_summary" value="{{ $cost_list->financial_branch }}" placeholder="例：ﾄｳｷﾖｳｶｲｼﾞﾖｳﾆﾁ">
                                                            <!-- バリデーション -->
                                                            <div class="user-tab invalid-feedback" id ="financial_summary_error">
                                                                摘要は必須です。
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                            <hr>
                                                        </div>
                                                                                                            
                                                        <!-- 勘定日 -->
                                                        <div class="col-6 col-md-6 col-lg-4 mt-3">
                                                            <label class="label_required mb-2" for=""></label>勘定日
                                                            <input type="text" class="form-control" id="account_date" name="account_date" autocomplete="off" value="">
                                                        </div>

                                                        <!-- 勘定科目 -->
                                                        <div class="col-6 col-md-8 col-lg-4 mt-3">
                                                            <label class="label_any mb-2"></label>勘定科目
                                                            
                                                            <select class="form-select" name="contract_progress_id" id="contract_progress_id">
                                                                <option></option>
                                                                @foreach($bank_list as $bank)
                                                                    <option value="{{$bank->bank_id}}" @if($cost_list->bank_id == $bank->bank_id) selected @endif>{{ $bank->bank_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="w-100"></div>

                                                        <!-- 出金種別 -->
                                                        <div class="col-6 col-md-8 col-lg-4 mt-3">
                                                            <label class="label_any mb-2"></label>出金種別
                                                            
                                                            <select class="form-select" name="contract_progress_id" id="contract_progress_id">
                                                                <option></option>
                                                                @foreach($bank_list as $bank)
                                                                    <option value="{{$bank->bank_id}}" @if($cost_list->bank_id == $bank->bank_id) selected @endif>{{ $bank->bank_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                    
                                                    </div>

                                                </div>
                                                <!-- 業者 -->

                                                <!-- 募集要項 -->
                                                <div class="tab-pane fade" id="nav-trade" role="tabpanel" aria-labelledby="nav-trade-tab">
                                                    <div class="row row-cols-2">

                                                    </div>
                                                </div>
                                                <!-- 条件 -->   

                                                <!-- 画像 -->
                                                <div class="tab-pane fade" id="nav-document" role="tabpanel" aria-labelledby="nav-document-tab">
                                                    <div class="row row-cols-3">

                                                        <!-- 添付書類 -->
                                                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                                                            <label class="mb-2">アップロード</label>
                                                            <input class="form-control" type="file" id="img_file">
                                                            <!-- エラーメッセージ -->
                                                            <div class="invalid-feedback" id ="img_file_error"></div>
                                                        </div>

                                                        <!-- 改行 -->
                                                        <div class="w-100"></div>

                                                        <!-- 種別 -->
                                                        <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                            <label class="mb-2">ファイル種別</label>
                                                            <select class="form-select" name="img_type" id="img_type">
                                                                <option selected></option>
                                                            </select>
                                                            <div class="invalid-feedback" id ="img_type_error"></div>
                                                        </div>

                                                        <!-- 補足 -->
                                                        <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                            <label for="">備考</label>
                                                            <textarea class="form-control" name="img_text" id="img_text" rows="2" placeholder="例：自由に入力"></textarea>
                                                            <div class="invalid-feedback" id ="img_text_error"></div>
                                                        </div>

                                                        <!-- 改行 -->
                                                        <div class="w-100"></div>



                                                    </div>
                                                </div>
                                                <!-- 画像 -->

                                            </div>
                                            <!-- 内容 -->
                                        </div>
                                    </div>
                                    <!-- タブ内のコンテンツ -->

                                    <!-- 境界線 -->
                                    <hr>
                                    <!-- 境界線 -->

                                    <!-- ボタン -->
                                    <div class="row row-cols-2 mb-5">

                                        <!-- 削除 -->
                                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                                            <div class="btn-group" role="group">
                                                <button type="button" id="btn_delete" class="btn btn-outline-danger btn-default">削除</button>
                                                <button type="button" id="btn_url_again" class="btn btn-outline-primary btn-default" data-bs-toggle="modal" data-bs-target="#urlModal">URL発行</button>
                                            </div>
                                        </div>
                                        <!-- 削除 -->

                                        <!-- 登録、帳票 -->
                                        <div class="col-12 col-md-6 col-lg-6 mt-3">
                                            <div class="btn-group float-xl-end" role="group">

                                                <!-- 登録 -->
                                                <button id="btn_edit" class="btn btn-outline-primary btn-default">登録</button>

                                            </div>
                                        </div>
                                        <!-- 登録、帳票 -->

                                    </div>     
                                    <!-- ボタン -->

                                    <!-- 不動産id -->
                                    <input type="text" name="application_id" id="application_id" value="{{ $cost_list->cost_id }}">
                                    
                                    <!-- 同居人id -->
                                    <input type="text" name="housemate_id" id="housemate_id" value="">
                                    
                                    <!-- 同居人追加フラグ(追加=true 追加無=false) -->
                                    <input type="text" name="housemate_add_flag" id="housemate_add_flag" value="false">
                                    
                                </div>
                                <!-- カード -->
                            </form>
                        </div>
                    </div>
                </div>
                <!-- 入力項目 -->

            </main>
            <!-- page-content" -->
            
		</div>
		<!-- page-wrapper -->

		@component('component.backJs')
		@endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_cost_edit.js') }}"></script>

        <script>

            // 契約者生年月日
            $('#account_date').datepicker({
                language:'ja'
            });


        </script>

	</body>
	
</html>