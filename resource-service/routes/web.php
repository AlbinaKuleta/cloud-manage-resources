<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\LoginPage;
use App\Livewire\ResourceList;
use App\Livewire\ResourceCreate;
use App\Livewire\ResourceEdit;

Route::get('/login', LoginPage::class)->name('login');

Route::middleware('auth.token')->group(function () {
    Route::get('/resources', ResourceList::class);
    Route::get('/resources/create', ResourceCreate::class);
    Route::get('/resources/{id}/edit', ResourceEdit::class);
});
