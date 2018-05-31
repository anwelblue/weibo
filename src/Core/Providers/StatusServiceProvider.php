<?php
/***
 * 微博-微博相关注册类
 */
namespace Anwelblue\Weibo\Core\Providers;

use Anwelblue\Weibo\Status\TimeLine;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class StatusServiceProvider implements ServiceProviderInterface
{
	
	public function register(Container $application){
		
		$application['timeline'] = function($application){
			return new TimeLine($application['access_token']);
		};
		
	}
	
}