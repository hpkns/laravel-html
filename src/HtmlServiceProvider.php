<?php

namespace Hpkns\Html;

use Collective\Html\HtmlServiceProvider as BaseProvider;

class HtmlServiceProvider extends BaseProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/html.php' => config_path('html.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/../views' => resource_path('views/hpkns/html'),
        ]);

         $this->loadViewsFrom(__DIR__ . '/../views', 'html');
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);

            return $form->setSessionStore($app['session.store']);
        });
    }
}
