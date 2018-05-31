<?php
namespace Anwelblue\Weibo\Message;

class UnSubscribe extends AbstractMessage
{

    public function getType()
    {
        // TODO: Implement getType() method.
        return 'event.unsubscribe';
    }

}