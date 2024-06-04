<?php

/**
 * Ajax 文件上传控件
 */

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\Validator;
use Nette\Utils\Html;
use Wenprise\Forms\Helpers;


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
        $this->control->multiple = $multiple;
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
        $multiple    = (bool)$this->control->multiple;
        $file_types  = Helpers::data_get($settings, 'extFilter', ["jpg", "jpeg", "png", "gif", "zip", "pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx"]);

        $upload_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
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
                    ->data('extFilter', $file_types)
                    ->data('settings', json_encode($settings));

        $html
            ->addHtml(
                Html::el('div class=rs-uploader__container')
                    ->addHtml(
                        Html::el('div class=rs-uploader__text')
                            ->addHtml('<div class="rs-uploader__image">' . $upload_icon . '</div>')
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
                    ))
            ->addHtml(
                Html::el('div class=rs-uploader__description')
                    ->addText(__('Maximum allowed size for uploaded files:', 'wprs') . (int)(ini_get('upload_max_filesize')) . 'M')
            )->addHtml(
                ($file_types) ? Html::el('div class=rs-uploader__description')
                                    ->addText(__('Allowed file extensions:', 'wprs') . implode(', ', $file_types)) : ''
            );


        return $html;
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
        if (empty($value)) {
            return '';
        }

        $attachment = get_post($value);
        $close_icon = '<svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="12" height="12"><path d="M49.6 158.4l104-108.8 358.4 352 356.8-352 105.6 105.6-352 356.8 352 355.2-102.4 107.2L512 620.8 155.2 974.4l-105.6-105.6L406.4 512z" p-id="3640" fill="#ffffff"></path></svg>';

        if (function_exists('wp_get_attachment_thumb_url')) {
            if (wp_attachment_is('image', $value)) {
                $thumb = wp_get_attachment_thumb_url($value);
            } else {
                $thumb = Helpers::get_assets_url('dist/images/file.svg');
            }
        } else {
            $thumb = $value;
        }

        $preview = Html::el('div class="rs-uploader__thumbnail"');
        $button  = Html::el('button type=button class=rs-uploader__close')
                       ->data('value', $value)
                       ->setHtml($close_icon);

        if (wp_attachment_is('image', $value)) {
            $thumb_full = wp_get_attachment_image_url($value, 'full');
            $image      = Html::el('a target="_blank" class=rs-uploader__preview-image')
                              ->href($thumb_full)
                              ->addHtml(Html::el('img')->setAttribute('src', $thumb));
        } else {
            $image = Html::el('div class=rs-uploader__preview-image')->addHtml(Html::el('img')->setAttribute('src', $thumb));
        }

        $file_name = Html::el('div class=rs-uploader__preview-name')
                         ->setHtml('<a target=_blank href="' . $attachment->guid . '">' . $attachment->post_title . '</a>');

        $preview->addHtml($button . $image . $file_name);

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
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }


    /**
     * 只要输入不为空，即为验证通过
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->isDisabled()
               || $this->getValue() == 0
               || $this->getValue() !== null;
    }

}