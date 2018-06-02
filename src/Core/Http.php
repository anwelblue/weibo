<?php
/***
 * request 类
 */
namespace Anwelblue\Weibo\Core;

use Anwelblue\Weibo\Support\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Http
{
	
	/**
     * Http client.
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /***
     * 发送请求
     * @param string $method
     * @param string $url
     * @param array $data
     * @return Collection
     */
	public function request($method,$url,$data = []){
		try{
			if(strtoupper($method) === 'GET' || strtoupper($method) === 'HEAD'){
				$response =  $this->getClient()->request('get',$url);
			}else{
			    if(empty($data['multipart']) && empty($data['body']) && empty($data['form_params'])){
			        $data = ['form_params' => $data];
                }
				$response = $this->getClient()->request($method,$url,$data);
			}
			$response = $response->getBody()->getContents();
		}catch(ClientException $e){
			$response = $e->getResponse()->getBody()->getContents();
		}
		
		$response = new Collection(json_decode($response,true));
		return $response;
	}


    /***
     * 发送一个get请求
     * @param string $url
     * @return Collection
     */
	public function get($url){
		return $this->request('GET',$url);
	}

    /***
     * 发送一个post请求
     * @param string $url
     * @param array $data
     * @return Collection
     */
	public function post($url,$data = []){
		return $this->request('POST',$url,$data);
	}
	
	/**
     * Set GuzzleHttp\Client.
     *
     * @param \GuzzleHttp\Client $client
     *
     * @return Http
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (!($this->client instanceof Client)) {
            $this->client = new Client([
                'verify' => false
            ]);
        }

        return $this->client;
    }
	
}