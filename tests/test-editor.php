<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Nette\Utils\Html;


class editorTests extends WP_UnitTestCase
{

    public function test_render()
    {
        $form = new Form();

        $input = $form->addEditor('editor', 'Visual Editor', []);

        echo $form;

        $this->assertInstanceOf(Html::class, $input->getLabel());

        $this->assertEquals('<label for="frm-editor">Visual Editor</label>', (string)$input->getLabel());
    }

}
