<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\LoginPage;
use App\Livewire\ResourceList;
use App\Livewire\ResourceCreate;
use App\Livewire\ResourceEdit;
use App\Livewire\MetricsComponent;


Route::get('/login', LoginPage::class)->name('login');
Route::get('/metrics', function () {
    return view('metrics');
});

Route::middleware('auth.token')->group(function () {
    Route::get('/resources', ResourceList::class);
    Route::get('/resources/create', ResourceCreate::class);
    Route::get('/resources/{id}/edit', ResourceEdit::class);
    Route::get('/metrics', MetricsComponent::class)->name('metrics');


});
