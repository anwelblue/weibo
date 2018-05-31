<?php
/***
 * 微博api基类，自动传递access_token参数
 */
namespace Anwelblue\Weibo\Core;


abstract class AbstractApi
{
	
	/***
	 * The http client
	 * @var \Anwelblue\Weibo\Core\Http
	 */
	protected $http;
	
	/**
     * The request token.
     *
     * @var \Anwelblue\Weibo\Core\AccessToken
     */
    protected $accessToken;
	
	/**
     * Constructor.
     *
     * @param \Anwelblue\Weibo\Core\AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
    }

    /***
     * 发送一个get请求
     * @param string $url
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function get($url){
		return $this->getHttp()->get($this->fixedUrl($url));
	}

    /***
     * 发送一个post请求
     * @param string $url
     * @param array $data
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function post($url,$data = []){
		return $this->getHttp()->post($this->fixedUrl($url),$data);
	}

    /***
     * 发送一个请求
     * @param string $method
     * @param string $url
     * @param array $data
     * @return \Anwelblue\Weibo\Support\Collection|mixed|\Psr\Http\Message\ResponseInterface|string
     */
	public function request($method,$url,$data = []){
		return $this->getHttp()->request($method,$this->fixedUrl($url),$data);
	}

    /***
     * 自动加入access_token 参数
     * @param string $url
     * @return string
     * @throws \Anwelblue\Weibo\Exception\UnsetAccessTokenException
     */
	protected function fixedUrl($url){
		return $url 
			.(strpos($url,'?') === false ? '?' : '&')
			. http_build_query([
				'access_token' => $this->getAccessToken()->getToken()	
			]);
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
	
	/**
     * Return the current accessToken.
     *
     * @return \Anwelblue\Weibo\Core\AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
	
	/**
     * Set the request token.
     *
     * @param \Anwelblue\Weibo\Core\AccessToken $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
	
	
}