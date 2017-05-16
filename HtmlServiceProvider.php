<?php

namespace Hpkns\Html;

use Collective\Html\HtmlServiceProvider;

class HtmlServiceProvider extends HtmlServiceProvider
{
    protected $defer = false;

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());
            $form->setTemplates(
                config('html.class_base', 'form__group'),
                config('html.control_class_base', 'form__control'),
                config('html.error_format', '<span class="form__error">:error</span>'),
                config('html.legend_format', '<span class="form__legend">:legend</span>')
            );

            return $form->setSessionStore($app['session.store']);
        });
    }
}
