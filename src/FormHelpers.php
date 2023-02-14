<?php

namespace Wenprise\Forms;

class FormHelpers {

	/**
	 * 获取上上级别类
	 *
	 * @param $object
	 *
	 * @return false|string
	 */
	public static function get_grandparent_class( $object ): bool|string {
		if ( is_object( $object ) ) {
			$object = get_class( $object );
		}

		return get_parent_class( get_parent_class( $object ) );
	}


	/**
	 * 获取指定值的默认值
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public static function value( mixed $value ): mixed {
		return $value instanceof \Closure ? $value() : $value;
	}


	/**
	 * 使用点注释获取数据
	 *
	 * @param array      $array
	 * @param string     $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	public static function data_get( array $array, string $key, mixed $default = null ): mixed {

		if ( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		}

		foreach ( explode( '.', $key ) as $segment ) {
			if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
				return static::value( $default );
			}

			$array = $array[ $segment ];
		}

		return $array;
	}


	/**
	 * Get request var, if no value return default value.
	 *
	 * @param null $key
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public static function http_get( $key = null, $default = null ): mixed {
		return static::data_get( $_REQUEST, $key, $default );
	}
}