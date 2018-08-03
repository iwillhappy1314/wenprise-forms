<?php
/**
 * 主题辅助函数
 *
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Renders\FormRender;

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
if ( ! function_exists( 'wprs_form' ) ) {
	function wprs_form( Form $form, $type = 'horizontal' )
	{

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
		$form->onRender[] = function ( $form )
		{
			foreach ( $form->getControls() as $control ) {
				if ( ! $control->getOption( 'class' ) ) {
					$control->setOption( 'class', 'col-md-12' );
				}
				$control->setOption( 'id', 'grp-' . $control->name );
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
if ( ! function_exists( 'wprs_admin_form' ) ) {
	function wprs_admin_form( Form $form, $type = 'horizontal' )
	{

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
					$renderer->wrappers[ 'pair' ][ 'container' ]     = 'tr class=wprs-form-filed';
				} else {
					$renderer->wrappers[ 'controls' ][ 'container' ] = '';
					$renderer->wrappers[ 'pair' ][ 'container' ]     = 'div class="form-field wprs-form-filed"';
				}
				break;
			default:
				$renderer->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
				$renderer->wrappers[ 'pair' ][ 'container' ]     = 'tr class=wprs-form-filed';
		}

		$renderer->wrappers[ 'label' ][ 'container' ]   = 'th class=row';
		$renderer->wrappers[ 'control' ][ 'container' ] = 'td';

	}
}


/**
 * 转换路径到 Url
 *
 * @param $directory
 *
 * @return string
 */
if ( ! function_exists( 'wprs_dir_to_url' ) ) {
	function wprs_dir_to_url( $directory )
	{
		$url   = \trailingslashit( $directory );
		$count = 0;

		# Sanitize directory separator on Windows
		$url = str_replace( '\\', '/', $url );

		$possible_locations = [
			WP_PLUGIN_DIR  => \plugins_url(), # If installed as a plugin
			WP_CONTENT_DIR => \content_url(), # If anywhere in wp-content
			ABSPATH        => \site_url( '/' ), # If anywhere else within the WordPress installation
		];

		foreach ( $possible_locations as $test_dir => $test_url ) {
			$test_dir_normalized = str_replace( '\\', '/', $test_dir );
			$url                 = str_replace( $test_dir_normalized, $test_url, $url, $count );

			if ( $count > 0 ) {
				return \untrailingslashit( $url );
			}
		}

		return ''; // return empty string to avoid exposing half-parsed paths
	}
}


if ( function_exists( 'wp_register_style' ) ) {

	// 插件版本
	if ( ! defined( 'WENPRISE_FORM_VERSION' ) ) {
		define( 'WENPRISE_FORM_VERSION', '1.6' );
	}

	# 设置根目录 Url
	if ( ! defined( 'WENPRISE_FORM_URL' ) ) {
		define( 'WENPRISE_FORM_URL', wprs_dir_to_url( __DIR__ ) );
	}

	// 附加样式和脚本
	wp_register_style( 'wprs-form-styles', WENPRISE_FORM_URL . '/assets/styles/main.css', [], WENPRISE_FORM_VERSION );
	wp_register_script( 'wprs-form-scripts', WENPRISE_FORM_URL . '/assets/scripts/main.js', [ 'jquery' ], WENPRISE_FORM_VERSION, true );

	// Chosen 样式和脚本
	wp_register_style( 'wprs-chosen-styles', WENPRISE_FORM_URL . '/assets/styles/chosen.css', [], WENPRISE_FORM_VERSION );
	wp_register_script( 'wprs-chosen-scripts', WENPRISE_FORM_URL . '/assets/scripts/chosen.js', [ 'jquery' ], WENPRISE_FORM_VERSION, true );

	// 表格输入
	wp_register_script( 'wprs-table-input', WENPRISE_FORM_URL . '/assets/scripts/table-input.js', [ 'jquery' ], WENPRISE_FORM_VERSION, true );

	// 前端验证
	wp_register_script( 'wprs-nette-forms', WENPRISE_FORM_URL . '/assets/scripts/nette-forms.js', [ 'jquery' ], WENPRISE_FORM_VERSION, true );

	wp_register_script( 'wprs-ajax-uploader', WENPRISE_FORM_URL . '/assets/scripts/ajax-uploader.js', [ 'jquery' ], WENPRISE_FORM_VERSION, true );

	// 颜色选择
	wp_enqueue_style( 'wp-color-picker' );
	wp_register_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), [ 'iris' ], false, 1 );
	wp_register_script( 'iris', admin_url( 'js/iris.min.js' ), [ 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ], false, 1 );

	$colorpicker_l10n = [ 'clear' => __( 'Clear' ), 'defaultString' => __( 'Default' ), 'pick' => __( 'Select Color' ), 'current' => __( 'Current Color' ), ];
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );


	// 注册公共样式和脚本
	wp_enqueue_style( 'wprs-form-styles' );
	wp_enqueue_script( 'wprs-form-scripts' );
}

