<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->to('login');
});

Route::get('/open-approvals/{customerId}', 'OpenApprovalController@landingUserIndex')->name('guest.open-approvals');

Route::prefix('dashboard')
	->middleware(['auth', 'verified'])
	->group(function () {
		Route::get('/', function () {
			if (Auth::user()->isAdmin()) {
				return redirect()->route('admin.overview.index');
			} else {
				return redirect()->route('user.loan-product.index');
			}
		})->name('dashboard.index');

		Route::prefix('my-information')
			->group(function () {
				Route::get('/', 'UserController@userEdit')->name('user.edit');
				Route::put('/', 'UserController@userUpdate')->name('user.update');
				Route::put('/update-email/{id}', 'UserController@updateEmail')->name('user.update-email');
				Route::put('/update-password', 'UserController@userUpdatePassword')->name('user.update-password');
				Route::get('/notify-new-email', 'UserController@userNotifyNewEmail')->name('user.notify-new-email');
				Route::get('/verify-new-email/{id}/{hash}', 'UserController@userVerifyNewEmail')->middleware('signed')->name('user.verify-new-email');
			});

		Route::name('user.')
			->middleware('role:user')
			->group(function () {
				Route::get('/loan-products', 'LoanProductController@userIndex')->name('loan-product.index');
				Route::get('/open-approvals', 'OpenApprovalController@userIndex')->name('open-approval.index');
				Route::get('/loans', 'LoanController@userIndex')->name('loan.index');
				Route::get('/loan/{id}/contract-documents', 'LoanController@downloadContractDocuments')->name('download-contract-documents');
				Route::get('/documents', 'DocumentController@userIndex')->name('document.index');

				Route::post('/loan-request', 'LoanRequestController@userStore')->name('loan-request.store');
				Route::post('/loan', 'LoanController@userStore')->name('loan.store');
				Route::get('/document/{id}', 'DocumentController@download')->name('document.download');
				Route::post('/document', 'DocumentController@userStore')->name('document.store');
				Route::put('/document/{id}', 'DocumentController@userUpdate')->name('document.update');
				Route::delete('/document/{id}', 'DocumentController@userDestroy')->name('document.destroy');

				Route::put('/notification/{id}', 'NotificationController@userUpdate')->name('notification.update');
			});

		Route::prefix('admin')
			->name('admin.')
			->middleware('role:admin')
			->group(function () {
				Route::get('/overview', 'OverviewController@index')->name('overview.index');

				Route::get('/loan-request', 'LoanRequestController@index')->name('loan-request.index');
				Route::get('/loan-request/{id}/edit', 'LoanRequestController@form')->name('loan-request.edit');
				Route::put('/loan-request/{id}', 'LoanRequestController@update')->name('loan-request.update');
				Route::delete('/loan-request/{id}', 'LoanRequestController@destroy')->name('loan-request.destroy');
				Route::post('/loan-request/{id}/notify', 'LoanRequestController@notify')->name('loan-request.notify');
				Route::post('/loan-request/{id}/restore', 'LoanRequestController@restore')->name('loan-request.restore');

				Route::get('/open-approval', 'OpenApprovalController@index')->name('open-approval.index');
				Route::get('/open-approval/{id}/edit', 'OpenApprovalController@form')->name('open-approval.edit');
				Route::put('/open-approval/{id}', 'OpenApprovalController@update')->name('open-approval.update');
				Route::get('/open-approval/create', 'OpenApprovalController@form')->name('open-approval.create');
				Route::post('/open-approval', 'OpenApprovalController@store')->name('open-approval.store');
				Route::delete('/open-approval/{id}', 'OpenApprovalController@destroy')->name('open-approval.destroy');
				Route::post('/open-approval/{id}/notify', 'OpenApprovalController@notify')->name('open-approval.notify');
				Route::post('/open-approval/{id}/restore', 'OpenApprovalController@restore')->name('open-approval.restore');

				Route::get('/loan', 'LoanController@index')->name('loan.index');
				Route::get('/loan/{id}/edit', 'LoanController@form')->name('loan.edit');
				Route::put('/loan/{id}', 'LoanController@update')->name('loan.update');
				Route::get('/loan/{id}/contract-documents', 'LoanController@downloadContractDocuments')->name('loan.download-contract-documents');
				Route::get('/loan/{id}/contract-documents/delete', 'LoanController@deleteContractDocuments')->name('loan.delete-contract-documents');
				Route::delete('/loan/{id}', 'LoanController@destroy')->name('loan.destroy');
				Route::post('/loan/{id}/restore', 'LoanController@restore')->name('loan.restore');

				Route::get('/lender', 'LenderController@index')->name('lender.index');
				Route::get('/lender/{id}/edit', 'LenderController@form')->name('lender.edit');
				Route::put('/lender/{id}', 'LenderController@update')->name('lender.update');
				Route::get('/lender/create', 'LenderController@form')->name('lender.create');
				Route::post('/lender', 'LenderController@store')->name('lender.store');
				Route::delete('/lender/{id}', 'LenderController@destroy')->name('lender.destroy');
				Route::post('/lender/{id}/restore', 'LenderController@restore')->name('lender.restore');

				Route::get('/document', 'DocumentController@index')->name('document.index');
				Route::get('/document/{id}', 'DocumentController@download')->name('document.download');
				Route::get('/document/{id}/edit', 'DocumentController@form')->name('document.edit');
				Route::put('/document/{id}', 'DocumentController@update')->name('document.update');
				Route::delete('/document/{id}', 'DocumentController@destroy')->name('document.destroy');
				Route::post('/document/{id}/restore', 'DocumentController@restore')->name('document.restore');

				Route::get('/notification', 'NotificationController@index')->name('notification.index');
				Route::get('/notification/{id}/edit', 'NotificationController@form')->name('notification.edit');
				Route::put('/notification/{id}', 'NotificationController@update')->name('notification.update');
				Route::get('/notification/create', 'NotificationController@form')->name('notification.create');
				Route::post('/notification', 'NotificationController@store')->name('notification.store');
				Route::delete('/notification/{id}', 'NotificationController@destroy')->name('notification.destroy');
				Route::post('/notification/{id}/restore', 'NotificationController@restore')->name('notification.restore');

				Route::get('/user', 'UserController@index')->name('user.index');
				Route::get('/user/{id}', 'UserController@form')->name('user.edit');
				Route::put('/user/{id}', 'UserController@update')->name('user.update');
				Route::delete('/user/{id}', 'UserController@destroy')->name('user.destroy');
				Route::post('/user/{id}/restore', 'UserController@restore')->name('user.restore');
				Route::put('/update-email/{id}', 'UserController@updateEmail')->name('user.update-email');
				
				//Route::get('/user/{id}/changepagecount', 'UserController@changePageCount')->name('user.changepagecount');						
				// Previews of user pages visualization to admins.
				Route::get('/user/{id}/open-approval', 'OpenApprovalController@userIndex')->name('user.open-approval.index');
				Route::get('/user/{id}/loan', 'LoanController@userIndex')->name('user.loan.index');
				Route::get('/user/{id}/document', 'DocumentController@userIndex')->name('user.document.index');

				Route::middleware('role:manager')
					->group(function () {
						Route::get('/loan-group', 'LoanGroupController@index')->name('loan-group.index');
						Route::get('/loan-group/{id}/edit', 'LoanGroupController@form')->name('loan-group.edit');
						Route::put('/loan-group/{id}', 'LoanGroupController@update')->name('loan-group.update');
						Route::get('/loan-group/create', 'LoanGroupController@form')->name('loan-group.create');
						
						Route::post('/loan-group', 'LoanGroupController@store')->name('loan-group.store');
						Route::delete('/loan-group/{id}', 'LoanGroupController@destroy')->name('loan-group.destroy');

						Route::get('/loan-product', 'LoanProductController@index')->name('loan-product.index');
						Route::get('/loan-product/{id}/edit', 'LoanProductController@form')->name('loan-product.edit');
						Route::put('/loan-product/{id}', 'LoanProductController@update')->name('loan-product.update');

						Route::get('/document-group', 'DocumentGroupController@index')->name('document-group.index');
						Route::get('/document-group/{id}/edit', 'DocumentGroupController@form')->name('document-group.edit');
						Route::put('/document-group/{id}', 'DocumentGroupController@update')->name('document-group.update');
						Route::get('/document-group/create', 'DocumentGroupController@form')->name('document-group.create');
						Route::post('/document-group', 'DocumentGroupController@store')->name('document-group.store');
						Route::delete('/document-group/{id}', 'DocumentGroupController@destroy')->name('document-group.destroy');

						Route::get('/document-type', 'DocumentTypeController@index')->name('document-type.index');
						Route::get('/document-type/{id}/edit', 'DocumentTypeController@form')->name('document-type.edit');
						Route::put('/document-type/{id}', 'DocumentTypeController@update')->name('document-type.update');
						Route::get('/document-type/create', 'DocumentTypeController@form')->name('document-type.create');
						Route::post('/document-type', 'DocumentTypeController@store')->name('document-type.store');
						Route::delete('/document-type/{id}', 'DocumentTypeController@destroy')->name('document-type.destroy');

						Route::get('/document-set', 'DocumentSetController@index')->name('document-set.index');
						Route::get('/document-set/{id}/edit', 'DocumentSetController@form')->name('document-set.edit');
						Route::put('/document-set/{id}', 'DocumentSetController@update')->name('document-set.update');
						Route::get('/document-set/create', 'DocumentSetController@form')->name('document-set.create');
						Route::post('/document-set', 'DocumentSetController@store')->name('document-set.store');
						Route::delete('/document-set/{id}', 'DocumentSetController@destroy')->name('document-set.destroy');

						Route::get('/advisor', 'AdminController@index')->name('advisor.index');
						Route::get('/advisor/{id}/edit', 'AdminController@form')->name('advisor.edit');
						Route::put('/advisor/{id}', 'AdminController@update')->name('advisor.update');
						Route::get('/advisor/create', 'AdminController@form')->name('advisor.create');
						Route::post('/advisor', 'AdminController@store')->name('advisor.store');
						Route::delete('/advisor/{id}', 'AdminController@destroy')->name('advisor.destroy');
						Route::post('/advisor/{id}/restore', 'AdminController@restore')->name('advisor.restore');

						Route::get('/manager', 'AdminController@index')->name('manager.index');
						Route::get('/manager/{id}/edit', 'AdminController@form')->name('manager.edit');
						Route::put('/manager/{id}', 'AdminController@update')->name('manager.update');
						Route::get('/manager/create', 'AdminController@form')->name('manager.create');
						Route::post('/manager', 'AdminController@store')->name('manager.store');
						Route::delete('/manager/{id}', 'AdminController@destroy')->name('manager.destroy');
						Route::post('/manager/{id}/restore', 'AdminController@restore')->name('manager.restore');

						Route::get('/reports', 'ReportController@index')->name('report.index');
						Route::get('/reports/export', 'ReportController@export')->name('report.export');

						Route::get('/settings', 'SettingsController@form')->name('settings.edit');
						Route::put('/settings', 'SettingsController@update')->name('settings.update');
						Route::get('/del-list', 'DelListController@index')->name('del-list.index');
						Route::post('/loan-group/{id}/restore', 'LoanGroupController@restore')->name('loan-group.restore');						
						Route::post('/document-set/{id}/restore', 'DocumentSetController@restore')->name('document-set.restore');						
						Route::post('/document-group/{id}/restore', 'DocumentGroupController@restore')->name('document-group.restore');						
						Route::post('/document-type/{id}/restore', 'DocumentTypeController@restore')->name('document-type.restore');						
						Route::get('/del-list/getpage', 'DelListController@getPage')->name('del-list.getpage');
						//Route::get('/user/{id}/changepagecount', 'UserController@changePageCount')->name('user.changepagecount');						
						Route::get('/user/getlist', 'UserController@getList')->name('user.getlist');

					});
			});
	});

require __DIR__.'/auth.php';
