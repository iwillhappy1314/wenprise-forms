<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Nette\Utils\Html;

class uploadTests extends WP_UnitTestCase
{

    public function test_render()
    {
        $form = new Form();

        $input = $form->addAjaxUpload('file', 'File');

        echo $form;

        $this->assertInstanceOf(Html::class, $input->getLabel());
        $this->assertEquals('<label for="frm-file">File</label>', (string)$input->getLabel());

        // $this->assertTrue(wp_style_is('wprs-ajax-uploader', $list = 'registered'));
        // $this->assertTrue(wp_style_is('wprs-ajax-uploader', $list = 'enqueued'));
        //
        // $this->assertTrue(wp_script_is('wprs-ajax-uploader', $list = 'registered'));
        // $this->assertTrue(wp_script_is('wprs-ajax-uploader', $list = 'enqueued'));
    }

}
