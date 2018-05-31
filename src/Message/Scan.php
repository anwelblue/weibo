<?php
namespace Anwelblue\Weibo\Message;

class Scan extends AbstractMessage
{

    public function getType()
    {
        // TODO: Implement getType() method.
        return 'event.scan_follow';
    }

}

