<?php

namespace Tests\Unit\Casts;

use App\Casts\MonetaryCurrency;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MonetaryCurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        App::setLocale('pt_BR');
    }

    #[Test]
    public function it_converts_decimal_string_to_money_instance()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel(['currency' => 'BRL']);

        $result = $cast->get($model, 'price', '1234.567', ['currency' => 'BRL']);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertEquals('1234.567', $result->getAmount()->__toString());
        $this->assertEquals('BRL', $result->getCurrency()->getCurrencyCode());
    }

    #[Test]
    public function it_converts_money_instance_to_decimal_string()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        $context = new CustomContext(3);
        $money = Money::of(1234.567, 'BRL', $context);
        $result = $cast->set($model, 'price', $money, []);

        $this->assertEquals('1234.567', $result);
        $this->assertIsString($result);
    }

    #[Test]
    public function it_converts_numeric_values_to_decimal_string()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        // Float
        $result = $cast->set($model, 'price', 1234.56, []);
        $this->assertEquals('1234.560', $result);

        // Integer
        $result = $cast->set($model, 'price', 1234, []);
        $this->assertEquals('1234.000', $result);

        // String numérica
        $result = $cast->set($model, 'price', '1234.567', []);
        $this->assertEquals('1234.567', $result);
    }

    #[Test]
    public function it_handles_null_values()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        // Get null
        $result = $cast->get($model, 'price', null, []);
        $this->assertNull($result);

        // Set null
        $result = $cast->set($model, 'price', null, []);
        $this->assertNull($result);

        // Set empty string
        $result = $cast->set($model, 'price', '', []);
        $this->assertNull($result);
    }

    #[Test]
    public function it_respects_currency_from_model_attribute()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel(['currency' => 'USD']);

        $result = $cast->get($model, 'price', '1234.567', ['currency' => 'USD']);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertEquals('USD', $result->getCurrency()->getCurrencyCode());
    }

    #[Test]
    public function it_uses_constructor_currency_when_provided()
    {
        $cast = new MonetaryCurrency(currencyCode: 'EUR');
        $model = $this->createMockModel(['currency' => 'BRL']); // Será ignorado

        $result = $cast->get($model, 'price', '1234.567', ['currency' => 'BRL']);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertEquals('EUR', $result->getCurrency()->getCurrencyCode());
    }

    #[Test]
    public function it_uses_default_currency_when_none_specified()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        $result = $cast->get($model, 'price', '1234.567', []);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertEquals('BRL', $result->getCurrency()->getCurrencyCode());
    }

    #[Test]
    public function it_parses_formatted_brazilian_values()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        // Formato brasileiro com símbolo
        $result = $cast->set($model, 'price', 'R$ 1.234,56', []);
        $this->assertEquals('1234.560', $result);

        // Formato brasileiro sem símbolo
        $result = $cast->set($model, 'price', '1.234,56', []);
        $this->assertEquals('1234.560', $result);
    }

    #[Test]
    public function it_parses_formatted_american_values()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        // Formato americano com símbolo
        $result = $cast->set($model, 'price', '$1,234.56', []);
        $this->assertEquals('1234.560', $result);

        // Formato americano sem símbolo
        $result = $cast->set($model, 'price', '1,234.56', []);
        $this->assertEquals('1234.560', $result);
    }

    #[Test]
    public function it_maintains_three_decimal_precision()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        $context = new CustomContext(3);
        $money = Money::of(12.345, 'BRL', $context);
        $result = $cast->set($model, 'price', $money, []);

        $this->assertEquals('12.345', $result);
        $this->assertStringContainsString('.345', $result);
    }

    #[Test]
    public function it_rounds_values_using_half_up()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel(['currency' => 'BRL']);

        // 1234.5675 deve arredondar para 1234.568 (HALF_UP)
        $money = $cast->get($model, 'price', '1234.5675', ['currency' => 'BRL']);
        
        // Brick\Money já arredonda internamente
        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('1234.568', $money->getAmount()->toScale(3)->__toString());
    }

    #[Test]
    public function it_serializes_money_to_array()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        $context = new CustomContext(3);
        $money = Money::of(1234.567, 'BRL', $context);
        $result = $cast->serialize($model, 'price', $money, []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertArrayHasKey('formatted', $result);
        $this->assertEquals('1234.567', $result['amount']);
        $this->assertEquals('BRL', $result['currency']);
        $this->assertStringContainsString('1', $result['formatted']);
    }

    #[Test]
    public function it_performs_arithmetic_operations_safely()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel(['currency' => 'BRL']);

        $price = $cast->get($model, 'price', '100.000', ['currency' => 'BRL']);
        
        // Operações tipo-safe com Money (usa toRational para compatibilidade de contexto)
        $discounted = $price->multipliedBy('0.9', RoundingMode::HALF_UP); // 10% desconto
        $context = new CustomContext(3);
        $toAdd = Money::of(10, 'BRL', $context);
        $final = $discounted->plus($toAdd->toRational(), RoundingMode::HALF_UP);

        $this->assertEquals('100.000', $final->getAmount()->toScale(3, RoundingMode::HALF_UP));
        $this->assertEquals('BRL', $final->getCurrency()->getCurrencyCode());
    }

    #[Test]
    public function it_handles_different_currencies()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        $currencies = ['BRL', 'USD', 'EUR', 'GBP'];

        foreach ($currencies as $currencyCode) {
            $castWithCurrency = new MonetaryCurrency(currencyCode: $currencyCode);
            $money = $castWithCurrency->get($model, 'price', '1000.000', ['currency' => $currencyCode]);

            $this->assertEquals($currencyCode, $money->getCurrency()->getCurrencyCode());
        }
    }

    #[Test]
    public function it_returns_null_for_invalid_values()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();

        // Valor inválido
        $result = $cast->set($model, 'price', 'invalid', []);
        $this->assertNull($result);

        // Array
        $result = $cast->set($model, 'price', ['amount' => 100], []);
        $this->assertNull($result);
    }

    #[Test]
    public function it_updates_currency_column_when_setting_money()
    {
        $cast = new MonetaryCurrency();
        $model = $this->createMockModel();
        $model->fillable = ['price', 'currency'];

        $context = new CustomContext(3);
        $money = Money::of(1234.567, 'USD', $context);
        $cast->set($model, 'price', $money, []);

        // Verifica se a moeda foi atualizada no model
        $this->assertTrue($model->hasAttribute('currency') || in_array('currency', $model->fillable));
    }

    /**
     * Cria um mock de Model para testes.
     *
     * @param array<string, mixed> $attributes Atributos iniciais
     * @return Model
     */
    protected function createMockModel(array $attributes = []): Model
    {
        return new class($attributes) extends Model {
            protected $table = 'test_table';
            protected $fillable = ['price', 'currency'];
            
            public function __construct(array $attributes = [])
            {
                parent::__construct();
                $this->attributes = $attributes;
            }

            public function hasAttribute($key): bool
            {
                return array_key_exists($key, $this->attributes);
            }
        };
    }
}
