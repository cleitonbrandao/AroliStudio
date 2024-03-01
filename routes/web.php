<?php

use App\Http\Controllers\CostumerController;

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\HomeDashBoard;
use App\Livewire\Dashboard\IndexDashBoard;

use App\Livewire\Employee\HomeEmployee;
use App\Livewire\Employee\IndexEmployee;
use App\Livewire\Employee\RegisterEmployee;

use App\Livewire\Service\IndexService;
use App\Livewire\Service\HomeService;
use App\Livewire\Service\RegisterService;
use App\Livewire\Service\RegisterProduct;
use App\Livewire\Service\RegisterPackage;

use App\Livewire\Commercial\HomeCommercial;
use App\Livewire\Commercial\SummaryCommercial;
use App\Livewire\Commercial\Consumption;

use App\Livewire\Costumer\HomeCostumer;
use App\Livewire\Costumer\IndexCostumer;
use App\Livewire\Costumer\RegisterCostumer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::name('root.')->prefix('dashboard')->group(function () {
      Route::get('/', [HomeDashBoard::class, 'home'])->name('dashboard.home');
      Route::get('/index', [IndexDashBoard::class, 'index'])->name('dashboard.index');
    });

    Route::name('root.')->prefix('employee')->group(function () {
        Route::get('/', [HomeEmployee::class, 'home'])->name('employee');
        Route::get('/index', [IndexEmployee::class, 'index'])->name('employee.index');
    });

    Route::name('root.')->prefix('costumer')->group(function (){
        Route::get('/', [HomeCostumer::class, 'home'])->name('costumer');
        Route::get('/index', [IndexCostumer::class, 'index'])->name('costumer.index');
    });

    Route::name('root.')->prefix('service')->group(function () {
        Route::get('/', [HomeService::class, 'home'])->name('service');
        Route::get('/index', [IndexService::class, 'index'])->name('service.index');
    });

    Route::name('root.')->prefix('form')->group(function () {
        Route::get('/employee', [RegisterEmployee::class, 'form'])->name('form.employee');
        Route::get('/costumer', [RegisterCostumer::class, 'form'])->name('form.costumer');
        Route::get('/service', [RegisterService::class, 'form'])->name('form.service');
        Route::get('/product', [RegisterProduct::class, 'form'])->name('form.product');
        Route::get('/package', [RegisterPackage::class, 'form'])->name('form.package');
    });

    Route::name('root.')->prefix('commercial')->group(function () {
        Route::get('/', [HomeCommercial::class, 'home'])->name('commercial');
        Route::get('/summary', [SummaryCommercial::class, 'summary'])->name('commercial.summary');
        Route::get('/consumption', [Consumption::class, 'home'])->name('commercial.consumption');
    });

    Route::name('root.')->prefix('register')->group(function () {
        Route::post('/costumer', [CostumerController::class, 'store'])->name('register.costumer');
    });

    Route::name('root.')->prefix('update')->group(function () {
        Route::patch('/costumer', [RegisterCostumer::class, 'update'])->name('update.costumer');
    });
});

Route::fallback(function () {
    if(!auth()->check()) {
        return view('auth.login');
    }
});
