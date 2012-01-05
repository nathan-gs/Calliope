<?php

namespace Calliope\Entity;

class Attachment {

    private $fileName;
    private $mimeType = null;
    private $content;
    
    private $static_content_types = array(
        '.doc' => 'application/msword',
        '.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.xls' => 'application/vnd.ms-excel',
        '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        '.ppt' => 'application/ms-powerpoint',
        '.pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    );

    public function __construct($fileName, $content) {
        $this->fileName = $fileName;
        $this->content = $content;
    }

    private function parseMimeType() {
        if ($this->mimeType !== null) { return; }

        foreach($this->static_content_types as $ext => $static_content_type)
        {
            if ($this->endswith($this->getFileName(), $ext)) {
                $this->mimeType = $static_content_type;
                return;
            }
        }

        $f = new \finfo();
        $this->mimeType = $f->buffer($this->content, \FILEINFO_MIME_TYPE);
    }

    public function getMimeType() {
        $this->parseMimeType();
        return $this->mimeType;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getContent() {
        return $this->content;
    }

    function endswith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, -$testlen) === 0;
    }
}