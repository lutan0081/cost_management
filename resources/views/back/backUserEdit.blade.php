<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>ユーザ詳細/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_user_edit.css') }}">  
		
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

            <!-- 入力項目 -->
            <div class="container mt-3">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">

                        <form id="editForm" class="needs-validation" novalidate>

                            <div class="info_title mt-2">
                                <i class="bi bi-gear-fill icon_blue me-2"></i>ユーザ詳細
                            </div>

                            <!-- 境界線 -->
                            <hr>

                            <!-- カード -->
                            <div class="card border border-0">

                                @include('component.formUser')

                                <!-- ボタン -->
                                <div class="row row-cols-2 mb-5">

                                    <!-- 削除・付与 -->
                                    <div class="col-6 col-md-6 col-lg-6 mt-3">
                                        <div class="btn-group" role="group">
                                            <button id="btn_delete" class="btn btn-outline-danger btn-default">削除</button>
                                            <button id="btn_set_authority" class="btn btn-outline-primary btn-default">権限付与</button>
                                        </div>
                                    </div>
                                    
                                    <!-- 登録、帳票 -->
                                    <div class="col-6 col-md-6 col-lg-6 mt-3">
                                        <!-- 契約詳細id='':帳票ボタン非表示 -->
                                        <button id="btn_edit" class="btn btn-outline-primary btn-default float-end">登録</button>
                                    </div>

                                </div>     
                                <!-- ボタン -->

                            <!-- id -->
                            <input type="hidden" name="create_user_id" id="create_user_id" value="{{ $create_user_list->create_user_id }}">

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
		<script src="{{ asset('back/js/back_user_edit.js') }}"></script>
	</body>
	
</html>