<?php
/***
 * 授权相关功能类
 */
namespace Anwelblue\Weibo\Core;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Anwelblue\Weibo\Exception\RequestException;
use Anwelblue\Weibo\Exception\UnsetAccessTokenException;

class AccessToken
{
	public $app;
	/**
	 * The weibo application appKey
	 * @var string
	 */
	protected $appKey;
	
	/**
	 * The weibo application appSecret
	 * @var string
	 */
	protected $appSecret;
	
	/***
	 * The http client
	 * @var \Anwelblue\Weibo\Core\Http
	 */
	protected $http;

    /***
     * @var string
     */
	protected $token;
	
	
	const API_OAUTH = 'https://api.weibo.com/oauth2/authorize';
	
	const API_TOKEN_GET = 'https://api.weibo.com/oauth2/access_token';
	
	const INVAIL_CODE = 21325;
	
	
	
	public function __construct($application,$appKey,$appSecret){
		$this->app = $application;
		$this->appKey = $appKey;
		$this->appSecret = $appSecret;
	}

    /***
     * 获取token
     * @return string
     * @throws UnsetAccessTokenException
     */
	public function getToken(){
		$token = $this->token;
		if(is_null($token)){
			throw new UnsetAccessTokenException('must be set access_token before used it');
		}
		
		return $this->token;
	}

    /***
     * 设置token
     * @param string $token
     * @return $this
     */
	public function setToken($token){
		$this->token = $token;
		return $this;
	}

    /***
     * 跳转授权
     */
	public function oauth($redirect = null,$state = null){
		$query = [
				'client_id' => $this->appKey,
				'redirect_uri' => $this->prepareCallbackUrl($redirect),
				'scope' => $this->app['config']->get('oauth.scope'),
				'state' => $state ?? $this->app['config']->get('oauth.state')
			];
		$url = static::API_OAUTH . '?' . http_build_query($query);
		$response = new RedirectResponse($url);
		$response->send();
		exit;
	}

    /***
     * 从服务器获取access_token
     * @param string $code
     * @return string
     * @throws RequestException
     * @throws UnsetAccessTokenException
     */
	public function getTokenFromServer($code){
		$data = [
			'client_id' => $this->appKey,
			'client_secret' => $this->appSecret,
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $this->prepareCallbackUrl()
		];
		$response = $this->getHttp()->post(static::API_TOKEN_GET,$data);
		if(! empty($response->error)){
			if($response->error_code == static::INVAIL_CODE){
				$this->getToken();
			}
			throw new RequestException($response);
		}
		
		$this->setToken($response->access_token);
		
		return $response->access_token;
	}
	
	
	
	/**
     * Return the appKey.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->appKey;
    }
	
	/**
     * Return the appSecret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->appSecret;
    }

    /***
     * 获取回调url
     * @return string
     */
	public function prepareCallbackUrl($callback){
		$callback = $callback ?? $this->app['config']->get('oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $this->app['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
	}
	
	/**
	 * Set the http
	 * @param \Anwelblue\Weibo\Core\Http $http
     * @return $this 	
     */	 
	public function setHttp(Http $http){
		$this->http = $http;
		return $this;
	}
	
	/**
	 * Return the http
	 * @return \Anwelblue\Weibo\Core\Http
	 */
	public function getHttp(){
		if(! $this->http instanceof Http){
			$this->http = new Http();
		}
		return $this->http;
	}
	
}