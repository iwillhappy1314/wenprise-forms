<?php

namespace Wenprise\Forms\Datastores;

class OptionsDatastore extends IDatastore
{
    var int $post_id = 0;

    public function __construct($form)
    {
        parent::__construct($form);
    }

    function save()
    {
        $values = $this->form->getValues();
        $fields = $this->getFields();

        foreach ($fields as $name => $label) {
            update_option($name, $values->$name);
        }

    }
}