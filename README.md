# Weather Widget

## Config

```
-
  id: weather1
  provider: \StpBoard\Weather\WeatherControllerProvider
  refresh: 3600
  width: 4
  params:
    city: Cracow
    appId: APP_ID
```

APP_ID is optional. OpenWeatherMap API has some limits for number of requests, you can use api key to omit this limit
