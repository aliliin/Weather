<?php

namespace Aliliin\Weather\Tests;

use Aliliin\Weather\Exceptions\HttpException;
use Aliliin\Weather\Exceptions\InvalidArgumentException;
use Aliliin\Weather\Weather;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{

    // 检测 type 参数
    public function testGetWeatherWithInvalidType()
    {
        $w = new Weather('mock-key');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type value(base/all): foo');
        $w->getWeather('深圳', 'foo');
        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    // 检查 $format 参数
    public function testGetWeatherWithInvalidFormat()
    {
        $w = new Weather('mock-key');
        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);
        // 断言异常消息为 'Invalid response format: array'
        $this->expectExceptionMessage('Invalid response format: array');
        // 因为支持的格式为 xml/json，所以传入 array 会抛出异常
        $w->getWeather('深圳', 'base', 'array');
        // 如果没有抛出异常，就会运行到这行，标记当前测试没成功
        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    // 测试 weather 方法 这里 因为获取天气是通过依赖 http 请求来的，所以模式 http请求默认为成功
    public function testGetWeather()
    {
        // xml
        $response = new Response(200, [], '<hello>content</hello>');
        $client   = \Mockery::mock(Client::class);
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key'        => 'mock-key',
                'city'       => '深圳',
                'extensions' => 'all',
                'output'     => 'xml',
            ],
        ])->andReturn($response);

        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        $this->assertSame('<hello>content</hello>', $w->getWeather('深圳', 'all', 'xml'));
        // json
        // 模拟 http 接口相应成功
        $response = new Response(200, [], '{"success": true}');
        // 创建模拟 http
        $client = \Mockery::mock(Client::class);
        // 指定将会产生的行为
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key'        => 'mock-key',
                'city'       => '北京',
                'output'     => 'json',
                'extensions' => 'base',
            ],
        ])
            ->andReturn($response);
        // 将 getHttpClient 方法体会为上面创建的 Http Client 为返回值的模拟方法
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client); //  $client 为上面创建的模拟实例
        //  然后调用 `getWeather` 方法，并断言返回值为模拟的返回值。
        $this->assertSame('"{\"success\": true}"', $w->getWeather('北京'));

    }

    // 测试请求异常
    public function testGetWeatherWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs()) // 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        // 接着需要断言调用时会产生异常。
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $w->getWeather('北京');
    }

    // 测试 http请求
    public function testGetHttpClient()
    {
        $w = new Weather('mock-key');

        // 断言返回结果为 GuzzleHttp\ClientInterface 实例
        $this->assertInstanceOf(ClientInterface::class, $w->getHttpClient());
    }

    public function testSetGuzzleOptions()
    {
        $w = new Weather('mock-key');
        // 设置参数前，timeout 为 null
        $this->assertNull($w->getHttpClient()->getConfig('timeout'));
        // 设置参数
        $w->setGuzzleOptions(['timeout' => 5000]);
        // 设置参数后，timeout 为 5000
        $this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
    }
    // 测试时时接口
    public function testGetLiveWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->expects()->getWeather('北京', 'base', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $w->getLiveWeather('北京'));
    }

    // 测试天气接口
    public function testGetForecastsWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->expects()->getWeather('北京', 'all', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $w->getForecastsWeather('北京'));
    }
}
