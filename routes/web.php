<?php

/**
 * ログイン
 */
// 表示
Route::get('/', 'Login\LoginController@loginInit');

// 登録
Route::post('loginEntry', 'Login\LoginController@loginEntry');

// ログアウト
Route::get('logOut', 'Common\LogOut\LogOutController@logOut');


/**
 * ホーム
 */
// home(表示)
Route::get('backHomeInit', 'Back\Home\BackHomeController@backHomeInit')->middleware("cost_auth");

/**
 * 銀行マスタ
 */
// 一覧画面表示
Route::any('backBankInit', 'Back\Bank\BackBankController@backBankInit')->middleware("cost_auth");

// 新規表示
Route::get('backBankNewInit', 'Back\Bank\BackBankController@backBankNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backBankEditInit', 'Back\Bank\BackBankController@backBankEditInit')->middleware("cost_auth");

// 新規・登録分岐)
Route::post('backBankEditEntry', 'Back\Bank\BackBankController@backBankEditEntry')->middleware("cost_auth");

// 削除
Route::post('backBankDeleteEntry', 'Back\Bank\BackBankController@backBankDeleteEntry')->middleware("cost_auth");

/**
 * 家主マスタ
 */
// 一覧画面表示
Route::any('backOwnerInit', 'Back\Owner\BackOwnerController@backOwnerInit')->middleware("cost_auth");

// 新規表示
Route::get('backOwnerNewInit', 'Back\Owner\BackOwnerController@backOwnerNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backOwnerEditInit', 'Back\Owner\BackOwnerController@backOwnerEditInit')->middleware("cost_auth");

// 新規・編集分岐
Route::post('backOwnerEditEntry', 'Back\Owner\BackOwnerController@backOwnerEditEntry')->middleware("cost_auth");

// 削除
Route::post('backOwnerDeleteEntry', 'Back\Owner\BackOwnerController@backOwnerDeleteEntry')->middleware("cost_auth");

/**
 * 不動産マスタ
 */
// 一覧画面表示
Route::any('backRealEstateInit', 'Back\RealEstate\BackRealEstateController@backRealEstateInit')->middleware("cost_auth");

// 新規表示
Route::get('backRealEstateNewInit', 'Back\RealEstate\BackRealEstateController@backRealEstateNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backRealEstateEditInit', 'Back\RealEstate\BackRealEstateController@backRealEstateEditInit')->middleware("cost_auth");

// 家主コンボボックス変更
Route::post('backOwnerNameChange', 'Back\RealEstate\BackRealEstateController@backOwnerNameChange')->middleware("cost_auth");

// 新規・編集分岐
Route::post('backRealEstateEditEntry', 'Back\RealEstate\BackRealEstateController@backRealEstateEditEntry')->middleware("cost_auth");

// 削除
Route::post('backRealEstateDeleteEntry', 'Back\RealEstate\BackRealEstateController@backRealEstateDeleteEntry')->middleware("cost_auth");

/**
 * 部屋マスタ
 */
// 一覧画面表示
Route::any('backRoomInit', 'Back\Room\BackRoomController@backRoomInit')->middleware("cost_auth");

// 新規表示
Route::get('backRoomNewInit', 'Back\Room\BackRoomController@backRoomNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backRoomEditInit', 'Back\Room\BackRoomController@backRoomEditInit')->middleware("cost_auth");

// 登録分岐
Route::post('backRoomEditEntry', 'Back\Room\BackRoomController@backRoomEditEntry')->middleware("cost_auth");

// 削除
Route::post('backRoomDeleteEntry', 'Back\Room\BackRoomController@backRoomDeleteEntry')->middleware("cost_auth");

/**
 * 売上管理
 */
// 一覧表示
Route::any('backProfitInit', 'Back\Profit\BackProfitController@backProfitInit')->middleware("cost_auth");

// 新規表示
Route::get('backProfitNewInit', 'Back\Profit\BackProfitController@backProfitNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backProfitEditInit', 'Back\Profit\BackProfitController@backProfitEditInit')->middleware("cost_auth");

// 不動産のコンボボックスを変更した場合の号室取得
Route::post('backRealEstateChangeInit', 'Back\Profit\BackProfitController@backRealEstateChangeInit')->middleware("cost_auth");

// 登録分岐（新規/編集）
Route::post('backProfitEditEntry', 'Back\Profit\BackProfitController@backProfitEditEntry')->middleware("cost_auth");

// 削除
Route::post('backProfitDeleteEntry', 'Back\Profit\BackProfitController@backProfitDeleteEntry')->middleware("cost_auth");

// 削除（詳細）
Route::post('backProfitDeleteEntryImgDetail', 'Back\Profit\BackProfitController@backProfitDeleteEntryImgDetail')->middleware("cost_auth");

// 承認の処理
Route::post('backProfitApprovalEntry', 'Back\Profit\BackProfitController@backProfitApprovalEntry')->middleware("cost_auth");

/**
 * 経費管理
 */
// 一覧表示
Route::any('backCostInit', 'Back\Cost\BackCostController@backCostInit')->middleware("cost_auth");

// 新規表示
Route::get('backCostNewInit', 'Back\Cost\BackCostController@backCostNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backCostEditInit', 'Back\Cost\BackCostController@backCostEditInit')->middleware("cost_auth");

// 登録分岐（新規/編集）
Route::post('backCostEditEntry', 'Back\Cost\BackCostController@backCostEditEntry')->middleware("cost_auth");

// 削除
Route::post('backCostDeleteEntry', 'Back\Cost\BackCostController@backCostDeleteEntry')->middleware("cost_auth");

// 削除：画像:詳細
Route::post('backDeleteEntryImgDetail', 'Back\Cost\BackCostController@backDeleteEntryImgDetail')->middleware("cost_auth");

// 承認の処理
Route::post('backCostApprovalEntry', 'Back\Cost\BackCostController@backCostApprovalEntry')->middleware("cost_auth");

/**
 * ユーザ一覧
 */
// 一覧表示
Route::any('backUserInit', 'Back\User\BackUserController@backUserInit')->middleware("cost_auth");

// 新規表示
Route::get('backUserNewInit', 'Back\User\BackUserController@backUserNewInit')->middleware("cost_auth");

// 編集表示
Route::get('backUserEditInit', 'Back\User\BackUserController@backUserEditInit')->middleware("cost_auth");

// 登録分岐（新規/編集）
Route::post('backUserEditEntry', 'Back\User\BackUserController@backUserEditEntry')->middleware("cost_auth");

// 削除
Route::post('backUserDeleteEntry', 'Back\User\BackUserController@backUserDeleteEntry')->middleware("cost_auth");

/**
 * CSV
 */
// 売上出力
Route::get('csvDownload', 'Common\Csv\CsvController@csvDownload')->middleware("cost_auth");

// 経費出力
Route::get('csvCostDownload', 'Common\Csv\CsvController@csvCostDownload')->middleware("cost_auth");

// 経費Import
Route::post('csvImport', 'Common\Csv\CsvController@csvImport')->middleware("cost_auth");

// message出力
Route::get('csvMessageExport', 'Common\Csv\CsvController@csvMessageExport')->middleware("cost_auth");
