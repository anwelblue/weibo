<?php
/****
 * api请求失败Exception类
 */
namespace Anwelblue\Weibo\Exception;

use Exception;

class RequestException extends Exception{
	protected $response;
	
	public function __construct($response){
		$message = '['.$response->error_code.']weibo api request error:'.$response->toJson();
		parent::__construct($message);
		$this->response = $response;
	}
}