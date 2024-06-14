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
        return view('livewire.service.item-package');
    }
    public function removeItem($item)
    {
        $items = cache('packages_items');
        if (isset($items[$item]))
        {
            Arr::forget($items, $item);
            Cache::put('packages_items', array_values($items));
            $this->dispatch('remove-items-update');
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
}
