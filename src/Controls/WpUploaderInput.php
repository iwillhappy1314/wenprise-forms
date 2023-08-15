<?php

/**
 * Ajax 文件上传控件
 */

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\Validator;
use Nette\Utils\Html;


/**
 * 拖拽 Ajax 上传，上传后返回 WordPress 媒体库 id 到隐藏的 input 中，然后随表单一起提交，
 * 保存媒体库 id 到 WordPress 文章自定义字段中
 *
 * todo: 考虑增加上传进度, 增加上传出错时的提示
 */
class WpUploaderInput extends BaseControl {

	/** validation rule */
	const VALID = ':uploadControlValid';

	private array $settings = [];

	/**
	 * @param null       $label
	 * @param bool       $multiple
     * @param array|null $settings Chosen 设置
	 */
	public function __construct($label = null, bool $multiple = false, array $settings = null ) {
		parent::__construct( $label );
		$this->control->multiple = $multiple;
		$this->control->type     = 'text';
		$this->settings          = (array) $settings;

		$this->setOption( 'type', 'wp-uploader' );
		$this->addCondition( Form::BLANK )
		     ->addRule( [ $this, 'isOk' ], Validator::$messages[ self::VALID ] );
	}


	/**
	 * 显示上传控件
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl(): Html {

		$el = parent::getControl();

		$name     = $this->getHtmlName();
		$id       = $this->getHtmlId();
		$settings = $this->settings;
		$value    = $this->getValue();
		$preview  = '';
		$values   = '';
		$hide     = 'rs-hide';
		$multiple = (bool) $this->control->multiple;

		$el->appendAttribute( 'class', $hide );

		// 如果有默认值，设置隐藏的真实表单
		if ( $value ) {
			$hide = '';
			if ( is_array( $value ) ) {
				foreach ( $value as $v ) {
					$el->setAttribute( 'value', $v );
					$preview .= $this->getPreview( $v );
					$values  .= "<input type='hidden' name='$name' value='$v'>";
				}
			} else {
				$el->setAttribute( 'value', $value );
				$preview .= $this->getPreview( $value );

				$values .= "<input type='hidden' name='$name' value='$value'>";
			}
		}

		$html = Html::el( 'div' )
		            ->setAttribute( 'class', 'js-wp-uploader rs-wp-uploader' )
		            ->data( 'name', $name )
		            ->data( 'multiple', $multiple )
		            ->data( 'settings', json_encode( $settings ) );

		$html->addHtml(
			Html::el( 'div class=rs-wp-uploader__field' )
			    ->addHtml(
				    Html::el( 'input type=button class=rs-wp-uploader__button' )
				        ->setAttribute( 'value', __( 'Upload Image', 'wprs' ) )
			    )
			    ->addHtml(
				    Html::el( 'div class=rs-uploader__value' )
				        ->setAttribute( 'id', $id )
				        ->addHtml( $values )
			    )
			    ->addHtml(
				    Html::el( 'div class=rs-uploader__preview' )
				        ->appendAttribute( 'class', $hide )
				        ->addHtml( $preview )
			    )
			    ->addHtml(
				    Html::el( 'div class=rs-wp-uploader__message' )
			    ) );

		return $html;
	}


	/**
	 * 显示预览图片
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function getPreview( $value ): string {
		$close_icon = '<svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="12" height="12"><path d="M49.6 158.4l104-108.8 358.4 352 356.8-352 105.6 105.6-352 356.8 352 355.2-102.4 107.2L512 620.8 155.2 974.4l-105.6-105.6L406.4 512z" p-id="3640" fill="#ffffff"></path></svg>';

		if ( function_exists( 'wp_get_attachment_thumb_url' ) ) {
			$thumb = wp_get_attachment_thumb_url( $value );
		} else {
			$thumb = $value;
		}

		$preview = Html::el( 'div class="rs-uploader__thumbnail"' );
		$button  = Html::el( 'button type=button class="rs-uploader__close rs-wp-uploader__close"' )
		               ->data( 'value', $value )
		               ->setHtml( $close_icon );

		$image = Html::el( 'img' )
		             ->setAttribute( 'src', $thumb );

		$preview->addHtml( $button . $image );

		return $preview;

	}

	/**
	 * Returns HTML name of control.
	 *
	 * @return string
	 */
	public function getHtmlName(): string {
		return parent::getHtmlName() . ( $this->control->multiple ? '[]' : '' );
	}


	/**
	 * 只要输入不为空，即为验证通过
	 *
	 * @return bool
	 */
	public function isOk(): bool {

		return $this->isDisabled()
		       || $this->getValue() == 0
		       || $this->getValue() !== null;
	}

}