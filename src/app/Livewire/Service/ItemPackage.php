<?php

namespace App\Livewire\Service;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class ItemPackage extends Component
{
    public mixed $packages_items = [];
    public float $price_cost;
    
    #[On('items-update')]
    public function render()
    {
        $this->packages_items = Cache::get('packages_items', []);
        $this->totalCost();
        $this->dispatchItemsToParent();
        return view('livewire.service.item-package');
    }
    
    #[On('package-saved')]
    public function clearItems()
    {
        Cache::forget('packages_items');
        $this->packages_items = [];
        $this->price_cost = 0;
    }
    
    public function removeItem($item)
    {
        $items = cache('packages_items');
        if (isset($items[$item]))
        {
            Arr::forget($items, $item);
            Cache::put('packages_items', array_values($items));
            $this->dispatch('remove-items-update');
            $this->packages_items = Cache::get('packages_items', []);
            $this->totalCost();
            $this->dispatchItemsToParent();
        }
    }
    
    private function totalCost(): void
    {
        $packagesItemsCollection = collect($this->packages_items);
        $this->price_cost = $packagesItemsCollection->sum(function ($item) {
            return floatval(
                str_replace(',','.',
                    str_replace('.','',
                            (
                                $item['cost_price'] ?? $item['price']
                            )
                    )
                )
            );
        });
    }
    
    /**
     * Dispatch items to parent RegisterPackage component
     */
    private function dispatchItemsToParent(): void
    {
        $items = [
            'services' => [],
            'products' => [],
            'packages' => [],
        ];
        
        foreach ($this->packages_items as $item) {
            $table = $item->getTable();
            $items[$table][] = $item->id;
        }
        
        $this->dispatch('items-updated', items: $items);
    }
}
