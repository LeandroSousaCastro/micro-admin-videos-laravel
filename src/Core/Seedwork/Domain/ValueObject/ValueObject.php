<?php

namespace Core\Seedwork\Domain\ValueObject;

abstract class ValueObject
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
