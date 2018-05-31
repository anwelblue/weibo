<?php
/***
 * 容器类
 */
namespace Anwelblue\Weibo\Core;

use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer{

    public function __get($id){
        return $this->offsetGet($id);
    }

    public function __set($id,$value){
        return $this->offsetSet($id,$value);
    }

    public function __isset($id)
    {
        return $this->offsetExists($id);
    }

    public function __unset($id)
    {
        return $this->offsetUnset($id);
    }

}