<?php
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::redirect('/', '/lobbies');
Route::resource('lobbies', LobbyController::class);
Route::post('/lobbies/getGameInfo', [LobbyController::class, 'getGameInfo'])->name('lobbies.getGameInfo');
Route::post('/lobbies/playerLeave', [LobbyController::class, 'playerLeave'])->name('lobbies.playerLeave');
Route::post('/lobbies/updateGameInfo', [LobbyController::class, 'updateGameInfo'])->name('lobbies.updateGameInfo');

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
