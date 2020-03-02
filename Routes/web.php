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

use Modules\Invoices\Events\InvoiceCompleted;

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.', 'middleware' => ['admin', 'registerActivity']], function () {

    /** Items */
    Route::get('items', 'ItemsController@index')->name('items');
    Route::post('items', 'ItemsController@store')->name('items.store');
    Route::delete('items/{id}', 'ItemsController@destroy')->name('items.destroy');
    Route::post('items/update', 'ItemsController@update')->name('items.update');
    Route::post('items/get-data', 'ItemsController@getData')->name('items.getData');

    /** Invoice Items */
    Route::post('invoice-tems/get-data', 'ItemsController@getInvoiceItemData')->name('invoiceItems.getData');
    Route::post('invoice-tems/update', 'ItemsController@updateInvoiceItem')->name('invoiceItems.update');

    /** Faturas */
    Route::get('invoices/overdue', 'InvoicesController@overdueInvoices')->name('invoice.overdue');
    Route::get('invoices/receipt/{id}/show', 'InvoicesController@getReceipt')->name('invoice.receipt');
    Route::get('invoices/preview/{id}/show', 'InvoicesController@getPdfPreview')->name('invoice.getPdfPreview');
    Route::get('invoices/preview/{id}', 'InvoicesController@getPdfDownload')->name('invoice.getPdfDownload');
    Route::post('invoice-get-reference', 'InvoicesController@getNextInvoiceID')->name('invoice.nextId');
    Route::post('invoice-remove-item', 'InvoicesController@removeInvoiceItem')->name('invoice.removeItem');
    Route::post('invoice/{id}/add-item', 'InvoicesController@addInvoiceItem')->name('invoice.addItem');
    Route::post('invoice-add-payment', 'InvoicesController@addPayment')->name('invoice.addPayment');
    Route::post('invoice-remove-payment', 'InvoicesController@removePayment')->name('invoice.removePayment');
    Route::post('invoice-send-receipt/{id}', 'InvoicesController@sendReceipt')->name('invoice.sendReceipt');
    Route::post('invoice-send-payment-receipt', 'InvoicesController@sendPayment')->name('invoice.sendPayment');
    Route::post('invoice-send-invoice/{invoice}', 'InvoicesController@sendInvoice')->name('invoice.sendInvoice');
    Route::get('invoices/filter/{status}/{period}', 'InvoicesController@filter')->name('invoice.filter');
    Route::post('invoice/{invoice}/uploadnf', 'InvoicesController@uploadNf')->name('invoice.uploadNf');
    Route::resource('invoices', 'InvoicesController');



});

Route::group(['prefix' => 'conta', 'namespace' => 'Web', 'as' => 'web.'], function () {

    /** Link temporário de acesso a fatura, login automático. */
    Route::get('showTemporaryInvoice/{user}/{invoice}', 'InvoicesController@showTemporaryInvoice')->name('temporaryInvoice');

    Route::group(['middleware' => ['auth', 'registerActivity']], function () {
        Route::get('invoice/{media}/downloadnf', 'InvoicesController@downloadnf')->name('invoice.downloadnf');
        /** Faturas */
        Route::get('faturas', 'InvoicesController@index')->name('dashboard.invoices.index');
        Route::get('faturas/atrasadas', 'InvoicesController@overdue')->name('dashboard.invoices.overdue');
        Route::get('faturas/{invoice}', 'InvoicesController@show')->name('dashboard.showInvoice');
        Route::get('faturas/{invoice}/pdf', 'InvoicesController@getPdfPreview')->name('dashboard.invoice.getPdfPreview');
        Route::get('faturas/{invoice}/download', 'InvoicesController@getPdfDownload')->name('dashboard.invoice.getPdfDownload');
        Route::get('faturas/{invoice}/comprovante', 'InvoicesController@getReceipt')->name('dashboard.invoice.getReceipt');
    });
});
