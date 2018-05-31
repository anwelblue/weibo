<?php
namespace Anwelblue\Weibo\Message;

use Anwelblue\Weibo\Support\XML;

class Article extends AbstractMessage
{

    /***
     * Article constructor.
     *
     * [
     *     articles => [
     *        [
     *              display_name  => 标题,
     *              summary => 描述
     *              image => 图片绝对地址
     *              url => 链接地址
     *         ]
     *    ]
     * ]
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        if(empty($items['articles'])){
            $items = [
                'articles' => $items
            ];
        }
        parent::__construct($items);
    }

    public function getType()
    {
        // TODO: Implement getType() method.
        return 'articles';
    }

    public function transformToJson()
    {
        $data = [
            'result' => true,
            'receiver_id' => $this->getTo(),
            'sender_id' => $this->getFrom(),
            'type' => 'articles',
            'data' => urlencode(json_encode([
                'articles' => $this->get('articles')
            ]))
        ];

        return json_encode($data);
    }

    public function transformToXml()
    {
        $articles = [];
        foreach($this->get('articles') as $a){
            $articles[] = [
                'Title' => $a['display_name'],
                'Description' => $a['summary'],
                'PicUrl' => $a['image'],
                'Url' => $a['url']
            ];
        }
        $data = [
            'ToUserName' => $this->getTo(),
            'FromUserName' => $this->getFrom(),
            'CreateTime' => time(),
            'MsgType' => 'news',
            'ArticleCount' => count($articles),
            'Articles' => $articles
        ];

        return XML::build($data,'xml','item','',false);
    }

}