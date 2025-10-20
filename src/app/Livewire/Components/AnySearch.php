<?php

namespace App\Livewire\Components;


use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AnySearch extends Component
{
    public array $packages_items = [];

    public mixed $products = [];
    public mixed $services = [];
    public mixed $packages = [];
    public string $search = '';
    public bool $showDropdown = false;

    public function render(): View
    {
        return view('livewire.components.any-search');
    }
    public function updatedSearch(): void
    {
        if (strlen($this->search) >= 3) {
            $teamId = Auth::user()->currentTeam->id;
            $this->showDropdown = true;
            $this->products = $this->fetchLimitedByTeam(Product::class, $teamId, $this->search);
            $this->services = $this->fetchLimitedByTeam(Service::class, $teamId, $this->search);
            $this->packages = $this->fetchLimitedByTeam(Package::class, $teamId, $this->search);
            $this->packages_items = Arr::collapse([$this->products, $this->services, $this->packages]);
        }
    }

    /**
     * Fetch limited results by team and search term for a given model.
     *
     * @param string $modelClass
     * @param int $teamId
     * @param string $term
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function fetchLimitedByTeam(string $modelClass, int $teamId, string $term, int $limit = 3)
    {
        return $modelClass::where('team_id', $teamId)
            ->where('name', 'like', '%' . $term . '%')
            ->limit($limit)
            ->get();
    }
    public function addProduct(Product $product):void
    {
        $packagesItems = Cache::get('packages_items', []);
        $products = $packagesItems ?? [];
        $products[] = $product;
        $packagesItems = $products;
        Cache::put('packages_items', $packagesItems, now()->addMinutes(5));
        $this->dispatch('items-update');
    }
    public function addService(Service $service): void
    {
        $packagesItems = Cache::get('packages_items', []);
        $services = $packagesItems ?? [];
        $services[] = $service;
        $packagesItems = $services;
        Cache::put('packages_items', $packagesItems, now()->addMinutes(5));
        $this->dispatch('items-update');
    }
    public function addPackage(Package $package): void
    {
        $packagesItems = Cache::get('packages_items', []);
        $packages = $packagesItems ?? [];
        $packages[] = $package;
        $packagesItems = $packages;
        Cache::put('packages_items', $packagesItems, now()->addMinutes(5));
        $this->dispatch('items-update');
    }
}
