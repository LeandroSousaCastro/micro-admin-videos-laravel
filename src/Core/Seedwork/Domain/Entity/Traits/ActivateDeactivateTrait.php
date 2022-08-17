<?php

namespace Core\Seedwork\Domain\Entity\Traits;

trait ActivateDeactivateTrait
{
    public function activate(): void
    {
        if (property_exists($this, 'isActive')) {
            $this->isActive = true;
        }
    }

    public function deactivate(): void
    {
       if (property_exists($this, 'isActive')) {
            $this->isActive = false;
        }
    }
}
