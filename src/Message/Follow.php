<?php
namespace Anwelblue\Weibo\Message;

class Follow extends AbstractMessage{

    public function getType()
    {
        // TODO: Implement getType() method.
        return 'event.follow';
    }

}