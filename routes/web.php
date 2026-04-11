<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|---------------------------------------------------------------------------
| Loğoğlu Hukuk Bürosu — ön yüz route'ları
|---------------------------------------------------------------------------
|
| Tüm sayfalar Türkçe URL segmentleri ile tanımlanır (SEO ve kullanıcı
| dostu). Route model binding slug üzerinden çalışır çünkü Service,
| Article ve Page modelleri getRouteKeyName() = 'slug' döner.
|
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/hakkimizda', AboutController::class)->name('about');

Route::prefix('faaliyet-alanlari')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{service:slug}', [ServiceController::class, 'show'])->name('show');
});

Route::prefix('makaleler')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/{article:slug}', [ArticleController::class, 'show'])->name('show');
});

Route::get('/sss', FaqController::class)->name('faq');

Route::get('/iletisim', [ContactController::class, 'show'])->name('contact');
Route::post('/iletisim', [ContactController::class, 'store'])
    ->middleware('throttle:6,1') // dakikada 6 request ek koruma
    ->name('contact.store');

Route::get('/sayfa/{page:slug}', [PageController::class, 'show'])->name('page.show');

// SEO endpoint'leri
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
