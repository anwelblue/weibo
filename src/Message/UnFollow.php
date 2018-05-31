<?php
namespace Anwelblue\Weibo\Message;

class UnFollow extends AbstractMessage{

    public function getType()
    {
        // TODO: Implement getType() method.
        return 'event.unfollow';
    }

}