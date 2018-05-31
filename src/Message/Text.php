<?php
namespace Anwelblue\Weibo\Message;

use Anwelblue\Weibo\Support\XML;

class Text extends AbstractMessage
{
    /***
     * 仅仅提供一个字符串或者完整的数组
     * Text constructor.
     * @param string|array $items
     */
    public function __construct($items = [])
    {
        if(!is_array($items)){
            $items = [
                'text' => $items
            ];
        }
        parent::__construct($items);
    }

    public function getType()
    {
        return 'text';
    }

    public function transformToJson(){
        $data = [
            'result' => true,
            'receiver_id' => $this->getTo(),
            'sender_id' => $this->getFrom(),
            'type' => 'text',
            'data' => urlencode(json_encode($this->getDataArray()))
        ];

        return json_encode($data);
    }

    public function getDataArray(){
        return [
            'text' => $this->get('text')
        ];
    }

    public function transformToXml(){
        $data = [
            'ToUserName' => $this->getTo(),
            'FromUserName' => $this->getFrom(),
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $this->get('text')
        ];

        return XML::build($data);
    }
}