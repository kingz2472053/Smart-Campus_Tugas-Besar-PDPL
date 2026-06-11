<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binder Mahasiswa
        $this->app->bind(
            \App\Repositories\Contracts\StudentRepositoryInterface::class,
            \App\Repositories\StudentRepository::class
        );

        // Binder Nilai (Grade)
        $this->app->bind(
            \App\Repositories\Contracts\GradeRepositoryInterface::class,
            \App\Repositories\GradeRepository::class
        );

        // Binder Tugas (Assignment)
        $this->app->bind(
            \App\Repositories\Contracts\AssignmentRepositoryInterface::class,
            \App\Repositories\AssignmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
