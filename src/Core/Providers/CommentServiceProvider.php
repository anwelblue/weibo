<?php
/***
 * 微博用户接口注册类
 */
namespace Anwelblue\Weibo\Core\Providers;

use Anwelblue\Weibo\Comment\Comment;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CommentServiceProvider implements ServiceProviderInterface
{
	
	public function register(Container $application){
		
		$application['comment'] = function($application){
			return new Comment($application['access_token']);
		};
		
	}
	
}