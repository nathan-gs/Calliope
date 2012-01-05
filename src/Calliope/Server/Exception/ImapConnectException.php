<?php

namespace Calliope\Server\Exception;

class ImapConnectException extends ConnectException
{
    private $mailbox;
    private $username;
    private $password;
    private $imapErrors = array();

    public function __construct($mailbox, $username, $password, $code = null, $previous = null) {
        $this->mailbox = $mailbox;
        $this->username = $username;
        $this->password = $password;
        $this->imapErrors = \imap_errors();

        $message = 'Error connecting to: '.$mailbox.' with user: '.$username.' ['.  \imap_last_error().']';
        parent::__construct($message, $code, $previous);
    }
}