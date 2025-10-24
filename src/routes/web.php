<?php

use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\HierarchyManager;

use App\Livewire\Employee\IndexEmployee;
use App\Livewire\Employee\RegisterEmployee;
use App\Livewire\Employee\EmployeeForm;

use App\Livewire\Service\IndexService;
use App\Livewire\Service\HomeService;
use App\Livewire\Service\RegisterService;
use App\Livewire\Service\RegisterProduct;
use App\Livewire\Service\RegisterPackage;

use App\Livewire\Commercial\HomeCommercial;
use App\Livewire\Commercial\SummaryCommercial;
use App\Livewire\Commercial\Consumption;

use App\Livewire\Customer\CustomerFormComponent;
use App\Livewire\Customer\IndexCustomer;

use App\Livewire\Companies\Index as CompaniesIndex;
use App\Livewire\Companies\Create as CompaniesCreate;
use App\Livewire\Companies\Hierarchy as CompaniesHierarchy;

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


// Custom route to accept invitations (allows access without authentication)
Route::get('/team-invitations/{invitation}', [\App\Http\Controllers\TeamInvitationController::class, 'accept'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('team-invitations.accept');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Locale routes (requires authentication)
    Route::post('/locale/change', [LocaleController::class, 'change'])->name('locale.change');
    Route::get('/locale/current', [LocaleController::class, 'current'])->name('locale.current');
    
    // Company routes
    Route::name('companies.')->prefix('companies')->group(function () {
        Route::get('/', CompaniesIndex::class)->name('index');
        Route::get('/create', CompaniesCreate::class)->name('create');
        Route::get('/hierarchy', CompaniesHierarchy::class)->name('hierarchy');
    });

    Route::name('root.')->prefix('dashboard')->group(function () {
            Route::get('/', HierarchyManager::class)->name('dashboard.hierarchy');
    });

    Route::name('root.')->prefix('employee')->group(function () {
        Route::get('/', IndexEmployee::class)->name('employee');
        Route::get('/index', IndexEmployee::class)->name('employee.index');
        Route::get('/create', EmployeeForm::class)->name('employee.create');
        Route::get('/{userId}/edit', EmployeeForm::class)->name('employee.edit');
    });

    // Customer routes (padrão consolidado)
    Route::name('customers.')->prefix('customers')->middleware('user.has.team')->group(function () {
        Route::get('/', IndexCustomer::class)->name('index');
        Route::get('/create', CustomerFormComponent::class)->name('create');
        Route::get('/{customerId}/edit', CustomerFormComponent::class)->name('edit');
    });

    Route::name('root.')->prefix('negotiable')->middleware('user.has.team')->group(function () {
        Route::get('/', IndexService::class)->name('negotiable');
        Route::get('product/index', [ProductController::class, 'index'])->name('product.index');
    });

    Route::name('root.')->prefix('form')->middleware('user.has.team')->group(function () {
        Route::get('/service', RegisterService::class)->name('form.service');
        Route::get('/product', RegisterProduct::class)->name('form.product');
        Route::get('/package', RegisterPackage::class)->name('form.package');
//        Route::get('/form/package', RegisterPackage::class);
    });

    Route::name('root.')->prefix('commercial')->middleware('user.has.team')->group(function () {
        Route::get('/', SummaryCommercial::class)->name('commercial.index');
        Route::get('/summary', SummaryCommercial::class)->name('commercial.summary');
        Route::get('/consumption', Consumption::class)->name('commercial.consumption');
    });

    Route::name('root.')->prefix('register')->middleware('user.has.team')->group(function () {
        // DEPRECATED: Usar customers.* routes (novo padrão Livewire)
        // Route::post('/costumer', [CostumerController::class, 'store'])->name('register.costumer');
        Route::post('/service', [ServiceController::class, 'store'])->name('register.service');
        Route::post('/package', [PackageController::class, 'store'])->name('register.package');
        Route::post('/enterprise', [EnterpriseController::class, 'store'])->name('register.enterprise');
    });

    Route::name('root.')->prefix('update')->middleware('user.has.team')->group(function () {
        // DEPRECATED: Usar customers.* routes (novo padrão Livewire)
        // Route::patch('/costumer', [RegisterCostumer::class, 'update'])->name('update.costumer');
        Route::patch('/product/{product}', [ProductController::class, 'update'])->name('update.product');
    });

    Route::name('root.')->prefix('delete')->middleware('user.has.team')->group(function () {
        Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('delete.product');
    });

});

use Illuminate\Support\Facades\Auth;

Route::fallback(function () {
    if(!Auth::check()) {
        return view('auth.login');
    }
});
