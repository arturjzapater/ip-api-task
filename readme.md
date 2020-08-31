# Desenio Test Task

## Build and Run

The project is built using Laravel 5.8 and React 16.2.

To clone the project and install dependencies, run:

```bash
git clone git@github.com:arturjzapater/ip-api-task.git
cd ip-api-task
composer install
npm install
```

To compile the front end for development, run:

```bash
npm run dev
```

To compile it for production, run:

```bash
npm run prod
```

Make a copy of [`.env.example`](.env.example), rename it as `.env` and generate a new app key:

```bash
cp -a .env.example .env
php artisan key:generate
```

To serve the files locally, add the folder to your [Homestead folders and sites](https://laravel.com/docs/7.x/homestead#configuring-homestead) or run:

```bash
php artisan serve
```

To test the project's feature tests, run:
```bash
phpunit
```

To run the browser tests, you will need to update the `.env` file and set the key `APP_URL` to the URL where your server is using. Then you can run the tests with:

```bash
php artisan dusk
```

If the chrome-driver's version doesn't match your browser version, you can set the driver to your specific version (e.gr. 84):

```bash
php artisan dusk:chrome-driver 84
```

_(Note: You will also need Chrome installed on your machine for them to work. I installed chromium-browser on my Homestead VM following [these steps](https://laracasts.com/discuss/channels/forge/can-i-run-laravel-dusk-on-my-forge-server#reply=327364))_

## Project Structure

The project is structured following Laravel's standard. It has a [`Controllers`](app/Http/Controllers) folder; a [`resources`](resources/) folder for front end assets; a [`routes`](routes/) folder, and a [`tests`](tests/) folder.

### Controllers

Contains the controllers used in the application's routes.

- The class `IpController` exposes the method `country`, which issues a call to [ip-api](https://ip-api.com/), parses the response and returns a JSON with the fields `status` and `country` if the request has been successful or an error description otherwise.

- The class `HomeController` exposes the method `index`, which renders the app's home page.

### Resources

Contains the application's front end. The React code is inside the [`react`](resources/js/react/) folder. It is divided in singe-responsibility components and it holds all the state in the [`App` component](resources/js/react/components/App.js).

`App` has a state variable named `status`, which can be either `loading` or `idle`. Initially it is set to `idle`. Clicking the button changes the state to `loading`, which triggers a request to the route `/api/country`. Once the response arrives, the status is set back to `idle` and the button's text is updated.

### Routes

- `/`: The home route that loads the main page. Located in [`web.php`](routes/web.php).

- `/api/country`: The route in charge of calling [ip-api](https://ip-api.com). Located in [`api.php`](routes/api.php).

### Tests

- [`Feature`](tests/Feature) contains feature tests in charge of testing the responses of the routes. [`GetCountryTest`](tests/Feature/GetCountryTest.php) mocks a Guzzle client and ensures that the route `/api/country` responds adequately to all possible responses from ip-api.

- [`Browser`](tests/Browser) contains [Dusk](https://laravel.com/docs/7.x/dusk) tests that ensure that the page is shown correctly and responds properly to user actions.

## Considerations on the Client's IP

Since the client's IP address can be obscured by reverse proxies and load balancers, getting it can be a bit tricky. Laravel's request object includes the method `getClientIp`, which this project uses. But this method by itself might not be enough. If there is a load balancer, its IP address needs to be included in the [trusted proxies](app/Http/Middleware/TrustProxies.php). For some services, such as Heroku, it needs to be set to trust all proxies and then ensure that only request from the load balancer can be sent to the server.

I considered using a custom method to find out the client's real IP address. But I discarded that option because it didn't seem as compliant with Laravel's philosophy as setting trusted proxies. Moreover, this solution would require additional changes to Laravel's throttle middleware if the application grew to need it.

Since in a local environment there is no external IP address, the server will use 24.48.0.1 as the client's IP (configurable in [config settings](config/dev.php)) when it is not running in production.

## Considerations on Making Requests from the Server

In order to allow the server to make requests to an external API, I have used [Guzzle](https://guzzle.readthedocs.io/en/latest/index.html). On Laravel 7.x, I would have used Laravel's [HTTP Client](https://laravel.com/docs/7.x/http-client), which would have been easy to mock as a Facade in the feature tests. However, since the project's instructions specified Laravel 5.x as the back end framework, I had to find a different way. Guzzle seemed a good option because it is easy to use in tests.

I have made the HTTP requests synchronous, because the route controller depends on ip-api's response before it can create a response to the original request. So it didn't make sense to use asynchronous calls.

## Considerations on Testing

The tests for this application are all feature and browser tests. Since all the logic is geared towards generating a proper response, it made sense to test the `IpController` as a whole using feature tests, rather than unit tests. The success of the feature tests ensures that its private functions work as they should.

In order to be able to use Guzzle in the feature tests, I used dependency injection in the class `IpController`, so that it would be easy to use a mock version instead.

The browser tests use Laravel Dusk, which provides an easy-to-use browser automation for testing. Dusk is configured to use ChromeDriver. So Chrome/Chromium needs to be installed to run these tests.
