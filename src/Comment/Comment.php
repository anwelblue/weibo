<?php
namespace Anwelblue\Weibo\Comment;


use Anwelblue\Weibo\Core\AbstractApi;

class Comment extends AbstractApi
{
    const API_SHOW = 'https://api.weibo.com/2/comments/show.json';

    const API_BY_ME = 'https://api.weibo.com/2/comments/by_me.json';

    const API_TO_ME = 'https://api.weibo.com/2/comments/to_me.json';

    const API_TIMELINE = 'https://api.weibo.com/2/comments/timeline.json';

    const API_MENTIONS = 'https://api.weibo.com/2/comments/mentions.json';

    const API_SHOW_BATCH = 'https://api.weibo.com/2/comments/show_batch.json';

    const API_CREATE = 'https://api.weibo.com/2/comments/create.json';

    const API_DESTROY = 'https://api.weibo.com/2/comments/destroy.json';

    const API_DESTROY_BATCH = 'https://api.weibo.com/2/comments/destroy_batch.json';

    const API_REPLY = 'https://api.weibo.com/2/comments/reply.json';

    /***
     * 获取某条微博的评论列表
     * @param int $id 微博ID
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function show($id,$since_id = 0,$max_id = 0,$count = 20,$page = 1,$filter_by_author = 0){
        $compact = compact('id','since_id','max_id','count','page','filter_by_author');

        $url = static::API_SHOW . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 我发出的评论列表
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博、2：来自微群，默认为0
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function byMe($since_id = 0,$max_id = 0,$count = 20,$page = 1,$filter_by_source = 0){
        $compact = compact('since_id','max_id','count','page','filter_by_source');

        $url = static::API_BY_ME . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 我收到的评论列表
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
     * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博、2：来自微群，默认为0
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function toMe($since_id = 0,$max_id = 0,$count = 20,$page = 1,$filter_by_author = 0,$filter_by_source = 0){
        $compact = compact('since_id','max_id','count','page','filter_by_author','filter_by_source');

        $url = static::API_TO_ME . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 获取用户发送及收到的评论列表
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $trim_user 返回值中user字段开关，0：返回完整user字段、1：user字段仅返回user_id，默认为0
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function timeline($since_id = 0,$max_id = 0,$count = 20,$page = 1,$trim_user = 0){
        $compact = compact('since_id','max_id','count','page','trim_user');

        $url = static::API_TIMELINE . '?' . http_build_query($compact);

        return $this->get($url);
    }


    /***
     * 获取@到我的评论
     * @param int $since_id 起始id
     * @param int $max_id 最大id
     * @param int $count 分页条数
     * @param int $page 第几页
     * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
     * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博、2：来自微群，默认为0
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function mentions($since_id = 0,$max_id = 0,$count = 20,$page = 1,$filter_by_author = 0,$filter_by_source = 0){
        $compact = compact('since_id','max_id','count','page','filter_by_author','filter_by_source');

        $url = static::API_MENTIONS . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /**
     * 批量获取评论内容
     * @param string|array $ids 评论id
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function showBatch($ids){
        if(! is_array($ids)){
            $ids = [$ids];
        }

        $ids = implode(',',$ids);

        $compact = ['cids' => $ids];

        $url = static::API_SHOW_BATCH . '?' . http_build_query($compact);

        return $this->get($url);
    }

    /***
     * 评论一条微博
     * @param string $comment 评论内容
     * @param int $id 微博id
     * @param int $comment_ori 当评论转发微博时，是否评论给原微博，0：否、1：是，默认为0
     * @param string $rip 开发者上报的操作用户真实IP，形如：211.156.0.1。
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function create($comment,$id,$comment_ori = 0,$rip = null){
        $compact = compact('comment','id','comment_ori','rip');

        return $this->post(static::API_CREATE,$compact);
    }

    /***
     * 删除评论
     * @param int $cid 评论的id
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function destroy($cid){
        return $this->post(static::API_DESTROY,['cid' => $cid]);
    }

    /****
     * 批量删除评论
     * @param int|array $ids 评论的id
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function destroyBatch($ids){
        if(! is_array($ids)){
            $ids = [$ids];
        }

        $ids = implode(',',$ids);

        $compact = ['cids' => $ids];

        return $this->post(static::API_DESTROY_BATCH,$compact);
    }

    /***
     * @param int $cid 需要回复的评论ID。
     * @param int $id 需要评论的微博ID。
     * @param string $comment 回复评论内容，必须做URLencode。
     * @param int $without_mention 回复中是否自动加入“回复@用户名”，0：是、1：否，默认为0。
     * @param int $comment_ori 当评论转发微博时，是否评论给原微博，0：否、1：是，默认为0。
     * @param string $rip 开发者上报的操作用户真实IP，形如：211.156.0.1。
     * @return \Anwelblue\Weibo\Support\Collection
     */
    public function reply($cid,$id,$comment,$without_mention = 0,$comment_ori = 0,$rip = null){
        $compact = compact('cid','id','comment','without_mention','comment_ori','rip');

        return $this->post(static::API_REPLY,$compact);
    }

}