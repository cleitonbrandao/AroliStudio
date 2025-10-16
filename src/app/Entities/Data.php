<?php

namespace App\Entities;

use DateTimeInterface;

class Data
{
    private DateTimeInterface $date;

    public function __construct($value)
    {
        $this->date = $value;
    }

    protected function validate(): void
    {
        dd($this->date);
        $this->serializeDate($date);
    }

    protected function serializeDate(): string
    {
        return $this->date->format('Y-m-d');
    }
}
