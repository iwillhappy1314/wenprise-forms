<?php

namespace Wenprise\Forms;

use Nette\HtmlStringable;
use Wenprise\Forms\Renders\DefaultFormRender;

class Form extends \Nette\Forms\Form implements HtmlStringable
{

    public $datastore = null;

    public function setDatastore($datastore)
    {
        $this->datastore = $datastore;
    }


    public function save()
    {
        if ($this->isSuccess()) {
            $this->datastore->save();
        }
    }


    public function __construct($name = null)
    {
        new Init();

        $this->setRenderer(new DefaultFormRender());
        $this->setTranslator(new FormTranslator());

        wp_enqueue_style('wprs-forms-main');

        /**
         * @todo 无法加载？
         */
        add_action('wp_enqueue_scripts', function(){
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('wprs-sweetalert');
        });

        parent::__construct($name);
    }


    /**
     * 添加 Csrf 跨站保护控件
     *
     * @param      $name
     * @param null $errorMessage
     *
     * @return \Wenprise\Forms\Controls\CsrfInput
     */
    public function addCsrf($name, $errorMessage = null): Controls\CsrfInput {
        return $this[ $name ] = (new Controls\CsrfInput($errorMessage));
    }


    /**
     * 添加 WordPress Tinymce 可视化编辑器控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     *
     * @return Controls\TextEditor
     */
    public function addEditor($name, string $label = null, array $settings = null): Controls\TextEditor {
        return $this[ $name ] = (new Controls\TextEditor($label, $settings));
    }


    /**
     * Ajax 上传，支持单文件和多文件控件
     *
     * @param string      $name
     * @param string|null $label
     * @param bool        $multiple
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\AjaxUploadInput
     */
    public function addAjaxUpload( string $name, string $label = null, bool $multiple = false, array $settings = null): Controls\AjaxUploadInput {
        return $this[ $name ] = (new Controls\AjaxUploadInput($label, $multiple, $settings));
    }


    /**
     * WordPress 上传，支持单文件和多文件控件
     *
     * @param string      $name
     * @param string|null $label
     * @param bool        $multiple
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\WpUploaderInput
     */
    public function addWpUploader( string $name, string $label = null, bool $multiple = false, array $settings = null): Controls\WpUploaderInput {
        return $this[ $name ] = (new Controls\WpUploaderInput($label, $multiple, $settings));
    }


    /**
     * 带前缀或者后缀的文本输入
     *
     * @param      $name
     * @param null $label
     * @param null $maxLength
     * @param null $prefix
     * @param null $suffix
     *
     * @return \Wenprise\Forms\Controls\GroupInput
     */
    public function addGroupInput($name, $label = null, $maxLength = null, $prefix = null, $suffix = null): Controls\GroupInput {
        return $this[ $name ] = (new Controls\GroupInput($label, $maxLength, $prefix, $suffix));
    }


    /**
     * 添加文本克隆输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 设置
     *
     * @return \Wenprise\Forms\Controls\CloneInput
     */
    public function addCloneInput($name, string $label = null, array $settings = null): Controls\CloneInput {
        return $this[ $name ] = (new Controls\CloneInput($label, $settings));
    }


    /**
     * 添加 Slider 滑动输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings Slider 设置
     *
     * @return \Wenprise\Forms\Controls\SliderInput
     */
    public function addSlider( string $name, string $label = null, array $settings = null): Controls\SliderInput {
        return $this[ $name ] = (new Controls\SliderInput($label, $settings));
    }


    /**
     * 日期选择
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\DatePickerInput
     */
    public function addDatePicker( string $name, string $label = null, array $settings = null): Controls\DatePickerInput {
        return $this[ $name ] = (new Controls\DatePickerInput($label, $settings));
    }


    /**
     * 日期选择
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 表单设置
     *
     * @return \Wenprise\Forms\Controls\DateRangePickerInput
     */
    public function addDateRangePicker( string $name, string $label = null, array $settings = null): Controls\DateRangePickerInput {
        return $this[ $name ] = (new Controls\DateRangePickerInput($label, $settings));
    }


    /**
     * 颜色选择
     *
     * @param string      $name     string  表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\BirthdayPickerInput
     */
    public function addBirthdayPicker( string $name, string $label = null, array $settings = null): Controls\BirthdayPickerInput {
        return $this[ $name ] = (new Controls\BirthdayPickerInput($label, $settings));
    }


    /**
     * 颜色选择
     *
     * @param string      $name     string  表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\ColorpickerInput
     */
    public function addColorPicker( string $name, string $label = null, array $settings = null): Controls\ColorpickerInput {
        return $this[ $name ] = (new Controls\ColorpickerInput($label, $settings));
    }


    /**
     * 关联选择
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     * @param array|null  $field
     *
     * @return \Wenprise\Forms\Controls\ChainedInput
     */
    public function addChainedSelect( string $name, string $label = null, array $settings = null, array $field = null): Controls\ChainedInput {
        return $this[ $name ] = (new Controls\ChainedInput($label, $settings, $field));
    }


    /**
     * 添加 Html 控件
     *
     * @param string      $name
     * @param string|null $caption
     *
     * @return \Wenprise\Forms\Controls\HtmlContent
     */
    public function addHtml( string $name, string $caption = null): Controls\HtmlContent {
        return $this[ $name ] = (new Controls\HtmlContent($caption));
    }


    /**
     * 获取 SMS 验证码
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\SmsInput
     */
    public function AddSmsInput($name, string $label = null, array $settings = null): Controls\SmsInput {
        return $this[ $name ] = (new Controls\SmsInput($label, $settings));
    }


    /**
     * 添加 Captcha 验证码
     *
     * @param string      $name
     * @param string|null $label
     *
     * @return \Wenprise\Forms\Controls\CaptchaInput
     */
    public function AddCaptcha( string $name, string $label = null): Controls\CaptchaInput {
        return $this[ $name ] = (new Controls\CaptchaInput($label));
    }


    /**
     * 表格输入
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     * @param array|null  $field
     *
     * @return \Wenprise\Forms\Controls\TableInput
     */
    public function addTableInput( string $name, string $label = null, array $settings = null, ?array $field = []): Controls\TableInput {
        return $this[ $name ] = (new Controls\TableInput($label, $settings, $field));
    }


    /**
     * Chosen 输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\ChosenInput
     */
    public function addChosen( string $name, string $label = null, array $items = null, array $settings = null): Controls\ChosenInput {
        return $this[ $name ] = (new Controls\ChosenInput($label, $items, $settings));
    }


    /**
     * Chosen 输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     *
     * @return \Wenprise\Forms\Controls\SwitchInput
     */
    public function addSwitch( string $name, string $label = null, array $items = null): Controls\SwitchInput {
        return $this[ $name ] = (new Controls\SwitchInput($label, $items));
    }


    /**
     * Chosen 多选输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\MultiChosenInput
     */
    public function addMultiChosen( string $name, string $label = null, array $items = null, array $settings = null): Controls\MultiChosenInput {
        return $this[ $name ] = (new Controls\MultiChosenInput($label, $items, $settings));
    }


    /**
     * 签字/签名
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 表单设置
     *
     * @return \Wenprise\Forms\Controls\SignatureInput
     */
    public function addSignature( string $name, string $label = null, array $settings = null): Controls\SignatureInput {
        return $this[ $name ] = (new Controls\SignatureInput($label, $settings));
    }


    /**
     * jQuery Autocomplete 输入提示
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 表单设置
     *
     * @return \Wenprise\Forms\Controls\AutoCompleteInput
     */
    public function addAutocomplete( string $name, string $label = null, array $settings = null): Controls\AutoCompleteInput {
        return $this[ $name ] = (new Controls\AutocompleteInput($label, $settings));
    }


    /**
     * Chosen 输入控件
     *
     * @param             $name
     * @param string|null $label
     * @param array|null  $items
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\ImagePickerInput
     */
    public function addImagePicker($name, string $label = null, array $items = null, array $settings = null): Controls\ImagePickerInput {
        return $this[ $name ] = (new Controls\ImagePickerInput($label, $items, $settings));
    }


    /**
     * 星级评分输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\StarRatingInput
     */
    public function addStarRating( string $name, string $label = null, array $settings = null): Controls\StarRatingInput {
        return $this[ $name ] = (new Controls\StarRatingInput($label, $settings));
    }

    /**
     * 星级评分输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\CheckboxTreeInput
     */
    public function addCheckboxTree( string $name, string $label = null, array $settings = null): Controls\CheckboxTreeInput {
        return $this[ $name ] = (new Controls\CheckboxTreeInput($label, $settings));
    }

}
