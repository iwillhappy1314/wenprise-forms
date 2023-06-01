<?php

namespace Wenprise\Forms;

class Helpers
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
            $object = get_class($object);
        }

        return get_parent_class(get_parent_class($object));
    }


    /**
     * 获取指定值的默认值
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function value(mixed $value): mixed
    {
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
    public static function data_get(array $array, string $key, mixed $default = null): mixed
    {

        if (isset($array[ $key ])) {
            return $array[ $key ];
        }

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return static::value($default);
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
    public static function http_get($key = null, $default = null): mixed
    {
        return static::data_get($_REQUEST, $key, $default);
    }


    /**
     * 转换路径到 Url
     *
     * @param $directory
     *
     * @return string
     */
    public static function dir_to_url($directory): string
    {
        $url   = trailingslashit($directory);
        $count = 0;

        # Sanitize directory separator on Windows
        $url = str_replace('\\', '/', $url);

        $possible_locations = [
            WP_PLUGIN_DIR  => plugins_url(),   # If installed as a plugin
            WP_CONTENT_DIR => content_url(),   # If anywhere in wp-content
            ABSPATH        => site_url('/'),   # If anywhere else within the WordPress installation
        ];

        foreach ($possible_locations as $test_dir => $test_url) {
            $test_dir_normalized = str_replace('\\', '/', $test_dir);
            $url                 = str_replace($test_dir_normalized, $test_url, $url, $count);

            if ($count > 0) {
                return untrailingslashit($url);
            }
        }

        return ''; // return empty string to avoid exposing half-parsed paths
    }


    /**
     * 获取资源列表
     *
     * @return mixed
     */
    public static function get_manifest(): mixed
    {
        static $manifest;
        static $manifest_path;

        if ( ! $manifest_path) {
            $manifest_path = realpath(__DIR__ . '/../frontend/mix-manifest.json');
        }

        if ( ! $manifest) {
            // @codingStandardsIgnoreLine
            $manifest = json_decode(file_get_contents($manifest_path), true);
        }

        return $manifest;
    }


    /**
     * 获取资源 URL
     *
     * @param      $path
     * @param null $manifest_directory
     *
     * @return string
     */
    public static function get_assets_url($path, $manifest_directory = null): string
    {
        $manifest = self::get_manifest();

        // Remove manifest directory from path
        $path = str_replace((string)$manifest_directory, '', $path);
        // Make sure there’s a leading slash
        $path = '/' . ltrim($path, '/');

        // Get file URL from manifest file
        $path = $manifest[ $path ];
        // Make sure there’s no leading slash
        $path = ltrim($path, '/');

        # 设置根目录 Url
        if ( ! defined('WENPRISE_FORM_URL')) {
            define('WENPRISE_FORM_URL', self::dir_to_url(realpath(__DIR__ . '/../')));
        }

        return esc_url(WENPRISE_FORM_URL . '/frontend/' . $path);
    }
}