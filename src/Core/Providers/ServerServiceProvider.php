<?php
/***
 * 粉丝服务注册类
 */
namespace Anwelblue\Weibo\Core\Providers;

use Anwelblue\Weibo\Server\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServerServiceProvider implements ServiceProviderInterface
{
	
	public function register(Container $application){
		
		$application['server'] = function($application){
			return new Server($application);
		};
		
	}
	
}