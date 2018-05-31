<?php
/****
 * 微博服务注册类
 */
namespace Anwelblue\Weibo;

use Anwelblue\Weibo\Core\Application;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class ServiceProvider implements ServiceProviderInterface{
	
	public function register(Container $app){
		$app['weibo'] = function($app){
			$config = $app['config']->get('weibo');
			return new Application($config);
		};
	}
	
}