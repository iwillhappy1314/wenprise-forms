<?php

namespace Wenprise\Forms;

use Nette\Forms\Controls\BaseControl;

class Init
{

    public function __construct()
    {
        // 插件版本
        if ( ! defined('WENPRISE_FORM_VERSION')) {
            define('WENPRISE_FORM_VERSION', '1.8');
        }

        $this->register_control_extension_methods();

        add_action('init', [$this, 'register_locals']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_assets']);
    }


    /**
     * 注册控件扩展方法，补充统一的布局 API。
     *
     * @return void
     */
    protected function register_control_extension_methods(): void
    {
        static $has_registered = false;

        if ($has_registered) {
            return;
        }

        $has_registered = true;

        $set_width = function (BaseControl $control, mixed ...$args): BaseControl {
            $allowed_breakpoints = ['base', 'sm', 'md', 'lg', 'xl'];

            if (count($args) === 0) {
                throw new \InvalidArgumentException('At least one width value is required.');
            }

            $current_widths = (array) $control->getOption('width');

            if (count($args) > 1 && !is_string($args[1] ?? null)) {
                $breakpoints = ['base', 'md', 'lg', 'xl'];

                foreach ($args as $index => $width) {
                    if (!isset($breakpoints[$index])) {
                        break;
                    }

                    if ($width < 1 || $width > 12) {
                        throw new \InvalidArgumentException('Width must be between 1 and 12.');
                    }

                    $current_widths[$breakpoints[$index]] = $width;
                }

                $control->setOption('width', $current_widths);

                return $control;
            }

            $width = $args[0];
            $breakpoint = $args[1] ?? 'md';

            if ($width < 1 || $width > 12) {
                throw new \InvalidArgumentException('Width must be between 1 and 12.');
            }

            if (!in_array($breakpoint, $allowed_breakpoints, true)) {
                throw new \InvalidArgumentException('Breakpoint must be one of: base, sm, md, lg, xl.');
            }

            $current_widths[$breakpoint] = $width;
            $control->setOption('width', $current_widths);

            return $control;
        };

        BaseControl::extensionMethod('setWidth', $set_width);
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
            'staticPath'          => Helpers::dir_to_url(realpath(__DIR__ . '/../frontend')),
            'admin_url'           => admin_url(),
            'includes_url'        => includes_url(),
            'ajax_url'            => admin_url('admin-ajax.php'),
            'upload_max_filesize' => (int)(ini_get('upload_max_filesize')),
            'error'               => __('Upload error, please try again.', 'wprs'),
            'canceled'            => __('Upload canceled.', 'wprs'),
            'file_type_error'     => __('You have uploaded an incorrect file type. Please try again.', 'wprs'),
            'file_size_error'     => __('The file you have uploaded exceeds the file size limit. Please try again.', 'wprs'),
            'file_ext_error'      => __('You have uploaded an incorrect file type. Please try again.', 'wprs'),
            'choose_image'        => __('Choose Image', 'wprs'),
            'insert_image'        => __('Insert Image', 'wprs'),
            'manifest'            => Helpers::get_manifest(),
            'clear'               => __('Clear', 'wprs'),
            'defaultString'       => __('Default', 'wprs'),
            'pick'                => __('Select Color', 'wprs'),
            'current'             => __('Current Color', 'wprs'),
        ]);

        wp_register_script('wprs-sweetalert', Helpers::get_assets_url('dist/scripts/sweet-alert.js'), ['jquery'], WENPRISE_FORM_VERSION, true);
    }
}
