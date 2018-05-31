<?php
/***
 * 微博api-微博相关接口类
 */
namespace Anwelblue\Weibo\Status;

use Anwelblue\Weibo\Core\AbstractApi;
use Anwelblue\Weibo\Support\Collection;

class TimeLine extends AbstractApi
{
	const API_TIMELINE_HOME = 'https://api.weibo.com/2/statuses/home_timeline.json';
	
	const API_TIMELINE_USER = 'https://api.weibo.com/2/statuses/user_timeline.json';
	
	const API_TIMELINE_REPOST = 'https://api.weibo.com/2/statuses/repost_timeline.json';

	const API_MENTIONS = 'https://api.weibo.com/2/statuses/mentions.json';

	const API_SHOW = 'https://api.weibo.com/2/statuses/show.json';

	const API_COUNT = 'https://api.weibo.com/2/statuses/count.json';

	const API_EMOTIONS = 'https://api.weibo.com/2/emotions.json';

	const API_SHARE = 'https://api.weibo.com/2/statuses/share.json';

    /***
     * 获取用户微博和用户关注的微博
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $base_app 仅当前应用的数据源
     * @param int $feature 过滤类型 0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0
     * @param int $trim_user 返回值中user字段开关，0：返回完整user字段、1：user字段仅返回user_id，默认为0。
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function home($since_id = 0,$max_id = 0,$count = 20,$page = 1,$base_app = 0,$feature = 0,$trim_user = 0){
		$url = static::API_TIMELINE_HOME . '?' 
		. http_build_query(compact('since_id','max_id','count','page','base_app','feature','trim_user'));
		
		return $this->get($url);
	}

    /***
     * 获取用户发表的微博 默认当前授权用户
     * @param int $uid 微博用户的id
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $base_app 仅当前应用的数据源
     * @param int $feature 过滤类型 0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0
     * @param int $trim_user 返回值中user字段开关，0：返回完整user字段、1：user字段仅返回user_id，默认为0。
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function user($uid = 0 ,$since_id = 0,$max_id = 0,$count = 20,$page = 1,$base_app = 0,$feature = 0,$trim_user = 0){
		$compact = compact('since_id','max_id','count','page','base_app','feature','trim_user');
		if($uid > 0){
			$compact['uid'] = $uid;
		}
		$url = static::API_TIMELINE_USER . '?' . http_build_query($compact);
		
		return $this->get($url);
	}

    /****
     * 获取微博被转发的微博
     * @param $id 微博的id
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function repost($id,$since_id = 0,$max_id = 0,$count = 20,$page = 1,$filter_by_author = 0){
		$compact = compact('id','since_id','max_id','count','page','filter_by_author');
		
		$url = static::API_TIMELINE_REPOST . '?' . http_build_query($compact);
		
		return $this->get($url);
	}

    /***
     * 获取 @我的微博
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
     * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博、2：来自微群，默认为0
     * @param int $filter_by_type 原创筛选类型，0：全部微博、1：原创的微博，默认为0。
     * @return \Anwelblue\Weibo\Support\Collection
     */
	public function mentions($since_id = 0,$max_id = 0,$count = 20,$page = 1
        ,$filter_by_author = 0,$filter_by_source = 0,$filter_by_type = 0){

        $compact = compact('since_id','max_id','count','page','filter_by_author','filter_by_source','filter_by_type');

        $url = static::API_MENTIONS . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /****
     * 根据ID获取单条微博信息
     * @param int $id
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function show($id){
        $compact = compact('id');

        $url = static::API_SHOW . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 批量获取指定微博的转发数评论数
     * @param array $ids
     * @return \Anwelblue\Weibo\Support\Collection
     */
    /****
     * @param array $ids

     */
    public function count($ids = []){
        if(! is_array($ids)){
            $ids = [$ids];
        }

        $ids = implode(',',$ids);

        $compact = compact('ids');

        $url = static::API_COUNT . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 获取官方表情信息
     * @param string $type 表情类别，face：普通表情、ani：魔法表情、cartoon：动漫表情，默认为face。
     * @param string $language 语言类别，cnname：简体、twname：繁体，默认为cnname。
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function emotions($type = 'face',$language = 'cnname'){
        $compact = compact('type','cnname');

        $url = static::API_EMOTIONS . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 第三方分享链接到微博
     * @param string $status 用户分享到微博的文本内容，必须做URLencode
     * @param string $pic 用户想要分享到微博的图片
     * @param string $rip 开发者上报的操作用户真实IP
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function share($status,$pic,$rip = null){
        $data = [
            [
                'name' => 'status',
                'contents' => $status
            ]
        ];
        $path = realpath($pic);
        if(! file_exists($path)){
            return new Collection([
                'request' => static::API_SHARE,
                'error_code' => 20006,
                'error' => '图片不存在'
            ]);
        }
        $data[] = [
            'name' => 'pic',
            'contents' => fopen($path,'r')
        ];

        if(! is_null($rip)){
            $data[] = [
                'name' => 'rip',
                'contents' => $rip
            ];
        }
        $data = ['multipart' => $data];

        return $this->post(static::API_SHARE,$data);
    }
	
}