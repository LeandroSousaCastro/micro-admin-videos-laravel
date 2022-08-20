<?php

namespace App\Providers;

use App\Repositories\Transaction\DbTransaction;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
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
            CategoryRepositoryInterface::class, CategoryEloquentRepository::class

        );

        $this->app->singleton(
            GenreRepositoryInterface::class, GenreEloquentRepository::class
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
