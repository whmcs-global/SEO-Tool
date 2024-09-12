<?php

use App\Http\Controllers\{ProfileController, Controller, KeywordController, WebsiteController, LabelController, BacklinkController};
use App\Http\Controllers\Admin\{RoleController, UserController};
use Illuminate\Support\Facades\Route;
use App\Models\Keyword;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return redirect()->route('keywords.details');
})->name('home');

Route::get('/login/{id}', function ($id) {
    $user = User::find($id);

    if ($user) {
        Auth::login($user);
        return redirect()->route('home');
    }

    return redirect()->route('error.page')->with('error', 'User not found');
})->name('login.with.id');


Route::middleware(['auth', 'project_status'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('admin', [\App\Http\Controllers\Admin\indexController::class, 'index'])->name('index');

    // roles permission
    Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index')->middleware('permission:Role list');
    Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create')->middleware('permission:Create role');
    Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store')->middleware('permission:Create role');
    Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:Edit role');
    Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update')->middleware('permission:Edit role');
    Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:Delete role');
    Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'givePermission'])->name('roles.permissions')->middleware('permission:Edit role');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->except('show');
    Route::post('/permissions/{permission}/roles', [\App\Http\Controllers\Admin\PermissionController::class, 'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}', [\App\Http\Controllers\Admin\PermissionController::class, 'removeRole'])->name('permissions.roles.remove');

    // users
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->middleware(['permission:User list'])->name('users.index');
    Route::get('/user/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->middleware(['permission:Create user'])->name('user.create');
    Route::post('/user/store', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit{user}', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->middleware(['permission:Edit user'])->name('user.edit');
    Route::put('/user/update{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('user.update');
    Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->middleware(['permission:Delete user'])->name('users.destroy');
    Route::post('/users/{user}/roles', [\App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('users.roles');
    Route::delete('/users/{user}/roles/{role}', [\App\Http\Controllers\Admin\UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');
    Route::get('/settings', [\App\Http\Controllers\Admin\GoogleAnalyticsController::class, 'configGoogleAnalytics'])->middleware(['permission:Google API'])->name('settings');
    Route::delete('users/{user}/delete-and-transfer', [\App\Http\Controllers\Admin\UserController::class, 'deleteAndTransfer'])->name('admin.users.delete-and-transfer');

    // projects
    Route::get('/websites/create', [WebsiteController::class, 'create'])->middleware(['permission:Add New Project'])->name('websites.create');
    Route::post('/websites', [WebsiteController::class, 'store'])->middleware(['permission:Add New Project'])->name('websites.store');
    Route::delete('/websites/{website}', [WebsiteController::class, 'delete'])->middleware(['permission:Delete Project'])->name('websites.delete');
    Route::get('/websites/{id}/edit', [WebsiteController::class, 'edit'])->middleware(['permission:Edit Project'])->name('websites.edit');
    Route::put('/websites/{id}', [WebsiteController::class, 'update'])->middleware(['permission:Edit Project'])->name('websites.update');
    Route::get('/projects', [WebsiteController::class, 'projects'])->middleware(['permission:Project list'])->name('projects');
});


Route::middleware(['auth', 'project_status'])->group(function () {
    // chnage country
    Route::get('/country', [KeywordController::class, 'set_country'])->name('countries.set');
    Route::post('/labels', [LabelController::class, 'store'])->name('labels.store');

    // Website
    Route::get('/website', [WebsiteController::class, 'set_website_default'])->name('websites.default');
    Route::get('/website/{website}', [WebsiteController::class, 'set_website'])->name('websites.set');

    // Google Analytics
    Route::get('/google_status/{adminSetting}', [\App\Http\Controllers\Admin\GoogleAnalyticsController::class, 'changeStatus'])->name('googleStatus');
    Route::get('/connect_google_auth', [\App\Http\Controllers\Admin\GoogleAnalyticsController::class, 'googleConnect'])->name('googleConnect');
    Route::get('/google/callback', [\App\Http\Controllers\Admin\GoogleAnalyticsController::class, 'callbackToGoogle'])->name('googleAuthCallback');

    // Google Ads
    Route::get('/google_ads_status/{adminSetting}', [\App\Http\Controllers\Admin\GoogleAdsController::class, 'changeStatus'])->name('googleAdsStatus');
    Route::get('/connect_google_ads_auth', [\App\Http\Controllers\Admin\GoogleAdsController::class, 'googleAdsConnect'])->name('googleAdsConnect');
    Route::get('/google_ads/callback', [\App\Http\Controllers\Admin\GoogleAdsController::class, 'callbackToGoogle'])->name('googleAdsAuthCallback');

    // Keywords
    Route::get('/keyword_tracker', [KeywordController::class, 'dashboard'])->name('dashboard');
    Route::get('/keywords_detail', [KeywordController::class, 'keywords_detail'])->name('keywords.details');
    Route::get('/keywords/{keyword}/analytics', [\App\Http\Controllers\GoogleAnalyticController::class, 'redirectToGoogle'])->name('keywords.analytics');
    Route::delete('/keywords/{keyword}', [KeywordController::class, 'destroy'])->name('keywords.destroy');
    Route::get('/keywords/{keyword}/edit', [KeywordController::class, 'edit'])->name('keywords.edit');
    Route::post('/keywords/{keyword}', [KeywordController::class, 'update'])->name('keywords.update');
    Route::get('/keywords/create', [KeywordController::class, 'create'])->name('keywords.create');
    Route::post('/keywords', [KeywordController::class, 'store'])->name('keywords.store');
    Route::get('/keywords', [KeywordController::class, 'show'])->name('keywords.show');
    Route::get('/refresh_keyword_data', [WebsiteController::class, 'refresh_data'])->name('keywords.refresh');
    Route::get('/keyword_data', [KeywordController::class, 'keyword_data'])->name('keywords.data');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Backlinks
    // Route::get('backlinks/index', [BacklinkController::class, 'index'])->name('backlinks.index');
    Route::prefix('backlinks')->name('backlinks.')->controller(BacklinkController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::any('create/{id?}', 'storeOrUpdate')->name('create');
        Route::delete('delete/{id}', 'destroy')->name('delete');
        Route::get('list/{approve_status}', 'statusList')->name('status.list');
        Route::match(['get', 'post'], 'approve/{id?}', 'approve')->name('approve');
    });
});

Route::get('/error', function () {
    return view('error');
})->name('error.page');

require __DIR__ . '/auth.php';
