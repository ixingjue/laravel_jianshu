<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Db;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    //此方法在所有其他服务提供者均已注册之后调用
    public function boot()
    {
        Schema::defaultStringLength(191);


        View::composer('layout.sidebar', function ($view) {
            $topics = \App\Topic::all();
            $view->with('topics', $topics);
        });

        Db::listen(function ($query) {
            $sql = $query->sql;
            $bindings = $query->bindings;
            $time = $query->time;
            //找出比较慢的sql 可以自己设置时间
            if ($time > 1) {
                Log::debug(var_export(compact('sql', 'bindings', 'time'), true));//true不打印到页面中 打印到控制台
            }
        });
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    //在所有的service注册前完成调用
    public function register()
    {
        //
    }
}
