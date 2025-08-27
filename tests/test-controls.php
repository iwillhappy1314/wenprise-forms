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