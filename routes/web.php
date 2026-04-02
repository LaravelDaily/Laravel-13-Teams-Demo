<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamSettingsController;
use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', 'team.member'])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('posts', PostController::class)->except(['show']);

        Route::post('leave', [TeamController::class, 'leave'])->name('team.leave');

        Route::middleware('team.member:admin')->group(function () {
            Route::get('settings', [TeamSettingsController::class, 'edit'])->name('team.settings');
            Route::put('settings', [TeamSettingsController::class, 'update'])->name('team.settings.update');
            Route::post('settings/avatar', [TeamSettingsController::class, 'updateAvatar'])->name('team.settings.avatar');
        });

        Route::middleware('team.member:owner')->group(function () {
            Route::delete('delete', [TeamController::class, 'destroy'])->name('team.destroy');
        });
    });

Route::get('join/{code}', function (string $code) {
    $invitation = TeamInvitation::where('code', $code)
        ->whereNull('accepted_at')
        ->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        })
        ->firstOrFail();

    if (! auth()->check()) {
        session(['pending_invitation' => $code]);

        return redirect()->route('register');
    }

    return redirect()->route('invitations.accept', $invitation);
})->name('teams.join');

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
