<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Entity\Entity;

interface ValidatorInterface
{
    public function validate(array $data, string $context, array $rules): void;
}
