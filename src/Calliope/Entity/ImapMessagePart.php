<?php

namespace Calliope\Entity;

class ImapMessagePart {

    private $imapResource;
    private $uid;
    private $partStructure;
    private $partPrefix;
    private $parts = array();
    private $body = null;
    private $rawBody = null;
    private $encoding = null;
    private $type = null;
    private $subType = null;
    private $isAttachment = false;
    private $fileName = null;
    private $characterEncoding = null;

    const ENCODING_7BIT = 0;
    const ENCODING_8BIT = 1;
    const ENCODING_BINARY = 2;
    const ENCODING_BASE64 = 3;
    const ENCODING_QUOTED_PRINTABLE = 4;
    const ENCODING_OTHER = 5;

    const TYPE_TEXT = 0;
    const TYPE_MULTIPART = 1;
    const TYPE_MESSAGE = 2;
    const TYPE_APPLICATION = 3;
    const TYPE_AUDIO = 4;
    const TYPE_IMAGE = 5;
    const TYPE_VIDEO = 6;
    const TYPE_MODEL = 7;
    const TYPE_X_UNKOWN = 8;
    const TYPE_X_UNKOWN9 = 9;
    const TYPE_X_UNKOWN10 = 10;
    const TYPE_X_UNKOWN11 = 11;
    const TYPE_X_UNKOWN12 = 12;
    const TYPE_X_UNKOWN13 = 13;
    const TYPE_X_UNKOWN14 = 14;
    const TYPE_MAX = 15;

    public function __construct($imapResource, $uid, $partStructure, $partPrefix = null) {
        $this->imapResource = $imapResource;
        $this->uid = $uid;
        $this->partStructure = $partStructure;
        $this->partPrefix = $partPrefix;

        $this->parseStructure();
        $this->fetchParts();
    }

    private function parseStructure() {
        $this->encoding = $this->partStructure->encoding;
        $this->type = $this->partStructure->type;
        $this->subType = $this->partStructure->subtype;

        if ($this->partStructure->ifdparameters) {
            foreach ($this->partStructure->dparameters as $parameterObject) {
                if (\strtolower($parameterObject->attribute) == 'filename') {
                    $this->isAttachment = true;
                    $this->fileName = $parameterObject->value;
                }
                if (\strtolower($parameterObject->attribute) == 'name') {
                    $this->isAttachment = true;
                    $this->fileName = $parameterObject->value;
                }
            }
        }
        if ($this->partStructure->ifparameters) {
            foreach ($this->partStructure->parameters as $parameterObject) {
                if (\strtolower($parameterObject->attribute) == 'filename') {
                    $this->isAttachment = true;
                    $this->fileName = $parameterObject->value;
                }
                if (\strtolower($parameterObject->attribute) == 'name') {
                    $this->isAttachment = true;
                    $this->fileName = $parameterObject->value;
                }
                if (\strtolower($parameterObject->attribute) == 'charset') {
                    $this->characterEncoding = $parameterObject->value;
                }
            }
        }
    }

    private function fetchParts() {

        if (isset($this->partStructure->parts)) {
            foreach ($this->partStructure->parts as $number => $subpart) {
                $partPrefix = '';
                if ($this->partPrefix !== null) {
                    $partPrefix .= $this->partPrefix . '.';
                }

                $partPrefix .= ( $number + 1);
                $this->parts[] = new ImapMessagePart($this->imapResource, $this->uid, $subpart, $partPrefix);
            }
        }
    }

    /**
     * @return array
     */
    public function getParts() {
        return $this->parts;
    }

    private function fetchBody() {
        if ($this->rawBody === null) {
            if (\strlen($this->partPrefix) > 0) {
                $this->rawBody = \imap_fetchbody($this->imapResource, $this->uid, $this->partPrefix, \FT_UID | \FT_PEEK);
            } else {
                $this->rawBody = \imap_body($this->imapResource, $this->uid, \FT_UID | \FT_PEEK);
            }
        }
    }

    public function getRawBody() {
        $this->fetchBody();

        return $this->rawBody;
    }

    private function parseBody() {
        if ($this->body === null) {
            switch ($this->encoding) {
                case self::ENCODING_BASE64:
                    $this->body = \base64_decode($this->rawBody);
                    break;
                case self::ENCODING_QUOTED_PRINTABLE:
                    $this->body = \imap_qprint($this->rawBody);
                    break;
                case self::ENCODING_7BIT:
                case self::ENCODING_8BIT:
                case self::ENCODING_BINARY:
                default:
                    $this->body = $this->rawBody;
                    break;
            }
        }
    }

    public function getBody() {
        $this->fetchBody();
        $this->parseBody();

        return $this->body;
    }

    public function getFileName() {
        return $this->fileName;
    }

    /**
     * @return boolean
     */
    public function isAttachment() {
        return $this->isAttachment;
    }

    /**
     *
     * @return ImapMessagePart::ENCODING_*
     */
    public function getEncoding() {
        return $this->encoding;
    }

    public function getCharacterEncoding() {
        return $this->characterEncoding;
    }

    /**
     * @return ÌmapMessagePart::TYPE_*
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSubType() {
        return $this->subType;
    }

    public function getPartStructure() {
        return $this->partStructure;
    }

    public function getPrefix() {
        return $this->partPrefix;
    }

    /**
     * @return boolean
     */
    public function isInline() {
        
        return in_array($this->getType(), array(self::TYPE_AUDIO, self::TYPE_IMAGE, self::TYPE_VIDEO)) && !$this->isAttachment();
    }

}