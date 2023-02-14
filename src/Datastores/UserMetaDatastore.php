<?php

namespace Wenprise\Forms\Datastores;


class UserMetaDatastore extends IDatastore
{
    var int $user_id = 0;

    public function __construct($user_id, $form)
    {
        $this->user_id = $user_id;
        parent::__construct($form);
    }

    function save()
    {
        $values = $this->form->getValues();
        $fields = $this->getFields();

        foreach ($fields as $name => $label) {
            update_user_meta($this->user_id, $name, $values->$name);
        }

    }
}