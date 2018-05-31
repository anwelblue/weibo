<?php
namespace Anwelblue\Weibo\Message;

use Anwelblue\Weibo\Exception\UnSupportMessageResponseException;

class Position extends AbstractMessage
{
    /***
     * 完整的数据或者单独的坐标数据
     * Position constructor.
     * @param array $items ['longitude' => 11,'latitude' => 12]
     */
    public function __construct($items = [])
    {
        if(empty($items['data'])){
            $items = [
                'data' => $items
            ];
        }
        parent::__construct($items);
    }

    public function getType()
    {
        return 'position';
    }

    public function transformToJson(){
        $data = [
            'result' => true,
            'receiver_id' => $this->getTo(),
            'sender_id' => $this->getFrom(),
            'type' => 'position',
            'data' => urlencode(json_encode([
                'longitude' => $this->get('data.longitude'),
                'latitude' => $this->get('data.latitude')
            ]))
        ];

        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
}