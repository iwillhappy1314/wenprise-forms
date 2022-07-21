<?php

namespace Wenprise\Forms;

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
    function get_messages()
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
     * 转换路径到 Url
     *
     * @param $directory
     *
     * @return string
     */
    function dir_to_url($directory)
    {
        $url   = trailingslashit($directory);
        $count = 0;

        # Sanitize directory separator on Windows
        $url = str_replace('\\', '/', $url);

        $possible_locations = [
            WP_PLUGIN_DIR  => plugins_url(), # If installed as a plugin
            WP_CONTENT_DIR => content_url(), # If anywhere in wp-content
            ABSPATH        => site_url('/'), # If anywhere else within the WordPress installation
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
     * @param $dir
     *
     * @return mixed
     */
    function get_manifest($dir)
    {
        $filepath = realpath(__DIR__ . '/' . $dir . '/manifest.json');

        if (file_exists($filepath)) {
            return json_decode(file_get_contents($filepath), true);
        }

        return false;
    }


    /**
     * 获取资源 URL
     *
     * @param $assets
     *
     * @return string
     */
    function get_assets_url($assets)
    {
        # 设置根目录 Url
        if ( ! defined('WENPRISE_FORM_URL')) {
            define('WENPRISE_FORM_URL', $this->dir_to_url(realpath(__DIR__ . '/../')));
        }

        return esc_url(WENPRISE_FORM_URL . '/frontend/dist/' . $assets);
    }


    /**
     * 注册多语言文件
     */
    function register_locals()
    {
        $locale = apply_filters('theme_locale', is_admin() ? get_user_locale() : get_locale(), 'wprs');
        load_textdomain('wprs', dirname(__FILE__) . '/languages/wprs-' . $locale . '.mo');
    }


    /**
     * 在 WordPress 中按需加载前端文件
     */
    function register_assets()
    {
        $assets = $this->get_manifest('../frontend/dist/app');

        // 运行环境
        wp_enqueue_script('wprs-forms-runtime', $this->get_assets_url($assets[ 'runtime.js' ]), [], WENPRISE_FORM_VERSION);

        // 主样式
        wp_register_style('wprs-forms-main', $this->get_assets_url($assets[ 'main.css' ]), [], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-forms-main', $this->get_assets_url($assets[ 'main.js' ]), ['jquery'], WENPRISE_FORM_VERSION, true);

        // Chosen 样式和脚本
        wp_register_style('wprs-chosen', $this->get_assets_url($assets[ 'chosen.css' ]), ['wprs-forms-main'], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-chosen', $this->get_assets_url($assets[ 'chosen.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        // ion-rangeslider 样式
        wp_register_style('wprs-ion-rangeslider', $this->get_assets_url($assets[ 'rangeslider.css' ]), ['wprs-forms-main'], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-ion-rangeslider', $this->get_assets_url($assets[ 'rangeslider.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        // Moment Js 日期处理
        wp_register_script('wprs-moment', $this->get_assets_url($assets[ 'moment.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        // 签字/签名
        wp_register_script('wprs-signature', $this->get_assets_url($assets[ 'signature.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        // Sweet Alert 弹窗美化
        wp_register_script('wprs-sweetalert', $this->get_assets_url($assets[ 'sweetalert.js' ]), ['jquery'], WENPRISE_FORM_VERSION, true);

        // Birthday Picker
        wp_register_script('wprs-combodate', $this->get_assets_url($assets[ 'combodate.js' ]), ['jquery', 'moment', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        // Datepicker 样式
        wp_register_style('wprs-datepicker', $this->get_assets_url($assets[ 'datepicker.css' ]), ['wprs-forms-main'], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-datepicker-zh', $this->get_assets_url($assets[ 'datepicker_zh.js' ]), ['jquery', 'jquery-ui-datepicker', 'wprs-forms-main'], WENPRISE_FORM_VERSION);

        // 日期区间选择器
        wp_register_style('wprs-daterangepicker', $this->get_assets_url($assets[ 'daterangepicker.css' ]), ['wprs-forms-main'], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-daterangepicker', $this->get_assets_url($assets[ 'daterangepicker.js' ]), ['jquery'], WENPRISE_FORM_VERSION);

        // jQuery AutoComplete
        wp_register_script('wprs-autocomplete', $this->get_assets_url($assets[ 'autocomplete.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION);

        // 表格输入
        wp_register_script('wprs-table-input', $this->get_assets_url($assets[ 'tableinput.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, false);

        // 图像选择
        wp_register_script('wprs-image-picker', $this->get_assets_url($assets[ 'image_picker.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, false);

        // Ajax 上传
        wp_register_style('wprs-ajax-uploader', $this->get_assets_url($assets[ 'uploader.css' ]), ['wprs-forms-main'], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-ajax-uploader', $this->get_assets_url($assets[ 'uploader.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        wp_localize_script('wprs-ajax-uploader', 'wprsUploaderL10n', [
            'error'           => __('Upload error, please try again.', 'wprs'),
            'canceled'        => __('Upload canceled.', 'wprs'),
            'file_type_error' => __('You have uploaded an incorrect file type. Please try again.', 'wprs'),
            'file_size_error' => __('The file you have uploaded exceeds the file size limit. Please try again.', 'wprs'),
            'file_ext_error'  => __('You have uploaded an incorrect file type. Please try again.', 'wprs'),
            'choose_image'    => __('Choose Image', 'wprs'),
            'insert_image'    => __('Insert Image', 'wprs'),
        ]);

        // 五星评分
        wp_register_style('wprs-star-rating', $this->get_assets_url($assets[ 'star_rating.css' ]), ['wprs-forms-main'], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-star-rating', $this->get_assets_url($assets[ 'star_rating.js' ]), ['jquery', 'wprs-forms-main'], WENPRISE_FORM_VERSION, true);

        // 颜色选择
        wp_register_script('iris', admin_url('js/iris.min.js'), ['jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'], false, true);

        // 移除jQuery自动完成
        wp_dequeue_script('jquery-ui-autocomplete');

        wp_localize_script('wp-color-picker', 'wenpriseFormsSettings', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);

        wp_localize_script('wp-color-picker', 'wpColorPickerL10n', [
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
    function save_form_data()
    {
        $data = $_POST;

        $post_data = [
            'post_type'  => FormHelpers::data_get($data, 'post_type', 'inquiry'),
            'post_title' => FormHelpers::data_get($data, 'name', '') . FormHelpers::data_get($data, 'phone', '') . FormHelpers::data_get($data, 'subject', ''),
        ];

        $post_id_or_error = wp_insert_post($post_data);

        if ( ! is_wp_error($post_id_or_error)) {
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
