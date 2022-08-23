<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Entity\Entity;

interface ValidatorInterface
{
    public function validate(Entity $entity, string $context, array $rules): void;
}
