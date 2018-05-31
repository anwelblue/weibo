<?php
namespace Anwelblue\Weibo\Service;

use Anwelblue\Weibo\Core\AbstractApi;
use Anwelblue\Weibo\Exception\UnSupportMessageResponseException;
use Anwelblue\Weibo\Message\AbstractMessage;
use InvalidArgumentException;

class Service extends AbstractApi
{
    const API_REPLY = 'https://m.api.weibo.com/2/messages/reply.json';

    const API_SEND_ALL = 'https://m.api.weibo.com/2/messages/sendall.json';

    /****
     * 发送客服消息
     * @param AbstractMessage $message
     * @param int $save_sender_box
     * @throws UnSupportMessageResponseException
     * @throws InvalidArgumentException
     */
    public function reply(AbstractMessage $message,$save_sender_box = 1){
        if(! in_array($message->getType(),['text','articles','position'])){
            throw new UnSupportMessageResponseException('can not send this type message');
        }
        if(empty($message->getTo())){
            throw new InvalidArgumentException('Message must set receiver id!');
        }
        $json = json_decode($message->transformToJson(),true);

        $data = [
            'type' => $message->getType(),
            'data' => $json['data'],
            'receiver_id' => $message->getTo(),
            'save_sender_box' =>$save_sender_box
        ];

        $this->post(static::API_REPLY,$data);
    }

    /****
     * 高级群发
     * @param AbstractMessage $message
     * @param int $group
     * @param array $touser
     * @return \Anwelblue\Weibo\Support\Collection
     * @throws UnSupportMessageResponseException
     * @throws InvalidArgumentException
     */
    public function sendAll(AbstractMessage $message,$group = 0,$touser = []){
        if(! in_array($message->getType(),['text','articles'])){
            throw new UnSupportMessageResponseException('can not send this type message');
        }
        if($group <= 0 && empty($touser)){
            throw new InvalidArgumentException('must be choose either a group or users');
        }

        if($group > 0){
            $data = [
                'filter' => [
                    'group_id' => $group
                ]
            ];
        }else{
            $data = [
                'touser' => $touser
            ];
        }

        if($message->getType() === 'text'){
            $data['text'] = [
                'content' => $message->get('text')
            ];
            $data['msgtype'] = 'text';
        }else{
            $data['articles'] = $message->get('articles');
            $data['msgtype'] = 'articles';
        }

        return $this->post(static::API_SEND_ALL,$data);
    }

}