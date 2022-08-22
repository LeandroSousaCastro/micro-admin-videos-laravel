<?php

namespace Core\Seedwork\Domain\Entity\Traits;

use Throwable;

trait MethodsMagicsTrait
{
    public function __get($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }

        $className = get_class($this);
        throw new Throwable("Property {$property} not found in class {$className}");
    }
}
