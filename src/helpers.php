<?php
/**
 * 主题辅助函数
 *
 */

use Wizhi\Forms\Form;
use Wizhi\Forms\Renders\FormRender;

/**
 * 格式化 Nette Form
 *
 * @package   helper
 *
 * @param  Form   $form Nette 表单
 * @param  string $type 表单显示类型
 *
 * @return string 订单号字符串
 */
if ( ! function_exists( 'wizhi_form' ) ) {
	function wizhi_form( Form $form, $type = 'horizontal' ) {

		// 设置自定义 Render 方法
		$form->setRenderer( new FormRender );

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
		$form->getElementPrototype()->class( $type == 'horizontal' ? 'form-horizontal' : '' );
		$form->onRender[] = function ( $form ) {
			foreach ( $form->getControls() as $control ) {
				if ( ! $control->getOption( 'class' ) ) {
					$control->setOption( 'class', 'col-md-12' );
				}
				$type = $control->getOption( 'type' );
				if ( $type === 'button' ) {
					$control->getControlPrototype()->addClass( empty( $usedPrimary ) ? 'btn btn-primary' : 'btn btn-default' );
					$usedPrimary = true;
				} elseif ( in_array( $type, [ 'text', 'textarea', 'select' ], true ) ) {
					$control->getControlPrototype()->addClass( 'form-control' );
				} elseif ( in_array( $type, [ 'checkbox', 'radio' ], true ) ) {
					$control->getSeparatorPrototype()->setName( 'div' )->addClass( $type . ' ' . $type . '-inline' );
				}
			}
		};
	}
}


/**
 * 为 WordPress 仪表盘格式化表单
 *
 * @param Form   $form
 * @param string $type
 */
if ( ! function_exists( 'wizhi_admin_form' ) ) {
	function wizhi_admin_form( Form $form, $type = 'horizontal' ) {

		$screen = get_current_screen();

		// 设置自定义 Render 方法
		$form->setRenderer( new FormRender );

		$renderer                                     = $form->getRenderer();
		$renderer->wrappers[ 'group' ][ 'container' ] = null;
		$renderer->wrappers[ 'group' ][ 'label' ]     = 'h2';

		switch ( $type ) {
			case 'term_meta':
				if ( $screen->base == 'term' ) {
					$renderer->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
					$renderer->wrappers[ 'pair' ][ 'container' ]     = 'tr class=wizhi-form-filed';
				} else {
					$renderer->wrappers[ 'controls' ][ 'container' ] = '';
					$renderer->wrappers[ 'pair' ][ 'container' ]     = 'div class="form-field wizhi-form-filed"';
				}
				break;
			default:
				$renderer->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
				$renderer->wrappers[ 'pair' ][ 'container' ]     = 'tr class=wizhi-form-filed';
		}

		$renderer->wrappers[ 'label' ][ 'container' ]   = 'th class=row';
		$renderer->wrappers[ 'control' ][ 'container' ] = 'td';

	}
}