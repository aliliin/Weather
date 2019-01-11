<?php

namespace Aliliin\Weather;

use yii;
use yii\base\BootstrapInterface;

class YiiWeatherService implements BootstrapInterface
{

    public function bootstrap($app)
    {
        $app->on(Weather::class, function () {
            return new Weather(Yii::$app->params['services.weather.key']);
        });
    }
}
