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
class AjaxUploadInput extends BaseControl
{

    /** validation rule */
    const VALID = ':uploadControlValid';

    private array $settings = [];

    public string $url;

    /**
     * @param null       $label
     * @param bool       $multiple
     * @param array|null $settings Chosen 设置
     */
    public function __construct($label = null, bool $multiple = false, array $settings = null)
    {
        parent::__construct($label);
        $this->control->multiple = (bool)$multiple;
        $this->control->type     = 'text';
        $this->settings          = (array)$settings;

        $this->setOption('type', 'uploader');
        $this->addCondition(Form::BLANK)
             ->addRule([$this, 'isOk'], Validator::$messages[ self::VALID ]);
    }


    /**
     * 显示上传控件
     *
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {

        $el = parent::getControl();

        $name        = $this->getHtmlName();
        $id          = $this->getHtmlId();
        $settings    = $this->settings;
        $placeholder = $this->control->getAttribute('placeholder') ? $this->control->getAttribute('placeholder') : __('Select File', 'wprs');
        $value       = $this->getValue();
        $preview     = '';
        $values      = '';
        $hide        = 'rs-hide';
        $multiple    = (bool) $this->control->multiple;

        $close_icon = '<svg width="103" height="76" xmlns="http://www.w3.org/2000/svg">
                        <path d="M43 60v16H24v-.007C11.218 75.723.998 65.283.998 52.499.998 40.1 10.628 29.836 23 29.047c0-.205-.005-.384-.005-.546 0-15.74 12.76-28.5 28.5-28.5s28.5 12.76 28.5 28.5c0 .182 0 .366-.005.546 12.379.781 22.019 11.049 22.019 23.453 0 12.4-9.635 22.666-22.01 23.452V76H61V60h9.2a5 5 0 0 0 3.6-8.479l-18.2-18.81a5 5 0 0 0-7.187 0L30.2 51.522A5 5 0 0 0 33.8 60H43z" fill="#9a9a9a"></path>
                    </svg>';

        $el->appendAttribute('class', $hide);

        // 如果有默认值，设置隐藏的真实表单
        if ($value) {
            $hide = '';
            if (is_array($value)) {
                foreach ($value as $v) {
                    $el->setAttribute('value', $v);
                    $preview .= $this->getPreview($v);

                    $values .= "<input type='hidden' name='$name' value='$v'>";
                }
            } else {
                $el->setAttribute('value', $value);
                $preview .= $this->getPreview($value);

                $values .= "<input type='hidden' name='$name' value='$value'>";
            }
        }

        $html = Html::el('div')
                    ->setAttribute('id', $id)
                    ->setAttribute('class', 'js-uploader rs-uploader')
                    ->data('name', $name)
                    ->data('multiple', $multiple)
                    ->data('settings', json_encode($settings));

        $html
            ->addHtml(
                Html::el('div class=rs-uploader__text')
                    ->addHtml('<div class="rs-uploader__image">' . $close_icon . '</div>')
                    ->addText(__('Drag & Drop Images Here', 'wprs'))
            )
            ->addHtml(
                Html::el('div class=rs-uploader__browser')
                    ->addHtml(
                        Html::el('label class="rs-btn rs-btn-default rs-uploader__button"')
                            ->addHtml(
                                Html::el('span')
                                    ->addHtml($placeholder)
                            )
                            ->addHtml(
                                Html::el('input type="file" class=rs-uploader__shadow')
                                    ->setAttribute('name', 'js-input-shadow')
                                    ->setAttribute('multiple', $multiple)
                                    ->setAttribute('title', $placeholder)
                                    ->data('url', $this->url)
                            )
                    )
                    ->addHtml(
                        Html::el('div class=rs-uploader__value')
                            ->addHtml($values)
                    )
                    ->addHtml(
                        Html::el('div class=rs-uploader__preview')
                            ->appendAttribute('class', $hide)
                            ->addHtml($preview)
                    )
                    ->addHtml(
                        Html::el('div class="js-uploader-message rs-uploader__message"')
                    )
            );

        $script = "<script>
			jQuery(document).ready(function($) {
				$('#$id').wprsAjaxUploader();
			});
		</script>";

        return Html::fromHtml( $html . $script);
    }


    /**
     * 显示预览图片
     *
     * @param $value
     *
     * @return string
     */
    public function getPreview($value): string
    {

        if (function_exists('wp_get_attachment_thumb_url')) {
            $thumb = wp_get_attachment_thumb_url($value);
        } else {
            $thumb = $value;
        }

        $close_icon = '<svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="12" height="12"><path d="M49.6 158.4l104-108.8 358.4 352 356.8-352 105.6 105.6-352 356.8 352 355.2-102.4 107.2L512 620.8 155.2 974.4l-105.6-105.6L406.4 512z" p-id="3640" fill="#ffffff"></path></svg>';

        $preview = Html::el('div class="rs-uploader__thumbnail"');
        $button  = Html::el('button type=button class=rs-uploader__close')
                       ->data('value', $value)
                       ->setHtml($close_icon);

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
    public function getHtmlName(): string
    {
        return parent::getHtmlName() . ($this->control->multiple ? '[]' : '');
    }


    /**
     * 设置后端 URL
     *
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url): static {
        $this->url = $url;

        return $this;
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