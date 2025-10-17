<?php

namespace Tests\Unit\Casts;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MonetaryCorrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Define locale e moeda padrão para testes
        App::setLocale('pt_BR');
        Session::put('currency', 'BRL');
    }

    #[Test]
    public function it_handles_brazilian_format_with_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato brasileiro: 1.234,56 → 1234.560 (3 casas decimais)
        $result = $cast->set($model, 'price', '1.234,56', []);
        $this->assertEquals(1234.560, $result);
        $this->assertIsFloat($result);
    }

    #[Test]
    public function it_handles_american_format_with_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato americano: 1,234.56 → 1234.560
        $result = $cast->set($model, 'price', '1,234.56', []);
        $this->assertEquals(1234.560, $result);
        $this->assertIsFloat($result);
    }

    #[Test]
    public function it_handles_brazilian_format_without_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato brasileiro: 234,56 → 234.560
        $result = $cast->set($model, 'price', '234,56', []);
        $this->assertEquals(234.560, $result);
        $this->assertIsFloat($result);
    }

    #[Test]
    public function it_handles_american_format_without_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato americano: 234.56 → 234.560
        $result = $cast->set($model, 'price', '234.56', []);
        $this->assertEquals(234.560, $result);
        $this->assertIsFloat($result);
    }

    #[Test]
    public function it_handles_currency_symbols()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Com símbolos de moeda
        $result = $cast->set($model, 'price', 'R$ 1.234,56', []);
        $this->assertEquals(1234.560, $result);
        
        $result = $cast->set($model, 'price', '$1,234.56', []);
        $this->assertEquals(1234.560, $result);
        
        $result = $cast->set($model, 'price', '€ 1.234,56', []);
        $this->assertEquals(1234.560, $result);
    }

    #[Test]
    public function it_handles_numeric_values()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Valores numéricos puros com 3 casas decimais
        $result = $cast->set($model, 'price', 1234.56, []);
        $this->assertEquals(1234.560, $result);
        
        $result = $cast->set($model, 'price', 1234, []);
        $this->assertEquals(1234.000, $result);
        
        $result = $cast->set($model, 'price', 1234.567, []);
        $this->assertEquals(1234.567, $result);
    }

    #[Test]
    public function it_handles_negative_values()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Valores negativos
        $result = $cast->set($model, 'price', '-1.234,56', []);
        $this->assertEquals(-1234.560, $result);
        
        $result = $cast->set($model, 'price', '-1,234.56', []);
        $this->assertEquals(-1234.560, $result);
        
        $result = $cast->set($model, 'price', -1234.56, []);
        $this->assertEquals(-1234.560, $result);
    }

    #[Test]
    public function it_handles_large_numbers_brazilian_format()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Números grandes: 1.234.567,89
        $result = $cast->set($model, 'price', '1.234.567,89', []);
        $this->assertEquals(1234567.890, $result);
    }

    #[Test]
    public function it_handles_large_numbers_american_format()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Números grandes: 1,234,567.89
        $result = $cast->set($model, 'price', '1,234,567.89', []);
        $this->assertEquals(1234567.890, $result);
    }

    #[Test]
    public function it_formats_output_based_on_locale()
    {
        $model = $this->createMockModel();
        
        // Formato brasileiro (BRL)
        App::setLocale('pt_BR');
        Session::put('currency', 'BRL');
        $cast = new MonetaryCorrency();
        $result = $cast->get($model, 'price', 1234.56, []);
        $this->assertStringContainsString('1.234,56', $result);
        
        // Formato americano (USD)
        App::setLocale('en');
        Session::put('currency', 'USD');
        $cast = new MonetaryCorrency();
        $result = $cast->get($model, 'price', 1234.56, []);
        $this->assertStringContainsString('1,234.56', $result);
    }

    #[Test]
    public function it_uses_custom_currency_parameter()
    {
        $model = $this->createMockModel();
        
        // Força USD mesmo com locale pt_BR
        App::setLocale('pt_BR');
        Session::put('currency', 'BRL');
        $cast = new MonetaryCorrency(currency: 'USD');
        $result = $cast->get($model, 'price', 1234.56, []);
        $this->assertStringContainsString('$', $result);
    }

    #[Test]
    public function it_handles_edge_cases()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Zero
        $result = $cast->set($model, 'price', '0', []);
        $this->assertEquals(0.000, $result);
        
        // Valor muito pequeno com 3 casas decimais
        $result = $cast->set($model, 'price', '0,01', []);
        $this->assertEquals(0.010, $result);
        
        $result = $cast->set($model, 'price', '0,001', []);
        $this->assertEquals(0.001, $result);
        
        // Precisão: 12.345 (importante para cálculos)
        $result = $cast->set($model, 'price', '12,345', []);
        $this->assertEquals(12.345, $result);
    }
    
    #[Test]
    public function it_returns_null_for_invalid_values()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // String vazia retorna null (não exception)
        $result = $cast->set($model, 'price', '', []);
        $this->assertNull($result);
        
        // Null retorna null
        $result = $cast->set($model, 'price', null, []);
        $this->assertNull($result);
        
        // String inválida retorna null
        $result = $cast->set($model, 'price', 'abc', []);
        $this->assertNull($result);
        
        // Símbolos sem números retorna null
        $result = $cast->set($model, 'price', 'R$', []);
        $this->assertNull($result);
    }
    
    #[Test]
    public function it_handles_three_decimal_precision()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Testa precisão de 3 casas decimais (importante para evitar perda em cálculos)
        $result = $cast->set($model, 'price', 10.12345, []);
        $this->assertEquals(10.123, $result); // Arredonda para 3 casas
        
        $result = $cast->set($model, 'price', '10,12345', []);
        $this->assertEquals(10.123, $result);
        
        // Valores pequenos mantêm precisão
        $result = $cast->set($model, 'price', 0.001, []);
        $this->assertEquals(0.001, $result);
    }

    /**
     * Cria um mock de Model para testes
     */
    protected function createMockModel(): Model
    {
        return new class extends Model {
            protected $table = 'test_table';
        };
    }
}
