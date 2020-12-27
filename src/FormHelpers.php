<?php

namespace Wenprise\Forms;

class FormHelpers
{

    /**
     * 获取上上级别类
     *
     * @param $object
     *
     * @return false|string
     */
    public static function get_grandparent_class($object)
    {
        if (is_object($object)) {
            $thing = get_class($object);
        }

        return get_parent_class(get_parent_class($object));
    }

}