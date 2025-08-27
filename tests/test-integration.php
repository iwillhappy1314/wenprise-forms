<?php
/**
 * Integration Tests
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Init;
use Wenprise\Forms\Datastores\PostMetaDatastore;
use Wenprise\Forms\Datastores\UserMetaDatastore;
use Wenprise\Forms\Renders\DefaultFormRender;

class IntegrationTests extends WP_UnitTestCase
{

    private $test_post_id;
    private $test_user_id;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->test_post_id = $this->factory->post->create([
            'post_title' => 'Test Post',
            'post_status' => 'publish'
        ]);

        $this->test_user_id = $this->factory->user->create([
            'user_login' => 'testuser',
            'user_email' => 'test@example.com'
        ]);

        // Initialize plugin
        new Init();
    }

    /**
     * Test complete form workflow: creation -> validation -> saving
     */
    public function test_complete_form_workflow()
    {
        // Create form
        $form = new Form('contact_form');
        $form->addText('name', 'Full Name')
             ->setRequired('Name is required');
        $form->addEmail('email', 'Email')
             ->setRequired('Email is required');
        $form->addTextArea('message', 'Message')
             ->setRequired('Message is required');
        $form->addCsrf('csrf_token', 'Security validation failed');

        // Set datastore
        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        // Test form rendering
        $rendered = (string)$form;
        $this->assertStringContains('<form', $rendered);
        $this->assertStringContains('name="name"', $rendered);
        $this->assertStringContains('name="email"', $rendered);
        $this->assertStringContains('name="message"', $rendered);
        $this->assertStringContains('name="csrf_token"', $rendered);

        // Test form validation and saving
        $form->setValues([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message'
        ]);

        $this->assertTrue($form->isValid());
        $form->save();

        // Verify data was saved
        $this->assertEquals('John Doe', get_post_meta($this->test_post_id, 'name', true));
        $this->assertEquals('john@example.com', get_post_meta($this->test_post_id, 'email', true));
        $this->assertEquals('This is a test message', get_post_meta($this->test_post_id, 'message', true));
    }

    /**
     * Test user profile form integration
     */
    public function test_user_profile_form_integration()
    {
        $form = new Form('user_profile');
        $form->addText('first_name', 'First Name');
        $form->addText('last_name', 'Last Name');
        $form->addText('bio', 'Bio');
        $form->addText('website', 'Website')
             ->addRule($form::URL, 'Please enter valid URL');

        $datastore = new UserMetaDatastore($this->test_user_id, $form);
        $form->setDatastore($datastore);

        // Test with valid data
        $form->setValues([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'bio' => 'Software developer',
            'website' => 'https://johndoe.com'
        ]);

        $this->assertTrue($form->isValid());
        $form->save();

        // Verify user meta was updated
        $this->assertEquals('John', get_user_meta($this->test_user_id, 'first_name', true));
        $this->assertEquals('Doe', get_user_meta($this->test_user_id, 'last_name', true));
        $this->assertEquals('Software developer', get_user_meta($this->test_user_id, 'bio', true));
        $this->assertEquals('https://johndoe.com', get_user_meta($this->test_user_id, 'website', true));
    }

    /**
     * Test multi-step form simulation
     */
    public function test_multi_step_form()
    {
        // Step 1: Personal Information
        $step1_form = new Form('step1');
        $step1_form->addText('first_name', 'First Name')->setRequired();
        $step1_form->addText('last_name', 'Last Name')->setRequired();
        $step1_form->addEmail('email', 'Email')->setRequired();

        $step1_form->setValues([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertTrue($step1_form->isValid());
        $step1_data = $step1_form->getValues();

        // Step 2: Additional Information
        $step2_form = new Form('step2');
        $step2_form->addText('phone', 'Phone');
        $step2_form->addTextArea('bio', 'Biography');
        $step2_form->addSelect('country', 'Country', [
            'us' => 'United States',
            'ca' => 'Canada',
            'uk' => 'United Kingdom'
        ]);

        $step2_form->setValues([
            'phone' => '123-456-7890',
            'bio' => 'Software developer',
            'country' => 'us'
        ]);

        $this->assertTrue($step2_form->isValid());
        $step2_data = $step2_form->getValues();

        // Combine and save all data
        $combined_form = new Form('combined');
        foreach (array_merge((array)$step1_data, (array)$step2_data) as $key => $value) {
            $combined_form->addHidden($key, $value);
        }

        $datastore = new UserMetaDatastore($this->test_user_id, $combined_form);
        $combined_form->setDatastore($datastore);
        $combined_form->save();

        // Verify all data was saved
        $this->assertEquals('John', get_user_meta($this->test_user_id, 'first_name', true));
        $this->assertEquals('Doe', get_user_meta($this->test_user_id, 'last_name', true));
        $this->assertEquals('john@example.com', get_user_meta($this->test_user_id, 'email', true));
        $this->assertEquals('123-456-7890', get_user_meta($this->test_user_id, 'phone', true));
        $this->assertEquals('Software developer', get_user_meta($this->test_user_id, 'bio', true));
        $this->assertEquals('us', get_user_meta($this->test_user_id, 'country', true));
    }

    /**
     * Test form with file upload integration
     */
    public function test_file_upload_integration()
    {
        $form = new Form('upload_form');
        $form->addText('title', 'Title')->setRequired();
        $form->addAjaxUpload('attachment', 'Upload File', false, [
            'extFilter' => ['jpg', 'jpeg', 'png', 'pdf']
        ]);

        // Simulate file upload
        $_FILES = [
            'attachment' => [
                'name' => 'test-file.jpg',
                'type' => 'image/jpeg',
                'size' => 50000,
                'tmp_name' => '/tmp/test-upload',
                'error' => UPLOAD_ERR_OK
            ]
        ];

        $form->setValues([
            'title' => 'Test Upload',
            'attachment' => '123' // Simulated media ID
        ]);

        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        $this->assertTrue($form->isValid());
        $form->save();

        $this->assertEquals('Test Upload', get_post_meta($this->test_post_id, 'title', true));
        $this->assertEquals('123', get_post_meta($this->test_post_id, 'attachment', true));
    }

    /**
     * Test conditional fields integration
     */
    public function test_conditional_fields_integration()
    {
        $form = new Form('conditional_form');
        $form->addSelect('user_type', 'User Type', [
            'individual' => 'Individual',
            'business' => 'Business'
        ]);
        
        $form->addText('personal_name', 'Full Name')
             ->addConditionOn($form['user_type'], $form::EQUAL, 'individual')
             ->setRequired('Full name is required for individuals');
             
        $form->addText('company_name', 'Company Name')
             ->addConditionOn($form['user_type'], $form::EQUAL, 'business')
             ->setRequired('Company name is required for businesses');
             
        $form->addText('tax_id', 'Tax ID')
             ->addConditionOn($form['user_type'], $form::EQUAL, 'business');

        // Test individual user
        $form->setValues([
            'user_type' => 'individual',
            'personal_name' => 'John Doe',
            'company_name' => '', // Should be ignored
            'tax_id' => '' // Should be ignored
        ]);

        $this->assertTrue($form->isValid());

        // Test business user
        $form->setValues([
            'user_type' => 'business',
            'personal_name' => '', // Should be ignored
            'company_name' => 'Acme Corp',
            'tax_id' => '123456789'
        ]);

        $this->assertTrue($form->isValid());

        // Test business user without required company name
        $form->setValues([
            'user_type' => 'business',
            'personal_name' => '',
            'company_name' => '', // Required but missing
            'tax_id' => '123456789'
        ]);

        $this->assertFalse($form->isValid());
    }

    /**
     * Test form rendering with different themes/renderers
     */
    public function test_form_rendering_integration()
    {
        $form = new Form('render_test');
        $form->addText('name', 'Name');
        $form->addEmail('email', 'Email');
        $form->addSubmit('submit', 'Submit Form');

        // Test default renderer
        $this->assertInstanceOf(DefaultFormRender::class, $form->getRenderer());

        $rendered = (string)$form;
        $this->assertStringContains('<form', $rendered);
        $this->assertStringContains('<label', $rendered);
        $this->assertStringContains('<input', $rendered);
        $this->assertStringContains('type="submit"', $rendered);
    }

    /**
     * Test WordPress actions and filters integration
     */
    public function test_wordpress_hooks_integration()
    {
        // Test that WordPress actions are properly hooked
        $this->assertTrue(has_action('init') !== false);
        $this->assertTrue(has_action('wp_enqueue_scripts') !== false);
        $this->assertTrue(has_action('admin_enqueue_scripts') !== false);

        // Test script and style registration
        global $wp_scripts, $wp_styles;
        
        do_action('wp_enqueue_scripts');
        
        $this->assertTrue(isset($wp_scripts->registered['wprs-forms-main']));
        $this->assertTrue(isset($wp_styles->registered['wprs-forms-main']));
    }

    /**
     * Test Ajax form submission integration
     */
    public function test_ajax_submission_integration()
    {
        $form = new Form('ajax_form');
        $form->addText('name', 'Name');
        $form->addEmail('email', 'Email');
        $form->addCsrf('csrf_token', 'CSRF validation failed');

        // Simulate Ajax request
        $_POST = [
            'action' => 'submit_form',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'csrf_token' => wp_create_nonce('csrf_token')
        ];

        define('DOING_AJAX', true);

        $form->setValues($_POST);
        $this->assertTrue($form->isValid());

        // Test that form can be processed via Ajax
        $this->assertTrue(wp_doing_ajax());
    }

    /**
     * Test error handling integration
     */
    public function test_error_handling_integration()
    {
        $form = new Form('error_test');
        $form->addText('name', 'Name')
             ->setRequired('Name is required')
             ->addRule($form::MIN_LENGTH, 'Name too short', 3);
        $form->addEmail('email', 'Email')
             ->setRequired('Email is required');

        // Test with multiple errors
        $form->setValues([
            'name' => 'Jo', // Too short
            'email' => 'invalid-email' // Invalid format
        ]);

        $this->assertFalse($form->isValid());
        $this->assertTrue($form->hasErrors());

        $errors = $form->getErrors();
        $this->assertGreaterThanOrEqual(2, count($errors));

        // Check that errors contain expected messages
        $error_messages = array_map(function($error) {
            return $error->message;
        }, $errors);

        $this->assertContains('Name too short', $error_messages);
    }

    /**
     * Test performance with complex forms
     */
    public function test_complex_form_performance()
    {
        $start_time = microtime(true);

        $form = new Form('complex_form');
        
        // Add many different types of fields
        for ($i = 1; $i <= 20; $i++) {
            $form->addText("text_{$i}", "Text Field {$i}");
        }
        
        for ($i = 1; $i <= 10; $i++) {
            $form->addSelect("select_{$i}", "Select Field {$i}", [
                'opt1' => 'Option 1',
                'opt2' => 'Option 2',
                'opt3' => 'Option 3'
            ]);
        }
        
        $form->addTextArea('description', 'Description');
        $form->addCheckboxList('interests', 'Interests', [
            'tech' => 'Technology',
            'sports' => 'Sports',
            'music' => 'Music',
            'travel' => 'Travel'
        ]);

        // Render the form
        $rendered = (string)$form;
        
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        // Complex form should render in reasonable time (less than 0.5 seconds)
        $this->assertLessThan(0.5, $execution_time, "Complex form rendering took too long: {$execution_time} seconds");
        
        // Ensure all fields are rendered
        $this->assertGreaterThan(1000, strlen($rendered), "Rendered form seems too short");
        $this->assertStringContains('<form', $rendered);
        $this->assertStringContains('</form>', $rendered);
    }

    public function tearDown(): void
    {
        wp_delete_post($this->test_post_id, true);
        wp_delete_user($this->test_user_id);
        
        $_POST = [];
        $_FILES = [];
        
        if (defined('DOING_AJAX')) {
            undef('DOING_AJAX');
        }
        
        parent::tearDown();
    }
}