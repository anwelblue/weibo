<?php
namespace Anwelblue\Weibo\Message;

class Subscribe extends AbstractMessage
{
    public function getType()
    {
        // TODO: Implement getType() method.
        return 'event.subscribe';
    }

}