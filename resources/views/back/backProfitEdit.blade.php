<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>売上詳細/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_profit_edit.css') }}">  
		
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
                            <div class="info_title mt-2">
                                <i class="fas bi bi-piggy-bank-fill icon_blue me-2"></i>売上一覧
                            </div>
                            <hr>
                        </div>

                        <!-- 承諾者・承諾日 -->
                        @if($profit_list->profit_approval_id != '0')
                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="form-check float-end">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        承諾日:{{ $profit_list->profit_approval_date }}　承諾者:{{ $profit_list->create_user_name }}
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- 承諾する -->
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-check form-switch float-end">
                                <input class="form-check-input" type="checkbox" name="approval_id" id="approval_id" @if($profit_list->profit_approval_id != 0)checked @endif>
                                <label class="form-check-label markerBlue" for="flexCheckDefault">
                                    承諾する
                                </label>
                            </div>
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
                                                    <a class="nav-link active" id="nav-profit-tab" data-bs-toggle="tab" href="#nav-profit" role="tab" aria-controls="nav-profit" aria-selected="true">売上概要</a>
                                                    <a class="nav-link" id="nav-file-tab" data-bs-toggle="tab" href="#nav-file" role="tab" aria-controls="nav-file" aria-selected="false">付属書類</a>
                                                    <a class="nav-link" id="nav-other-tab" data-bs-toggle="tab" href="#nav-other" role="tab" aria-controls="nav-other" aria-selected="false">連絡事項</a>
                                                </div>
                                            </nav>
                                            <!-- ナビゲーションの設定 -->
                                        </div>
                                    </div>
                                    <!-- タブタイトル -->

                                    <!-- タブ内のコンテンツ -->
                                    <div class="col-12 col-md-12 col-lg-12 mb-3">
                                        <!-- 内容 -->
                                        <div class="tab-content" id="nav-tabContent">
                                        
                                            <!-- 概要 -->
                                            <div class="tab-pane fade show active" id="nav-profit" role="tabpanel" aria-labelledby="nav-profit-tab">
                                                
                                                <div class="row row-cols-3">

                                                    <!-- 照会口座名 -->
                                                    <div class="col-12 col-md-12 col-lg-6 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>照会口座名
                                                        <select class="form-select disabled_class" name="bank_id" id="bank_id" required>
                                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                            <option></option>
                                                            @foreach($profit_bank_list as $profit_bank)
                                                                <option value="{{$profit_bank->bank_id}}" @if($profit_list->bank_id == $profit_bank->bank_id) selected @endif>{{ $profit_bank->bank_name }}_{{ $profit_bank->bank_number }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="profit-tab invalid-feedback" id =bank_id_error">
                                                            照会口座名は必須です。
                                                        </div>
                                                    </div>
                                                    <!-- 照会口座名 -->

                                                    <div class="w-100"></div>

                                                    <!-- 売上担当 -->
                                                    <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>売上担当
                                                        <select class="form-select disabled_class" name="profit_person_id" id="profit_person_id" required>
                                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                            <option></option>
                                                            @foreach($create_user_list as $create_users)
                                                                <option value="{{$create_users->create_user_id}}" @if($profit_list->profit_person_id == $create_users->create_user_id) selected @endif>{{ $create_users->create_user_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="profit-tab invalid-feedback" id =profit_person_id_error">
                                                            売上担当は必須です。
                                                        </div>
                                                    </div>
                                                    <!-- 売上担当 -->

                                                    <!-- 勘定科目 -->
                                                    <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>勘定科目
                                                        <select class="form-select disabled_class" name="profit_account_id" id="profit_account_id" value="{{ $profit_list->profit_account_id }}" required>
                                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                            <option></option>
                                                            @foreach($profit_account_list as $profit_accounts)
                                                                <option value="{{ $profit_accounts->profit_account_id }}" @if($profit_list->profit_account_id == $profit_accounts->profit_account_id) selected @endif>{{ $profit_accounts->profit_account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="profit-tab invalid-feedback" id ="profit_account_id_error">
                                                            勘定科目は必須です。
                                                        </div>
                                                    </div>
                                                    <!-- 勘定科目 -->

                                                    <div class="w-100"></div>

                                                    <!-- 勘定日 -->
                                                    <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>勘定日
                                                        <input type="text" class="form-control disabled_class" name="profit_account_date" id="profit_account_date" placeholder="例：2022/05/17" value="{{ $profit_list->profit_date }}" required>
                                                        <!-- エラーメッセージ -->
                                                        <div class="profit-tab invalid-feedback" id ="profit_account_date_error">
                                                            勘定日は必須です。
                                                        </div>
                                                    </div>
                                                    <!-- 勘定日 -->
                                                    
                                                    <!-- 金額 -->
                                                    <div class="col-12 col-md-12 col-lg-3 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>利益額
                                                        <input type="text" class="form-control disabled_class" name="profit_fee" id="profit_fee" placeholder="例：3000000" value="{{ $profit_list->profit_fee }}" style="text-align:right" required>
                                                        <!-- エラーメッセージ -->
                                                        <div class="profit-tab invalid-feedback" id ="profit_fee_error">
                                                            利益額は必須です。
                                                        </div>
                                                    </div>
                                                    <!-- 金額 -->

                                                    <div class="col-6 col-md-8 col-lg-12 mt-3">
                                                        <hr>
                                                    </div>

                                                    <!-- 取引先 -->
                                                    <div class="col-12 col-md-12 col-lg-6 mt-3">
                                                        <label class="label_any mb-2" for="textBox"></label>取引先
                                                        <input type="text" class="form-control disabled_class" name="customer_name" id="customer_name" placeholder="例：株式会社〇〇〇〇不動産" value="{{ $profit_list->customer_name }}">
                                                        <!-- エラーメッセージ -->
                                                        <div class="profit-tab invalid-feedback" id ="customer_name_error">
                                                            取引先は必須です。
                                                        </div>
                                                    </div>
                                                    <!-- 取引先 -->

                                                    <div class="w-100"></div>

                                                    <!-- 物件名 -->
                                                    <div class="col-12 col-md-8 col-lg-6 mt-3">
                                                        <label class="label_any mb-2" for="textBox"></label>物件名
                                                        <div class="input-group">
                                                            <select class="form-select disabled_class" name="real_estate_id" id="real_estate_id" class="real_estate_id">
                                                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                                <option></option>
                                                                @foreach($real_estate_list as $real_estates)
                                                                    <option value="{{$real_estates->real_estate_id}}" @if($profit_list->real_estate_id == $real_estates->real_estate_id) selected @endif>{{ $real_estates->real_estate_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button id="real_estate-zip" class="btn btn-outline-primary btn_zip disabled_class"><i class="fas fa-search"></i></button>
                                                        </div>
                                                        <div class="profit-tab invalid-feedback" id ="real_estate_id_error">
                                                        </div>
                                                    </div>
                                                    <!-- 物件名 -->

                                                    <div class="w-100"></div>

                                                    <!-- 部屋番号 -->
                                                    <div class="col-12 col-md-8 col-lg-2 mt-3">
                                                        <label class="label_any mb-2" for="textBox"></label>号室
                                                        
                                                        <select class="form-select disabled_class" name="room_id" id="room_id">
                                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                                            <option></option>
                                                            @foreach($room_list as $rooms)
                                                                <option value="{{$rooms->room_id}}" @if($profit_list->room_id == $rooms->room_id) selected @endif>{{ $rooms->room_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="profit-tab invalid-feedback" id ="room_id_error">
                                                        </div>
                                                    </div>
                                                    <!-- 部屋番号 -->

                                                    <div class="col-6 col-md-8 col-lg-12 mt-3">
                                                        <hr>
                                                    </div>
                                                                                
                                                    <!-- 備考 -->
                                                    <div class="col-6 col-md-8 col-lg-12 mt-3">
                                                        <label class="label_any mb-2" for="textBox"></label>備考
                                                        <textarea class="form-control disabled_class" name="profit_memo" id="profit_memo" rows="5" placeholder="自由に入力">{{ $profit_list->profit_memo }}</textarea>
                                                        <div class="profit-tab invalid-feedback" id ="profit_memo_error">
                                                        </div> 
                                                    </div>
                                                    <!-- 備考 -->
                                
                                                </div>

                                            </div>
                                            <!-- 業者 -->

                                            <!-- 画像 -->
                                            <div class="tab-pane fade" id="nav-file" role="tabpanel" aria-labelledby="nav-file-tab">
                                                <div class="row row-cols-3">

                                                    <!-- 添付書類 -->
                                                    <div class="col-12 col-md-6 col-lg-6 mt-2">
                                                        <label class="mb-2 label_any"></label>アップロード
                                                        <input class="form-control disabled_class" type="file" id="img_file">
                                                        <!-- エラーメッセージ -->
                                                        <div class="file-tab invalid-feedback" id ="img_file_error"></div>
                                                    </div>

                                                    <!-- 改行 -->
                                                    <div class="w-100"></div>

                                                    <!-- 種別 -->
                                                    <div class="col-12 col-md-12 col-lg-3 mt-2">
                                                        <label class="mb-2 label_any"></label>ファイル種別
                                                        <select class="label_any form-select disabled_class" name="img_type" id="img_type">
                                                            <option selected></option>
                                                            @foreach($profit_img_type_list as $profit_img_type)
                                                                <option value="{{$profit_img_type->profit_img_type_id}}">{{ $profit_img_type->profit_img_type_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="file-tab invalid-feedback" id ="img_type_error"></div>
                                                    </div>

                                                    <!-- 補足 -->
                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <label class="label_any" for=""></label>備考
                                                        <textarea class="form-control disabled_class" name="img_text" id="img_text" rows="2" placeholder="例：自由に入力"></textarea>
                                                        <div class="file-tab invalid-feedback" id ="img_text_error"></div>
                                                    </div>

                                                    <!-- 画像ファイル -->
                                                    @if(count($profit_img_list) > 0)
                                                        <div class="col-12 col-md-12 col-lg-12 mt-4">

                                                            <!-- タイトル -->
                                                            <i class="fas fa-file icon_blue me-2"></i>付属書類
                                                            <hr class="hr_album">

                                                            <div class="row">
                                                                
                                                                @foreach($profit_img_list as $imgs)
                                                                    <div class="col-12 col-md-12 col-lg-4 mt-3 mb-2">
                                                                        <div class="card" style="min-height:25rem;">
                                                                            
                                                                            @php
                                                                                $file_type = explode('.', $imgs->profit_img_path)[1];
                                                                            @endphp

                                                                            <!-- ファイルタイプがPDFの場合 -->
                                                                            @if($file_type == 'pdf')
                                                                                <div class="pdf_icon_box">
                                                                                    <a href="storage/{{ $imgs->profit_img_path }}" target="_blank"><img src="./back/img/pdf_icon.jpeg" class="pdf_icon_size"></a>
                                                                                </div>
                                                                            <!-- ファイルタイプがPDF以外の場合 -->
                                                                            @else
                                                                                <img src="storage/{{ $imgs->profit_img_path }}" class="card-img-top">
                                                                            @endif
                                                                            
                                                                            <!-- カードボディ -->
                                                                            <div class="card-body">
                                                                                <ul class="list-group list-group-flush">
                                                                                    <li class="list-group-item">種別：{{ $imgs->profit_img_type_name }}</li>
                                                                                    <li class="list-group-item">備考：{{ $imgs->profit_img_memo }}</li>
                                                                                </ul>
                                                                            </div>
                                                                            <!-- カードボディ -->

                                                                            <!-- 削除ボタン -->
                                                                            @if(Session::get('permission_type_id') == 1)
                                                                                <div class="card-footer">
                                                                                    <span id="{{ $imgs->profit_img_id }}" class="btn_img_delete text_red float-end" style="cursor: hand; cursor:pointer;">削除</span>
                                                                                </div>
                                                                            @endif
                                                                            <!-- 削除ボタン -->

                                                                        </div>
                                                                    </div>
                                                                @endforeach                        

                                                            </div>
                                                        </div>
                                                    @endif
                                                    <!-- 画像ファイル -->


                                                </div>
                                            </div>
                                            <!-- 画像 -->

                                            <!-- 質問 -->
                                            <div class="tab-pane fade" id="nav-other" role="tabpanel" aria-labelledby="nav-other-tab">
                                                <div class="row row-cols-2">

                                                    <!-- 質問内容 -->
                                                    <div class="col-12 col-md-12 col-lg-6 mt-2">
                                                        <label class="label_any" for=""></label>質問内容
                                                        <textarea class="form-control" name="question_contents" id="question_contents" rows="8" placeholder="例：内容を自由に入力">{{ $profit_list->profit_question_contents }}</textarea>
                                                        <div class="other-tab invalid-feedback" id ="question_contents_error"></div>
                                                    </div>

                                                    <!-- 回答内容 -->
                                                    <div class="col-12 col-md-12 col-lg-6 mt-2">
                                                        <label class="label_any" for=""></label>回答内容
                                                        <textarea class="form-control disabled_class" name="answer_contents" id="answer_contents" rows="8" placeholder="例：内容を自由に入力">{{ $profit_list->profit_answer_contents }}</textarea>
                                                        <div class="other-tab invalid-feedback" id ="answer_contents_error"></div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- 質問 -->   

                                        </div>
                                        <!-- 内容 -->
                                    </div>
                                    <!-- タブ内のコンテンツ -->

                                    <!-- 境界線 -->
                                    <hr>
                                    <!-- 境界線 -->

                                    <!-- ボタン -->
                                    <div class="row row-cols-2 mb-5">

                                        <!-- 削除 -->
                                        <div class="col-12 col-md-6 col-lg-6 mt-2">
                                            <div class="btn-group" role="group">
                                                <button type="button" id="btn_delete" class="btn btn-outline-danger btn-default disabled_class">削除</button>
                                                <button type="button" id="btn_url_again" class="btn btn-outline-primary btn-default" data-bs-toggle="modal" data-bs-target="#urlModal">URL発行</button>
                                            </div>
                                        </div>
                                        <!-- 削除 -->

                                        <!-- 登録、帳票 -->
                                        <div class="col-12 col-md-6 col-lg-6 mt-2">
                                            <div class="btn-group float-xl-end" role="group">

                                                <!-- 登録 -->
                                                <button id="btn_edit" class="btn btn-outline-primary btn-default disabled_class">登録</button>

                                            </div>
                                        </div>
                                        <!-- 登録、帳票 -->

                                    </div>     
                                    <!-- ボタン -->

                                    <!-- 売上id -->
                                    <input type="hidden" name="profit_id" id="profit_id" value="{{ $profit_list->profit_id }}">
                                    
                                    <!-- 権限id sessionから取得 -->
                                    <input type="hidden" name="permission_type_id" id="permission_type_id" value="{{ Session::get('permission_type_id') }}">

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
		<script src="{{ asset('back/js/back_profit_edit.js') }}"></script>
    
        <!-- bootstrap-datepickerのjavascriptコード -->
        <script>
            
            $('#profit_account_date').datepicker({
                language:'ja'
            });

        </script>

	</body>
	
</html>