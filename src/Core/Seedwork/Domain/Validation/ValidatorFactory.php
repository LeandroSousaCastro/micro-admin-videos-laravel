<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Validation\ValidatorInterface;
use Core\Seedwork\Domain\Validation\LaravelValidator;
use Core\Seedwork\Domain\Validation\RakitValidator;

class ValidatorFactory
{
    public static function create(): ValidatorInterface
    {
        // return new LaravelValidator();
        return new RakitValidator();
    }
}
