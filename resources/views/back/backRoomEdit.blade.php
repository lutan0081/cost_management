<!DOCTYPE html>
<html lang="ja">

	<head>
        <title>部屋詳細/COSTS</title>

		<!-- head -->
        @component('component.backHead')
        @endcomponent

		<!-- 自作css -->
		<link rel="stylesheet" href="{{ asset('back/css/back_room_edit.css') }}">  
		
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
                    
                    <form id="editForm" class="needs-validation" novalidate>

                        <div class="info_title mt-3">
                            <i class="fas fa-key icon_blue me-2"></i>部屋詳細
                        </div>

                        <!-- 境界線 -->
                        <hr>

                        <!-- 物件名 -->
                        <div class="col-6 col-md-8 col-lg-5 mt-3">
                            <label class="label_required mb-2"></label>物件名
                            
                            <select class="form-select" name="real_estate_name" id="real_estate_name" required>
                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                <option></option>
                                @foreach($real_estate_list as $real_estates)
                                    <option value="{{ $real_estates->real_estate_id }}" @if($room_list->real_estate_id == $real_estates->real_estate_id) selected @endif>{{ $real_estates->real_estate_name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id ="real_estate_name_error">
                                物件名は必須です。
                            </div>  
                        </div>

                        <!-- 号室 -->
                        <div class="col-12 col-md-10 col-lg-3 mt-3">
                            <label class="label_required mb-2" for="textBox"></label>号室
                            <input type="text" class="form-control" name="roon_name" id="roon_name" placeholder="例：101" value="{{ $room_list->room_name }}" required>
                            <!-- エラーメッセージ -->
                            <div class="invalid-feedback" id ="roon_name_error">
                                号室は必須です。
                            </div>
                        </div>

                        <!-- 種別 -->
                        <div class="col-6 col-md-8 col-lg-3 mt-3 pb-2">
                            <label class="label_any mb-2"></label>種別
                            <select class="form-select" name="room_type_id" id="room_type_id">
                                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                <option></option>
                                @foreach($room_type_list as $room_types)
                                    <option value="{{ $room_types->room_type_id }}" @if($room_list->room_type_id == $room_types->room_type_id) selected @endif>{{ $room_types->room_type_name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id ="room_type_id_error">
                            </div>  
                        </div>

                        <!-- 専有面積 -->
                        <div class="col-12 col-md-12 col-lg-2 mt-3">
                            <label class="label_any mb-2" for="textBox"></label>契約面積
                            <!-- テキストボックスの右側に文字表示 -->
                            <div class="input-group">
                                <input type="number" class="form-control" name="room_size" id="room_size" value="" style="text-align:right" placeholder="例：60.00">
                                <span class="d-flex align-items-end ms-1">㎡</span>                                                      <!-- バリデーション -->
                                <div class="real_estate-tab invalid-feedback" id ="room_size_error">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 mt-4">
                            <hr>
                        </div>
        
                        <!-- ボタン -->
                        <div class="row row-cols-2 mb-5">

                            <!-- 削除 -->
                            <div class="col-6 col-md-6 col-lg-6 mt-3">
                                <button id="btn_delete" class="btn btn-outline-danger btn-default">削除</button>
                            </div>
                            
                            <!-- 登録 -->
                            <div class="col-6 col-md-6 col-lg-6 mt-3">
                                <button id="btn_edit" class="btn btn-outline-primary float-end btn-default">登録</button>
                            </div>

                        </div>     
                        <!-- ボタン -->

                        <!-- id -->
                        <input type="text" name="room_id" id="room_id" value="{{ $room_list->room_id }}">

                    </form>

                </div>
            </div>
            <!-- コンテンツ -->

		</main>
		<!-- page-content" -->

		</div>
		<!-- page-wrapper -->

        @component('component.backJs')
        @endcomponent

		<!-- 自作js -->
		<script src="{{ asset('back/js/back_room_edit.js') }}"></script>
	</body>
	
</html>