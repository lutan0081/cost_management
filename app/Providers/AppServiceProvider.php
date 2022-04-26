<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ★独自バリデーションクラス追加
use App\lib\CustomValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ★独自クラス追加↓コピペ
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages) {
            return new CustomValidator($translator, $data, $rules, $messages);
        });
        
    }
}
