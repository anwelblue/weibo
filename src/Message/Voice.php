<?php
namespace Anwelblue\Weibo\Message;

use Anwelblue\Weibo\Exception\UnSupportMessageResponseException;

class Voice extends AbstractMessage
{
    /***
     * 完整的数据或者单独的voiceID
     * Position constructor.
     * @param array $items ['vfid' => 11,'tovfid' => 12]
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
        return 'voice';
    }
}