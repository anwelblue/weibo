<?php

/****
 * 用户相关接口类
 */

namespace Anwelblue\Weibo\User;

use Anwelblue\Weibo\Core\AbstractApi;

class User extends AbstractApi
{
	
	const API_CURRENT_UID = 'https://api.weibo.com/2/account/get_uid.json';
	
	const API_USER_INFO = 'https://api.weibo.com/2/users/show.json';
	
	const API_USER_INFO_BY_DOMAIN = 'https://api.weibo.com/2/users/domain_show.json';
	
	const API_USER_COUNTS = 'https://api.weibo.com/2/users/counts.json';

    /***
     * 获取授权用户的uid
     * @return int
     */
	public function getCurrentUid(){
		
		$response = $this->get(static::API_CURRENT_UID);
		
		return $response->uid;
		
	}

    /***
     * 根据uid获取用户信息
     * @param int $uid
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function getUserByUid($uid = null){
		if(is_null($uid)){
			$uid = $this->getCurrentUid();
		}
		
		$response = $this->get(static::API_USER_INFO . '?uid='.$uid);
		
		return $response;
	}

    /****
     * 根据昵称获取用户信息
     * @param string $nickname
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function getUserByNickname($nickname){
		return $this->get(static::API_USER_INFO . '?screen_name='.$nickname);
	}

    /****
     * 根据用户个性url的关键字获取用户信息
     * @param string $domain
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function getUserByDomain($domain){
		return $this->get(static::API_USER_INFO_BY_DOMAIN . '?domain='.$domain);
	}

    /****
     * 批量获取用户的粉丝数、关注数、微博数
     * @param string|array $uids
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function getCounts($uids = null){
		if(is_null($uids)){
			$uids = [$this->getCurrentUid()];
		}elseif(! is_array($uids)){
			$uids = [$uids];
		}
		return $this->get(static::API_USER_COUNTS . '?uids='.implode(',',$uids));
	}
	
}