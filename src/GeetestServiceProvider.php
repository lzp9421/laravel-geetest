<?php namespace Lzp\Geetest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class GeetestServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config.php' => config_path('geetest.php'),
		], 'geetest');

        Route::get(Config::get('geetest.url'), 'Lzp\Geetest\GeetestController@getGeetest');

        Validator::extend('geetest', function () {
            $request = \request();
		    $req = $request->only('geetest_key', 'geetest_challenge', 'geetest_validate', 'geetest_seccode');
			list($geetest_key, $challenge, $validate, $seccode) = array_values($req);
            $key = sprintf(Config::get('geetest.session_key'), $geetest_key);
			$session = Cache::pull($key);
            $data = [
                'user_id' => @Auth::user() ? @Auth::user()->id : $session['user_id'],
                'client_type' => 'web',
                'ip_address' => $request->ip()
            ];
			if ($session['status'] == 1) {
                //服务器正常
                $result = Geetest::success_validate($challenge, $validate, $seccode, $data);
			} else {
                $result = Geetest::fail_validate($challenge, $validate);
			}
            return $result;
		});

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('geetest', function () {
		    $param = [
                'captcha_id' => Config::get('geetest.id'),
                'private_key' => Config::get('geetest.key'),
            ];
			return $this->app->make(GeetestLib::class, $param);
		});
	}
}
