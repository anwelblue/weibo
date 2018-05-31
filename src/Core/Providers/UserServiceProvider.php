<?php
/***
 * 微博用户接口注册类
 */
namespace Anwelblue\Weibo\Core\Providers;


use Anwelblue\Weibo\User\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
	
	public function register(Container $application){
		
		$application['user'] = function($application){
			return new User($application['access_token']);
		};
		
	}
	
}