<?php

namespace Wenprise\Forms\Datastores;

use Wenprise\Forms\Form;

abstract class IDatastore
{
    var Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }


    abstract public function save();


    public function getFields(): array
    {
        $items = [];
        foreach ($this->form->getComponents() as $key => $item) {
            if ( ! is_string($item->getControl()) && $item->getControl()->type !== 'submit') {
                $items[ $key ] = $item->caption;
            }
        }

        return $items;
    }
}