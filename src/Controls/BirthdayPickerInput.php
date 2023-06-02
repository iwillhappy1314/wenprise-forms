<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * BirthdayPicker Input
 */
class BirthdayPickerInput extends TextInput
{

    private array $settings = [];

    /**
     * @param null       $label
     * @param array|null $settings TinyMce 设置
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'birthday');
    }

	/**
	 * 生成控件 HTML 内容
	 *
	 * @return \Nette\Utils\Html
	 */
    public function getControl(): Html
    {
        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'format'      => 'YYYY-MM-DD',
            'template'    => 'YYYY-MM-DD',
            'maxYear'     => date("Y"),
            'customClass' => 'rs-form-control',
            'errorClass'  => 'rs-was-validated',
        ];

        $settings = array_merge($settings_default, $settings);

        $el->data('settings', json_encode($settings));

        return $el;
    }
}
