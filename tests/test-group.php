<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Nette\Utils\Html;


class groupTests extends WP_UnitTestCase
{

    public function test_render()
    {
        $form = new Form();

        $input = $form->addGroupInput('group', 'Group', '11');

        $this->assertInstanceOf(Html::class, $input->getLabel());

        $this->assertEquals('<label for="frm-group">Group</label>', (string)$input->getLabel());
        $this->assertEquals('<input type="text" name="group" maxlength="11" id="frm-group">', (string)$input->getControl());

        $input = $input->setPrefix('<span class="fa fa-user"></span>');
        $this->assertEquals('<span class="fa fa-user"></span>', (string)$input->getPrefix());

        $input = $input->setSuffix('<span class="fa fa-search"></span>');
        $this->assertEquals('<span class="fa fa-search"></span>', (string)$input->getSuffix());

    }

}
