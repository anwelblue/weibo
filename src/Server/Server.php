<?php

/****
 * 粉丝服务类
 */
namespace Anwelblue\Weibo\Server;

use Anwelblue\Weibo\Core\AbstractApi;
use Anwelblue\Weibo\Core\Application;
use Anwelblue\Weibo\Message\AbstractMessage;
use Anwelblue\Weibo\Message\Click;
use Anwelblue\Weibo\Message\Follow;
use Anwelblue\Weibo\Message\Image;
use Anwelblue\Weibo\Message\Link;
use Anwelblue\Weibo\Message\Mention;
use Anwelblue\Weibo\Message\Position;
use Anwelblue\Weibo\Message\Scan;
use Anwelblue\Weibo\Message\Subscribe;
use Anwelblue\Weibo\Message\Text;
use Anwelblue\Weibo\Message\UnFollow;
use Anwelblue\Weibo\Message\UnSubscribe;
use Anwelblue\Weibo\Message\UnSupport;
use Anwelblue\Weibo\Message\Voice;
use Anwelblue\Weibo\Support\Collection;
use Anwelblue\Weibo\Support\XML;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class Server extends AbstractApi
{

    /***
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /***
     * @var \Anwelblue\Weibo\Core\Application
     */
    protected $app;

    /***
     * @var \Anwelblue\Weibo\Core\Config
     */
    protected $config;

    /***
     * @var string json|xml
     */
    protected $format = 'json';

    /**
     * @var AbstractMessage
     */
    protected $receive = null;

    /***
     * 消息处理器
     * @var array
     */
    protected $messageHandlers = [];

    /***
     * 空消息处理器
     * @var null
     */
    protected $nullHandler = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->request = $app->request;
        parent::__construct($app->access_token);
    }


    public function serve(){
        // 微博接入情况自动响应
        if(! $this->validate()){
            $this->app->logger->addError('validate error',$this->request->query->all());
            $response = new Response('validate error');
            $response->send();
            exit;
        }
        if($this->request->get('echostr')){
            $response = new Response($this->request->get('echostr'));
            $response->send();
            exit;
        }

        $message = $this->getMessage();

        $result = $this->handleMessage($message);

        $response = $this->makeResponse($result);

        return $response;
    }

    /****
     * @param $result
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function makeResponse($result){
        if(is_null($result)){
            if($this->nullHandler){
                $result = call_user_func($this->nullHandler,$this->getReceive());
                if(is_null($result)){
                    return new Response();
                }
            }else{
                $this->app->logger->addDebug('receive an un handle message',$this->getReceive()->all());
                return new Response();
            }
        }elseif($result instanceof AbstractMessage){

        }else{
            $result = new Text($result);
        }

        $result->setFrom($this->getReceive()->getTo())
            ->setTo($this->getReceive()->getFrom());

        $this->app->logger->addDebug('response',$result->all());

        $response = new Response($result->transform($this->getFormat()));

        return $response;
    }

    /****
     * @param AbstractMessage $message
     * @return mixed
     */
    protected function handleMessage(AbstractMessage $message){
        // 不支持不能识别的消息
        if($message instanceof UnSupport){
            $this->app->logger->addWarning('receive an unsupport message',$message->all());
        }
        $result = null;
        foreach ($this->messageHandlers as $handler){
            if($this->checkOption($message,$handler['option'])){
                $result = call_user_func($handler['handler'],$message);
                if(! is_null($result)){
                    break;
                }
            }
        }

        return $result;
    }

    /***
     * @param AbstractMessage $message
     * @param $option
     * @return bool
     */
    protected function checkOption(AbstractMessage $message,$option){
        if($option === 'all'){
            return true;
        }elseif (is_array($option) && in_array($message->getType(),$option)){
            return true;
        }elseif(is_callable($option)){
            return call_user_func($option,$message);
        }

        return false;
    }

    /****
     * 设置消息处理
     * @param $callback 回调函数
     * @param mixed $option 选项
     * @return $this
     */
    public function setMessageHandler($callback, $option = 'all'){
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument is not callable.');
        }

        array_push($this->messageHandlers,[
            'handler' => $callback,
            'option' => $option
        ]);

        return $this;
    }

    /***
     * 设置空消息处理
     * @param $callback
     * @return $this
     */
    public function setNullHandler($callback){
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument is not callable.');
        }

        $this->nullHandler = $callback;
        return $this;
    }

    /***
     * 校验签名
     * @return bool
     */
    protected function validate(){
        //debug模式不验证signature
        if($this->config->debug)return true;

        $appSecret = $this->config->appSecret;
        $timestamp = $this->request->get('timestamp');
        $nonce = $this->request->get('nonce');
        $signature = $this->request->get('signature');

        $tmpArr = [$appSecret,$timestamp,$nonce];

        sort($tmpArr,SORT_STRING);
        $this->app->logger->addDebug('validate',array_merge($tmpArr,[$signature]));
        return sha1(implode($tmpArr)) === $signature;

    }

    /***
     * @return AbstractMessage|Follow|Image|Mention|Position|Text|UnFollow|UnSupport|Voice
     */
    protected function getMessage(){
        if(! is_null($this->receive)){
            return $this->getReceive();
        }
        $content = $this->request->getContent();
        if(preg_match('/^\s*\<xml(.*)\/xml>\s*$/s',$content)){
            $message = $this->parseMessageFromXml($content);
        }else{
            $message = $this->parseMessageFromJson($content);
        }

        $this->setReceive($message);

        return $message;
    }

    /****
     * @param $content
     * @return Follow|Image|Position|Text|UnFollow|UnSupport|Voice
     */
    protected function parseMessageFromXml($content){
        $this->setFormat('xml');
        $data = XML::parse($content);
        $this->app->logger->addDebug('xml:',$data);
        $data = new Collection($data);

        if (empty($data['MsgType'])){
            $message = new UnSupport($data->all());
            $message->setOrigin($content);
            return $message;
        }

        switch ($data['MsgType']){
            case 'text':
                $message = new Text($data['Content']);
                break;
            case 'location' :
                $message = new Position([
                    'latitude' => $data['Location_X'],
                    'longitude' => $data['Location_Y']
                ]);
                break;
            case 'voice' :
                $message = new Voice(['tovfid' => $data['MediaId']]);
                break;
            case 'image':
                $message = new Image(['tovfid' => $data['MediaId']]);
                break;
            case 'event' :
                switch ($data->get('Event')){
                    case 'follow':
                        $message = new Follow($data->all());
                        break;
                    case 'unfollow':
                        $message = new UnFollow($data->all());
                        break;
                    case 'subscribe':
                        $message = new Subscribe($data->all());
                        break;
                    case 'unsubscribe':
                        $message = new UnSubscribe($data->all());
                        break;
                    case 'scan_follow':
                        $message = new Scan($data->all());
                        break;
                    case 'scan':
                        $message = new Scan($data->all());
                        break;
                    case 'click':
                        $message = new Click($data->all());
                        break ;
                    case 'view':
                        $message = new Link($data->all());
                        break;
                    default:
                        $message = new UnSupport($data->all());
                        break;
                }
                break;
            default :
                $message = new UnSupport($data->all());
                break;
        }

        $message->setFrom($data['FromUserName'])
            ->setTo($data['ToUserName'])
            ->setOrigin($content);

        return $message;
    }

    /***
     * @param $content
     * @return Follow|Image|Mention|Position|Text|UnFollow|UnSupport|Voice
     */
    protected function parseMessageFromJson($content){
        $this->setFormat('json');
        $data = json_decode($content,true);
        $this->app->logger->addDebug('json:',$data);
        $data = new Collection($data);

        if(empty($data['type'])){
            $message = new UnSupport($data->all());
            $message->setOrigin($content);
            return $message;
        }

        switch($data['type']){
            // 普通消息
            case 'text':
                $message = new Text($data['text']);
                break;
            case 'position' :
                $message = new Position($data['data']);
                break;
            case 'voice' :
                $message = new Voice($data['data']);
                break;
            case 'image':
                $message = new Image($data['data']);
                break;


            //事件推送
            case 'event':
                switch ($data->get('data.subtype')){
                    case 'follow':
                        $message = new Follow($data->all());
                        break;
                    case 'unfollow':
                        $message = new UnFollow($data->all());
                        break;
                    case 'subscribe':
                        $message = new Subscribe($data->all());
                        break;
                    case 'unsubscribe':
                        $message = new UnSubscribe($data->all());
                        break;
                    case 'scan_follow':
                        $message = new Scan($data->all());
                        break;
                    case 'scan':
                        $message = new Scan($data->all());
                        break;
                    case 'click':
                        $message = new Click($data->all());
                        break ;
                    case 'view':
                        $message = new Link($data->all());
                        break;
                    default:
                        $message = new UnSupport($data->all());
                        break;
                }
                break;

            // 被@ 消息
            case 'mention':
                return new Mention($data->all());
                break;
            default :
                $message = new UnSupport($data->all());
                break;
        }

        $message->setFrom($data['sender_id'])
            ->setTo($data['receiver_id'])
            ->setOrigin($content);

        return $message;
    }

    /***
     * 设置数据格式
     * @param $format
     * @return $this
     */
    public function setFormat($format){
        $this->format = $format;

        return $this;
    }

    /***
     * @param $format
     * @return string
     */
    public function getFormat(){
        return $this->format;
    }

    /***
     * 设置接收的数据
     * @param AbstractMessage $message
     * @return $this
     */
    public function setReceive(AbstractMessage $message){
        $this->receive = $message;

        return $this;
    }

    /****
     * @return AbstractMessage
     */
    public function getReceive(){
        return $this->receive;
    }

}