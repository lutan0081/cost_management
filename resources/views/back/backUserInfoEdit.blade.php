<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>ユーザ情報/COST</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_user_info_edit.css') }}">  
		
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
                                <i class="bi bi-gear-fill icon_blue me-2"></i>ユーザ情報
                            </div>

                            <!-- 境界線 -->
                            <hr>

                            <!-- カード -->
                            <div class="card border border-0">

                                @include('component.formUser')

                                <!-- ボタン -->
                                <div class="row row-cols-2 mb-5">

                                    <!-- 削除 -->
                                    <div class="col-6 col-md-6 col-lg-6 mt-3">
                                        <button id="btn_edit" class="btn btn-outline-primary btn-default float-start">ユーザ申請</button>
                                    </div>
                                    
                                    <!-- 登録、帳票 -->
                                    <div class="col-6 col-md-6 col-lg-6 mt-3">
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
		<script src="{{ asset('back/js/back_user_info_edit.js') }}"></script>
	</body>
	
</html>