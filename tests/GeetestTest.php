<?php

use Lzp\Geetest\Geetest;

class GeetestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $user_id = 'testGeetest';

    /**
     * Test something true.
     */
    public function testProcess()
    {
	    $data = [
		    'user_id' => $this->user_id,
		    'client_type' => 'web',
		    'ip_address' => '127.0.0.1'
	    ];
        Geetest::shouldReceive('pre_process')->once()->with($data)->andReturn();
    }

    /**
     * Test response.
     */
    public function testResponseStr()
    {
        Geetest::shouldReceive('get_response')->once()->with()->andReturn();
    }

}

