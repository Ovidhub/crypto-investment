<?php

use App\Http\Controllers\AutoTaskController;
use App\Http\Controllers\HomePageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
    // Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

//cron url
Route::get('/cron', [AutoTaskController::class, 'autotopup'])->name('cron');


Route::get('/cronwallet', function () {


    Artisan::call('schedule:run');
    return 'Cron executed';
});



//Front Pages Route
Route::get('/', [HomePageController::class, 'index'])->name('home');
Route::get('index', [HomePageController::class, 'index'])->name('home');
Route::get('terms', [HomePageController::class, 'terms'])->name('terms');
Route::get('privacy', [HomePageController::class, 'privacy'])->name('privacy');
Route::get('about', [HomePageController::class, 'about'])->name('about');
Route::get('contact', [HomePageController::class, 'contact'])->name('contact');
Route::get('faq', [HomePageController::class, 'faq'])->name('faq');
Route::get('statistic', [HomePageController::class, 'statistics'])->name('statistics');
Route::get('services', [HomePageController::class, 'investment'])->name('investment');
Route::get('verify',[HomePageController::class,'verify']);
Route::post('codeverify', [HomePageController::class, 'codeverify'])->name('codeverify');

Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'Cleared all caches';
});
