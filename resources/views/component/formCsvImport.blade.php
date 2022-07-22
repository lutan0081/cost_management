<!-- CSV読込 -->
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

                            <!-- 対象口座 -->
                            <div class="col-12 col-md-12 col-lg-12 mt-1">
                                <label class="" for="textBox"></label>対象口座
                                
                                <select class="form-select" name="modal_bank_id" id="modal_bank_id" required>
                                    <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                    <option></option>
                                    @foreach($bank_list as $bank)
                                        <option value="{{ $bank->bank_id }}">{{ $bank->bank_name. '_'. $bank->bank_number }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id ="modal_bank_id_error">
                                    対象口座は必須です。
                                </div>
                            </div>
                            <!-- 照会口座 -->

                            <!-- CSV形式 -->
                            <div class="col-12 col-md-12 col-lg-6 mt-1">
                                <label class="" for="textBox"></label>CSV形式
                                
                                <select class="form-select" name="modal_bank_format_type_id" id="modal_bank_format_type_id" required>
                                    <!-- タグ内に値を追加、値追加後同一の場合選択する -->
                                    <option></option>
                                
                                </select>
                                <div class="invalid-feedback" id ="modal_bank_format_type_id_error">
                                    CSV形式は必須です。
                                </div>
                            </div>
                            <!-- CSV形式 -->

                            <!-- ファイル -->
                            <div class="col-12 col-md-12 col-lg-12 mt-1 mb-3">
                                <label class="mb-2">アップロード</label>
                                <input class="form-control" type="file" id="modal_img_file" required>
                                <!-- エラーメッセージ -->
                                <div class="invalid-feedback" id ="modal_img_file_error">
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
<!-- CSV読込 -->