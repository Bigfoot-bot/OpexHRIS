<?php
use Illuminate\Support\Facades\Route;
Route::post('/lang', function() {
    $locale = request('locale', 'en');
    if (in_array($locale, ['en', 'sw'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch')->middleware('web');

