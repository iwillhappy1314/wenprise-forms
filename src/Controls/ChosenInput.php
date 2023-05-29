<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\SelectBox;
use Nette\Utils\Html;

/**
 * Chosen 增加选择
 */
class ChosenInput extends SelectBox
{

    private array $settings = [];

    /**
     * @param null       $label    标签
     * @param array|null $items    选择项
     * @param array|null $settings Chosen 设置
     */
    public function __construct($label = null, array $items = null, array $settings = null)
    {
        parent::__construct($label, $items);
        $this->settings = (array)$settings;

        $this->setOption('type', 'chosen');
    }

    public function getControl(): Html
    {

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'disable_search'            => false,
            'placeholder_text_multiple' => __('Select Some Options', 'wprs'),
            'placeholder_text_single'   => __('Select an Option', 'wprs'),
            'no_results_text'           => __('No results match', 'wprs'),
        ];

        $settings = array_merge($settings_default, $settings);

        $el->data('settings', json_encode($settings));

        return Html::fromHtml( $el );

    }

}
