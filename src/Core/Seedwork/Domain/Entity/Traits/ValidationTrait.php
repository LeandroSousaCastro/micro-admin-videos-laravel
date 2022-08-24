<?php

namespace Core\Seedwork\Domain\Entity\Traits;

use Core\Seedwork\Domain\Validation\ValidatorFactory;

trait ValidationTrait
{
    protected function validate(): void
    {
        ValidatorFactory::create()->validate($this->getDataValidator());
    }

    protected function getDataValidator(): array
    {
        $className = explode('\\', __CLASS__);
        return [
            'context' => end($className),
            'data' => $this->toArray(),
            'rules' => $this->rules
        ];
    }
}
