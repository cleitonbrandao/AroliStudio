<?php

namespace App\Console\Commands;

use App\Models\Product;
use Brick\Money\Money;
use Illuminate\Console\Command;

class TestMonetaryCurrency extends Command
{
    protected $signature = 'test:monetary';
    protected $description = 'Testa o MonetaryCurrency cast';

    public function handle()
    {
        $this->info('🧪 Testando MonetaryCurrency Cast...');
        $this->newLine();

        // Busca primeiro produto
        $product = Product::first();

        if ($product) {
            $this->info("✅ Produto encontrado: {$product->name}");
            $this->info("   ID: {$product->id}");
            $this->info("   Price Type: " . get_class($product->price));
            $this->info("   Price Amount: " . $product->price->getAmount());
            $this->info("   Price Currency: " . $product->price->getCurrency()->getCurrencyCode());
            $this->info("   Price Formatted (pt_BR): " . $product->price->formatTo('pt_BR'));
            $this->info("   Price Formatted (en_US): " . $product->price->formatTo('en_US'));
            $this->newLine();

            // Teste de operações
            $this->info("🧮 Testando operações aritméticas:");
            $discount = $product->price->multipliedBy('0.9', \Brick\Math\RoundingMode::HALF_UP);
            $this->info("   Com 10% desconto: " . $discount->formatTo('pt_BR'));
            
            // Usa toRational() para compatibilidade de contexto
            $toAdd = Money::of(10, 'BRL', new \Brick\Money\Context\CustomContext(3));
            $withTax = $product->price->plus($toAdd->toRational(), \Brick\Math\RoundingMode::HALF_UP);
            $this->info("   Com R$ 10 de taxa: " . $withTax->formatTo('pt_BR'));
        } else {
            $this->warn('⚠️  Nenhum produto encontrado. Criando um novo...');
            
            $product = Product::create([
                'team_id' => 1,
                'name' => 'Test Product - MonetaryCurrency',
                'price' => Money::of(1234.567, 'BRL'),
                'cost_price' => 987.654,
                'currency' => 'BRL',
                'description' => 'Produto de teste para MonetaryCurrency'
            ]);

            $this->info("✅ Produto criado com sucesso!");
            $this->info("   Nome: {$product->name}");
            $this->info("   Preço: " . $product->price->formatTo('pt_BR'));
            $this->info("   Custo: " . $product->cost_price->formatTo('pt_BR'));
        }

        $this->newLine();
        $this->info('✨ Teste concluído!');
        
        return 0;
    }
}
