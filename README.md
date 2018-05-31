   # WeiboSDK
   
  微博SDK
  
  ## 框架要求
  
  php>=5.5
  
  ## 安装
```shell
    composer require anwelblue/weibo
```

   ## 配置
   
   复制 config.php 到配置文件，修改成对应的配置
   
   ## 使用方式
   #### 获取服务容器
   
       $app = new \Anwelblue\Weibo\Core\Application($config);
       
   #### Oauth2授权  
       $access_token = $app->access_token;
       
       //callback 留空表示启用配置上的url
       $access_token->oauth($callback);
       
   #### 微博API  
       $access_token = $app->access_token;
       $access_token->setToken($token);
       
       //微博接口
       $timeline = $app->timeline;
       $result = $timeline->home();
       
       //评论接口
       $comment = $app->comment;
       $result = $comment->toMe();
       
       //用户接口
       $user = $app->user;
       $result = $user->getUserByUid();
       
       
   #### 粉丝服务  
   1.获取服务
   
        $server = $app->server;
        
   2.设置消息处理器
   
   >callback方式
   
       $server->setMessageHandler(function($message){
            return '回复内容';
       });
   
   >对象方式
       
       $server->setMessageHandler([$this,'handle']);
           
       
   3.条件过滤
   
        $server->setMessageHandler(function($message){
            return '收到文本';
        },['text']);
        
        $server->setMessageHandler(function($message){
            return '收到图片或者语音';
        },['image','voice']);
        
        $server->setMessageHandler(function($message){
            return '收到事件';
        },function($message){
            return strpos($message->getType(),'event.') !== false;
        });
        
   4.设置无法识别的消息的响应
   
        $server->setMessageHandler(function($message){
            return '您发的消息我无法识别';
        },['un-support']);
        
   5.设置空响应(当所有消息服务器均为无效响应时)
   
        $server->setNullHandler(function($message){
            return '暂时找不到处理方式';
        });
        
   6.响应类型
   
   >文本
   
        $server->setMessageHandler(function($message){
            return '响应文本';
        });
   
   >位置
   
        $server->setMessageHandler(function($message){
            return new \Anwelblue\Weibo\Message\Position([
                'longitude' => '120.12555',
                'latitude' => '25.3645555'
            ]);
        });  
        
   >图文
   
        $server->setMessageHandler(function($message){
             return new \Anwelblue\Weibo\Message\Article([
                [
                    'display_name' => '标题1',
                    'summary' => '描述1',
                    'image' => '图片1',
                    'url' => '链接地址1'
                ],[
                    'display_name' => '标题2',
                    'summary' => '描述2',
                    'image' => '图片2',
                    'url' => '链接地址2'
                ]
             ]);
        });        
        
   
   
   >开始服务
   
        $response = $server->serve();
        $response->send();
        
   >注：
   
   >1.服务可以设置多个消息处理器
   2.响应的时候只要return一个消息类就可以文本直接回复字符串，不响应return null或者不return
   3.按照设置的顺序，只要是有效响应，则会终止执行消息往下继续执行，直接将响应消息发送出去  
   
   完整的例子
   
        $app = new \Anwelblue\Weibo\Core\Application($config);
        
        $server = $app->server;
        
        $server->setMessageHandler(function($message){
            return '我收到消息了';
        });
        
        $response = $server->serve();
        
        $response->send();
   
   ## License
   MIT  
        
            
       
        