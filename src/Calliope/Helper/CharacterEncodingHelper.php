<?php

namespace Calliope\Helper;

class CharacterEncodingHelper {
    const UTF8 = 'UTF-8';
    const RAW = null;
    const ISO8859_1 = 'ISO-8859-1';
    const ISO8859_15 = 'ISO-8859-15';
    const ASCII = 'ASCII';
    
    static private function getEncoding($string, $encoding) {
        if (!ArrayHelper::in_arrayi($encoding, \mb_list_encodings())) {
            if (strpos(strtolower($encoding), strtolower('Windows-125')) !== false) {
                $encoding = 'Windows-1252';
            } else {
                $encoding = \mb_detect_encoding($string);
            }
        }
        return $encoding;
    }

    static public function text($string, $to, $from = null) {
        $from = self::getEncoding($string, $from);
        if ($to === self::RAW || $to === $from) {
            return $string;
        }
        return \mb_convert_encoding($string, $to, $from);
    }

    static public function header($mimeString, $targetCharset = self::UTF8) {
        $decodedStr = '';
        $mimeParts = \imap_mime_header_decode($mimeString);
        foreach ($mimeParts as $mimePart) {
            $charset = $mimePart->charset;
            if ($charset == 'default') {
                $charset = self::ASCII;
            }

            $decodedStr .= self::text($mimePart->text, $targetCharset, $charset);
        }

        return $decodedStr;
    }

}