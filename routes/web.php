<?php

use App\Http\Controllers\{ProfileController,Controller,KeywordController};
use App\Http\Controllers\Admin\{RoleController,UserController};
use Illuminate\Support\Facades\Route;
use App\Models\Keyword;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function(){
    $keywords = Keyword::all();
    return view('dashboard', compact('keywords'));
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth','role:admin'])->name('admin.')->prefix('admin')->group(function(){
    Route::get('admin',[\App\Http\Controllers\Admin\indexController::class,'index'])->name('index');
    Route::resource('roles',\App\Http\Controllers\Admin\RoleController::class)->except('show');
    Route::post('/roles/{role}/permissions',[\App\Http\Controllers\Admin\RoleController::class,'givePermission'])->name('roles.permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])
        ->name('roles.permissions.revoke');
    Route::resource('permissions',\App\Http\Controllers\Admin\PermissionController::class)->except('show');
    Route::post('/permissions/{permission}/roles',[\App\Http\Controllers\Admin\PermissionController::class,'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}',[\App\Http\Controllers\Admin\PermissionController::class,'removeRole'])->name('permissions.roles.remove');
    Route::get('users',[\App\Http\Controllers\Admin\UserController::class,'index'])->name('users.index');
    Route::get('users/{user}',[\App\Http\Controllers\Admin\UserController::class,'show'])->name('users.show');
    Route::delete('users/{user}',[\App\Http\Controllers\Admin\UserController::class,'destroy'])->name('users.destroy');
    Route::post('/users/{user}/roles',[\App\Http\Controllers\Admin\UserController::class,'assignRole'])->name('users.roles');
    Route::delete('/users/{user}/roles/{role}',[\App\Http\Controllers\Admin\UserController::class,'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');
    Route::get('/settings',[\App\Http\Controllers\Admin\GoogleAnalyticsController::class,'configGoogleAnalytics'])->name('settings');

});


Route::middleware('auth')->group(function () {
    Route::get('/google_status/{AdminSetting}',[\App\Http\Controllers\Admin\GoogleAnalyticsController::class,'changeStatus'])->name('googleStatus');
    Route::get('/connect_google_auth',[\App\Http\Controllers\Admin\GoogleAnalyticsController::class,'googleConnect'])->name('googleConnect');
    Route::get('/google/callback',[\App\Http\Controllers\Admin\GoogleAnalyticsController::class,'callbackToGoogle'])->name('googleAuthCallback');
    Route::get('/keywords/{keyword}/analytics', [\App\Http\Controllers\GoogleAnalyticController::class, 'redirectToGoogle'])->name('keywords.analytics');
    Route::delete('/keywords/{keyword}', [KeywordController::class, 'destroy'])->name('keywords.destroy');
    Route::get('/keywords/{keyword}/edit', [KeywordController::class, 'edit'])->name('keywords.edit');
    Route::post('/keywords/{keyword}', [KeywordController::class, 'update'])->name('keywords.update');
    Route::get('/keywords/create', [KeywordController::class, 'create'])->name('keywords.create');
    Route::post('/keywords', [KeywordController::class, 'store'])->name('keywords.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
