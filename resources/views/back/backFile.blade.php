<!DOCTYPE html>
<html lang="ja">

	<head>
		<title>ファイル一覧/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_file.css') }}">  
		
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
                                        <i class="far fa-gem icon_blue me-2"></i>ファイル一覧
                                    </div>
                                    <!-- 境界線 -->
                                    <hr>
                                </div>
                            </div>
                            <!-- タイトル -->
                            
                            <div class="row">
                                <form action="backInformationInit" method="post">
                                    {{ csrf_field() }}
                                    <div class="col-sm-12">
                                        <div class="card border border-0">
                                            <div class="row align-items-end">

                                                <!-- フリーワード -->
                                                <div class="col-7 col-md-8 col-lg-4">
                                                    <label for="">フリーワード</label>
                                                    <input type="text" class="form-control" name="free_word" id="free_word" value="{{ $free_word }}">
                                                </div>
                                                
                                                <!-- 検索ボタン -->
                                                <div class="col-12 col-md-12 col-lg-8 mt-2">
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
                        <div class="col-12 col-md-12 col-lg-12 mt-3">

                            <div class="card">
                        
                                <!-- カードボディ -->
                                <div class="card-body">
                                    <!-- スクロール -->
                                    <div class="overflow-auto" style="height:35rem;">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-condensed">
                                                <!-- テーブルヘッド -->
                                                <thead>
                                                    <tr>
                                                        <th scope="col" id="create_user_id" style="display:none">id</th>
                                                        <th><i class="bi bi-check2-square"></i></th>
                                                        <th scope="col" id="legal_place_name">タイトル</th>
                                                        <th scope="col" id="legal_place_post_number">種別</th>
                                                        <th scope="col" id="legal_place_address">内容</th>
                                                        <th scope="col" id="legal_place_address">登録・更新日</th>
                                                    </tr>
                                                </thead>

                                                <!-- テーブルボディ -->
                                                <tbody>
                                                    @foreach($res as $information_list)
                                                        <tr>
                                                            <td id="id_{{ $information_list->information_id }}" class="click_class" style="display:none"></td>
                                                            <td id="cb_{{ $information_list->information_id }}" class="click_class"><input id="{{ $information_list->information_id }}" type="radio" class="align-middle" name="flexRadioDisabled"></td>
                                                            <td id="title_{{ $information_list->information_id }}" class="click_class">{{ $information_list->information_name }}</td>
                                                            <td id="type_{{ $information_list->information_id }}" class="click_class">{{ $information_list->information_type_name }}</td>
                                                            <td id="contents_{{ $information_list->information_id }}" class="click_class">{{ $information_list->information_contents }}</td>
                                                            <td id="entryDate_{{ $information_list->information_id }}" class="click_class">{{ $information_list->entry_date }}</td>
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
                                {{ $res->appends($paginate_params)->links() }}
                            </div>

                        </div>
                        <!-- テーブルcard -->

                        <!-- ボタン -->
                        <div class="col-sm-12 mt-3">
                            <div class="card border border-0">
                                <!-- row -->
                                <div class="row">
                                    <!-- 新規、編集 -->
                                    <div class="col-12">
                                        <div class="btn-group float-end" role="group">
                                            <button type="button" id="btn_new" class="btn btn-outline-primary float-end btn-default">新規登録</button>
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

                <!-- 新着情報編集画面 -->
                <div class="modal fade" id="informaitonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">

                            <!-- ヘッダー -->
                            <div class="modal-header">

                                <div class="modal-title info_title" id="exampleModalLabel">
                                    <i class="far fa-gem icon_blue me-2"></i>新着情報詳細
                                </div>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>

                            <!-- ボディ -->
                            <div class="modal-body">
                                <form id="modalForm" class="needs-validation" novalidate>

                                    <div class="col-12 col-md-6 col-lg-12 mb-3">
                                        <div class="row">

                                            <div class="col-12 col-md-6 col-lg-12">
                                                <label class="col-form-label">タイトル</label>
                                                <input type="text" class="form-control was-validated" id="information_title" required>
                                                <div class="invalid-feedback" id ="information_title_error">
                                                    タイトルは必須です。
                                                </div>
                                            </div>
                                            
                                            <!-- 種別 -->
                                            <div class="col-12 col-md-12 col-lg-4 mt-3">
                                                <label class="mb-2">種別</label>
                                                <select class="form-select" name="information_type" id="information_type" required>
                                                    <option selected></option>
                                                    @foreach($information_type_list as $information_types)
                                                        <option value="{{ $information_types->information_type_id }}">{{ $information_types->information_type_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id ="information_type_error">
                                                    種別は必須です。
                                                </div>
                                            </div>

                                            <!-- 内容 -->
                                            <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                <label for="">内容</label>
                                                <textarea class="form-control" name="information_contents" id="information_contents" rows="10" placeholder="例：自由に入力" required></textarea>
                                                <div class="invalid-feedback" id ="information_contents_error">
                                                    内容は必須です。
                                                </div>
                                            </div>
                                            
                                            <!-- id -->
                                            <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                <input type="hidden" class="form-control" id="information_id">
                                            </div>

                                        </div>  
                                    </div>

                                </form>
                            </div>
                            <!-- ボディ -->

                            <!-- フッター -->
                            <div class="modal-footer">

                                <div class="col my-3">

                                    <div class="btn-group" role="group">
                                        <button type="button" id="btn_modal_back" class="btn btn-outline-primary btn-default" data-bs-dismiss="modal">戻る</button>
                                        <button type="button" id="btn_modal_delete" class="btn btn-outline-danger btn-default">削除</button>
                                    </div>

                                    <!-- 登録 -->
                                    <button type="button" id="btn_modal_edit" class="btn btn-outline-primary btn-default float-end">登録</button>
                                </div>

                            </div>
                            <!-- フッター -->
                            
                        </div>
                    </div>
                </div>
                <!-- 新着情報編集画面 -->

            </main>
            <!-- page-content" -->

		</div>
		<!-- page-wrapper -->
        
        <!-- js -->
        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_file.js') }}"></script>
	</body>
	
</html>