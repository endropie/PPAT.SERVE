<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->namespace('Api')->group(function() {

    Route::prefix('auth')->name('auth.')->group(function () {
        // Route::apiResource('admins', 'auth\adminApiController');
        // Route::apiResource('users', 'auth\userApiController');
        // Route::apiResource('roles', 'auth\RoleApiController');
        // Route::apiResource('permissions', 'auth\PermissionApiController');
    });

    Route::prefix('accounting')->name('.accounting')->group(function () {
        
        Route::apiResource('accounts', 'Accounting\Accounts');
        // Route::apiResource('account-type', 'Accounting\AccountTypes');
        // Route::apiResource('journals', 'Accounting\Journals');
        // Route::post('journals/import', 'Accounting\Journals@setImport');

        // Route::get('journal-entries', 'Accounting\Journals@entries');
        // Route::get('reports/ProfitLoss', 'Accounting\reports@viewProfitLoss');
        // Route::get('reports/BalanceSheet', 'Accounting\reports@viewBalanceSheet');
    });

    Route::prefix('common')->name('.common')->group(function () {
        Route::apiResource('items', 'Common\Items');
    });
    
    Route::prefix('incomes')->name('.incomes')->group(function () {
        Route::apiResource('customers', 'Incomes\Customers');
    });

    Route::prefix('references')->name('.references')->group(function () {
        Route::apiResource('units', 'References\Units');
        Route::apiResource('sizes', 'References\Sizes');
        Route::apiResource('brands', 'References\Brands');
        Route::apiResource('colors', 'References\Colors');
        Route::apiResource('ordertypes', 'References\OrderTypes');
        Route::apiResource('marketplaces', 'References\Marketplaces');
        Route::apiResource('specifications', 'References\Specifications');
    });
});