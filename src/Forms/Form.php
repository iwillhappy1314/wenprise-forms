<?php

namespace Wenprise\Forms;

class Form extends \Nette\Forms\Form implements \Nette\Utils\IHtmlString
{

    /**
     * 添加 Csrf 跨站保护控件
     *
     * @param      $name
     * @param null $errorMessage
     *
     * @return \Wenprise\Forms\Controls\CsrfInput
     */
    public function addCsrf($name, $errorMessage = null)
    {
        return $this[ $name ] = (new Controls\CsrfInput($errorMessage));
    }


    /**
     * 添加 WordPress Tinymce 可视化编辑器控件
     *
     * @param  string
     * @param  string|object
     * @param  int
     *
     * @return Controls\TextEditor
     */
    public function addEditor($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\TextEditor($label, $settings));
    }


    /**
     * Ajax 上传，支持单文件和多文件控件
     *
     * @param      $name
     * @param null $label
     * @param bool $multiple
     *
     * @return \Wenprise\Forms\Controls\AjaxUploadInput
     */
    public function addAjaxUpload($name, $label = null, $multiple = false)
    {
        return $this[ $name ] = (new Controls\AjaxUploadInput($label, $multiple));
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
    public function addGroupInput($name, $label = null, $maxLength = null, $prefix = null, $suffix = null)
    {
        return $this[ $name ] = (new Controls\GroupInput($label, $maxLength, $prefix, $suffix));
    }


    /**
     * 添加文本克隆输入控件
     *
     * @param      $name        string  表单名称
     * @param      $label       string  表单标签
     * @param      $settings    array  设置
     *
     * @return \Wenprise\Forms\Controls\CloneInput
     */
    public function addCloneInput($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\CloneInput($label, $settings));
    }


    /**
     * 添加 Slider 滑动输入控件
     *
     * @param      $name        string  表单名称
     * @param      $label       string  表单标签
     * @param      $settings    array  Slider 设置
     *
     * @return \Wenprise\Forms\Controls\SliderInput
     */
    public function addSlider($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\SliderInput($label, $settings));
    }


    /**
     * 日期选择
     *
     * @param      $name     string  表单名称
     * @param      $label    string  表单标签
     * @param      $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\DatePickerInput
     */
    public function addDatePicker($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\DatePickerInput($label, $settings));
    }

    /**
     * 颜色选择
     *
     * @param      $name     string  表单名称
     * @param      $label    string  表单标签
     * @param      $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\BirthdayPickerInput
     */
    public function addBirthdayPicker($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\BirthdayPickerInput($label, $settings));
    }


    /**
     * 颜色选择
     *
     * @param      $name     string  表单名称
     * @param      $label    string  表单标签
     * @param      $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\ColorpickerInput
     */
    public function addColorPicker($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\ColorpickerInput($label, $settings));
    }

    /**
     * 关联选择
     *
     * @param      $name
     * @param null $label
     * @param      $settings
     * @param      $field
     *
     * @return \Wenprise\Forms\Controls\ChainedInput
     */
    public function addChainedSelect($name, $label = null, array $settings = null, array $field = null)
    {
        return $this[ $name ] = (new Controls\ChainedInput($label, $settings, $field));
    }


    /**
     * 添加 Html 控件
     *
     * @param      $name
     * @param null $caption
     *
     * @return \Wenprise\Forms\Controls\HtmlContent
     */
    public function addHtml($name, $caption = null)
    {
        return $this[ $name ] = (new Controls\HtmlContent($caption));
    }


    /**
     * 获取 SMS 验证码
     *
     * @param       $name
     * @param null  $label
     * @param array $settings
     *
     * @return \Wenprise\Forms\Controls\SmsInput
     */
    public function AddSmsInput($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\SmsInput($label, $settings));
    }


    /**
     * 添加 Captcha 验证码
     *
     * @param       $name
     * @param null  $label
     *
     * @return \Wenprise\Forms\Controls\CaptchaInput
     */
    public function AddCaptcha($name, $label = null)
    {
        return $this[ $name ] = (new Controls\CaptchaInput($label));
    }


    /**
     * 表格输入
     *
     * @param      $name
     * @param null $label
     * @param      $settings
     * @param      $field
     *
     * @return \Wenprise\Forms\Controls\TableInput
     */
    public function addTableInput($name, $label = null, array $settings = null, $field = [])
    {
        return $this[ $name ] = (new Controls\TableInput($label, $settings, $field));
    }


    /**
     * Chosen 输入空间
     *
     * @param            $name
     * @param null       $label
     * @param array|null $items
     * @param array|null $settings
     *
     * @return \Wenprise\Forms\Controls\ChosenInput
     */
    public function addChosen($name, $label = null, array $items = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\ChosenInput($label, $items, $settings));
    }


    /**
     * Chosen 多选输入控件
     *
     * @param            $name
     * @param null       $label
     * @param array|null $items
     * @param array|null $settings
     *
     * @return \Wenprise\Forms\Controls\MultiChosenInput
     */
    public function addMultiChosen($name, $label = null, array $items = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\MultiChosenInput($label, $items, $settings));
    }


    /**
     * 签字/签名
     *
     * @param      $name     string  表单名称
     * @param      $label    string  表单标签
     * @param      $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\SignatureInput
     */
    public function addSignature($name, $label = null, array $settings = null)
    {
        return $this[ $name ] = (new Controls\SignatureInput($label, $settings));
    }

}