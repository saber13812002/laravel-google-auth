<?php namespace Themonkeys\LaravelGoogleAuth;

use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Support\Facades\Session;


class LaravelGoogleAuthServiceProvider extends AuthServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('themonkeys/laravel-google-auth', null, realpath(__DIR__.'/../../'));
        parent::boot();

        $this->app['auth']->extend('google', function($app) {
            return new GoogleAuthGuard(new GoogleUserProvider(), $app['session.store']);
        });
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        parent::register();

        $app = $this->app;

        $app['google-client'] = $app->share(function($app)
        {
            $client = new \Google_Client();
            $client->setApplicationName($app['config']->get('laravel-google-auth::clientId'));
            $client->setClientId($app['config']->get('laravel-google-auth::clientId'));
            $client->setClientSecret($app['config']->get('laravel-google-auth::clientSecret'));
            $client->setRedirectUri(Session::get('url.intended', $app['config']->get('laravel-google-auth::redirectUri')));
            $client->setDeveloperKey($app['config']->get('laravel-google-auth::developerKey'));

            return $client;
        });

	}
}