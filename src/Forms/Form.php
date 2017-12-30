<?php


namespace Wizhi\Forms;


class Form extends \Nette\Forms\Form implements \Nette\Utils\IHtmlString {


	/**
	 * 添加 Csrf 跨站保护控件
	 *
	 * @param      $name
	 * @param null $errorMessage
	 *
	 * @return \Wizhi\Forms\Controls\CsrfInput
	 */
	public function addCsrf( $name, $errorMessage = null ) {
		return $this[ $name ] = ( new Controls\CsrfInput( $errorMessage ) );
	}


	/**
	 * 添加 WordPress Tinymce 可视化编辑器控件
	 *
	 * @param  string
	 * @param  string|object
	 * @param  int
	 * @param  int
	 *
	 * @return Controls\TextEditor
	 */
	public function addEditor( $name, $label = null, $cols = null, $rows = null ) {
		return $this[ $name ] = ( new Controls\TextEditor( $label ) )
			->setHtmlAttribute( 'cols', $cols )->setHtmlAttribute( 'rows', $rows );
	}


	/**
	 * Ajax 上传，支持单文件和多文件控件
	 *
	 * @param      $name
	 * @param null $label
	 * @param bool $multiple
	 *
	 * @return \Wizhi\Forms\Controls\AjaxUploadInput
	 */
	public function addAjaxUpload( $name, $label = null, $multiple = false ) {
		return $this[ $name ] = ( new Controls\AjaxUploadInput( $label, $multiple ) );
	}


	/**
	 * 下拉选择模拟输入控件
	 *
	 * @param            $name
	 * @param null       $label
	 * @param array|null $items
	 *
	 * @return \Wizhi\Forms\Controls\DropdownSelectInput
	 */
	public function addDropdownSelect( $name, $label = null, array $items = null ) {
		return $this[ $name ] = ( new Controls\DropdownSelectInput( $label, $items ) );
	}


	/**
	 * 添加文本克隆输入控件
	 *
	 * @param      $name     string  表单名称
	 * @param      $label    string  表单标签
	 *
	 * @return \Wizhi\Forms\Controls\CloneInput
	 */
	public function addClone( $name, $label = null ) {
		return $this[ $name ] = ( new Controls\CloneInput( $label ) );
	}


	/**
	 * 添加 Slider 滑动输入控件
	 *
	 * @param      $name     string  表单名称
	 * @param      $label    string  表单标签
	 *
	 * @return \Wizhi\Forms\Controls\SliderInput
	 */
	public function addSlider( $name, $label = null, $args ) {
		return $this[ $name ] = ( new Controls\SliderInput( $label, $args ) );
	}


	/**
	 * 日期选择
	 *
	 * @param      $name     string  表单名称
	 * @param      $label    string  表单标签
	 * @param      $settings array   表单设置
	 *
	 * @return \Wizhi\Forms\Controls\DatePickerInput
	 */
	public function addDatePicker( $name, $label = null, $settings ) {
		return $this[ $name ] = ( new Controls\DatePickerInput( $label, $settings ) );
	}


	/**
	 * 颜色选择
	 *
	 * @param      $name     string  表单名称
	 * @param      $label    string  表单标签
	 * @param      $settings array   表单设置
	 *
	 * @return \Wizhi\Forms\Controls\ColorpickerInput
	 */
	public function addColorPicker( $name, $label = null, $settings ) {
		return $this[ $name ] = ( new Controls\ColorpickerInput( $label, $settings ) );
	}

	/**
	 * 关联选择
	 *
	 * @param      $name
	 * @param null $label
	 * @param      $settings
	 * @param      $field
	 *
	 * @return \Wizhi\Forms\Controls\ChainedInput
	 */
	public function addChainedSelect( $name, $label = null, $settings, $field ) {
		return $this[ $name ] = ( new Controls\ChainedInput( $label, $settings, $field ) );
	}


	/**
	 * 添加 Html 控件
	 *
	 * @param      $name
	 * @param null $caption
	 *
	 * @return \Wizhi\Forms\Controls\HtmlContent
	 */
	public function addHtml( $name, $caption = null ) {
		return $this[ $name ] = ( new Controls\HtmlContent( $caption ) );
	}


	/**
	 * 获取 SMS 验证码
	 *
	 * @param       $name
	 * @param null  $label
	 * @param array $settings
	 *
	 * @return \Wizhi\Forms\Controls\SmsInput
	 */
	public function AddSms( $name, $label = null, $settings = [] ) {
		return $this[ $name ] = ( new Controls\SmsInput( $label, $settings ) );
	}


	/**
	 * 添加 Captcha 验证码
	 *
	 * @param       $name
	 * @param null  $label
	 * @param array $settings
	 *
	 * @return \Wizhi\Forms\Controls\CaptchaInput
	 */
	public function AddCaptcha( $name, $label = null, $settings = [] ) {
		return $this[ $name ] = ( new Controls\CaptchaInput( $label, $settings ) );
	}


}