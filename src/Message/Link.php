<?php
namespace Anwelblue\Weibo\Message;

class Link extends AbstractMessage
{

    public function getType()
    {
        // TODO: Implement getType() method.
        return 'event.view';
    }

}