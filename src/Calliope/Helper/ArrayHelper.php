<?php

namespace Calliope\Helper;

class ArrayHelper {

    static public function in_arrayi($needle, array $haystack) {
        return \in_array(\strtolower($needle), \array_map('strtolower', $haystack));
    }

}