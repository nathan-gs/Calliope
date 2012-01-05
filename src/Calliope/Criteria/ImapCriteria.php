<?php

namespace Calliope\Criteria;

class ImapCriteria {
    protected $criteria = '';

    public function unread() {
        $this->criteria .= 'UNSEEN ';
        return $this;
    }
    
    public function read() {
        $this->criteria .= 'SEEN ';
        return $this;
    }

    /**
     * @return $this
     */
    public function from($fromEmailAddress) {
        $this->criteria .= 'FROM "'.$fromEmailAddress.'" ';
        return $this;
    }
    /**
     * @return $this
     */
    public function cc($ccEmailAddress) {
        $this->criteria .= 'CC "'.$ccEmailAddress.'" ';
        return $this;
    }
    /**
     * @return $this
     */
    public function bcc($bccEmailAddress) {
        $this->criteria .= 'BCC "'.$bccEmailAddress.'" ';
        return $this;
    }
    /**
     * @return $this
     */
    public function to($toEmailAddress) {
        $this->criteria .= 'TO "'.$toEmailAddress.'" ';
        return $this;
    }

    /**
     * @return $this
     */
    public function on(\DateTime $date)
    {
        $this->criteria .= 'ON "'.$date->format('j M Y, g.ia O').'" ';
        return $this;
    }

    /**
     * @return $this
     */
    public function since(\DateTime $date)
    {
        $this->criteria .= 'SINCE "'.$date->format('j M Y, g.ia O').'" ';
        return $this;
    }

    /**
     * @return $this
     */
    public function before(\DateTime $date) {
        $this->criteria .= 'BEFORE "'.$date->format('j M Y, g.ia O').'" ';
        return $this;
    }

    /**
     * @return $this
     */
    public function subject($subject) {
        $this->criteria .= 'SUBJECT "'.$subject.'" ';
        return $this;
    }

    public function orCriteria(ImapCriteria $criteria1, ImapCriteria $criteria2) {
        $this->criteria .= 'OR ('.$criteria1->getCriteria().') ('.$criteria2->getCriteria().')';
        return $this;
    }

    public function not() {
        $this->criteria .= 'NOT ';
        return $this;
    }

    public function custom($customCriteria) {
        $this->criteria .= $customCriteria.' ';
        return $this;
    }

    public function text($text) {
        $this->criteria .= 'TEXT "'.$text.'" ';
        return $this;
    }
    public function body($text) {
        $this->criteria .= 'BODY "'.$text.'" ';
        return $this;
    }
    public function header($field, $text) {
        $this->criteria .= 'HEADER "'.$field.'" "'.$text.'" ';
        return $this;
    }

    /**
     * @return string
     */
    public function getCriteria() {
        return trim($this->criteria);
    }
}