<?php

namespace Calliope\Server;

use Calliope\Server\Exception\ImapConnectException;
use Calliope\Collection\ImapCollection;
use Calliope\Criteria\ImapCriteria;

class ImapServer implements ServerInterface {

    protected $imapResource = null;

    public function __construct($mailbox, $user, $password, $options = null, $nRetries = 1, $params = array()) {
        $resource = @\imap_open($mailbox, $user, $password, $options, $nRetries, $params);

        if ($resource === false) {
            throw new ImapConnectException($mailbox, $user, $password);
        }

        $this->imapResource = $resource;
    }

    

    public function __destruct() {
        \imap_close($this->imapResource);
    }

    public function fetchUnread() {
        $c = new ImapCriteria();
        $c->unread();
        return $this->fetch($c);
    }

    public function fetch(ImapCriteria $criteria) {
        $messageUidList = \imap_search($this->imapResource, $criteria->getCriteria(), \SE_UID);

        if($messageUidList === false)
        {
            $messageUidList = array();
        }

        $messageCollection = new ImapCollection($this->imapResource, $messageUidList);

        return $messageCollection;
    }

}