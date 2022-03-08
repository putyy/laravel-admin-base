<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase;

use Illuminate\Support\ServiceProvider;

class LaravelAdminBaseServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(LaravelAdminBase $extension)
    {
        if (!LaravelAdminBase::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'admin-base');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/admin-base')],
                'admin-base'
            );
        }
    }
}
