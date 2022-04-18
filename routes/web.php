<?php

/**
 * ログイン
 */
// 表示
Route::get('/', 'Login\LoginController@loginInit');

// 登録
Route::post('loginEntry', 'Login\LoginController@loginEntry');

/**
 * back_home
 */
// home(表示)
// Route::get('backHomeInit', 'Back\Home\BackHomeController@backHomeInit')->middleware("kasegu_auth");
Route::get('backHomeInit', 'Back\Home\BackHomeController@backHomeInit');

/**
 * back_bank
 */
// 一覧画面表示
Route::get('backBankInit', 'Back\Bank\BackBankController@backBankInit');

// 新規表示
Route::get('backBankNewInit', 'Back\Bank\BackBankController@backBankNewInit');

// 編集表示
Route::get('backBankEditInit', 'Back\Bank\BackBankController@backBankEditInit');

// 新規・登録分岐)
Route::post('backBankEditEntry', 'Back\Bank\BackBankController@backBankEditEntry');

// 削除
Route::post('backBankDeleteEntry', 'Back\Bank\BackBankController@backBankDeleteEntry');