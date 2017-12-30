<?php

/**
 * 多级上传组件
 */

namespace Wizhi\Forms\Controls;

use Nette;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\Validator;
use Nette\Utils\Html;


/**
 * 拖拽 Ajax 上传，上传后返回 WordPress 媒体库 id 到隐藏的 input 中，然后随表单一起提交，
 * 保存媒体库 id 到 WordPress 文章自定义字段中
 *
 * todo: 添加验证规则，验证规则其实就是 text input 的验证规则，选中的值要在选择项的数组中
 * todo: 考虑增加上传进度, 增加上传出错时的提示
 * todo: 和后端相册数据统一
 */
class  AjaxUploadInput extends BaseControl {

	/** validation rule */
	const VALID = ':uploadControlValid';

	/**
	 * @param  string|object
	 * @param  bool
	 */
	public function __construct( $label = null, $multiple = false ) {
		parent::__construct( $label );
		$this->control->multiple = (bool) $multiple;
		$this->setOption( 'type', 'text' );
		$this->control->type = 'hidden';
		$this->addCondition( Form::BLANK )
		     ->addRule( [ $this, 'isOk' ], Validator::$messages[ self::VALID ] );
	}


	/**
	 * This method will be called when the component (or component's parent)
	 * becomes attached to a monitored object. Do not call this method yourself.
	 *
	 * @param  Nette\ComponentModel\IComponent
	 *
	 * @return void
	 */
	protected function attached( $form ) {
		if ( $form instanceof Nette\Forms\Form ) {
			if ( ! $form->isMethod( 'post' ) ) {
				throw new Nette\InvalidStateException( 'File upload requires method POST.' );
			}
			$form->getElementPrototype()->enctype = 'multipart/form-data';
		}
		parent::attached( $form );
	}


	/**
	 * 显示上传控件
	 *
	 * @return string
	 */
	public function getControl() {

		$el = parent::getControl();

		wp_enqueue_script( 'frm-upload' );

		$name        = $this->getHtmlName();
		$id          = $this->getHtmlId();
		$placeholder = $this->control->getAttribute( 'placeholder' );
		$data_url    = $this->control->getAttribute( 'data-url' );
		$value       = $this->value;
		$default     = '';
		$preview     = '';
		$hide        = 'fn-hide';

		// 如果有默认值，设置隐藏的真实表单
		if ( $value && ! empty( $value ) ) {
			$hide = '';
			if ( is_array( $value ) ) {
				foreach ( $value as $v ) {
					$default .= $el->setAttribute( 'value', $v );
					$preview .= $this->getPreview( $v );
				}
			} else {
				$default .= $el->setAttribute( 'value', $value );
				$preview .= $this->getPreview( $value );
			}
		}

		$html = '<div id="' . $id . '" class="fn-dnd-zone uploader" data-name="' . $name . '">
            <div class="text">Drag &amp; Drop Images Here</div>
            <div class="or">-or-</div>
            <div class="browser">
              <label class="btn btn-default">
                <span>' . $placeholder . '</span>
                <input class="upload_shadow" type="file" data-url="' . $data_url . '" name="input_shadow" ' . ( $this->control->multiple ? 'multiple="multiple"' : '' ) . ' title="' . $placeholder . '">
              </label>
              <div class="frm-thumbs">' . $default . '</div>
              <div class="frm-preview clearfix ' . $hide . '">' . $preview . '</div>
            </div>
        </div>';

		return $html;
	}


	/**
	 * 显示预览图片
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function getPreview( $value ) {

		$thumb = wp_get_attachment_thumb_url( $value );

		$preview = Html::el( 'div class="col-xs-6 col-md-3"' );
		$button  = Html::el( 'button type=button class=close data-value="' . $value . '"' )->addHtml( Html::el( 'span' )->setText( 'x' ) );
		$image   = Html::el( 'img' )->src( $thumb );

		$preview->addHtml( $button . $image );

		return $preview;

	}


	/**
	 * Loads HTTP data.
	 *
	 * @return void
	 */
	public function loadHttpData() {
		$this->setValue( $this->getHttpData( Form::DATA_LINE ) );
	}


	/**
	 * 返回表单值
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @return static
	 * @internal
	 */
	public function setValue( $value ) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Returns HTML name of control.
	 *
	 * @return string
	 */
	public function getHtmlName() {
		return parent::getHtmlName() . ( $this->control->multiple ? '[]' : '' );
	}


	/**
	 * 只要输入不为空，即为验证通过
	 *
	 * @return bool
	 */
	public function isOk() {

		return $this->isDisabled()
		       || $this->getValue() == 0
		       || $this->getValue() !== null;
	}

}