<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;

/**
 * 链式选择输入
 */
class ChainedInput extends BaseControl
{

    private array $settings = [];

    private array $fields = [];

    /**
     * @param null       $label    Html 标签
     * @param array|null $settings TinyMce 设置
     * @param array|null $fields   TinyMce 设置
     */
    public function __construct($label = null, ?array $settings = [], ?array $fields = [])
    {
        parent::__construct($label);
        $this->settings = $settings;
        $this->fields   = $fields;

        $this->setOption('type', 'chained');
    }


    /**
     * Loads HTTP data.
     */
    public function loadHttpData(): void
    {
        $fields = $this->fields;

        $values = [];
        foreach ($fields as $field) {
            $values[ $field ] = $_POST[ $field ];
        }

        $this->setValue($values);
    }


    /**
     *  生成 html
     *
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {

        $id            = $this->getHtmlId();
        $settings      = $this->settings;
        $fields        = $this->fields;
        $default_value = $this->getValue() ? $this->getValue() : [];

        $settings_default = [
            'selects'    => $fields,
            'emptyStyle' => 'none',
        ];

        $settings = array_merge($settings_default, $settings);

        $el = Html::el('div')
                  ->setAttribute('id', $id)
                  ->setAttribute('class', 'input-group frm-chained');

        foreach ($fields as $field) {

            $el->addHtml(
                Html::el('select class=rs-form-control')
                    ->appendAttribute('class', $field)
                    ->setAttribute('name', $field)
                    ->data('value', $default_value[ $field ])
            );
        }

        $el->data('settings', json_encode($settings));

        return $el;
    }

}