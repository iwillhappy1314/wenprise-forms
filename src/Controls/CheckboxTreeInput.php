<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\MultiChoiceControl;
use Nette\Utils\Html;

/**
 * 克隆输入
 */
class CheckboxTreeInput extends MultiChoiceControl
{
    /**
     * @var string
     */
    public string $item_key = '';

    public function __construct($label = null, array $items = null)
    {
        parent::__construct($label, $items);
        $this->control->type = 'checkbox';
    }

    public function getControl(): Html
    {
        return $this->recursiveRender($this->getItems());
    }

    private function recursiveRender($list)
    {
        $html = Html::el('ul class=rs-checkbox-tree');

        foreach ($list as $key => $value) {
            $this->item_key = $key;
            if (is_array($value)) {
                $html->addHtml(Html::el('li')->addHtml($this->recursiveRender($value)));
            } else {
                $html->addHtml(Html::el('li')
                               ->addHtml($this->getControlPart())
                               ->addHtml(Html::el('label', ['for' => $this->getHtmlId() . '-' . $key])
                                         ->addHtml($value)
                               )
                );
            }
        }

        return $html;
    }

    public function setValue($values)
    {
        if (is_scalar($values) || $values === null) {
            $values = (array)$values;
        } elseif ( ! is_array($values)) {
            throw new \Nette\InvalidArgumentException(sprintf("Value must be array or NULL, %s given in field '%s'.", gettype($values), $this->name));
        }
        $flip = [];
        foreach ($values as $value) {
            if ( ! is_scalar($value) && ! method_exists($value, '__toString')) {
                throw new \Nette\InvalidArgumentException(sprintf("Values must be scalar, %s given in field '%s'.", gettype($value), $this->name));
            }
            $flip[ (string)$value ] = true;
        }
        $values     = array_keys($flip);
        $items      = $this->items;
        $nestedKeys = [];
        array_walk_recursive($items, function ($value, $key) use (&$nestedKeys)
        {
            $nestedKeys[] = $key;
        });
        if ($diff = array_diff($values, $nestedKeys)) {
            $range = \Nette\Utils\Strings::truncate(implode(', ', array_map(function ($s) { return var_export($s, true); }, $nestedKeys)), 70, '...');
            $vals  = (count($diff) > 1 ? 's' : '') . " '" . implode("', '", $diff) . "'";
            throw new \Nette\InvalidArgumentException("Value$vals are out of allowed range [$range] in field '{$this->name}'.");
        }
        $this->value = $values;

        return $this;
    }

    public function getLabel($caption = null)
    {
        return parent::getLabel($caption)->for(null);
    }

    public function getControlPart(): ?Html
    {
        $key = $this->item_key;

        return parent::getControl()->addAttributes([
            'id'       => $this->getHtmlId() . '-' . $key,
            'checked'  => in_array($key, (array)$this->value),
            'disabled' => is_array($this->disabled) ? isset($this->disabled[ $key ]) : $this->disabled,
            'required' => null,
            'value'    => $key,
        ]);
    }

    public function getLabelPart(): ?Html
    {
        $key = $this->item_key;

        return parent::getLabel($this->items[ $key ])->for($this->getHtmlId() . '-' . $key);
    }

    public function getSelectedItems() :array
    {
        return array_intersect_key($this->recursiveJoin($this->items), array_flip($this->value));
    }

    public function getValue() :array
    {
        return array_values(array_intersect($this->value, array_keys($this->recursiveJoin($this->items))));
    }

    private function recursiveJoin(array $array, $arry = [])
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $arry = $this->recursiveJoin($val, $arry);
            } else {
                $arry[ $key ] = $val;
            }
        }

        return $arry;
    }

}
