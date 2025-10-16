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
        
        // Formato brasileiro: 1.234,56
        $result = $cast->set($model, 'price', '1.234,56', []);
        $this->assertEquals('1234.56', $result);
    }

    #[Test]
    public function it_handles_american_format_with_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato americano: 1,234.56
        $result = $cast->set($model, 'price', '1,234.56', []);
        $this->assertEquals('1234.56', $result);
    }

    #[Test]
    public function it_handles_brazilian_format_without_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato brasileiro: 234,56
        $result = $cast->set($model, 'price', '234,56', []);
        $this->assertEquals('234.56', $result);
    }

    #[Test]
    public function it_handles_american_format_without_thousands()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Formato americano: 234.56
        $result = $cast->set($model, 'price', '234.56', []);
        $this->assertEquals('234.56', $result);
    }

    #[Test]
    public function it_handles_currency_symbols()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Com símbolos de moeda
        $result = $cast->set($model, 'price', 'R$ 1.234,56', []);
        $this->assertEquals('1234.56', $result);
        
        $result = $cast->set($model, 'price', '$1,234.56', []);
        $this->assertEquals('1234.56', $result);
    }

    #[Test]
    public function it_handles_numeric_values()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Valores numéricos puros
        $result = $cast->set($model, 'price', 1234.56, []);
        $this->assertEquals('1234.56', $result);
        
        $result = $cast->set($model, 'price', 1234, []);
        $this->assertEquals('1234.00', $result);
    }

    #[Test]
    public function it_handles_negative_values()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Valores negativos
        $result = $cast->set($model, 'price', '-1.234,56', []);
        $this->assertEquals('-1234.56', $result);
        
        $result = $cast->set($model, 'price', '-1,234.56', []);
        $this->assertEquals('-1234.56', $result);
    }

    #[Test]
    public function it_handles_large_numbers_brazilian_format()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Números grandes: 1.234.567,89
        $result = $cast->set($model, 'price', '1.234.567,89', []);
        $this->assertEquals('1234567.89', $result);
    }

    #[Test]
    public function it_handles_large_numbers_american_format()
    {
        $cast = new MonetaryCorrency();
        $model = $this->createMockModel();
        
        // Números grandes: 1,234,567.89
        $result = $cast->set($model, 'price', '1,234,567.89', []);
        $this->assertEquals('1234567.89', $result);
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
        $this->assertEquals('0.00', $result);
        
        // Valor muito pequeno
        $result = $cast->set($model, 'price', '0,01', []);
        $this->assertEquals('0.01', $result);
        
        // String vazia (será convertido para 0)
        $result = $cast->set($model, 'price', '', []);
        $this->assertEquals('0.00', $result);
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
