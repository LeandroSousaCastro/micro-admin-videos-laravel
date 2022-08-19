<?php

namespace App\Repositories\Transaction;

use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use Illuminate\Support\Facades\DB;

class DbTransaction implements DbTransactionInterface
{

    public function __construct()
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollBack(): void
    {
        DB::rollBack();
    }
}