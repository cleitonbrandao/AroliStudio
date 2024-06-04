<?php

namespace App\Livewire\Components;


use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AnySearch extends Component
{
    public array $items = [];

    public $products = [];
    public $services = [];
    public $packages = [];
    public $search = '';
    public bool $showDropdown = false;

    public function render(): View
    {
        $this->updatedSearch($this->search);
        return view('livewire.components.any-search', [
            'products' => $this->products,
            'services' => $this->services,
            'packages' => $this->packages
        ]);
    }
    public function updatedSearch($value): void
    {
        if (strlen($value) >= 3) {
            $this->showDropdown = true;
            $this->products = Product::where('name', 'like', '%' . $value . '%')->limit(3)->get();
            $this->services = Service::where('name', 'like', '%' . $value . '%')->limit(3)->get();
            $this->packages = Package::where('name', 'like', '%' . $value . '%')->limit(3)->get();
        }
    }
    public function addProduct($product):void
    {
        $packagesItems = Cache::get('packages_items', []);
        $products = $packagesItems['products'] ?? [];
        $products[] = $product;
        $packagesItems['products'] = $products;
        Cache::put('packages_items', $packagesItems, now()->addMinutes(5));
        $this->dispatch('items-update');
    }
    public function addService($service): void
    {
        $packagesItems = Cache::get('packages_items', []);
        $services = $packagesItems['services'] ?? [];
        $services[] = $service;
        $packagesItems['services'] = $services;
        Cache::put('packages_items', $packagesItems, now()->addMinutes(5));
        $this->dispatch('items-update');
    }
    public function addPackage(Package $package): void
    {
        $this->packages = Cache::get('packages_items.packages', []);
        $this->packages['packages'] = $package;
        Cache::put('packages_items.packages', $this->packages,  now()->addMinutes(5));
        $this->dispatch('items-update');
    }
}
