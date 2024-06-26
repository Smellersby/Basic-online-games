<?php
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\LanguageController;

Route::redirect('/', '/lobbies');
Route::resource('lobbies', LobbyController::class);
Route::post('/lobbies/getGameInfo', [LobbyController::class, 'getGameInfo'])->name('lobbies.getGameInfo');
Route::post('/lobbies/playerLeave', [LobbyController::class, 'playerLeave'])->name('lobbies.playerLeave');
Route::post('/lobbies/updateGameInfo', [LobbyController::class, 'updateGameInfo'])->name('lobbies.updateGameInfo');
Route::post('/lobbies/getGameInfoSnake', [LobbyController::class, 'getGameInfoSnake'])->name('lobbies.getGameInfoSnake');
Route::post('/lobbies/updateSnake1', [LobbyController::class, 'updateSnake1'])->name('lobbies.updateSnake1');
Route::post('/lobbies/updateSnake2', [LobbyController::class, 'updateSnake2'])->name('lobbies.updateSnake2');
Route::post('/lobbies/updateStatus', [LobbyController::class, 'updateStatus'])->name('lobbies.updateStatus');
Route::post('/lobbies/sendFood', [LobbyController::class, 'sendFood'])->name('lobbies.sendFood');


Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
});

URL::forceScheme('https');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
