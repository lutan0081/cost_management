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
                                <form action="backFileInit" method="post">
                                    {{ csrf_field() }}
                                    <div class="col-sm-12">
                                        <div class="card border border-0">
                                            <div class="row align-items-end">

                                                <!-- フリーワード -->
                                                <div class="col-12 col-md-8 col-lg-4">
                                                    <label for="">フリーワード</label>
                                                    <input type="text" class="form-control" name="free_word" id="free_word" value="{{ $free_word }}">
                                                </div>

                                                <!-- 区分 -->
                                                <div class="col-12 col-md-12 col-lg-3 mt-2">
                                                    <label class="mb-2">区分</label>
                                                    <select class="form-select" name="file_type" id="file_type">
                                                        <option selected></option>
                                                        @foreach($file_type_list as $file_types)
                                                            <option value="{{ $file_types->file_type_id }}" @if( $file_type == $file_types->file_type_id) selected @endif>{{ $file_types->file_type_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 区分 -->
                                                
                                                <!-- 検索ボタン -->
                                                <div class="col-12 col-md-12 col-lg-5 mt-2">
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
                                                        <th scope="col" id="legal_place_address">種別</th>
                                                        <th scope="col" id="legal_place_post_number">区分</th>
                                                        <th scope="col" id="legal_place_name">ファイル名</th>
                                                        <th scope="col" id="legal_place_address">備考</th>
                                                        <th scope="col" id="legal_place_address">登録・更新日</th>
                                                    </tr>
                                                </thead>

                                                <!-- テーブルボディ -->
                                                <tbody>
                                                    @foreach($res as $file)
                                                        <tr>
                                                            <td id="id_{{ $file->file_id }}" class="click_class" style="display:none"></td>
                                                            <td id="cb_{{ $file->file_id }}" class="click_class"><input id="{{ $file->file_id }}" type="radio" class="align-middle" name="flexRadioDisabled"></td>
                                                            
                                                            @switch($file->file_extension)
                                                                @case('jpg')
                                                                    <td id="extension_{{ $file->file_id }}" class="click_class file_icon_green"><svg width="1.5rem" height="1.5rem" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-filetype-jpg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-4.34 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.507.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.24v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.066-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.347.158.48.275.133.117.238.253.314.407ZM0 14.786c0 .164.027.319.082.465.055.147.136.277.243.39.11.113.245.202.407.267.164.062.354.093.569.093.42 0 .748-.115.984-.345.238-.23.358-.566.358-1.005v-2.725h-.791v2.745c0 .202-.046.357-.138.466-.092.11-.233.164-.422.164a.499.499 0 0 1-.454-.246.577.577 0 0 1-.073-.27H0Zm4.92-2.86H3.322v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475.108-.201.161-.427.161-.677 0-.25-.052-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.546 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H4.11v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Z"/></svg></td>
                                                                    @break
                                                                @case('png')
                                                                    <td id="extension_{{ $file->file_id }}" class="click_class file_icon_blue"><svg xmlns="http://www.w3.org/2000/svg" width="1.5rem" height="1.5rem" fill="currentColor" class="bi bi-filetype-png" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3.76 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.506.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.82v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.067-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.348.158.48.275.133.117.238.253.314.407Zm-8.64-.706H0v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H.788v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Zm1.964 2.666V13.25h.032l1.761 2.675h.656v-3.999h-.75v2.66h-.032l-1.752-2.66h-.662v4h.747Z"/></svg></td>
                                                                    @break
                                                                @case('pdf')
                                                                    <td id="extension_{{ $file->file_id }}" class="click_class file_icon_red"><svg xmlns="http://www.w3.org/2000/svg" width="1.5rem" height="1.5rem" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z"/></svg></td>
                                                                    @break
                                                                @default
                                                                    <!-- 判定したい変数に0 1 2意外が格納されていた時の処理 -->
                                                            @endswitch
                                                            
                                                            <td id="id_{{ $file->file_id }}" class="click_class">{{ $file->file_type_name }}</td>
                                                            <td id="id_{{ $file->file_id }}" class="click_class">{{ $file->file_name }}</td>
                                                            <td id="id_{{ $file->file_id }}" class="click_class">{{ $file->file_memo }}</td>
                                                            <td id="id_{{ $file->file_id }}" class="click_class">{{ $file->entry_date }}</td>
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

                                    <!-- 権限id -->
                                    <input type="hidden" name="permission_type_id" id="permission_type_id" value="{{ Session::get('permission_type_id') }}">
                                
                                </div>
                                <!-- row -->
                            </div>
                        </div>
                        <!-- ボタン -->

                    </div>
                </div>
                <!-- 一覧 --> 

                <!-- ファイル詳細 -->
                <div class="modal fade" id="fileEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">

                            <!-- ヘッダー -->
                            <div class="modal-header">

                                <div class="modal-title info_title" id="exampleModalLabel">
                                    <i class="far fa-gem icon_blue me-2"></i>ファイル詳細
                                </div>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>

                            <!-- ボディ -->
                            <div class="modal-body">
                                <form id="modalForm" class="needs-validation" novalidate>

                                    <div class="col-12 col-md-6 col-lg-12 mb-3">
                                        <div class="row">

                                            <div class="col-12 col-md-6 col-lg-12">
                                                <label class="col-form-label">ファイル名</label>
                                                <input type="text" class="form-control was-validated" id="file_name" required>
                                                <div class="invalid-feedback" id ="file_name_error">
                                                    ファイル名は必須です。
                                                </div>
                                            </div>
                                            
                                            <!-- 種別 -->
                                            <div class="col-12 col-md-12 col-lg-4 mt-2">
                                                <label class="mb-2">区分</label>
                                                <select class="form-select" name="file_type_id" id="file_type_id" required>
                                                    <option selected></option>
                                                    @foreach($file_type_list as $file_type)
                                                        <option value="{{ $file_type->file_type_id }}">{{ $file_type->file_type_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id ="file_type_id_error">
                                                    区分は必須です。
                                                </div>
                                            </div>

                                            <!-- ファイル -->
                                            <div class="col-12 col-md-12 col-lg-12 mt-2">
                                                <label class="mb-2">アップロード</label>
                                                <input class="form-control" type="file" id="file_upload" required>
                                                <!-- エラーメッセージ -->
                                                <div class="invalid-feedback" id ="file_upload_error">
                                                    ファイルは必須です。
                                                </div>
                                            </div>
                                            <!-- ファイル -->

                                            <!-- 内容 -->
                                            <div class="col-12 col-md-12 col-lg-12 mt-2 mb-3">
                                                <label for="">内容</label>
                                                <textarea class="form-control" name="file_memo" id="file_memo" rows="3" placeholder="例：自由に入力" required></textarea>
                                                <div class="invalid-feedback" id ="file_memo_error">
                                                    内容は必須です。
                                                </div>
                                            </div>

                                            <!-- 画像 -->
                                            <div class="col-12 col-md-12 col-lg-12 mt-2 mb-3">
                                                <div id="file_box" class="pdf_icon_box">

                                                </div>
                                            </div>

                                            <!-- id -->
                                            <div class="col-12 col-md-12 col-lg-12 mt-3">
                                                <input type="hidden" class="form-control" id="file_id">
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
                                        <button type="button" id="btn_modal_delete" class="btn btn-outline-danger btn-default disabled_class">削除</button>
                                    </div>

                                    <!-- 登録 -->
                                    <button type="button" id="btn_modal_edit" class="btn btn-outline-primary btn-default float-end disabled_class">登録</button>                                
                                </div>

                            </div>
                            <!-- フッター -->
                            
                        </div>
                    </div>
                </div>
                <!-- ファイル詳細 -->

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