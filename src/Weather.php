<?php

namespace Aliliin\Weather;

use Aliliin\Weather\Exceptions\HttpException;
use Aliliin\Weather\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

class Weather
{
    /**
     * $key 通过注册高德平台提供的
     * @var [string]
     */
    protected $key;

    /**
     * $guzzleOptions guzzle 实例]
     * @var array
     */
    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * 获取天气需要用到 http 请求，所以需要先创建一个方法用于返回 guzzle 实例
     * @Author:   GaoYongLi
     * @DateTime: 2019-01-10
     * @param     array      $options          [参数]
     * @return    [object]                    [实例]
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 用户可以自定义 guzzle 实例的参数，如超时时间等。
     * @Author:   GaoYongLi
     * @DateTime: 2019-01-10
     * @param     array      $options [参数]
     * @return    [type]                    [description]
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 获取实时天气
     * @Author:   GaoYongLi
     * @DateTime: 2019-01-10
     * @param     string     $city
     * @param     string     $format
     * @return    [jsonOrXml]
     */
    public function getLiveWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'base', $format);
    }

    /**
     * 获取近期天气预报
     * @Author:   GaoYongLi
     * @DateTime: 2019-01-10
     * @param     string     $city
     * @param     string     $format
     * @return    [jsonOrXml]
     */
    public function getForecastsWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'all', $format);
    }

    /**
     *  获取天气接口
     * @Author:   GaoYongLi
     * @DateTime: 2019-01-10
     * @param     [string]    $city    [城市]
     * @param     string      $type    [内容类型 base:返回实况天气 or  all:返回预报天气]
     * @param     string      $format  [输出的数据格式 默认是json 支持 XML]
     * @return    [json]              [天气结果]
     */
    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url    = 'https://restapi.amap.com/v3/weather/weatherInfo';
        $format = \strtolower($format);
        $type   = \strtolower($type);
        // 处理参数出错的情况
        if (!\in_array($format, ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }
        if (!in_array($type, ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        //封装 query 参数，并对空值进行过滤。
        $query = array_filter([
            'key'        => $this->key,
            'city'       => $city,
            'output'     => $format,
            'extensions' => $type,
        ]);

        try {

            // 调用 getHttpClient 获取实例，并调用该实例的 `get` 方法， 传递参数为两个：$url、['query' => $query]，
            $response = $this->getHttpClient()
                ->get($url, ['query' => $query])
                ->getBody()
                ->getContents();

            // 返回值根据 $format 返回不同的格式， 当 $format 为 json 时，返回数组格式，否则为 xml。
            return 'json' === $format ? json_encode($response, true) : $response;
        } catch (\Exception $error) {

            // 当调用出现异常时捕获并抛出，消息为捕获到的异常消息， 并将调用异常作为 $previousException 传入。
            throw new HttpException($error->getMessage(), $error->getCode(), $error);
        }

    }
}
