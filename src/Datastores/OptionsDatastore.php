<?php

namespace Wenprise\Forms\Datastores;

class OptionsDatastore extends IDatastore
{
    public function __construct($form)
    {
        parent::__construct($form);
    }

    public function save(): void
    {
        $values = $this->form->getValues();
        $fields = $this->getFields();

        foreach ($fields as $name => $label) {
            update_option($name, $values->$name);
        }

    }
}