<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Exception\EntityValidationException;

class DomainValidation
{
    public static function notNull(string $value, string $exceptMessage = null): void
    {
        if (empty($value)) {
            throw new EntityValidationException($exceptMessage ?? "Should not be empty or null");
        }
    }

    public static function strMaxLength(string $value, int $maxLength = 255, string $exceptMessage = null): void
    {
        if (strlen($value) > $maxLength) {
            throw new EntityValidationException($exceptMessage ?? "The value must not be greater than $maxLength characters");
        }
    }

    public static function strMinLength(string $value, int $minLength = 2, string $exceptMessage = null): void
    {
        if (strlen($value) < $minLength) {
            throw new EntityValidationException($exceptMessage ?? "The value must not be least than $minLength characters");
        }
    }

    public static function strCanNullAndMaxLength(string $value = '', int $maxLength = 255, string $exceptMessage = null): void
    {
        if (!empty($value) && strlen($value) > $maxLength) {
            throw new EntityValidationException($exceptMessage ?? "The value must not be greater than $maxLength characters");
        }
    }
}
