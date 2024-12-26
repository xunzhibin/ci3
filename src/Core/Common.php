<?php

// ------------------------------------------------------------------------

if (! function_exists('class_basename')) {
    /**
     * 获取 对象或类的 basename
     * 
     * @param string|object
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

// ------------------------------------------------------------------------

// if (! function_exists('class_traits')) {
//     /**
//      * 获取 对象或类的 所有 trait
//      * 
//      * @param string|object $class
//      * @return string
//      */
//     function class_traits($class)
//     {
//         if (is_object($class)) {
//             $class = get_class($class);
//         }

//         $traits = [];

//         foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
// 			$traits += class_uses($class) ?: [];
//         }

//         return array_unique($traits);
//     }
// }

// ------------------------------------------------------------------------
