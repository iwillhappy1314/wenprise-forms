<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;

class csrfTests extends WP_UnitTestCase
{

    public function test_render()
    {
        $form = new Form();

        $input = $form->addCsrf('nonce', 'Nonce', []);

        echo $form;

        $this->assertEquals(null, $input->getLabel());
        $this->assertEquals('', (string)$input->getLabel());

        $this->assertEquals('<input type="hidden" name="nonce" value="' . wp_create_nonce('nonce') . '">', (string)$input->getControl());

        $this->assertEquals(wp_verify_nonce($input->generateToken(), 'nonce'), 1);
    }

}
