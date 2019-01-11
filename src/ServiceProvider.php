<?php

namespace Aliliin\Weather;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    // 设置 $defer 属性为 true
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function () {
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    // 添加 provides 方法 这是 Laravel 扩展包的延迟注册方式
    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}
