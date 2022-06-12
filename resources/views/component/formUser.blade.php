<!-- 入力フォーム -->
<div class="col-12 col-md-12 col-lg-12 mb-3">

    <div class="row row-cols-2">

        <!-- ユーザ名 -->
        <div class="col-12 col-md-10 col-lg-6 mt-3">
            <label class="label_required mb-2" for="textBox"></label>ユーザ名
            <input type="text" class="form-control" name="create_user_name" id="create_user_name" placeholder="例：長谷　亘" value="{{ $create_user_list->create_user_name }}" required>
            <!-- エラーメッセージ -->
            <div class="invalid-feedback" id ="create_user_name_error">
                ユーザ名は必須です。
            </div>
        </div>

        <div class="w-100"></div>

        <!-- パスワード -->
        <div class="col-12 col-md-10 col-lg-3 mt-3">
            <label class="label_required mb-2" for="textBox"></label>ユーザID
            <input type="text" class="form-control" name="create_user_mail" id="create_user_mail" placeholder="例：lutan0081" value="{{ $create_user_list->create_user_mail }}" required>
            <!-- エラーメッセージ -->
            <div class="invalid-feedback" id ="create_user_mail_error">
                ユーザIDは必須です。
            </div>
        </div>

        <div class="w-100"></div>

        <div class="col-12 col-md-10 col-lg-3 mt-3">
            <label class="label_required mb-2" for="textBox"></label>パスワード
            <input type="text" class="form-control" name="create_user_password" id="create_user_password" placeholder="例：lutan0081" value="{{ $create_user_list->create_user_password }}" required>
            <!-- エラーメッセージ -->
            <div class="invalid-feedback" id ="create_user_password_error">
                パスワードは必須です。
            </div>
        </div>

        <div class="w-100"></div>

        <!-- 権限 -->
        <div class="col-6 col-md-6 col-lg-3 mt-3 mb-4">
            <label class="label_required mb-2" for="textBox"></label>権限
            <select class="form-select " name="permission_type_id" id="permission_type_id" @if($create_user_list->permission_type_id !== 1) disabled @endif required>
                <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                <option></option>
                @foreach($permission_type_list as $permission_type)
                    <option value="{{ $permission_type->permission_type_id }}" @if($permission_type->permission_type_id == $create_user_list->permission_type_id) selected @endif>{{ $permission_type->permission_type_name }}</option>
                @endforeach
            </select>
            
            <div class="invalid-feedback" id ="permission_type_id_error">
                権限は必須です。
            </div>
        </div>

        <div class="col-12 col-md-10 col-lg-5 mt-3 d-flex align-items-center">
            <label class="mb-2 pink_line" for="textBox"><i class="bi bi-megaphone icon_blue me-2 "></i>システム管理者：全操作可/一般ユーザ：操作規制有</label>
        </div>
        
    </div>

</div>

<!-- 境界線 -->
<hr>