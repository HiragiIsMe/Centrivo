<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ServiceManagementController;
use App\Http\Controllers\Admin\UsersManagementConntroller;
use App\Http\Controllers\Admin\ServiceTransactionController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Sellers\DashboardController as SellersDashboardController;
use App\Http\Controllers\Sellers\LocationController;
use App\Http\Controllers\Sellers\ServiceController;
use App\Http\Controllers\Users\MarketController;
use App\Http\Controllers\Users\NegotiationController;
use App\Http\Controllers\Users\ChatController;
use App\Http\Controllers\Users\SearchController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\Users\TransactionController as UserTransactionController;
use App\Http\Controllers\Users\ReportController as UserReportController;
use App\Http\Controllers\Sellers\WalletController;
use App\Http\Controllers\Sellers\AdvertisementController as SellerAdController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Admin\AdvertisementController as AdminAdController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReportCenterController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Sellers\ReportController as SellerReportController;
use App\Http\Controllers\BannedController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('landing')->middleware('guest');

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

Route::get('/banned/{reportCode?}', [BannedController::class, 'show'])->name('banned.notice');

Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/register/user', [AuthController::class, 'showUserRegister'])->name('register.user');
Route::get('/register/seller', [AuthController::class, 'showSellerRegister'])->name('register.seller');

Route::post('/register/user', [AuthController::class, 'registerUser'])->name('register.user.post');
Route::post('/register/seller', [AuthController::class, 'registerSeller'])->name('register.seller.post');

Route::get('/activation/notice/{email}', function ($email) {
    return view('auth.activation-notice', compact('email'));
})->name('activation.notice');

Route::post('/activation/resend/{email}', [AuthController::class, 'resendActivation'])
    ->name('activation.resend');

Route::get('/activate/{token}', [AuthController::class, 'activate'])
    ->name('activate.account');

Route::middleware(['auth'])->group(function() {
    Route::post('/negotiation/initiate/{service}', [NegotiationController::class, 'initiate'])->name('negotiation.initiate');
    Route::get('/negotiation/{serviceRequest}', [NegotiationController::class, 'show'])->name('negotiation.show');
    Route::post('/negotiation/{serviceRequest}/message', [NegotiationController::class, 'sendMessage'])->name('negotiation.send');
    Route::get('/negotiation/{serviceRequest}/fetch', [NegotiationController::class, 'fetchMessages'])->name('negotiation.fetch');
    Route::delete('/negotiation/message/{message}', [NegotiationController::class, 'deleteMessage'])->name('negotiation.message.delete');
    Route::delete('/negotiation/{serviceRequest}', [NegotiationController::class, 'deleteConversation'])->name('negotiation.destroy');
});

Route::middleware(['auth', 'admin'])->group(function(){

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('admin.withdrawals');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
    
    Route::get('/users-management', [UsersManagementConntroller::class, 'index'])->name('users.management');
    Route::get('/users-management/{user}/reports', [UsersManagementConntroller::class, 'reports'])->name('users.reports');
    Route::post('/users-management/{user}/ban', [UsersManagementConntroller::class, 'ban'])->name('users.ban');
    Route::post('/users-management/{user}/unban', [UsersManagementConntroller::class, 'unban'])->name('users.unban');

    Route::get('/admin/report-center', [ReportCenterController::class, 'index'])->name('admin.report-center.index');
    Route::get('/admin/report-center/{report}', [ReportCenterController::class, 'show'])->name('admin.report-center.show');
    Route::post('/admin/report-center/{report}/status', [ReportCenterController::class, 'updateStatus'])->name('admin.report-center.status');
    Route::post('/admin/report-center/{report}/resolve', [ReportCenterController::class, 'markResolved'])->name('admin.report-center.resolve');
    Route::post('/admin/disputed-transactions/{transaction}/resolve', [ReportCenterController::class, 'resolveTransaction'])->name('admin.disputed-transactions.resolve');

    Route::get('/services-categories', [ServiceManagementController::class, 'index'])->name('admin.services.index');
    Route::get('/services-categories/{service}/reports', [ServiceManagementController::class, 'reports'])->name('admin.services.reports');
    Route::post('/services-categories/{service}/ban', [ServiceManagementController::class, 'ban'])->name('admin.services.ban');
    Route::post('/services-categories/{service}/unban', [ServiceManagementController::class, 'unban'])->name('admin.services.unban');

    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/service-transactions', [ServiceTransactionController::class, 'index'])->name('admin.service.transactions');

    Route::get('/advertisements', [AdminAdController::class, 'index'])->name('admin.ads.index');
    Route::post('/advertisements/packages', [AdminAdController::class, 'storePackage'])->name('admin.ads.store');
    Route::post('/advertisements/packages/{adPackage}/toggle', [AdminAdController::class, 'togglePackage'])->name('admin.ads.toggle');
    Route::delete('/advertisements/packages/{adPackage}', [AdminAdController::class, 'destroyPackage'])->name('admin.ads.destroy');

    Route::get('/admin/reports/export', [AdminReportController::class, 'exportExcel'])->name('admin.reports.export');

    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings/update', [AdminSettingsController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/admin/billboards', [AdminSettingsController::class, 'storeBillboard'])->name('admin.billboards.store');
    Route::post('/admin/billboards/{billboard}', [AdminSettingsController::class, 'updateBillboard'])->name('admin.billboards.update');
    Route::delete('/admin/billboards/{billboard}', [AdminSettingsController::class, 'destroyBillboard'])->name('admin.billboards.destroy');
    Route::post('/admin/billboards/{billboard}/toggle', [AdminSettingsController::class, 'toggleBillboard'])->name('admin.billboards.toggle');
    Route::get('/admin/seller-verifications', [\App\Http\Controllers\Admin\SellerVerificationController::class, 'index'])->name('admin.seller-verifications.index');
    Route::post('/admin/seller-verifications/{sellerProfile}/approve', [\App\Http\Controllers\Admin\SellerVerificationController::class, 'approve'])->name('admin.seller-verifications.approve');
    Route::post('/admin/seller-verifications/{sellerProfile}/reject', [\App\Http\Controllers\Admin\SellerVerificationController::class, 'reject'])->name('admin.seller-verifications.reject');

});

Route::middleware(['auth', 'sellers'])->group(function(){

    Route::get('/dashboard-sellers', [SellersDashboardController::class, 'index'])->name('sellers.dashboard');

    Route::get('/transactions-sellers', [\App\Http\Controllers\Sellers\TransactionController::class, 'index'])->name('sellers.transactions');

    Route::get('/wallet-sellers', [WalletController::class, 'index'])->name('seller.wallet');
    Route::post('/wallet-sellers/withdraw', [WalletController::class, 'withdraw'])->name('seller.wallet.withdraw');

    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::middleware('seller_verified')->group(function () {
        Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle');
        Route::delete('service-images/{image}', [ServiceController::class, 'destroyImage'])->name('service-images.destroy');
    });

    Route::resource('locations', LocationController::class);

    Route::get('/verify-identity', [\App\Http\Controllers\Sellers\KycController::class, 'show'])->name('seller.kyc.show');
    Route::post('/verify-identity', [\App\Http\Controllers\Sellers\KycController::class, 'submit'])->name('seller.kyc.submit');

    Route::get('/advertisements-sellers', [SellerAdController::class, 'index'])->name('seller.advertisements');
    Route::post('/advertisements-sellers/checkout', [SellerAdController::class, 'checkout'])->name('seller.advertisements.checkout');
    Route::get('/advertisements-sellers/pay/{advertisementTransaction}', [SellerAdController::class, 'pay'])->name('seller.advertisements.pay');

    Route::get('/reports-sellers', [SellerReportController::class, 'index'])->name('seller.reports.index');
    Route::get('/reports-sellers/export', [SellerReportController::class, 'exportExcel'])->name('seller.reports.export');

    Route::post('/seller/report-user', [\App\Http\Controllers\Sellers\TransactionController::class, 'reportUser'])->name('seller.report.user');
});

Route::middleware(['auth', 'users'])->group(function(){

    Route::get('/market', [MarketController::class, 'main'])->name('market');
    Route::get('/market/search', [SearchController::class, 'search'])->name('market.search');

    Route::get('/detail-market/{service}', [MarketController::class, 'detailmain'])->name('detail-market');
    
    Route::get('/checkout/{message}', [MarketController::class, 'checkoutUI'])->name('checkout.show');
    Route::post('/checkout/process/{message}', [UserTransactionController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/payment/{transaction}', [UserTransactionController::class, 'paymentPage'])->name('user.payment');
    Route::get('/user/transactions', [UserTransactionController::class, 'index'])->name('user.transactions');
    Route::post('/user/transactions/{transaction}/complete', [UserTransactionController::class, 'complete'])->name('user.transactions.complete');

    Route::get('/chats', [ChatController::class, 'index'])->name('user.chats');
    Route::delete('/chats/{serviceRequest}', [ChatController::class, 'destroyConversation'])->name('user.chats.destroy');
    Route::delete('/chats/message/{message}', [ChatController::class, 'destroyMessage'])->name('user.chats.message.destroy');

    Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('user.settings');
    Route::post('/settings/location', [ProfileController::class, 'updateLocation'])->name('user.profile.location.update');

    Route::get('/user/reports', [\App\Http\Controllers\Users\ReportController::class, 'index'])->name('user.reports.index');
    Route::post('/user/report', [\App\Http\Controllers\Users\ReportController::class, 'store'])->name('user.report.store');
});



Route::post('/webhook/midtrans', [WebhookController::class, 'midtransCallback'])->name('webhook.midtrans');

