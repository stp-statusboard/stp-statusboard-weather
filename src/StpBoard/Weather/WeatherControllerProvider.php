<?php

namespace StpBoard\Weather;

use Cmfcmf\OpenWeatherMap;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use StpBoard\Base\BoardProviderInterface;
use StpBoard\Base\TwigTrait;

class WeatherControllerProvider implements ControllerProviderInterface, BoardProviderInterface
{
    use TwigTrait;

    /**
     * Returns route prefix, starting with "/"
     *
     * @return string
     */
    public static function getRoutePrefix()
    {
        return '/weather';
    }

    /**
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $this->initTwig(__DIR__ . '/views');
        $controllers = $app['controllers_factory'];

        $controllers->get(
            '/',
            function (Application $app) {
                $request = $app['request'];

                $city = $request->get('city');
                if (empty($city)) {
                    return $this->twig->render('error.html.twig');
                }

                $appId = $request->get('appId', '');

                $openWeatherMap = new OpenWeatherMap();
                try {
                    $weather = $openWeatherMap->getWeather($city, 'metric', 'en', $appId);
                } catch (\Exception $e) {
                    return $this->twig->render('error.html.twig');
                }

                return $this->twig->render(
                    'weather.html.twig',
                    [
                        'id' => $request->get('id'),
                        'temperature' => (int)$weather->temperature->now->getValue(),
                        'icon' => $this->codeToIcon($weather->weather->icon),
                        'city' => $weather->city->name
                    ]
                );
            }
        );

        return $controllers;
    }

    public function codeToIcon($code)
    {
        $icons = [
            '01d' => 'wi-day-sunny',
            '02d' => 'wi-day-cloudy',
            '03d' => 'wi-cloud',
            '04d' => 'wi-cloudy',
            '09d' => 'wi-showers',
            '10d' => 'wi-rain',
            '11d' => 'wi-thunderstorm',
            '13d' => 'wi-snow',
            '50d' => 'wi-fog',
            '01n' => 'wi-night-clear',
            '02n' => 'wi-night-cloudy',
            '03n' => 'wi-cloud',
            '04n' => 'wi-cloudy',
            '09n' => 'wi-showers',
            '10n' => 'wi-rain',
            '11n' => 'wi-thunderstorm',
            '13n' => 'wi-snow',
            '50n' => 'wi-fog'
        ];

        return $icons[$code];
    }
}
