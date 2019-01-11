
<h1 align="center">Weather</h1>

<p align="center"> 基于高德开放平台的 PHP 天气信息组件。</p>

## 安装

```sh
$ composer require aliliin/weather -vvv
```

## 配置

在使用本扩展之前，你需要去 [高德开放平台](https://lbs.amap.com/dev/id/newuser) 注册账号，然后创建应用，获取应用的 API Key。


## 使用

```php
use Aliliin\Weather\Weather;

$key = 'kkkkkkkkkkkkkkkkkkkkkk';

$weather = new Weather($key);
```

###  获取实时天气

```php
$response = $weather->getLiveWeather('北京');
```
示例：

```json
{
    "status": "1",
    "count": "1",
    "info": "OK",
    "infocode": "10000",
    "lives": [
        {
            "province": "北京",
            "city": "北京市",
            "adcode": "110000",
            "weather": "晴",
            "temperature": "0",
            "winddirection": "南",
            "windpower": "≤3",
            "humidity": "26",
            "reporttime": "2019-01-10 18:49:58"
        }
    ]
}
```

### 获取近期天气预报

```
$response = $weather->getForecastsWeather('北京');
```
示例：

```json
{
    "status": "1",
    "count": "1",
    "info": "OK",
    "infocode": "10000",
    "forecasts": [
        {
            "city": "北京市",
            "adcode": "110000",
            "province": "北京",
            "reporttime": "2019-01-10 18:49:58",
            "casts": [
                {
                    "date": "2019-01-10",
                    "week": "4",
                    "dayweather": "晴",
                    "nightweather": "多云",
                    "daytemp": "4",
                    "nighttemp": "-7",
                    "daywind": "西南",
                    "nightwind": "西南",
                    "daypower": "≤3",
                    "nightpower": "≤3"
                },
                {
                    "date": "2019-01-11",
                    "week": "5",
                    "dayweather": "多云",
                    "nightweather": "晴",
                    "daytemp": "5",
                    "nighttemp": "-7",
                    "daywind": "西南",
                    "nightwind": "西南",
                    "daypower": "≤3",
                    "nightpower": "≤3"
                },
                {
                    "date": "2019-01-12",
                    "week": "6",
                    "dayweather": "晴",
                    "nightweather": "晴",
                    "daytemp": "6",
                    "nighttemp": "-5",
                    "daywind": "北",
                    "nightwind": "北",
                    "daypower": "≤3",
                    "nightpower": "≤3"
                },
                {
                    "date": "2019-01-13",
                    "week": "7",
                    "dayweather": "晴",
                    "nightweather": "多云",
                    "daytemp": "6",
                    "nighttemp": "-7",
                    "daywind": "西南",
                    "nightwind": "西南",
                    "daypower": "≤3",
                    "nightpower": "≤3"
                }
            ]
        }
    ]
}
```

### 获取 XML 格式返回值

以上两个方法第二个参数为返回值类型，可选 `json` 与 `xml`，默认 `json`：

```php
$response = $weather->getLiveWeather('北京', 'xml');
```

示例：

```xml
<response>
    <status>1</status>
    <count>1</count>
    <info>OK</info>
    <infocode>10000</infocode>
    <lives type="list">
        <live>
            <province>北京</province>
            <city>北京市</city>
            <adcode>110000</adcode>
            <weather>晴</weather>
            <temperature>0</temperature>
            <winddirection>南</winddirection>
            <windpower>≤3</windpower>
            <humidity>26</humidity>
            <reporttime>2019-01-10 18:49:58</reporttime>
        </live>
    </lives>
</response>
```

### 参数说明

```
array | string   getLiveWeather(string $city, string $format = 'json')
array | string   getForecastsWeather(string $city, string $format = 'json')
```

> - `$city` - 城市名/[高德地址位置 adcode](https://lbs.amap.com/api/webservice/guide/api/district)北京 或者（adcode：110000）；
> - `$format`  - 输出的数据格式，默认为 json 格式，当 output 设置为 “`xml`” 时，输出的为 XML 格式的数据。


### 在 Laravel 中使用

在 Laravel 中使用也是同样的安装方式，配置写在 `config/services.php` 中：

```php
    .
    .
    .
     'weather' => [
        'key' => env('WEATHER_API_KEY'),
    ],
```

然后在 `.env` 中配置 `WEATHER_API_KEY` ：

```env
WEATHER_API_KEY=xxxxxxxxxxxxxxxxxxxxx
```

可以用两种方式来获取 `Aliliin\Weather\Weather` 实例：

#### 方法参数注入

```php
    .
    .
    .
    public function getWeatherInfo(Weather $weather) 
    {
        $response = $weather->getLiveWeather('北京');
    }
    .
    .
    .
```

#### 服务名访问

```php
    .
    .
    .
    public function getWeatherInfo() 
    {
        $response = app('weather')->getLiveWeather('北京');
    }
    .
    .
    .

```

## 参考

- [高德开放平台天气接口](https://lbs.amap.com/api/webservice/guide/api/weatherinfo/)

## License

MIT
