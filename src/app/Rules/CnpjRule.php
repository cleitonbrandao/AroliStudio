<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = preg_replace('/[.\-\/]/', '', $value);

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $value[$i] * $weights1[$i];
        }
        $firstCheckDigit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        if ($value[12] != $firstCheckDigit) {
            $fail('Cnpj :Inválido.');
            return;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $value[$i] * $weights2[$i];
        }
        $secondCheckDigit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        if ($value[13] != $secondCheckDigit) {
            $fail('Cnpj :Inválido.');
        }
    }
}
