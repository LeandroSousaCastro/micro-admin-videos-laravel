<?php

namespace Core\Seedwork\Domain\Validation;

use Core\Seedwork\Domain\Entity\Entity;

interface ValidatorInterface
{
    /**
     * @param $dataValidation array[context, data, rules]
     */
    public function validate(array $dataValidation): void;
}
