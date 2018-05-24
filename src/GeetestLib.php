<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2018/3/8
 * Time: 上午24:42
 */

namespace Lzp\Geetest;

use GuzzleHttp\Client;

/**
 * 极验行为式验证安全平台，php 网站主后台包含的库文件
 * Class Geetest
 */
class GeetestLib
{

    const GT_SDK_VERSION = 'php_3.0.0';

    const GEETEST_API = 'http://api.geetest.com/';

    /**
     * @var int $timeout
     */
    public static $timeout = 5;

    /**
     * @var $response
     */
    private $response;

    /**
     * @var $captcha_id
     */
    private $captcha_id;

    /**
     * @var $private_key
     */
    private $private_key;

    /**
     * Geetest constructor.
     * @param $captcha_id
     * @param $private_key
     */
    public function __construct($captcha_id, $private_key)
    {
        $this->captcha_id = $captcha_id;
        $this->private_key = $private_key;
    }

    /**
     * 判断极验服务器是否宕机
     * @param array $param
     * @param true $new_captcha
     * @return bool
     */
    public function pre_process(Array $param, $new_captcha = true)
    {
        $data = [
            'gt' => $this->captcha_id,
            'new_captcha' => intval($new_captcha)
        ];
        $data = array_merge($data, $param);
        $query = http_build_query($data);
        $uri = 'register.php' . '?' . $query;
        $client = new Client([
            'base_uri' => self::GEETEST_API,
            'timeout' => self::$timeout,
        ]);
        $response = $client->request('GET', $uri);
        if ($response->getStatusCode() === 200) {
            $challenge = $response->getBody();
            if (strlen($challenge) === 32) {
                $this->success_process($challenge);
                return true;
            }
        }
        $this->failback_process();
        return false;
    }

    /**
     * 正常模式处理
     * @param $challenge
     */
    private function success_process($challenge)
    {
        $challenge = md5($challenge . $this->private_key);
        $result = [
            'success' => 1,
            'gt' => $this->captcha_id,
            'challenge' => $challenge,
            'new_captcha' => 1
        ];
        $this->response = $result;
    }

    /**
     * 宕机模式处理
     */
    private function failback_process()
    {
        $rnd1 = md5(rand(0, 100));
        $rnd2 = md5(rand(0, 100));
        $challenge = $rnd1 . substr($rnd2, 0, 2);
        $result = [
            'success' => 0,
            'gt' => $this->captcha_id,
            'challenge' => $challenge,
            'new_captcha' => 1
        ];
        $this->response = $result;
    }

    /**
     * 返回数组方便扩展
     * @return array
     */
    public function get_response()
    {
        return $this->response;
    }

    /**
     * 正常模式获取验证结果
     * @param string $challenge
     * @param string $validate
     * @param string $seccode
     * @param array $param
     * @return bool
     */
    public function success_validate($challenge, $validate, $seccode, $param)
    {
        if (!$this->check_validate($challenge, $validate)) {
            return false;
        }
        $query = [
            'seccode' => $seccode,
            'timestamp' => time(),
            'challenge' => $challenge,
            'captchaid' => $this->captcha_id,
            'json_format' => 1,
            'sdk' => self::GT_SDK_VERSION
        ];
        $query = array_merge($query, $param);
        $uri = 'validate.php';
        $client = new Client([
            'base_uri' => self::GEETEST_API,
            'timeout' => self::$timeout,
        ]);
        $options = [
            'form_params' => $query,
        ];
        $response = $client->request('POST', $uri, $options);
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $validate = $response->getBody();
        $obj = json_decode($validate, true);
        if (!$obj) {
            return false;
        }
        return $obj['seccode'] == md5($seccode);
    }

    /**
     * 宕机模式获取验证结果
     * @param $challenge
     * @param $validate
     * @return bool
     */
    public function fail_validate($challenge, $validate)
    {
        return md5($challenge) == $validate;
    }

    /**
     * 校验数据
     * @param $challenge
     * @param $validate
     * @return bool
     */
    private function check_validate($challenge, $validate)
    {
        if (strlen($validate) != 32) {
            return false;
        }
        if (md5($this->private_key . 'geetest' . $challenge) != $validate) {
            return false;
        }
        return true;
    }

}