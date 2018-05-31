<?php
/***
 * 微博api接口容器
 */
namespace Anwelblue\Weibo\Core;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;


/***
 * @property \Anwelblue\Weibo\Core\Config $config
 * @property \Monolog\Logger $logger
 * @property \Symfony\Component\HttpFoundation\Request $request
 * @property \Anwelblue\Weibo\Core\AccessToken $access_token
 * @property \Anwelblue\Weibo\Server\Server $server
 * @property \Anwelblue\Weibo\User\User $user
 * @property \Anwelblue\Weibo\Status\TimeLine $timeline
 * @property \Anwelblue\Weibo\Comment\Comment $comment
 *
 * Class Application
 * @package Anwelblue\Weibo\Core
 */
class Application extends Container
{
    /**
     * 微博服务
     * @var array
     */
	protected $providers = [
		Providers\ServerServiceProvider::class,
		Providers\UserServiceProvider::class,
		Providers\StatusServiceProvider::class,
        Providers\CommentServiceProvider::class
	];
	
	public function __construct($config){
		parent::__construct();
		
		$this['config'] = new Config($config);
		
		if ($this['config']['debug']) {
            error_reporting(E_ALL);
        }

        $this->initializeLogger();
		
		$this->registerBase();
		
		$this->registerProviders();
	}

    /**
     * 注册基础服务
     */
	private function registerBase(){
		$this['request'] = function () {
            return Request::createFromGlobals();
        };
		$this['access_token'] = function ($application){
			$config = $application['config'];
			
			return new AccessToken($application,$config['appKey'],$config['appSecret']);
		};
	}

	private function initializeLogger(){
        $logger = new Logger('anwelblue.weibo');
        $logFile = $this['config']['log.file'];
        $logger->pushHandler(new StreamHandler(
                $logFile,
                $this['config']->get('log.level', Logger::WARNING),
                true,
                $this['config']->get('log.permission', null))
        );

        $this['logger'] = $logger;
    }
	
	/**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Application
     */
    public function addProvider($provider)
    {
        array_push($this->providers, $provider);

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }
	
	/**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }
	
	
	/**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }
	
	
	/**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }
}