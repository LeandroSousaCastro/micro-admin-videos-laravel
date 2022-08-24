<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Notification\Notification;
use Core\Seedwork\Domain\Validation\ValidatorInterface;
use Core\Seedwork\Domain\Validation\RakitValidator;
use Rakit\Validation\Validator;

class ValidatorFactory
{
    public static function create(): ValidatorInterface
    {
        return new RakitValidator(
            new Notification(),
            new Validator()
        );
    }
}
