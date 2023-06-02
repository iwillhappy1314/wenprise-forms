<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * DatePicker input control.
 */
class StarRatingInput extends TextInput
{

    private array $settings = [];

	/**
	 * @param null       $label    Html 标签
	 * @param array|null $settings TinyMce 设置
	 */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'star-rating');
    }


    /**
     * Generates control's HTML element.
     *
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {
        $settings = $this->settings;

        $el = parent::getControl();

        $settings_default = [
            'displayOnly' => false,
            'showClear' => false,
            'theme'       => 'krajee-svg',
            'step'        => 1,
        ];

        $settings = array_merge($settings_default, $settings);

        $el->data('min', $settings[ 'min' ]);
        $el->data('max', $settings[ 'max' ]);
        $el->data('step', $settings[ 'step' ]);
        $el->data('settings', json_encode($settings));

        return $el;
    }
}
