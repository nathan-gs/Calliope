<?php

namespace Calliope\Entity;

use Calliope\Helper\CharacterEncodingHelper;

class EmailAddress {

    private $name;
    private $mailbox;
    private $host;

    public function __construct($name, $mailbox, $host) {
        $this->name = $name;
        $this->mailbox = $mailbox;
        $this->host = $host;
    }

    public function getName() {
        return CharacterEncodingHelper::header($this->name, CharacterEncodingHelper::UTF8);
    }

    public function getAddress() {
        return CharacterEncodingHelper::header(
                        $this->mailbox, CharacterEncodingHelper::UTF8
                ) . '@' . CharacterEncodingHelper::header(
                        $this->host, CharacterEncodingHelper::UTF8);
    }

}