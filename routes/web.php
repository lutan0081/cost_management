<?php

/**
 * ログイン
 */
// 表示
Route::get('/', 'Login\LoginController@loginInit');

// 登録
Route::post('loginEntry', 'Login\LoginController@loginEntry');

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
Route::post('backBankDeleteEntry', 'Back\Profit\BackBankController@backBankDeleteEntry')->middleware("cost_auth");

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