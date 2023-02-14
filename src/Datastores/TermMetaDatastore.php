<?php

namespace Wenprise\Forms\Datastores;

class TermMetaDatastore extends IDatastore
{
    var int $term_id = 0;

    public function __construct($term_id, $form)
    {
        $this->term_id = $term_id;
        parent::__construct($form);
    }

    function save()
    {
        $values = $this->form->getValues();
        $fields = $this->getFields();

        foreach ($fields as $name => $label) {
            update_term_meta($this->term_id, $name, $values->$name);
        }

    }
}