<?php

namespace Calliope\Collection;

use Calliope\Entity\Message\ImapMessage;

class ImapCollection implements \Iterator, \Countable {
    private $imapResource;
    private $messageUidList = array();

    private $messageUidListCount = 0;
    private $currentMessageUidKey = 0;

    public function __construct($imapResource, $messageUidList) {
        $this->imapResource = $imapResource;
        $this->messageUidList = $messageUidList;
        $this->messageUidListCount = count($this->messageUidList);
    }
    
    public function rewind() {
        $this->currentMessageUidKey = 0;
    }
    
    public function next() {
        $this->currentMessageUidKey++;
    }

    public function valid() {
        return $this->currentMessageUidKey < $this->messageUidListCount;
    }

    public function current() {
        return new ImapMessage($this->imapResource, $this->key());
    }

    public function key() {
        return $this->messageUidList[$this->currentMessageUidKey];
    }

    public function count() {
        return $this->messageUidListCount;
    }

}