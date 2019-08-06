<?php
/**
 * 主题辅助函数
 *
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Translator;
use Wenprise\Forms\Renders\FormRender;

/**
 * 加载多语言文件
 */
if (function_exists('load_textdomain')) {
    $locale = apply_filters('theme_locale', is_admin() ? get_user_locale() : get_locale(), 'wprs');
    load_textdomain('wprs', dirname(__FILE__) . '/languages/wprs-' . $locale . '.mo');
}

/**
 * 格式化 Nette Form
 *
 * @param Form   $form Nette 表单
 * @param string $type 表单显示类型
 *
 * @return string 订单号字符串
 * @package   helper
 *
 */
if ( ! function_exists('wprs_form')) {
    function wprs_form(Form $form, $type = 'horizontal')
    {

        // 设置自定义 Render 方法
        $form->setRenderer(new FormRender);
        $form->setTranslator(new Translator());

        $renderer                                            = $form->getRenderer();
        $renderer->wrappers[ 'group' ][ 'container' ]        = 'fieldset class=row';
        $renderer->wrappers[ 'group' ][ 'label' ]            = 'legend class=col-md-12';
        $renderer->wrappers[ 'controls' ][ 'container' ]     = null;
        $renderer->wrappers[ 'pair' ][ 'container' ]         = 'div class=form-group';
        $renderer->wrappers[ 'pair' ][ '.error' ]            = 'has-error';
        $renderer->wrappers[ 'control' ][ 'container' ]      = $type == 'horizontal' ? 'div class=col-sm-9' : '';
        $renderer->wrappers[ 'label' ][ 'container' ]        = $type == 'horizontal' ? 'div class="col-sm-3 control-label"' : '';
        $renderer->wrappers[ 'control' ][ 'description' ]    = 'span class=help-block';
        $renderer->wrappers[ 'control' ][ 'errorcontainer' ] = 'span class=help-block';
        $form->getElementPrototype()->class($type == 'horizontal' ? 'form-horizontal' : '');


        $form->onRender[] = function ($form)
        {
            $text_control_type = ['text', 'textarea', 'select', 'sms', 'datepicker', 'color-picker'];

            foreach ($form->getControls() as $control) {

                $type = $control->getOption('type');

                if ( ! $control->getOption('class')) {
                    $control->setOption('class', 'col-md-12 c-form--' . $type);
                }

                $control->setOption('id', 'grp-' . $control->name);

                if ($type === 'button') {

                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
                    $usedPrimary = true;

                } elseif (in_array($type, $text_control_type, true)) {

                    $control->getControlPrototype()->addClass('form-control');

                } elseif (in_array($type, ['checkbox', 'radio'], true)) {

                    $control->getSeparatorPrototype()->setName('div')->addClass($type . ' ' . $type . '-inline');

                }

            }
        };

    }
}


/**
 * 为 WordPress 仪表盘格式化表单
 *
 * @param Form   $form
 * @param string $type post_meta|term_meta|user_meta|options
 */
if ( ! function_exists('wprs_admin_form')) {
    function wprs_admin_form(Form $form, $type = 'post_meta')
    {

        $screen = get_current_screen();

        // 设置自定义 Render 方法
        $form->setRenderer(new FormRender);
        $form->setTranslator(new Translator());

        $renderer                                     = $form->getRenderer();
        $renderer->wrappers[ 'group' ][ 'container' ] = null;
        $renderer->wrappers[ 'group' ][ 'label' ]     = 'h2';

        switch ($type) {
            case 'term_meta':
                if ($screen->base == 'term') {
                    $renderer->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
                    $renderer->wrappers[ 'pair' ][ 'container' ]     = 'tr class=c-form-field';
                } else {
                    $renderer->wrappers[ 'controls' ][ 'container' ] = '';
                    $renderer->wrappers[ 'pair' ][ 'container' ]     = 'div class="form-field wprs-form-field"';
                }
                break;
            default:
                $renderer->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
                $renderer->wrappers[ 'pair' ][ 'container' ]     = 'tr class=c-form-field';
        }

        $renderer->wrappers[ 'label' ][ 'container' ]   = 'th class=row scope=row';
        $renderer->wrappers[ 'control' ][ 'container' ] = 'td';


        $form->onRender[] = function ($form)
        {
            $text_control_type = ['text', 'textarea', 'select', 'sms', 'datepicker', 'color-picker'];

            foreach ($form->getControls() as $control) {

                $type = $control->getOption('type');

                if ( ! $control->getOption('class')) {
                    $control->setOption('class', 'c-form--' . $type);
                }

                $control->setOption('id', 'grp-' . $control->name);

                if ($type === 'button') {

                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'button button-primary' : 'button');
                    $usedPrimary = true;

                } elseif (in_array($type, $text_control_type, true)) {

                    $control->getControlPrototype()->addClass('regular-text');

                } elseif (in_array($type, ['checkbox', 'radio'], true)) {

                    $control->getSeparatorPrototype()->setName('fieldset')->addClass($type . ' ' . $type . '-inline');

                }

            }
        };

    }
}


/**
 * 获取表单验证信息
 * 只是为了翻译功能，实际应该是用不到这个函数的
 *
 * @return array
 */
if ( ! function_exists('wprs_form_messages')) {
    function wprs_form_messages()
    {
        $messages = [
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

        return $messages;
    }
}


/**
 * 转换路径到 Url
 *
 * @param $directory
 *
 * @return string
 */
if ( ! function_exists('wprs_dir_to_url')) {
    function wprs_dir_to_url($directory)
    {
        $url   = \trailingslashit($directory);
        $count = 0;

        # Sanitize directory separator on Windows
        $url = str_replace('\\', '/', $url);

        $possible_locations = [
            WP_PLUGIN_DIR  => \plugins_url(), # If installed as a plugin
            WP_CONTENT_DIR => \content_url(), # If anywhere in wp-content
            ABSPATH        => \site_url('/'), # If anywhere else within the WordPress installation
        ];

        foreach ($possible_locations as $test_dir => $test_url) {
            $test_dir_normalized = str_replace('\\', '/', $test_dir);
            $url                 = str_replace($test_dir_normalized, $test_url, $url, $count);

            if ($count > 0) {
                return \untrailingslashit($url);
            }
        }

        return ''; // return empty string to avoid exposing half-parsed paths
    }
}


/**
 * 在 WordPress 中按需加载前端文件
 */
if (function_exists('wp_register_style')) {

    // 插件版本
    if ( ! defined('WENPRISE_FORM_VERSION')) {
        define('WENPRISE_FORM_VERSION', '1.6');
    }

    # 设置根目录 Url
    if ( ! defined('WENPRISE_FORM_URL')) {
        define('WENPRISE_FORM_URL', wprs_dir_to_url(__DIR__));
    }

    /**
     * Register stylesheet and scripts.
     */
    add_action('wp_enqueue_scripts', function ()
    {

        // 附加样式和脚本
        wp_register_style('wprs-form', WENPRISE_FORM_URL . '/assets/styles/main.css', [], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-form', WENPRISE_FORM_URL . '/assets/scripts/main.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // Chosen 样式和脚本
        wp_register_style('wprs-chosen', WENPRISE_FORM_URL . '/assets/styles/chosen.css', [], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-chosen', WENPRISE_FORM_URL . '/assets/scripts/chosen.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // ion-rangeslider 样式
        wp_register_style('wprs-ion-rangeslider', WENPRISE_FORM_URL . '/assets/styles/ion-rangeslider.css', [], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-ion-rangeslider', WENPRISE_FORM_URL . '/assets/scripts/ion-rangeslider.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // Moment Js 日期处理
        wp_register_script('wprs-moment', WENPRISE_FORM_URL . '/assets/scripts/moment.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // 签字/签名
        wp_register_script('wprs-signature', WENPRISE_FORM_URL . '/assets/scripts/signature.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // Birthday Picker
        wp_register_script('wprs-combodate', WENPRISE_FORM_URL . '/assets/scripts/combodate.js', ['jquery', 'moment'], WENPRISE_FORM_VERSION, true);

        // Datepicker 样式
        wp_register_style('jquery-ui-datepicker', WENPRISE_FORM_URL . '/assets/styles/datepicker.css', ['jquery'], WENPRISE_FORM_VERSION);

        // jQuery AutoComplete
	    wp_register_script('wprs-autocomplete', WENPRISE_FORM_URL . '/assets/scripts/autocomplete.js', ['jquery'], WENPRISE_FORM_VERSION);

        // 表格输入
        wp_register_script('wprs-table-input', WENPRISE_FORM_URL . '/assets/scripts/table-input.js', ['jquery', 'jquery-ui-button'], WENPRISE_FORM_VERSION,
            true);

        // 前端验证
        wp_register_script('wprs-nette-forms', WENPRISE_FORM_URL . '/assets/scripts/nette-forms.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // Ajax 上传
        wp_register_style('wprs-ajax-uploader', WENPRISE_FORM_URL . '/assets/styles/ajax-uploader.css', [], WENPRISE_FORM_VERSION);
        wp_register_script('wprs-ajax-uploader', WENPRISE_FORM_URL . '/assets/scripts/ajax-uploader.js', ['jquery'], WENPRISE_FORM_VERSION, true);

        // 颜色选择
        wp_register_script('wp-color-picker', admin_url('js/color-picker.min.js'), ['iris'], false, 1);
        wp_register_script('iris', admin_url('js/iris.min.js'), ['jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'], false, 1);

        // 颜色选择
        wp_enqueue_style('wp-color-picker');

	    wp_dequeue_script('jquery-ui-autocomplete');

        $colorpicker_l10n = [
            'clear'         => __('Clear', 'wprs'),
            'defaultString' => __('Default', 'wprs'),
            'pick'          => __('Select Color', 'wprs'),
            'current'       => __('Current Color', 'wprs'),
        ];

        wp_localize_script('wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n);

        // 注册公共样式和脚本
        wp_enqueue_style('wprs-form');
        wp_enqueue_script('wprs-form');

    });


}

