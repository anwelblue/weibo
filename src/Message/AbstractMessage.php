<?php
namespace Anwelblue\Weibo\Message;

use Anwelblue\Weibo\Exception\UnSupportMessageResponseException;
use Anwelblue\Weibo\Support\Collection;

abstract class AbstractMessage extends Collection
{

    /***
     * 发送用户ID
     * @var string
     */
    protected $from;

    /****
     * 接收用户ID
     * @var string
     */
    protected $to;

    /***
     * 原始消息字符串
     * @var string
     */
    protected $origin;


    abstract public function getType();

    public function transform($format){
        if($format === 'xml'){
            return $this->transformToXml();
        }else{
            return $this->transformToJson();
        }
    }

    public function transformToJson(){
        throw new UnSupportMessageResponseException('can not support voice response');
    }

    public function transformToXml(){
        throw new UnSupportMessageResponseException('can not support voice response');
    }

    /****
     * @param string $from
     * @return $this
     */
    public function setFrom($from){
        $this->from = $from;

        return $this;
    }

    /***
     * @return string
     */
    public function getFrom(){
        return $this->from;
    }

    /***
     * @param string $to
     * @return $this
     */
    public function setTo($to){
        $this->to = $to;

        return $this;
    }

    /***
     * @return string
     */
    public function getTo(){
        return $this->to;
    }

    /***
     * @param string $origin
     * @return $this
     */
    public function setOrigin($origin){
        $this->origin = $origin;

        return $this;
    }

    /***
     * @return string
     */
    public function getOrigin(){
        return $this->origin;
    }

}