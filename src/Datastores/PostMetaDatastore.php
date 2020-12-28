<?php

namespace Wenprise\Forms\Datastores;


class PostMetaDatastore extends \Wenprise\Forms\Datastores\IDatastore
{
    var int $post_id = 0;

    public function __construct($post_id, $form)
    {
        $this->post_id = $post_id;
        parent::__construct($form);
    }

    function save()
    {
        $values = $this->form->getValues();
        $fields = $this->getFields();

        $post_id = wp_update_post([
            'ID'          => $this->post_id,
            'post_title'  => $values->post_title,
            'post_type'   => 'post',
            'post_status' => 'publish',
        ]);

        unset($fields[ 'post_title' ], $fields[ 'post_type' ], $fields[ 'post_status' ]);

        foreach ($fields as $name => $label) {
            update_post_meta($post_id, $name, $values->$name);
        }

    }
}