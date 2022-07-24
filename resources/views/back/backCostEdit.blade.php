<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>経費詳細/COST</title>

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
                            <div class="info_title mt-2">
                                <i class="fas bi bi-piggy-bank-fill icon_blue me-2"></i>経費一覧
                            </div>
                            <hr>
                        </div>

                        <!-- 承諾者・承諾日 -->
                        @if($cost_list->approval_id != '0')
                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="form-check float-end">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        承諾日:{{ $cost_list->approval_date }}　承諾者:{{ $cost_list->create_user_name }}
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- 承諾する -->
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-check form-switch float-end">
                                <input class="form-check-input" type="checkbox" name="approval_id" id="approval_id" @if($cost_list->approval_id != 0)checked @endif>
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
                                                    <a class="nav-link active" id="nav-cost-tab" data-bs-toggle="tab" href="#nav-cost" role="tab" aria-controls="nav-cost" aria-selected="true">経費概要</a>
                                                    <a class="nav-link" id="nav-file-tab" data-bs-toggle="tab" href="#nav-file" role="tab" aria-controls="nav-file" aria-selected="false">付属書類</a>
                                                    <a class="nav-link" id="nav-other-tab" data-bs-toggle="tab" href="#nav-other" role="tab" aria-controls="nav-other" aria-selected="false">連絡事項</a>
                                                    <a class="nav-link" id="nav-list-tab" data-bs-toggle="tab" href="#nav-list" role="tab" aria-controls="nav-list" aria-selected="false">一覧</a>
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
                                            <div class="tab-pane fade show active" id="nav-cost" role="tabpanel" aria-labelledby="nav-cost-tab">
                                                
                                                <div class="row row-cols-3">

                                                    <!-- 照会口座名 -->
                                                    <div class="col-6 col-md-8 col-lg-6 mt-2">
                                                        <label class="label_any mb-2"></label>照会口座名
                                                        
                                                        <select class="form-select" name="bank_id" id="bank_id" disabled>
                                                            <option></option>
                                                            @foreach($bank_list as $bank)
                                                                <option value="{{$bank->bank_id}}" @if($cost_list->bank_id == $bank->bank_id) selected @endif>{{ $bank->bank_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="cost-tab invalid-feedback" id ="bank_id_error">
                                                            照会口座名は必須です。
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <hr>
                                                    </div>

                                                    <!-- 金融機関名 -->
                                                    <div class="col-6 col-md-8 col-lg-6 mt-2">
                                                        <label class="label_any mb-2" for="textBox"></label>金融機関名
                                                        <input type="text" class="form-control" name="financial_name" id="financial_name" value="{{ $cost_list->financial_name }}" placeholder="例：ﾐﾂﾋﾞｼUFJ" disabled>
                                                        <!-- バリデーション -->
                                                        <div class="cost-tab invalid-feedback" id ="financial_name_error">
                                                            金融機関名は必須です。
                                                        </div>
                                                    </div>

                                                    <!-- 支店名 -->
                                                    <div class="col-12 col-md-12 col-lg-6 mt-2">
                                                        <label class="label_any mb-2" for="textBox"></label>支店名
                                                        <input type="text" class="form-control" name="financial_branch" id="financial_branch" value="{{ $cost_list->financial_branch }}" placeholder="例：ﾔｴｽﾄﾞｵﾘ" disabled>
                                                        <!-- バリデーション -->
                                                        <div class="cost-tab invalid-feedback" id ="financial_branch_error">
                                                            支店名は必須です。
                                                        </div>
                                                    </div>

                                                    <!-- 改行 -->
                                                    <div class="w-100"></div>

                                                    <!-- 摘要 -->
                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <label class="label_any mb-2" for="textBox"></label>摘要
                                                        <input type="text" class="form-control" name="financial_summary" id="financial_summary" value="{{ $cost_list->financial_summary }}" placeholder="例：ﾄｳｷﾖｳｶｲｼﾞﾖｳﾆﾁ" disabled>
                                                        <!-- バリデーション -->
                                                        <div class="cost-tab invalid-feedback" id ="financial_summary_error">
                                                            摘要は必須です。
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <hr>
                                                    </div>

                                                    <!-- 経費か否か -->
                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input disabled_class" type="checkbox" id="cost_flag_id" name="cost_flag_id" @if($cost_list->cost_flag_id == 1)checked @endif>
                                                            <label class="form-check-label pink_line" for="cost_flag_id">経費か否か</label>
                                                        </div>
                                                    </div>

                                                    <!-- 出金区分 -->
                                                    <div class="col-6 col-md-8 col-lg-4 mt-3">
                                                        <label class="label_required mb-2"></label>出金区分
                                                        
                                                        <select class="form-select disabled_class" name="private_or_bank_id" id="private_or_bank_id" required>
                                                            <option></option>
                                                            @foreach($private_or_bank_list as $private_or_bank)
                                                                <option value="{{$private_or_bank->private_or_bank_id}}" @if($cost_list->private_or_bank_id == $private_or_bank->private_or_bank_id) selected @endif>{{ $private_or_bank->private_or_bank_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="cost-tab invalid-feedback" id ="private_or_bank_id_error">
                                                            出金区分は必須です。
                                                        </div>
                                                    </div>

                                                    <!-- 勘定日 -->
                                                    <div class="col-6 col-md-6 col-lg-4 mt-3">
                                                        <label class="label_required mb-2" for=""></label>勘定日
                                                        <input type="text" class="form-control disabled_class" id="account_date" name="account_date" autocomplete="off" value="{{ $cost_list->account_date }}" required>
                                                        <div class="cost-tab invalid-feedback" id ="account_date_error">
                                                            勘定日は必須です。
                                                        </div>
                                                    </div>

                                                    <!-- 勘定科目 -->
                                                    <div class="col-6 col-md-8 col-lg-4 mt-3">
                                                        <label class="label_required mb-2"></label>勘定科目
                                                        <select class="form-select disabled_class" name="cost_account_id" id="cost_account_id" required>
                                                            <option></option>
                                                            @foreach($cost_account_list as $cost_account)
                                                                <option value="{{$cost_account->cost_account_id}}" @if($cost_list->cost_account_id == $cost_account->cost_account_id) selected @endif>{{ $cost_account->cost_account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="cost-tab invalid-feedback" id ="cost_account_id_error">
                                                            勘定科目は必須です。
                                                        </div>
                                                    </div>

                                                    <div class="w-100"></div>

                                                    <!-- 出金額 -->
                                                    <div class="col-6 col-md-8 col-lg-4 mt-3">
                                                        <label class="label_required mb-2" for="textBox"></label>出金額
                                                        <input type="number" class="form-control disabled_class" name="outgo_fee" id="outgo_fee" value="{{ $cost_list->outgo_fee }}" placeholder="例：100000" style="text-align:right" required>
                                                        <!-- バリデーション -->
                                                        <div class="cost-tab invalid-feedback" id ="outgo_fee_error">
                                                            出金額は必須です。
                                                        </div>
                                                    </div>

                                                    <!-- 入金額 -->
                                                    <div class="col-6 col-md-8 col-lg-4 mt-2">
                                                        <label class="label_required mb-2" for="textBox"></label>入金額
                                                        <input type="number" class="form-control disabled_class" name="income_fee" id="income_fee" value="{{ $cost_list->income_fee }}" placeholder="例：100000" style="text-align:right" required>
                                                        <!-- バリデーション -->
                                                        <div class="cost-tab invalid-feedback" id ="income_fee_error">
                                                            入金額は必須です。
                                                        </div>
                                                    </div>

                                                    <!-- 残高 -->
                                                    <div class="col-6 col-md-8 col-lg-4 mt-2">
                                                        <label class="label_any mb-2" for="textBox"></label>残高
                                                        <input type="number" class="form-control" name="balance_fee" id="balance_fee" value="{{ $cost_list->balance_fee }}" placeholder="例：100000" style="text-align:right" disabled>
                                                        <!-- バリデーション -->
                                                        <div class="cost-tab invalid-feedback" id ="balance_fee_error">
                                                            残高は必須です。
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <hr>
                                                    </div>

                                                    <!-- 備考 -->
                                                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                        <label class="label_any" for=""></label>備考
                                                        <textarea class="form-control disabled_class" name="cost_memo" id="cost_memo" rows="4" placeholder="例：自由に入力">{{ $cost_list->cost_memo }}</textarea>
                                                        <div class="cost-tab invalid-feedback" id ="cost_memo_error">
                                                        </div>
                                                    </div>
                                
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
                                                            @foreach($cost_img_type_list as $cost_img_type)
                                                                <option value="{{$cost_img_type->cost_img_type_id}}">{{ $cost_img_type->cost_img_type_name }}</option>
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
                                                    @if(count($cost_img_list) > 0)
                                                        <div class="col-12 col-md-12 col-lg-12 mt-4">

                                                            <!-- タイトル -->
                                                            <i class="fas fa-file icon_blue me-2"></i>付属書類
                                                            <hr class="hr_album">

                                                            <div class="row">
                                                                
                                                                @foreach($cost_img_list as $imgs)
                                                                    <div class="col-12 col-md-12 col-lg-4 mt-3 mb-2">
                                                                        <div class="card" style="min-height:25rem;">
                                                                            
                                                                            @php
                                                                                $file_type = explode('.', $imgs->cost_img_path)[1];
                                                                            @endphp

                                                                            <!-- ファイルタイプがPDFの場合 -->
                                                                            @if($file_type == 'pdf')
                                                                                <div class="pdf_icon_box">
                                                                                    <a href="storage/{{ $imgs->cost_img_path }}" target="_blank"><img src="./back/img/pdf_icon.jpeg" class="pdf_icon_size"></a>
                                                                                </div>
                                                                            <!-- ファイルタイプがPDF以外の場合 -->
                                                                            @else
                                                                                <img src="storage/{{ $imgs->cost_img_path }}" class="card-img-top">
                                                                            @endif
                                                                            
                                                                            <!-- カードボディ -->
                                                                            <div class="card-body">
                                                                                <ul class="list-group list-group-flush">
                                                                                    <li class="list-group-item">種別：{{ $imgs->cost_img_type_name }}</li>
                                                                                    <li class="list-group-item">備考：{{ $imgs->cost_img_memo }}</li>
                                                                                </ul>
                                                                            </div>
                                                                            <!-- カードボディ -->

                                                                            <!-- 削除ボタン -->
                                                                            @if(Session::get('permission_type_id') == 1)
                                                                                <div class="card-footer">
                                                                                    <span id="{{ $imgs->cost_img_id }}" class="btn_img_delete text_red float-end" style="cursor: hand; cursor:pointer;">削除</span>
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
                                                        <textarea class="form-control" name="question_contents" id="question_contents" rows="8" placeholder="例：内容を自由に入力">{{ $cost_list->question_contents }}</textarea>
                                                        <div class="other-tab invalid-feedback" id ="question_contents_error"></div>
                                                    </div>

                                                    <!-- 回答内容 -->
                                                    <div class="col-12 col-md-12 col-lg-6 mt-2">
                                                        <label class="label_any" for=""></label>回答内容
                                                        <textarea class="form-control disabled_class" name="answer_contents" id="answer_contents" rows="8" placeholder="例：内容を自由に入力">{{ $cost_list->answer_contents }}</textarea>
                                                        <div class="other-tab invalid-feedback" id ="answer_contents_error"></div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- 質問 --> 

                                            <!-- 一覧 -->
                                            <div class="tab-pane fade" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                                                <div class="row row-cols-2">
                                                    
                                                    <!-- テーブルcard -->
                                                    <div class="col-12 col-md-12 col-lg-12 mt-3">

                                                        <div class="card">

                                                            <!-- カードボディ -->
                                                            <div class="card-body">
                                                                <!-- スクロール -->
                                                                <div class="overflow-auto" style="height:27rem;">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-hover table-condensed">

                                                                            <!-- テーブルヘッド -->
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col" id="create_user_id" style="display:none">id</th>
                                                                                    <th><i class="bi bi-check2-square"></i></th>
                                                                                    <th scope="col" id="approval_id">承諾</th>
                                                                                    <th scope="col" id="credit_card_date">カード名</th>
                                                                                    <th scope="col" id="credit_card_date">勘定日</th>
                                                                                    <th scope="col" id="cost_account_id">勘定科目</th>
                                                                                    <th scope="col" id="credit_card_fee">金額</th>
                                                                                    <th scope="col" id="credit_card_summary">摘要</th>
                                                                                    <th scope="col" id="credit_card_memo">備考</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <!-- テーブルヘッド -->

                                                                            <!-- テーブルボディ -->
                                                                            <tbody>
                                                                                @foreach($credit_card_list as $credit_card_list)
                                                                                    <tr>
                                                                                        <td id="id_{{ $credit_card_list->credit_card_id }}" class="click_class" style="display:none"></td>
                                                                                        <td id="select_{{ $credit_card_list->credit_card_id }}" class="click_class"><input id="{{ $credit_card_list->cost_id }}" type="radio" class="align-middle" name="flexRadioDisabled"></td>
                                                                                        <td id="approvalId_{{ $credit_card_list->credit_card_id }}" class="click_class">@if($credit_card_list->approval_id !== 0) <i class="bi bi-check-lg"></i> @endif</td>
                                                                                        <td id="creditcardTypeId_{{ $credit_card_list->credit_card_id }}" class="click_class">{{ $credit_card_list->credit_card_type_name }}</td>
                                                                                        <td id="creditcardDate_{{ $credit_card_list->credit_card_id }}" class="click_class">{{ $credit_card_list->credit_card_date }}</td>
                                                                                        <td id="creditcardAccount_{{ $credit_card_list->credit_card_id }}" class="click_class">{{ $credit_card_list->cost_account_name }}</td>
                                                                                        <td id="creditcardFee_{{ $credit_card_list->credit_card_id }}" class="click_class">{{ $credit_card_list->credit_card_fee }}</td>
                                                                                        <td id="creditcardSummary_{{ $credit_card_list->credit_card_id }}" class="click_class">{{ $credit_card_list->credit_card_summary }}</td>
                                                                                        <td id="creditcardMemo_{{ $credit_card_list->credit_card_id }}" class="click_class">{{ $credit_card_list->credit_card_memo }}</td>
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

                                                        <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                            <button type="button" id="btn_csv_capture" class="btn btn-outline-primary float-end btn-default disabled_class" data-bs-toggle="modal" data-bs-target="#csvModal">CSV取込</button>
                                                        </div>

                                                    </div>
                                                    <!-- テーブルcard -->

                                                </div>
                                            </div>
                                            <!-- 一覧 --> 

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
                                                <button id="btn_edit" class="btn btn-outline-primary btn-default">登録</button>

                                            </div>
                                        </div>
                                        <!-- 登録、帳票 -->

                                    </div>     
                                    <!-- ボタン -->

                                    <!-- 経費id -->
                                    <input type="hidden" name="cost_id" id="cost_id" value="{{ $cost_list->cost_id }}">
                                    
                                    <!-- 権限id -->
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

        <!-- csvインポートのモーダル -->
        <div class="modal fade" id="csvModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <!-- ヘッダー -->
                    <div class="modal-header">
                        <div class="modal-title info_title" id="exampleModalLabel">
                            <i class="fas fa-file-csv icon_blue me-2"></i>CSV取込
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- ボディ -->
                    <div class="modal-body px-4">
                        <form id="modalForm" class="needs-validation" novalidate>

                            <div class="col-12 col-md-6 col-lg-12">
                                <div class="row">

                                    <!-- クレジットカードタイプ -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-1">
                                        <label class="" for="textBox"></label>CSV形式
                                        
                                        <select class="form-select" name="modal_credit_card_format_type_id" id="modal_credit_card_format_type_id" required>
                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                            <option></option>
                                            @foreach($credit_card_format_type_list as $credit_card_format_types)
                                                <option value="{{ $credit_card_format_types->credit_card_format_type_id }}">{{ $credit_card_format_types->credit_card_format_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id ="modal_credit_card_format_type_id_error">
                                            CSV形式は必須です。
                                        </div>
                                    </div>
                                    <!-- 照会口座 -->

                                    <!-- ファイル -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-1 mb-3">
                                        <label class="mb-2">アップロード</label>
                                        <input class="form-control" type="file" id="modal_credit_card_file" required>
                                        <!-- エラーメッセージ -->
                                        <div class="invalid-feedback" id ="modal_credit_card_file_error">
                                            ファイルは必須です。
                                        </div>
                                    </div>
                                    <!-- ファイル -->

                                </div>  
                            </div>

                        </form>
                    </div>
                    <!-- ボディ -->

                    <!-- フッター -->
                    <div class="modal-footer">

                        <div class="col my-3">

                            <!-- 戻る -->
                            <button type="button" id="btn_modal_csv_back" class="btn btn-outline-primary btn-default" data-bs-dismiss="modal">戻る</button>

                            <!-- 取込 -->
                            <button type="button" id="btn_modal_csv_import" class="btn btn-outline-primary btn-default float-end">CSV取込</button>
                            
                        </div>

                    </div>
                    <!-- フッター -->
                    
                </div>
            </div>
        </div>
        <!-- csvインポートのモーダル -->

        <!-- クレジット詳細編集画面 -->
        <div class="modal fade" id="csvModalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">

                <div class="modal-content">
                    <!-- ヘッダー -->
                    <div class="modal-header">
                        <div class="modal-title info_title" id="exampleModalLabel">
                            <i class="bi bi-credit-card icon_blue me-2"></i>クレジットカード詳細
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- ボディ -->
                    <div class="modal-body px-4">
                        <form id="creditCardModalForm" class="needs-validation" novalidate>

                            <div class="col-12 col-md-12 col-lg-12">
                                <div class="row g-3">

                                    <!-- クレジットカード名 -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-3">
                                        <label class="label_required mb-2" for="textBox"></label>クレジットカード名
                                        <select class="form-select disabled_class" name="modal_credit_card_type_id" id="modal_credit_card_type_id" required>
                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                            <option></option>
                                            @foreach($credit_card_type_list as $credit_card_type)
                                                <option value="{{ $credit_card_type->credit_card_type_id }}">{{ $credit_card_type->credit_card_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id =credit_card_type_id_error">
                                            クレジットカード名は必須です。
                                        </div>
                                    </div>
                                    <!-- クレジットカード名 -->

                                    <!-- 勘定日 -->
                                    <div class="col-6 col-md-6 col-lg-6 mt-3">
                                        <label class="label_required mb-2" for=""></label>勘定日
                                        <input type="text" class="form-control disabled_class" id="modal_credit_card_date" name="modal_credit_card_date" autocomplete="off" value="" required>
                                        <div class="invalid-feedback" id ="credit_card_date_error">
                                            勘定日は必須です。
                                        </div>
                                    </div>
                                

                                    <!-- 勘定科目 -->
                                    <div class="col-12 col-md-12 col-lg-6 mt-3">
                                        <label class="label_required mb-2" for="textBox"></label>勘定科目
                                        <select class="form-select disabled_class" name="modal_credit_card_account_id" id="modal_credit_card_account_id" required>
                                            <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                            <option></option>
                                            @foreach($cost_account_list as $cost_account)
                                                <option value="{{ $cost_account->cost_account_id }}">{{ $cost_account->cost_account_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id = credit_card_account_id_error">
                                            勘定科目は必須です。
                                        </div>
                                    </div>
                                    <!-- 勘定科目 -->

                                    <!-- 金額 -->
                                    <div class="col-6 col-md-6 col-lg-6 mt-3">
                                        <label class="label_required mb-2" for=""></label>金額
                                        <input type="text" class="form-control disabled_class" id="modal_credit_card_fee" name="modal_credit_card_fee" autocomplete="off" value="" style="text-align:right" required>
                                        <div class="invalid-feedback" id ="credit_card_fee_error">
                                            金額は必須です。
                                        </div>
                                    </div>

                                    <!-- 摘要 -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-3">
                                        <label class="label_required mb-2" for=""></label>摘要
                                        <textarea class="form-control disabled_class" name="modal_credit_card_summary" id="modal_credit_card_summary" rows="4" placeholder="例：内容を自由に入力">{{ $cost_list->answer_contents }}</textarea>
                                        <div class="invalid-feedback" id ="credit_card_summary_error"></div>
                                    </div>

                                    <!-- 備考 -->
                                    <div class="col-12 col-md-12 col-lg-12 mt-3 mb-3">
                                        <label class="label_any mb-2" for=""></label>備考
                                        <textarea class="form-control" name="modal_credit_card_memo" id="modal_credit_card_memo" rows="4" placeholder="例：内容を自由に入力">{{ $cost_list->answer_contents }}</textarea>
                                        <div class="invalid-feedback" id ="credit_card_memo_error"></div>
                                    </div>
                                    
                                </div>  
                            </div>

                        </form>
                    </div>
                    <!-- ボディ -->
                    <!-- フッター -->
                    <div class="modal-footer">

                        <div class="col my-3">

                            <!-- 戻る -->
                            <button type="button" id="btn_modal_csv_back" class="btn btn-outline-primary btn-default" data-bs-dismiss="modal">戻る</button>

                            <!-- 取込 -->
                            <button type="button" id="btn_modal_credit_card_edit" class="btn btn-outline-primary btn-default float-end">登録</button>
                            
                            <!-- id -->
                            <input type="hidden" class="form-control" id="modal_credit_card_id" name="modal_credit_card_id">

                        </div>

                    </div>
                    <!-- フッター -->
                </div>
            </div>
        </div>
        <!-- クレジット詳細編集画面 -->



		@component('component.backJs')
		@endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_cost_edit.js') }}"></script>

        <script>

            // 契約者生年月日
            $('#account_date').datepicker({
                language:'ja'
            });

            // 勘定日(モーダル)
            $('#modal_credit_card_date').datepicker({
                language:'ja'
            });

        </script>

	</body>
	
</html>