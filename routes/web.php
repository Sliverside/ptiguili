<?php

use App\Http\Controllers\WalletsController;
use App\Http\Controllers\GiftsBagsController;
use App\Http\Controllers\GiftsController;
use App\Http\Controllers\ProfileController;
use App\Notifications\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
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

// Route::get('/setup', function() {
//     Artisan::call("migrate:fresh", ["--force" => null]);
// });

Route::middleware('auth')->group(function () {
    Route::get('/send-register-mail', function () {
        Notification::route('mail', 'lea97@live.fr')->notify(new RegistrationRequest(Auth::user()));
    });

    Route::get('/', [GiftsController::class, 'index'])->name('gifts');

    Route::post('/wallet/use-coin', [WalletsController::class, 'useCoin'])->name('wallets.useCoin');

    Route::get('/gifts-bag', [GiftsBagsController::class, 'index'])->name('giftsBag');
    Route::patch('/gifts-bag/{giftsBag}', [GiftsBagsController::class, 'update'])->name('giftsBag.update');

    Route::post('/gifts', [GiftsController::class, 'store'])->name('gifts.store');
    // Route::get('/gifts/pending', [GiftsController::class, 'pending'])->name('gifts.pending');
    Route::get('/gifts/pending/{wonGift}', [GiftsController::class, 'pendingDetail'])->name('gifts.pendingDetail');
    Route::patch('/gifts/confirm/{wonGift}', [GiftsController::class, 'confirm'])->name('gifts.confirm');
    Route::post('/gifts/request/{gift}', [GiftsController::class, 'request'])->name('gifts.request');
    Route::get('/gifts/{gift}', [GiftsController::class, 'show'])->name('gifts.show');
    Route::patch('/gifts/{gift}', [GiftsController::class, 'update'])->name('gifts.update');
    Route::delete('/gifts/{gift}', [GiftsController::class, 'delete'])->name('gifts.delete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
