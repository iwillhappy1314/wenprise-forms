<?php
/*
Plugin Name:        Wenprise Forms
Plugin URI:         https://www.wpzhiku.com/wenprise-pinyin-slug/
Description:        WordPress 表单功能
Version:            1.4.1
Author:             WordPress 智库
Author URI:         https://www.wpzhiku.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

define('WENPRISE_FORMS_URL', plugin_dir_url(__FILE__));

require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');

new \Wenprise\Forms\Init();