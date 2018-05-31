<?php
/***
 * 微博用户接口注册类
 */
namespace Anwelblue\Weibo\Core\Providers;

use Anwelblue\Weibo\Service\Service;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceServiceProvider implements ServiceProviderInterface
{
	
	public function register(Container $application){
		
		$application['service'] = function($application){
			return new Service($application['access_token']);
		};
		
	}
	
}