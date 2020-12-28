<?php

namespace Wenprise\Forms\Datastores;

abstract class IDatastore
{
    var $form = null;

    public function __construct($form)
    {
        $this->form = $form;
    }


    abstract public function save();


    public function getFields(): array
    {
        $items = [];
        foreach ($this->form->getComponents() as $key => $item) {
            if ($item->getControl()->type !== 'submit') {
                $items[ $key ] = $item->caption;
            }
        }

        return $items;
    }
}