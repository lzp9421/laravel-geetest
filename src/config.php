<?php
return [

	/*
	|--------------------------------------------------------------------------
	| Config Geetest Id
	|--------------------------------------------------------------------------
	|
	| Here you can config your yunpian api key from yunpian provided.
	|
	*/
	'id' => env('GEETEST_ID'),

	/*
	|--------------------------------------------------------------------------
	| Config Geetest Key
	|--------------------------------------------------------------------------
	|
	| Here you can config your yunpian api key from yunpian provided.
	|
	*/
	'key' => env('GEETEST_KEY'),

	/*
	|--------------------------------------------------------------------------
	| Config Geetest Session Key
	|--------------------------------------------------------------------------
	|
	| Here you can config your yunpian api key from yunpian provided.
	|
	*/
	'session_key' => 'LZP_GEETEST_KEY_%s',

	/*
	|--------------------------------------------------------------------------
	| Config Geetest URL
	|--------------------------------------------------------------------------
	|
	| Here you can config your geetest url for ajax validation.
	|
	*/
	'url' => '/geetest',

	/*
	|--------------------------------------------------------------------------
	| Config Geetest Protocol
	|--------------------------------------------------------------------------
	|
	| Here you can config your geetest url for ajax validation.
	| 
	| Options: http or https
	|
	*/
	'protocol' => 'http',

];