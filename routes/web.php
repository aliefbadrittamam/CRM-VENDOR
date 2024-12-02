<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
   return view('welcome');
})->name('welcome');

Route::view('/contact', 'contact')->name('contact');

Route::middleware([
   'auth:sanctum',
   config('jetstream.auth_session'),
   'verified',
])->group(function () {
   // Dashboard
   Route::get('/dashboard', function () {
       return view('dashboard');
   })->name('dashboard');

   // Users Management
   Route::prefix('users')->group(function () {
       Route::get('/', [UserController::class, 'index'])->name('users.index');
       Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
       Route::get('/access', [UserController::class, 'access'])->name('users.access');
   });

   // Customers
   Route::prefix('customers')->group(function () {
       Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
       Route::get('/interaction', [CustomerController::class, 'interaction'])->name('customers.interaction');
       Route::get('/segmentation', [CustomerController::class, 'segmentation'])->name('customers.segmentation');
   });

   // Sales
   Route::prefix('sales')->group(function () {
       Route::get('/quotation', [SaleController::class, 'quotation'])->name('sales.quotation');
       Route::get('/orders', [SaleController::class, 'orders'])->name('sales.orders');
       Route::get('/shipping', [SaleController::class, 'shipping'])->name('sales.shipping');
   });

   // Marketing
   Route::prefix('marketing')->group(function () {
       Route::get('/whatsapp', [MarketingController::class, 'whatsapp'])->name('marketing.whatsapp');
       Route::get('/leads', [MarketingController::class, 'leads'])->name('marketing.leads');
       Route::get('/analysis', [MarketingController::class, 'analysis'])->name('marketing.analysis');
   });

   // Products
   Route::prefix('products')->group(function () {
       Route::get('/catalog', [ProductController::class, 'catalog'])->name('products.catalog');
       Route::get('/categories', [ProductController::class, 'categories'])->name('products.categories');
       Route::get('/prices', [ProductController::class, 'prices'])->name('products.prices');
   });

   // Projects 
   Route::prefix('projects')->group(function () {
       Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
       Route::get('/timeline', [ProjectController::class, 'timeline'])->name('projects.timeline');
       Route::get('/status', [ProjectController::class, 'status'])->name('projects.status');
   });

   // Reports
   Route::prefix('reports')->group(function () {
       Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
       Route::get('/customers', [ReportController::class, 'customers'])->name('reports.customers');
       Route::get('/marketing', [ReportController::class, 'marketing'])->name('reports.marketing');
   });

   // Settings
   Route::prefix('settings')->group(function () {
       Route::get('/system', [SettingController::class, 'system'])->name('settings.system');
       Route::get('/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
       Route::get('/backup', [SettingController::class, 'backup'])->name('settings.backup');
   });
});