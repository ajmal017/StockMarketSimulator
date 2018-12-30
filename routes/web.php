<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PagesController@index')->name('myhome');
Route::get('/getlastevents/{last_updated}', 'PagesController@get_last_events')->name('getlastevents');

Route::get('/getAllConfig', 'ConfigController@getAllConfig')->name('getallconfig');
/**
Route::get('/', function () {
    return view('pages.home')->withTitle('');
})->name('myhome');
**/
Route::get('/logout', 'Auth\LoginController@logout');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/showtrademarket/{market?}', 'MarketController@showmarket')->name('showtrademarket');


Route::get('/showmarket/{market?}', 'MarketController@showmarket')->name('showmarket');
Route::get('/marketinfo/{market}/{type?}', 'MarketController@get_info')->name('getmarketinfo');
Route::get('/get_fullcode_prefix', 'MarketController@get_fullcode_prefix')->name('getfullcodeprefix');

Route::get('/account/{id}', 'AccountController@showmyaccount')->name('myaccount');

Route::get('/admin', 'AdminController@showadmin')->name('showadmin');

Route::post('/globalconfig', 'AdminController@setglobal')->name('globalconfig');
Route::get('/admin/update_admin_cache', 'AdminController@update_admin_cache')->name('update_admin_cache');

Route::get('/admin/showmarkets', 'AdminController@showmarkets')->name('admin_show_markets');
Route::get('/admin/addmarketform', 'AdminController@showaddmarketform')->name('admin_show_add_market_form');
Route::post('/admin/addmarket', 'AdminController@addmarket')->name('addmarket');
Route::get('/admin/editmarketform/{market_index}', 'AdminController@showeditmarketform')->name('admin_show_edit_market_form');
Route::post('/admin/editmarket/{id}', 'AdminController@editmarket')->name('editmarket');

Route::get('/admin/showindice', 'AdminController@showindice')->name('admin_show_indice');
Route::get('/admin/addindexform', 'AdminController@showaddindexform')->name('admin_show_add_index_form');
Route::post('/admin/addindex', 'AdminController@addindex')->name('addindex');
Route::get('/admin/editindexform/{index_index}', 'AdminController@showeditindexform')->name('admin_show_edit_index_form');
Route::post('/admin/editindex/{id}', 'AdminController@editindex')->name('editindex');

Route::get('/admin/showcurrency', 'AdminController@showcurrency')->name('admin_show_currency');
Route::get('/admin/addcurrencyform', 'AdminController@showaddcurrencyform')->name('admin_show_add_currency_form');
Route::post('/admin/addcurrency', 'AdminController@addcurrency')->name('addcurrency');
Route::get('/admin/editcurrencyform/{currency_index}', 'AdminController@showeditcurrencyform')->name('admin_show_edit_currency_form');
Route::post('/admin/editcurrency/{id}', 'AdminController@editcurrency')->name('editcurrency');

Route::get('/admin/showinvestors', 'AdminController@showinvestors')->name('showinvestors');
Route::post('/admin/editinvestor/{iid}', 'AdminController@editinvestor')->name('editinvestor');
Route::get('/admin/baninvestor/{iid}', 'AdminController@baninvestor')->name('baninvestor');
Route::post('/admin/editshare/{sid}', 'AdminController@editshare')->name('editshare');
Route::post('/admin/forcesell/{sid}', 'AdminController@forcesell')->name('forcesell');

Route::get('/admin/showprofile/{iid}', 'AdminController@showprofile')->name('showprofile');
Route::get('/admin/showshares/{iid}', 'AdminController@showshares')->name('showshares');

Route::get('/admin/manageevents/{filter?}', 'AdminController@showevents')->name('showevents');
Route::get('/admin/grabevents/{filter}/{orderby}', 'AdminController@ajax_showevents')->name('grabevents');
Route::post('/admin/delevents', 'AdminController@ajax_delevents')->name('delevents');

Route::get('/myprofile', 'ProfileController@myprofile')->name('myprofile');
Route::get('/avator/{iid}/{name_encode}/{size?}', 'ProfileController@showavator')->name('showavator');
Route::post('/uploadavator', 'ProfileController@uploadavator')->name('uploadavator');

Route::get('/myaccount', 'AccountController@myaccount')->name('myaccount');
Route::get('/exchange/{cid}', 'AccountController@exchange')->name('exchange');
Route::get('/getMyInfo', 'AccountController@getMyInfo')->name('getMyInfo');
Route::get('/getQueryHeadByPlatform', 'AccountController@getQueryHeadByPlatform')->name('getQueryHeadByPlatform');

Route::get('/showtradeform/{thismarket}/{code}', 'TransactionController@showtradeform')->name('showtradeform');
Route::get('/searchshares/{location}/{key_word}', 'TransactionController@searchshares')->name('searchshares');
//Route::get('/searchshares/{type}/{key_word}', 'TransactionController@searchshares')->name('searchshares');
Route::get('/getshareinfo/{thismarket}/{code}', 'TransactionController@ajax_getshare')->name('getshareinfo');
Route::get('/getopenmarkets', 'TransactionController@get_openmarkets')->name('getopenmarkets');
Route::post('/tradeshare/{tradetype}', 'TransactionController@tradeshare')->name('tradeshare');

/************************************************************************************************************/
//Route::get('/update_market_cache/{market?}', 'MarketController@update_market_cache');
Route::get('/update_market_all_cache', 'MarketController@update_market_all_cache');

Route::get('/update_news_cache', 'NewsController@update_news_cache');

Route::get('/activate_all_shares', 'UpdateShareController@activate_all_shares');
Route::get('/sync_time', 'UpdateShareController@sync_time');
Route::get('/initialize_us_share', 'UpdateShareController@initialize_us_share');

Route::get('/correct_us_shares_for_market', 'MaintanenceController@correct_us_shares_market');

Route::get('/getrank', 'RankController@getrank');
Route::get('/update_all_active_shares', 'RankController@update_all_active_shares');

Route::get('/test_add_active_shares', 'RankController@test_add_active_shares');

Route::get('/test_MarketService', 'RankController@test_add_active_shares');
