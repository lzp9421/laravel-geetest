<?php namespace Lzp\Geetest;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

trait GeetestCaptcha
{
	/**
	 * Get geetest.
	 */
	public function getGeetest()
	{
		$data = [
			'user_id' => @Auth::user() ? @Auth::user()->id : uniqid(Str::random(), true),
			'client_type' => 'web',
			'ip_address' => Request::ip()
		];
        $status = Geetest::pre_process($data, true);
        $result = Geetest::get_response();
        $result['geetest_key'] = uniqid(Str::random(), true);
        $key = sprintf(Config::get('geetest.session_key'), $result['geetest_key']);
        Cache::put($key, [
            'status' => $status,
            'user_id' => $data['user_id'],
        ], 60);
        return $result;
	}
}