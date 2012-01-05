<?php

namespace Calliope\Entity\Message;

use Calliope\Collection\EmailAddressCollection;
use Calliope\Entity\ImapMessagePart;
use Calliope\Entity\ImapBody;
use Calliope\Entity\Attachment;
use Calliope\Helper\ArrayHelper;
use Calliope\Helper\CharacterEncodingHelper;
use \DateTime;

class ImapMessage implements MessageInterface {

    private $imapResource;
    private $uid;
    private $headers;
    private $body = null;
    private $textBody = null;
    private $characterEncodingText = null;
    private $htmlBody = null;
    private $characterEncodingHtml = null;
    private $isPartsProcessed = false;
    private $attachments = array();
    private $inlines = array();

    public function __construct($imapResource, $uid) {
        $this->imapResource = $imapResource;
        $this->uid = $uid;
        $this->headers = $this->fetchHeaders($imapResource, $uid);
    }

    private function fetchHeaders($imapResource, $uid) {
        return \imap_rfc822_parse_headers(\imap_fetchheader($imapResource, $uid, \FT_UID));
    }

    /**
     *
     * @return ImapMessagePart
     */
    public function getBody() {

        if ($this->body === null) {
            $partsStructure = \imap_fetchstructure($this->imapResource, $this->uid, \FT_UID);
            $this->body = new ImapMessagePart($this->imapResource, $this->uid, $partsStructure);
        }
        return $this->body;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getTo() {
        if (!isset($this->headers->to)) {
            $this->headers->to = array();
        }
        return new EmailAddressCollection($this->headers->to);
    }

    public function getCc() {
        if (!isset($this->headers->cc)) {
            $this->headers->cc = array();
        }
        return new EmailAddressCollection($this->headers->cc);
    }

    public function getBcc() {
        if (!isset($this->headers->bcc)) {
            $this->headers->bcc = array();
        }
        return new EmailAddressCollection($this->headers->bcc);
    }

    public function getFrom() {
        if (!isset($this->headers->from)) {
            $this->headers->from = array();
        }
        return new EmailAddressCollection($this->headers->from);
    }

    public function getReplyTo() {
        if (!isset($this->headers->reply_to)) {
            $this->headers->reply_to = array();
        }
        return new EmailAddressCollection($this->headers->reply_to);
    }

    public function getSender() {
        if (!isset($this->headers->sender)) {
            $this->headers->sender = array();
        }
        return new EmailAddressCollection($this->headers->sender);
    }

    public function getReturnPath() {
        if (!isset($this->headers->return_path)) {
            $this->headers->return_path = array();
        }
        return new EmailAddressCollection($this->headers->return_path);
    }

    public function getMessageId() {
        return $this->headers->message_id;
    }

    public function getSubject() {
        return CharacterEncodingHelper::header($this->headers->subject, CharacterEncodingHelper::UTF8);
    }

    public function isRead() {
        return!($this->headers->Unseen == 'U' OR $this->headers->Recent == 'N');
    }

    public function getFollowUpTo() {
        return $this->headers->followup_to;
    }

    public function getInReplyTo() {
        return $this->headers->in_reply_to;
    }

    public function getReceivedAt() {
        try {
            return new \DateTime($this->headers->Date);
        } catch(\Exception $e) {
            return new \DateTime();
        }
    }

    public function getReferences() {
        if(isset($this->headers->references)) {
            return \explode(' ', $this->headers->references);
        } else {
            return array();
        }
    }

    public function getSentAt() {
        try {
            return new \DateTime($this->headers->date);
        } catch(\Exception $e) {
            return new \DateTime();
        }
    }

    public function markAsRead() {
        \imap_fetchbody($this->imapResource, $this->uid, 0, \FT_UID);
    }

    public function markAsDeleted() {
        \imap_delete($this->imapResource, $this->uid, \FT_UID);
    }

    private function parseTextPart(ImapMessagePart $basePart) {
        if ($basePart->getSubType() === 'PLAIN') {
            $this->textBody = $basePart->getBody();
            $this->characterEncodingText = $basePart->getCharacterEncoding();
        }
        if ($basePart->getSubType() === 'HTML') {
            $this->htmlBody = $basePart->getBody();
            $this->characterEncodingHtml = $basePart->getCharacterEncoding();
        }
    }
    
    private function parsePart(ImapMessagePart $basePart,
            $saveAttachment = false, $saveInline = false) {
        switch ($basePart->getType()) {
            case ImapMessagePart::TYPE_TEXT:
                $this->parseTextPart($basePart);
                break;
            case ImapMessagePart::TYPE_MULTIPART:
                foreach ($basePart->getParts() as $part) {
                    $this->parsePart($part, $saveAttachment, $saveInline);
                }
                break;
            default:
                break;
        }
        if ($saveAttachment === true && $basePart->isAttachment() && !$basePart->isInline()) {
            $this->attachments[] = new Attachment($basePart->getFileName(), $basePart->getBody());
        }
        if($saveInline === true && $basePart->isInline() && !$basePart->isAttachment()) {
            $this->inlines[] = new Attachment($basePart->getFileName(), $basePart->getBody());
        }
    }

    private function parseParts() {
        if ($this->isPartsProcessed === false) {
            $this->isPartsProcessed = true;
            $this->parsePart($this->getBody());
        }
    }

    public function getTextBody($encoding = CharacterEncodingHelper::UTF8) {
        $this->parseParts();

        return CharacterEncodingHelper::text($this->textBody, $encoding, $this->characterEncodingText);
    }

    public function getHtmlBody($encoding = CharacterEncodingHelper::UTF8) {
        $this->parseParts();

        return CharacterEncodingHelper::text($this->htmlBody, $encoding, $this->characterEncodingHtml);
    }

    public function getAttachments() {
        $this->parsePart($this->getBody(), true, true);

        return $this->attachments;
    }
    
    public function getInlines() {
        $this->parsePart($this->getBody(), true, true);

        return $this->inlines;
    }
    
    public function hasInlines() {
        return (count($this->getInlines()) > 0);
    }
    public function hasAttachments() {
        return (count($this->getAttachments()) > 0);
    }

}