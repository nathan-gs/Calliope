<?php

namespace Calliope\Entity\Message;

interface MessageInterface
{
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getTo();
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getFrom();
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getCc();
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getBcc();
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getReplyTo();
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getSender();
    /**
     * @return \Calliope\Collection\EmailAddressCollection
     */
    public function getReturnPath();

    /**
     * @return \DateTime
     */
    public function getSentAt();
    /**
     * @return \DateTime
     */
    public function getReceivedAt();

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @return boolean
     */
    public function isRead();

    /**
     * 
     */
    public function markAsRead();

    public function getReferences();

    public function getFollowUpTo();

    public function getInReplyTo();

    public function getMessageId();

    public function markAsDeleted();
}