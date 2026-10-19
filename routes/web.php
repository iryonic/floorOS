<?php

use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RbacController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PwaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal B: Public Customer Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/catalog', [CatalogController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/products/{product:slug}', [CatalogController::class, 'product'])->name('products.show');
Route::post('/enquiry', [CatalogController::class, 'enquirySubmit'])->name('enquiry.submit');
Route::get('/contact', [CatalogController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [CatalogController::class, 'privacyPolicy'])->name('privacyPolicy');

/*
|--------------------------------------------------------------------------
| Staff Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Portal A: Private Admin Management System Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Products & Categories
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/shades', [ProductController::class, 'storeShade'])->name('products.shades.store');
    Route::delete('/shades/{shade}', [ProductController::class, 'destroyShade'])->name('products.shades.destroy');
    Route::resource('categories', CategoryController::class);

    // Hero Banner Slides Management & Draggable Sorting
    Route::get('/hero-slides', [HeroSlideController::class, 'index'])->name('hero-slides.index');
    Route::post('/hero-slides', [HeroSlideController::class, 'store'])->name('hero-slides.store');
    Route::put('/hero-slides/{heroSlide}', [HeroSlideController::class, 'update'])->name('hero-slides.update');
    Route::delete('/hero-slides/{heroSlide}', [HeroSlideController::class, 'destroy'])->name('hero-slides.destroy');
    Route::match(['delete', 'post'], '/hero-slides/{heroSlide}/delete', [HeroSlideController::class, 'destroy'])->name('hero-slides.delete');
    Route::post('/hero-slides/reorder', [HeroSlideController::class, 'reorder'])->name('hero-slides.reorder');

    // Inventory & Stock
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('/inventory/adjust', [InventoryController::class, 'createAdjustment'])->name('inventory.adjust');
    Route::post('/inventory/adjust', [InventoryController::class, 'storeAdjustment'])->name('inventory.storeAdjust');

    // Accounting & Cash Flow
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::post('/accounting/credit', [AccountingController::class, 'storeCredit'])->name('accounting.credit.store');
    Route::post('/accounting/debit', [AccountingController::class, 'storeDebit'])->name('accounting.debit.store');
    Route::delete('/accounting/transactions/{transaction}', [AccountingController::class, 'destroyTransaction'])->name('accounting.transactions.destroy');
    Route::get('/accounting/profit-loss', [AccountingController::class, 'profitAndLoss'])->name('accounting.profit-loss');
    Route::get('/accounting/profit-and-loss', [AccountingController::class, 'profitAndLoss'])->name('accounting.profitLoss');
    Route::get('/accounting/journal', [AccountingController::class, 'journal'])->name('accounting.journal');

    // Customer Enquiries Workflow
    Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
    Route::put('/enquiries/{enquiry}/status', [EnquiryController::class, 'updateStatus'])->name('enquiries.status');
    Route::put('/enquiries/{enquiry}/update-status', [EnquiryController::class, 'updateStatus'])->name('enquiries.updateStatus');

    // Users & Roles (RBAC)
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::resource('rbac', RbacController::class);
    Route::post('/rbac/{role}/toggle-permission', [RbacController::class, 'togglePermission'])->name('rbac.togglePermission');

    // Settings & Appearance Variables
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
