<?php

namespace App\Rules;

use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Validation\ValidationRule;

class DataRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate($attribute, mixed $value, Closure $fail): void
    {
        $this->serializeDate($attribute);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }
}
