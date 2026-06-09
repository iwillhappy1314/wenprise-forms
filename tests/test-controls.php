<?php
/**
 * Input Controls Tests
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Controls\AjaxUploadInput;
use Wenprise\Forms\Controls\CsrfInput;
use Wenprise\Forms\Controls\TextEditor;
use Wenprise\Forms\Controls\GroupInput;
use Wenprise\Forms\Containers\Repeater;
use Wenprise\Forms\Renders\DefaultFormRender;
use Wenprise\Forms\Renders\InlineFormRender;
use Wenprise\Forms\Renders\GridFormRender;
use Nette\Utils\Html;

class ControlsTests extends WP_UnitTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $_POST = [];
        $_FILES = [];
    }

    /**
     * Test basic text input control
     */
    public function test_text_input_control()
    {
        $form = new Form('test_form');
        $input = $form->addText('name', 'Full Name');

        $this->assertInstanceOf(Html::class, $input->getLabel());
        $this->assertEquals('<label for="frm-name">Full Name</label>', (string)$input->getLabel());
        $this->assertEquals('<input type="text" name="name" id="frm-name">', (string)$input->getControl());
    }

    /**
     * Test email input control
     */
    public function test_email_input_control()
    {
        $form = new Form('test_form');
        $input = $form->addEmail('email', 'Email Address');

        $this->assertEquals('<label for="frm-email">Email Address</label>', (string)$input->getLabel());
        $this->assertEquals('<input type="email" name="email" id="frm-email">', (string)$input->getControl());
    }

    /**
     * Test password input control
     */
    public function test_password_input_control()
    {
        $form = new Form('test_form');
        $input = $form->addPassword('password', 'Password');

        $this->assertEquals('<label for="frm-password">Password</label>', (string)$input->getLabel());
        $this->assertEquals('<input type="password" name="password" id="frm-password">', (string)$input->getControl());
    }

    /**
     * Test textarea control
     */
    public function test_textarea_control()
    {
        $form = new Form('test_form');
        $input = $form->addTextArea('description', 'Description');

        $this->assertEquals('<label for="frm-description">Description</label>', (string)$input->getLabel());
        $this->assertStringContains('<textarea name="description" id="frm-description">', (string)$input->getControl());
    }

    /**
     * Test repeater control structure
     */
    public function test_repeater_control_structure()
    {
        $form = new Form('test_form');
        $repeater = $form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
            $row->addText('name', 'Name');
            $row->addText('phone', 'Phone');
        }, 1, 5);

        $this->assertInstanceOf(Repeater::class, $repeater);

        $rows = $repeater->getRows();
        $this->assertCount(1, $rows);

        $row = reset($rows);
        $this->assertInstanceOf(\Wenprise\Forms\Container::class, $row);
        $this->assertNotNull($row->getComponent('name', false));
        $this->assertNotNull($row->getComponent('phone', false));
    }

    /**
     * Test select control
     */
    public function test_select_control()
    {
        $form = new Form('test_form');
        $options = ['option1' => 'Option 1', 'option2' => 'Option 2'];
        $input = $form->addSelect('select', 'Select Option', $options);

        $this->assertEquals('<label for="frm-select">Select Option</label>', (string)$input->getLabel());
        $control = (string)$input->getControl();
        $this->assertStringContains('<select name="select" id="frm-select">', $control);
        $this->assertStringContains('<option value="option1">Option 1</option>', $control);
        $this->assertStringContains('<option value="option2">Option 2</option>', $control);
    }

    /**
     * Test checkbox control
     */
    public function test_checkbox_control()
    {
        $form = new Form('test_form');
        $input = $form->addCheckbox('agree', 'I agree to terms');

        $this->assertEquals('<label for="frm-agree">I agree to terms</label>', (string)$input->getLabel());
        $this->assertStringContains('<input type="checkbox" name="agree" id="frm-agree">', (string)$input->getControl());
    }

    /**
     * Test radio button control
     */
    public function test_radio_control()
    {
        $form = new Form('test_form');
        $options = ['yes' => 'Yes', 'no' => 'No'];
        $input = $form->addRadioList('choice', 'Choose Option', $options);

        $this->assertEquals('<label>Choose Option</label>', (string)$input->getLabel());
        $control = (string)$input->getControl();
        $this->assertStringContains('type="radio"', $control);
        $this->assertStringContains('name="choice"', $control);
        $this->assertStringContains('value="yes"', $control);
        $this->assertStringContains('value="no"', $control);
    }

    /**
     * Test checkbox list control
     */
    public function test_checkbox_list_control()
    {
        $form = new Form('test_form');
        $options = ['opt1' => 'Option 1', 'opt2' => 'Option 2', 'opt3' => 'Option 3'];
        $input = $form->addCheckboxList('options', 'Select Options', $options);

        $this->assertEquals('<label>Select Options</label>', (string)$input->getLabel());
        $control = (string)$input->getControl();
        $this->assertStringContains('type="checkbox"', $control);
        $this->assertStringContains('name="options[]"', $control);
        $this->assertStringContains('value="opt1"', $control);
        $this->assertStringContains('value="opt2"', $control);
        $this->assertStringContains('value="opt3"', $control);
    }

    /**
     * Test hidden field control
     */
    public function test_hidden_control()
    {
        $form = new Form('test_form');
        $input = $form->addHidden('hidden_field', 'hidden_value');

        $this->assertEquals('', (string)$input->getLabel());
        $this->assertEquals('<input type="hidden" name="hidden_field" value="hidden_value">', (string)$input->getControl());
    }

    /**
     * Test file upload control
     */
    public function test_file_upload_control()
    {
        $form = new Form('test_form');
        $input = $form->addUpload('file', 'Upload File');

        $this->assertEquals('<label for="frm-file">Upload File</label>', (string)$input->getLabel());
        $this->assertStringContains('<input type="file" name="file" id="frm-file">', (string)$input->getControl());
    }

    /**
     * Test CSRF input control
     */
    public function test_csrf_input_control()
    {
        $form = new Form('test_form');
        $input = $form->addCsrf('csrf_token', 'CSRF validation failed');

        $this->assertInstanceOf(CsrfInput::class, $input);
        $this->assertEquals('', (string)$input->getLabel());
        
        $control = (string)$input->getControl();
        $this->assertStringContains('<input type="hidden" name="csrf_token"', $control);
        $this->assertStringContains('value="', $control);
    }

    /**
     * Test Ajax upload control
     */
    public function test_ajax_upload_control()
    {
        $form = new Form('test_form');
        $input = $form->addAjaxUpload('ajax_file', 'Ajax Upload', false, [
            'extFilter' => ['jpg', 'jpeg', 'png', 'gif']
        ]);

        $this->assertInstanceOf(AjaxUploadInput::class, $input);
        $this->assertEquals('<label for="frm-ajax_file">Ajax Upload</label>', (string)$input->getLabel());
        
        $control = (string)$input->getControl();
        $this->assertStringContains('class="rs-hide"', $control);
    }

    /**
     * Test text editor control
     */
    public function test_text_editor_control()
    {
        $form = new Form('test_form');
        $input = $form->addEditor('content', 'Content Editor', [
            'textarea_rows' => 10
        ]);

        $this->assertInstanceOf(TextEditor::class, $input);
        $this->assertEquals('<label for="frm-content">Content Editor</label>', (string)$input->getLabel());
    }

    /**
     * Test input attributes and properties
     */
    public function test_input_attributes()
    {
        $form = new Form('test_form');
        $input = $form->addText('name', 'Name')
                      ->setAttribute('placeholder', 'Enter your name')
                      ->setAttribute('maxlength', 50)
                      ->setAttribute('class', 'form-control');

        $control = (string)$input->getControl();
        $this->assertStringContains('placeholder="Enter your name"', $control);
        $this->assertStringContains('maxlength="50"', $control);
        $this->assertStringContains('class="form-control"', $control);
    }

    /**
     * Test input validation states
     */
    public function test_input_validation_states()
    {
        $form = new Form('test_form');
        $input = $form->addText('email', 'Email')
                      ->setRequired('Email is required')
                      ->addRule($form::EMAIL, 'Please enter valid email');

        // Test with invalid data
        $form->setValues(['email' => 'invalid-email']);
        $this->assertFalse($form->isValid());
        $this->assertTrue($form->hasErrors());

        // Test with valid data
        $form->setValues(['email' => 'test@example.com']);
        $this->assertTrue($form->isValid());
        $this->assertFalse($form->hasErrors());
    }

    /**
     * Test input default values
     */
    public function test_input_default_values()
    {
        $form = new Form('test_form');
        $form->addText('name', 'Name')->setDefaultValue('John Doe');
        $form->addSelect('country', 'Country', [
            'us' => 'United States',
            'ca' => 'Canada',
            'uk' => 'United Kingdom'
        ])->setDefaultValue('us');

        $values = $form->getValues();
        $this->assertEquals('John Doe', $values->name);
        $this->assertEquals('us', $values->country);
    }

    /**
     * Test input disabled state
     */
    public function test_input_disabled_state()
    {
        $form = new Form('test_form');
        $input = $form->addText('readonly_field', 'Readonly Field')
                      ->setDisabled(true);

        $control = (string)$input->getControl();
        $this->assertStringContains('disabled', $control);
    }

    /**
     * Test input rules and conditions
     */
    public function test_input_rules_and_conditions()
    {
        $form = new Form('test_form');
        $form->addCheckbox('has_website', 'Has Website');
        $website_input = $form->addText('website', 'Website URL')
                              ->addConditionOn($form['has_website'], $form::EQUAL, true)
                              ->setRequired('Website URL is required')
                              ->addRule($form::URL, 'Please enter valid URL');

        // Test when checkbox is not checked
        $form->setValues(['has_website' => false, 'website' => '']);
        $this->assertTrue($form->isValid());

        // Test when checkbox is checked but URL is invalid
        $form->setValues(['has_website' => true, 'website' => 'invalid-url']);
        $this->assertFalse($form->isValid());

        // Test when checkbox is checked and URL is valid
        $form->setValues(['has_website' => true, 'website' => 'https://example.com']);
        $this->assertTrue($form->isValid());
    }

    /**
     * Test multiple file upload
     */
    public function test_multiple_file_upload()
    {
        $form = new Form('test_form');
        $input = $form->addAjaxUpload('files', 'Multiple Files', true, [
            'extFilter' => ['jpg', 'png', 'pdf', 'doc']
        ]);

        $this->assertInstanceOf(AjaxUploadInput::class, $input);
        $control = (string)$input->getControl();
        $this->assertStringContains('data-multiple="1"', $control);
    }

    /**
     * Test input HTML rendering consistency
     */
    public function test_html_rendering_consistency()
    {
        $form = new Form('test_form');
        
        // Add various input types
        $form->addText('text_field', 'Text Field');
        $form->addEmail('email_field', 'Email Field');
        $form->addPassword('password_field', 'Password Field');
        $form->addTextArea('textarea_field', 'Textarea Field');

        $rendered_form = (string)$form;

        // Check that all fields are rendered
        $this->assertStringContains('name="text_field"', $rendered_form);
        $this->assertStringContains('name="email_field"', $rendered_form);
        $this->assertStringContains('name="password_field"', $rendered_form);
        $this->assertStringContains('name="textarea_field"', $rendered_form);

        // Check proper HTML structure
        $this->assertStringContains('<form', $rendered_form);
        $this->assertStringContains('</form>', $rendered_form);
    }

    /**
     * Test repeater HTML rendering consistency
     */
    public function test_repeater_html_rendering_consistency()
    {
        $form = new Form('test_form');
        $form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
            $row->addText('name', 'Name');
            $row->addText('phone', 'Phone');
        }, 1, 3);

        $rendered_form = (string) $form;

        $this->assertStringContains('class="rs-repeater"', $rendered_form);
        $this->assertStringContains('name="contacts[0][name]"', $rendered_form);
        $this->assertStringContains('name="contacts[0][phone]"', $rendered_form);
        $this->assertStringContains('class="rs-repeater__add"', $rendered_form);
    }

    /**
     * Test Tailwind grid renderer maps Bootstrap column classes.
     */
    public function test_tailwind_grid_renderer_maps_bootstrap_columns()
    {
        $form = new Form('grid_form');
        $form->setRenderer(new GridFormRender('vertical'));

        $form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
            $row->addText('name', 'Name')->setOption('class', 'col-md-4');
            $row->addText('phone', 'Phone')->setOption('class', 'custom-field col-md-4');
            $row->addText('email', 'Email')->setOption('class', 'rs-col-md-4');
        }, 1, 3);

        $rendered_form = (string) $form;

        $this->assertStringContains('rs-grid-form__grid', $rendered_form);
        $this->assertStringContains('rs-grid-form__span-md-4', $rendered_form);
        $this->assertStringContains('custom-field', $rendered_form);
        $this->assertStringNotContainsString('rs-row', $rendered_form);
    }

    /**
     * Test Tailwind grid renderer supports setWidth API.
     */
    public function test_tailwind_grid_renderer_supports_set_width_api()
    {
        $form = new Form('grid_width_form');
        $form->setRenderer(new GridFormRender('vertical'));

        $form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
            $row->addText('name', 'Name')->setWidth(4);
            $row->addText('phone', 'Phone')->setWidth(6, 'lg');
            $row->addText('email', 'Email')->setWidth(12, 'base');
        }, 1, 3);

        $rendered_form = (string) $form;

        $this->assertStringContains('rs-grid-form__span-md-4', $rendered_form);
        $this->assertStringContains('rs-grid-form__span-lg-6', $rendered_form);
        $this->assertStringContains('rs-grid-form__span-base-12', $rendered_form);
    }

    /**
     * Test Tailwind grid renderer supports shorthand setWidth API.
     */
    public function test_tailwind_grid_renderer_supports_shorthand_set_width_api()
    {
        $form = new Form('grid_width_short_form');
        $form->setRenderer(new GridFormRender('vertical'));

        $form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
            $row->addText('name', 'Name')->setWidth(6, 3, 4);
        }, 1, 3);

        $rendered_form = (string) $form;

        $this->assertStringContains('rs-grid-form__span-base-6', $rendered_form);
        $this->assertStringContains('rs-grid-form__span-md-3', $rendered_form);
        $this->assertStringContains('rs-grid-form__span-lg-4', $rendered_form);
    }

    /**
     * Test Tailwind grid renderer does not duplicate repeater grid wrappers.
     */
    public function test_tailwind_grid_renderer_does_not_duplicate_repeater_grid_wrappers()
    {
        $form = new Form('grid_wrapper_form');
        $form->setRenderer(new GridFormRender('vertical'));

        $form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
            $row->addText('name', 'Name')->setWidth(4);
            $row->addText('phone', 'Phone')->setWidth(4);
            $row->addText('email', 'Email')->setWidth(4);
        }, 1, 3);

        $rendered_form = (string) $form;

        $this->assertStringContains('class="rs-repeater__row-controls"', $rendered_form);
        $this->assertStringNotContainsString('class="rs-repeater__row-controls rs-grid-form__grid"', $rendered_form);
    }

    /**
     * Test Tailwind grid renderer forces full width in horizontal layout.
     */
    public function test_tailwind_grid_renderer_forces_full_width_in_horizontal_layout()
    {
        $form = new Form('grid_horizontal_form');
        $form->setRenderer(new GridFormRender('horizontal'));

        $form->addText('name', 'Name')->setWidth(4)->setOption('class', 'custom-field col-md-4');

        $rendered_form = (string) $form;

        $this->assertStringContains('rs-grid-form__span-base-12', $rendered_form);
        $this->assertStringContains('rs-grid-form__span-md-12', $rendered_form);
        $this->assertStringContains('custom-field', $rendered_form);
        $this->assertStringNotContainsString('rs-grid-form__span-md-4', $rendered_form);
    }

    /**
     * Test default renderer maps setWidth to Bootstrap column classes.
     */
    public function test_default_renderer_maps_set_width_to_bootstrap_columns()
    {
        $form = new Form('default_width_form');
        $form->setRenderer(new DefaultFormRender('vertical'));

        $form->addText('name', 'Name')->setWidth(4);
        $form->addText('phone', 'Phone')->setWidth(6, 'lg');

        $rendered_form = (string) $form;

        $this->assertStringContains('rs-col-md-4', $rendered_form);
        $this->assertStringContains('rs-col-lg-6', $rendered_form);
    }

    /**
     * Test inline renderer maps setWidth to Bootstrap column classes.
     */
    public function test_inline_renderer_maps_set_width_to_bootstrap_columns()
    {
        $form = new Form('inline_width_form');
        $form->setRenderer(new InlineFormRender());

        $form->addText('name', 'Name')->setWidth(4)->setOption('class', 'custom-field');

        $rendered_form = (string) $form;

        $this->assertStringContains('rs-col-md-4', $rendered_form);
        $this->assertStringContains('custom-field', $rendered_form);
    }

    /**
     * Test input error handling
     */
    public function test_input_error_handling()
    {
        $form = new Form('test_form');
        $input = $form->addText('name', 'Name')
                      ->setRequired('Name is required')
                      ->addRule($form::MIN_LENGTH, 'Name must be at least 2 characters', 2);

        // Test with empty value
        $form->setValues(['name' => '']);
        $this->assertFalse($form->isValid());
        
        $errors = $form->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Name is required', $errors[0]->message);

        // Test with too short value
        $form->setValues(['name' => 'A']);
        $this->assertFalse($form->isValid());
        
        $errors = $form->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Name must be at least 2 characters', $errors[0]->message);
    }

    public function tearDown(): void
    {
        $_POST = [];
        $_FILES = [];
        parent::tearDown();
    }
}
