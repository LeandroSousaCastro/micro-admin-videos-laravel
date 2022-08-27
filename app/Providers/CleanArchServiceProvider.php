<?php

namespace App\Providers;

use App\Events\VideoEvent;
use App\Repositories\Eloquent\{
    CastMemberEloquentRepository,
    GenreEloquentRepository,
    CategoryEloquentRepository,
    VideoEloquentRepository
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Seedwork\Application\Interfaces\DbTransactionInterface;
use App\Repositories\Transaction\DbTransaction;
use App\Services\Storage\FileStorage;
use Core\Seedwork\Application\Interfaces\FileStorageInterface;
use Core\Video\Domain\Events\VideoEventManagerInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class CleanArchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindRepositories();

        $this->app->singleton(
            FileStorageInterface::class,
            FileStorage::class
        );

        $this->app->singleton(
            VideoEventManagerInterface::class,
            VideoEvent::class
        );

        /**
         * Db Transaction
         */
        $this->app->bind(
            DbTransactionInterface::class,
            DbTransaction::class
        );
    }

    private function bindRepositories()
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryEloquentRepository::class
        );

        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreEloquentRepository::class
        );

        $this->app->singleton(
            CastMemberRepositoryInterface::class,
            CastMemberEloquentRepository::class
        );

        $this->app->singleton(
            VideoRepositoryInterface::class,
            VideoEloquentRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
