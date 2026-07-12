<?php

use App\Http\Controllers\Admin\AdminModerationController;
use App\Http\Controllers\Admin\ResourceManagementController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Creator\CreatorDashboardController;
use App\Http\Controllers\Creator\ResourceUpdateController;
use App\Http\Controllers\CreatorRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceCommentController;
use App\Http\Controllers\ResourceRatingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceReviewController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/resources/{resource:slug}', [CatalogController::class, 'show'])->name('resources.show');
Route::get('/resources/{resource:slug}/download', [CatalogController::class, 'download'])->name('resources.download');
Route::post('/resources/{resource:slug}/track-time', [CatalogController::class, 'trackTime'])->name('resources.trackTime');
Route::get('/creators/{user:username}', [CatalogController::class, 'creatorProfile'])->name('creators.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/services/{service:slug}/contact', [ServiceController::class, 'contact'])->name('services.contact');

Route::view('/legal/tos', 'legal.tos')->name('legal.tos');

Route::get('/dashboard', [CreatorDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/creator-request', [CreatorRequestController::class, 'store'])->name('creator-request.store');

    Route::post('/resources/{resource:slug}/ratings', [ResourceRatingController::class, 'store'])->name('resources.ratings.store');
    Route::delete('/resources/{resource:slug}/ratings/{rating}', [ResourceRatingController::class, 'destroy'])->name('resources.ratings.destroy');

    Route::post('/resources/{resource:slug}/comments', [ResourceCommentController::class, 'store'])->name('resources.comments.store');
    Route::delete('/resources/{resource:slug}/comments/{comment}', [ResourceCommentController::class, 'destroy'])->name('resources.comments.destroy');

    Route::post('/services/{service:slug}/reviews', [ServiceReviewController::class, 'store'])->name('services.reviews.store');
    Route::delete('/services/{service:slug}/reviews/{review}', [ServiceReviewController::class, 'destroy'])->name('services.reviews.destroy');
});

Route::middleware(['auth'])->prefix('creator')->name('creator.')->group(function () {
    // Any authenticated user can submit a resource — trusted roles are
    // published immediately, everyone else goes to moderation (see
    // CreatorDashboardController@store).
    Route::get('/resources/create', [CreatorDashboardController::class, 'create'])->name('resources.create');
    Route::post('/resources', [CreatorDashboardController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}/edit', [CreatorDashboardController::class, 'edit'])->name('resources.edit');
    Route::put('/resources/{resource}', [CreatorDashboardController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}', [CreatorDashboardController::class, 'destroy'])->name('resources.destroy');

    Route::post('/resources/{resource}/updates', [ResourceUpdateController::class, 'store'])->name('resources.updates.store');
    Route::delete('/resources/{resource}/updates/{update}', [ResourceUpdateController::class, 'destroy'])->name('resources.updates.destroy');

    // Analytics covers any authenticated user's own resources now, not just
    // creators. Profile/avatar/bio/social links live at /profile for everyone
    // (see ProfileController) — there is no separate creator settings page.
    Route::get('/analytics', [CreatorDashboardController::class, 'analytics'])->name('analytics');

    // Freelance services remain a certified-creator-only feature.
    Route::middleware('role:creator')->group(function () {
        Route::get('/services', [ServiceController::class, 'myServices'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/moderation', [AdminModerationController::class, 'index'])->name('moderation.index');
    Route::patch('/moderation/{resource}', [AdminModerationController::class, 'updateStatus'])->name('moderation.update');
    Route::post('/creators/{user}/verify', [AdminModerationController::class, 'verifyCreator'])->name('creators.verify');

    Route::get('/creator-requests', [AdminModerationController::class, 'creatorRequests'])->name('creator-requests.index');
    Route::patch('/creator-requests/{creatorRequest}', [AdminModerationController::class, 'updateCreatorRequestStatus'])->name('creator-requests.update');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/ban', [UserManagementController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [UserManagementController::class, 'unban'])->name('users.unban');

    Route::get('/resources', [ResourceManagementController::class, 'index'])->name('resources.index');

    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
});

require __DIR__.'/auth.php';
