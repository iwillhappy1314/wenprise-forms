<?php
/**
 * Form Validation Tests
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Helpers;

class FormValidationTests extends WP_UnitTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // Reset $_POST and $_REQUEST for each test
        $_POST = [];
        $_REQUEST = [];
    }

    /**
     * Test basic form creation and validation setup
     */
    public function test_form_creation()
    {
        $form = new Form('test_form');
        
        $this->assertInstanceOf(Form::class, $form);
        $this->assertEquals('test_form', $form->getName());
    }

    /**
     * Test required field validation
     */
    public function test_required_field_validation()
    {
        $form = new Form('test_form');
        $form->addText('required_field', 'Required Field')
             ->setRequired('This field is required');

        // Test empty submission
        $_POST = [];
        $form->setValues($_POST);
        
        $this->assertFalse($form->isValid());
        $this->assertTrue($form->hasErrors());
    }

    /**
     * Test email validation
     */
    public function test_email_validation()
    {
        $form = new Form('test_form');
        $form->addEmail('email', 'Email Address')
             ->setRequired('Email is required');

        // Test invalid email
        $_POST = ['email' => 'invalid-email'];
        $form->setValues($_POST);
        
        $this->assertFalse($form->isValid());

        // Test valid email
        $_POST = ['email' => 'test@example.com'];
        $form->setValues($_POST);
        
        $this->assertTrue($form->isValid());
    }

    /**
     * Test text length validation
     */
    public function test_text_length_validation()
    {
        $form = new Form('test_form');
        $form->addText('name', 'Name')
             ->setRequired('Name is required')
             ->addRule($form::MIN_LENGTH, 'Name must be at least 2 characters', 2)
             ->addRule($form::MAX_LENGTH, 'Name must be no more than 50 characters', 50);

        // Test too short
        $_POST = ['name' => 'A'];
        $form->setValues($_POST);
        $this->assertFalse($form->isValid());

        // Test too long
        $_POST = ['name' => str_repeat('A', 51)];
        $form->setValues($_POST);
        $this->assertFalse($form->isValid());

        // Test valid length
        $_POST = ['name' => 'Valid Name'];
        $form->setValues($_POST);
        $this->assertTrue($form->isValid());
    }

    /**
     * Test numeric validation
     */
    public function test_numeric_validation()
    {
        $form = new Form('test_form');
        $form->addInteger('age', 'Age')
             ->setRequired('Age is required')
             ->addRule($form::RANGE, 'Age must be between 1 and 120', [1, 120]);

        // Test non-numeric
        $_POST = ['age' => 'not a number'];
        $form->setValues($_POST);
        $this->assertFalse($form->isValid());

        // Test out of range
        $_POST = ['age' => '150'];
        $form->setValues($_POST);
        $this->assertFalse($form->isValid());

        // Test valid number
        $_POST = ['age' => '25'];
        $form->setValues($_POST);
        $this->assertTrue($form->isValid());
    }

    /**
     * Test conditional validation
     */
    public function test_conditional_validation()
    {
        $form = new Form('test_form');
        $form->addCheckbox('has_phone', 'Has Phone');
        $form->addText('phone', 'Phone Number')
             ->addConditionOn($form['has_phone'], $form::EQUAL, true)
             ->setRequired('Phone is required when "Has Phone" is checked');

        // Test when checkbox is not checked - phone should not be required
        $_POST = ['has_phone' => false, 'phone' => ''];
        $form->setValues($_POST);
        $this->assertTrue($form->isValid());

        // Test when checkbox is checked but phone is empty - should fail
        $_POST = ['has_phone' => true, 'phone' => ''];
        $form->setValues($_POST);
        $this->assertFalse($form->isValid());

        // Test when checkbox is checked and phone is provided - should pass
        $_POST = ['has_phone' => true, 'phone' => '1234567890'];
        $form->setValues($_POST);
        $this->assertTrue($form->isValid());
    }

    /**
     * Test custom validation rules
     */
    public function test_custom_validation()
    {
        $form = new Form('test_form');
        $form->addText('username', 'Username')
             ->setRequired('Username is required')
             ->addRule(function($control) {
                 $value = $control->getValue();
                 return !in_array(strtolower($value), ['admin', 'root', 'administrator']);
             }, 'Username cannot be admin, root, or administrator');

        // Test forbidden username
        $_POST = ['username' => 'admin'];
        $form->setValues($_POST);
        $this->assertFalse($form->isValid());

        // Test valid username
        $_POST = ['username' => 'user123'];
        $form->setValues($_POST);
        $this->assertTrue($form->isValid());
    }

    /**
     * Test form data retrieval
     */
    public function test_form_data_retrieval()
    {
        $form = new Form('test_form');
        $form->addText('name', 'Name');
        $form->addEmail('email', 'Email');

        $_POST = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
        $form->setValues($_POST);

        $this->assertTrue($form->isValid());
        
        $values = $form->getValues();
        $this->assertEquals('John Doe', $values->name);
        $this->assertEquals('john@example.com', $values->email);
    }

    /**
     * Test error message handling
     */
    public function test_error_messages()
    {
        $form = new Form('test_form');
        $form->addText('name', 'Name')
             ->setRequired('Custom required message');

        $_POST = [];
        $form->setValues($_POST);
        
        $this->assertFalse($form->isValid());
        $errors = $form->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Custom required message', $errors[0]->message);
    }

    /**
     * Test helpers data_get function
     */
    public function test_helpers_data_get()
    {
        $data = [
            'user' => [
                'profile' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ]
        ];

        $this->assertEquals('John Doe', Helpers::data_get($data, 'user.profile.name'));
        $this->assertEquals('john@example.com', Helpers::data_get($data, 'user.profile.email'));
        $this->assertEquals('default', Helpers::data_get($data, 'user.profile.phone', 'default'));
        $this->assertNull(Helpers::data_get($data, 'nonexistent.key'));
    }

    /**
     * Test helpers input_get function
     */
    public function test_helpers_input_get()
    {
        $_REQUEST = [
            'test_field' => 'test_value',
            'numeric_field' => '123'
        ];

        $this->assertEquals('test_value', Helpers::input_get('test_field'));
        $this->assertEquals('123', Helpers::input_get('numeric_field'));
        $this->assertEquals('default', Helpers::input_get('nonexistent', 'default'));
        $this->assertNull(Helpers::input_get('nonexistent'));
    }

    public function tearDown(): void
    {
        // Clean up
        $_POST = [];
        $_REQUEST = [];
        parent::tearDown();
    }
}