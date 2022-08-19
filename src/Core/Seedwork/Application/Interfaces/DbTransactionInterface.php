<?php

namespace Core\Seedwork\Application\Interfaces;

interface DbTransactionInterface
{
    public function commit();
    public function rollBack();
}