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
class WpUploaderInput extends BaseControl
{

    /** validation rule */
    const VALID = ':uploadControlValid';

    private $settings = [];

    /**
     * @param null       $label
     * @param bool
     * @param array|null $settings Chosen 设置
     */
    public function __construct($label = null, $multiple = false, array $settings = null)
    {
        parent::__construct($label);
        $this->control->multiple = (bool)$multiple;
        $this->control->type     = 'text';
        $this->settings          = (array)$settings;

        $this->setOption('type', 'wp-uploader');
        $this->addCondition(Form::BLANK)
             ->addRule([$this, 'isOk'], Validator::$messages[ self::VALID ]);
    }


    /**
     * 显示上传控件
     *
     * @return string
     */
    public function getControl()
    {

        $el = parent::getControl();

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_media();
            wp_enqueue_style('wprs-ajax-uploader');
            wp_enqueue_script('wprs-ajax-uploader');
        }

        $name     = $this->getHtmlName();
        $id       = $this->getHtmlId();
        $settings = $this->settings;
        $value    = $this->getValue();
        $preview  = '';
        $values   = '';
        $hide     = 'rs-hide';
        $multiple = $this->control->multiple ? true : false;

        $el->appendAttribute('class', $hide);

        // 如果有默认值，设置隐藏的真实表单
        if ($value) {
            $hide = '';
            if (is_array($value)) {
                foreach ($value as $v) {
                    $el->setAttribute('value', $v);
                    $preview .= $this->getPreview($v);
                    $values  .= "<input type='hidden' name='$name' value='$v'>";

                }
            } else {
                $el->setAttribute('value', $value);
                $preview .= $this->getPreview($value);

                $values  .= "<input type='hidden' name='$name' value='$value'>";
            }
        }

        $html = Html::el('div')
                    ->setAttribute('class', 'js-wp-uploader rs-wp-uploader')
                    ->data('name', $name)
                    ->data('multiple', $multiple)
                    ->data('settings', json_encode($settings));

        $html->addHtml(
            Html::el('div class=rs-wp-uploader__field')
                ->addHtml(
                    Html::el('div class=rs-uploader__value')
                        ->setAttribute('id', $id)
                        ->addHtml($values)
                )
                ->addHtml(
                    Html::el('input type=button class=rs-wp-uploader__button')
                        ->setAttribute('value', __('Upload Image', 'wprs'))
                )
                ->addHtml(
                    Html::el('div class=rs-uploader__preview')
                        ->appendAttribute('class', $hide)
                        ->addHtml($preview)
                )
                ->addHtml(
                    Html::el('div class=rs-wp-uploader__message')
                ));


        return $html;
    }


    /**
     * 显示预览图片
     *
     * @param $value
     *
     * @return string
     */
    public function getPreview($value)
    {

        if (function_exists('wp_get_attachment_thumb_url')) {
            $thumb = wp_get_attachment_thumb_url($value);
        } else {
            $thumb = $value;
        }

        $preview = Html::el('div class="rs-uploader__thumbnail"');
        $button  = Html::el('button type=button class=rs-uploader__close')
                       ->data('value', $value)
                       ->addHtml(
                           Html::el('span')
                               ->setText('x')
                       );

        $image = Html::el('img')
                     ->setAttribute('src', $thumb);

        $preview->addHtml($button . $image);

        return $preview;

    }

    /**
     * Returns HTML name of control.
     *
     * @return string
     */
    public function getHtmlName()
    {
        return parent::getHtmlName() . ($this->control->multiple ? '[]' : '');
    }


    /**
     * 只要输入不为空，即为验证通过
     *
     * @return bool
     */
    public function isOk()
    {

        return $this->isDisabled()
               || $this->getValue() == 0
               || $this->getValue() !== null;
    }

}