<?php


namespace Calliope\Collection;
use Calliope\Entity\EmailAddress;

class EmailAddressCollection implements \Iterator, \Countable
{
    private $emailAddressArray;
    private $currentKey;
    private $count;

    public function __construct($emailAddressArray)
    {
        $this->emailAddressArray = $emailAddressArray;
        $this->count = count($emailAddressArray);
    }
    
    public function current() {
        
        $current = $this->emailAddressArray[$this->currentKey];
        $personal = isset($current->personal) ? $current->personal : '';
        
        return new EmailAddress($personal, $current->mailbox, $current->host);
    }
    public function key() {
        return $this->emailAddressArray[$this->currentKey];
    }
    public function next() {
        $this->currentKey++;
    }
    public function rewind() {
        $this->currentKey = 0;
    }
    public function valid() {
        return $this->currentKey < $this->count;
    }

    public function count() {
        return $this->count;
    }
}