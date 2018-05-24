<?php

namespace Lzp\Geetest;

use Illuminate\Routing\Controller;

class GeetestController extends Controller
{
    use GeetestCaptcha;

	public function __construct()
	{
        $this->middleware('api');
	}

}