<?php

namespace App\Providers;

use App\Repositories\Transaction\DbTransaction;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Category\Domain\Repository\CategoryRepositoryInterface as CategoryCategoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CategoryCategoryRepositoryInterface::class, CategoryEloquentRepository::class
        );

        /**
         * Db Transaction
         */
         $this->app->bind(
            DbTransactionInterface::class, DbTransaction::class
         );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
