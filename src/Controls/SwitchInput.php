<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\RadioList;
use Nette\Forms\Helpers;
use Nette\Utils\Html;
use Wenprise\Forms\FormHelpers;

/**
 * Chosen 增加选择
 */
class SwitchInput extends RadioList
{
    
    /**
     * @param null       $label 标签
     * @param array|null $items 选择项
     */
    public function __construct($label = null, array $items = null)
    {
        parent::__construct($label, $items);

        $this->container = Html::el('div class=rs-switch__options');
        $this->itemLabel = Html::el('div class=rs-switch');

        $this->setOption('type', 'switch');
    }


    /**
     * Generates control's HTML element.
     * @return Html
     */
    public function getControl()
    {

        $grandparent = FormHelpers::get_grandparent_class($this);

        $input = $grandparent::getControl();

        $options = $this->getItems();

        $items = [];
        foreach ($options as $key => $option) {
            $items[ $key ] = Html::el('span class=rs-switch__text')
                                 ->setText($option);
        }

        $ids = [];
        if ($this->generateId) {
            foreach ($items as $value => $label) {
                $ids[ $value ] = $input->id . '-' . $value;
            }
        }

        $input->setAttribute('class', 'rs-switch__control');

        return $this->container->setHtml(
            Helpers::createInputList(
                $this->translate($items),
                array_merge($input->attrs, [
                    'id:'               => $ids,
                    'checked?'          => $this->value,
                    'disabled:'         => $this->disabled,
                    'data-nette-rules:' => [key($items) => $input->attrs[ 'data-nette-rules' ]],
                ]),
                ['for:' => $ids] + $this->itemLabel->attrs
            )
        );

    }

}
