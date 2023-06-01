<?php

namespace Wenprise\Forms;

use JetBrains\PhpStorm\NoReturn;

class Init
{

    public function __construct()
    {
        // 插件版本
        if ( ! defined('WENPRISE_FORM_VERSION')) {
            define('WENPRISE_FORM_VERSION', '1.8');
        }

        add_action('init', [$this, 'register_locals']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_assets']);
    }


    /**
     * 获取表单验证信息
     * 只是为了翻译功能，实际应该是用不到这个函数的
     *
     * @return array
     */
    function get_messages(): array
    {
        return [
            __('Your session has expired. Please return to the home page and try again.', 'wprs'),
            __('Please enter %s.', 'wprs'),
            __('This value should not be %s.', 'wprs'),
            __('This field is required.', 'wprs'),
            __('This field should be blank.', 'wprs'),
            __('Please enter at least %d characters.', 'wprs'),
            __('Please enter no more than %d characters.', 'wprs'),
            __('Please enter a value between %d and %d characters long.', 'wprs'),
            __('Please enter a valid email address.', 'wprs'),
            __('Please enter a valid URL.', 'wprs'),
            __('Please enter a valid integer.', 'wprs'),
            __('Please enter a valid number.', 'wprs'),
            __('Please enter a value greater than or equal to %d.', 'wprs'),
            __('Please enter a value less than or equal to %d.', 'wprs'),
            __('Please enter a value between %d and %d.', 'wprs'),
            __('The size of the uploaded file can be up to %d bytes.', 'wprs'),
            __('The uploaded data exceeds the limit of %d bytes.', 'wprs'),
            __('The uploaded file is not in the expected format.', 'wprs'),
            __('The uploaded file must be image in format JPEG, GIF or PNG.', 'wprs'),
            __('Please select a valid option.', 'wprs'),
            __('An error occurred during file upload.', 'wprs'),
        ];
    }


    /**
     * 注册多语言文件
     */
    function register_locals(): void
    {
        $locale = apply_filters('theme_locale', is_admin() ? get_user_locale() : get_locale(), 'wprs');
        load_textdomain('wprs', dirname(__FILE__) . '/languages/wprs-' . $locale . '.mo');
    }


    /**
     * 在 WordPress 中按需加载前端文件
     */
    function register_assets(): void
    {
        // 主样式
        wp_register_style('wprs-forms-main', Helpers::get_assets_url('dist/styles/main.css'), [], WENPRISE_FORM_VERSION);
        wp_enqueue_script('wprs-forms-main', Helpers::get_assets_url('dist/scripts/main.js'), ['jquery'], WENPRISE_FORM_VERSION, true);

        wp_localize_script('wprs-forms-main', 'wenpriseFormSettings', [
            'staticPath'      => Helpers::dir_to_url(realpath(__DIR__ . '/../frontend')),
            'ajaxurl'         => admin_url('admin-ajax.php'),
            'error'           => __('Upload error, please try again.', 'wprs'),
            'canceled'        => __('Upload canceled.', 'wprs'),
            'file_type_error' => __('You have uploaded an incorrect file type. Please try again.', 'wprs'),
            'file_size_error' => __('The file you have uploaded exceeds the file size limit. Please try again.', 'wprs'),
            'file_ext_error'  => __('You have uploaded an incorrect file type. Please try again.', 'wprs'),
            'choose_image'    => __('Choose Image', 'wprs'),
            'insert_image'    => __('Insert Image', 'wprs'),
            'manifest'        => Helpers::get_manifest(),
        ]);

        wp_register_script('iris', admin_url('js/iris.min.js'), ['jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'], false, true);

        wp_register_script('wprs-sweetalert', Helpers::get_assets_url('dist/scripts/sweet-alert.js'), ['jquery'], WENPRISE_FORM_VERSION, true);

        wp_dequeue_script('jquery-ui-autocomplete');

        wp_localize_script('wprs-forms-main', 'wpColorPickerL10n', [
            'clear'         => __('Clear', 'wprs'),
            'defaultString' => __('Default', 'wprs'),
            'pick'          => __('Select Color', 'wprs'),
            'current'       => __('Current Color', 'wprs'),
        ]);

    }


    /**
     * 保存Ajax提交的表单数据
     *
     * @return void
     */
    #[NoReturn] function save_form_data(): void
    {
        $data = $_POST;

        // 基本文章数据
        $post_data = [
            'post_type'  => Helpers::data_get($data, 'post_type', 'inquiry'),
            'post_title' => Helpers::data_get($data, 'name', '') . Helpers::data_get($data, 'phone', '') . Helpers::data_get($data, 'subject', ''),
        ];

        $post_id_or_error = wp_insert_post($post_data);

        if ( ! is_wp_error($post_id_or_error)) {
            // 其他数据添加到自定义子字段中
            foreach ($data as $key => $value) {
                update_post_meta($post_id_or_error, $key, $value);
            }

            wp_send_json_success('Post submitted successfully.');
        } else {
            wp_send_json_error($post_id_or_error->get_error_message());
        }

        exit();
    }
}
