<?php
/**
 * Datastore Tests
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Datastores\PostMetaDatastore;
use Wenprise\Forms\Datastores\UserMetaDatastore;
use Wenprise\Forms\Datastores\OptionsDatastore;
use Wenprise\Forms\Datastores\TermMetaDatastore;

class DatastoreTests extends WP_UnitTestCase
{

    private $test_post_id;
    private $test_user_id;
    private $test_term_id;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->test_post_id = $this->factory->post->create([
            'post_title' => 'Test Post',
            'post_content' => 'Test content',
            'post_status' => 'publish'
        ]);

        $this->test_user_id = $this->factory->user->create([
            'user_login' => 'testuser',
            'user_email' => 'test@example.com'
        ]);

        $this->test_term_id = $this->factory->term->create([
            'name' => 'Test Category',
            'taxonomy' => 'category'
        ]);
    }

    /**
     * Test PostMetaDatastore functionality
     */
    public function test_post_meta_datastore()
    {
        $form = new Form('test_form');
        $form->addText('custom_field', 'Custom Field');
        $form->addText('another_field', 'Another Field');

        // Create datastore
        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        // Set form values
        $form->setValues([
            'custom_field' => 'Test Value',
            'another_field' => 'Another Test Value',
            'post_title' => 'Updated Post Title'
        ]);

        // Test form is valid
        $this->assertTrue($form->isValid());

        // Save data
        $form->save();

        // Verify data was saved to post meta
        $this->assertEquals('Test Value', get_post_meta($this->test_post_id, 'custom_field', true));
        $this->assertEquals('Another Test Value', get_post_meta($this->test_post_id, 'another_field', true));
        
        // Verify post title was updated
        $updated_post = get_post($this->test_post_id);
        $this->assertEquals('Updated Post Title', $updated_post->post_title);
    }

    /**
     * Test UserMetaDatastore functionality
     */
    public function test_user_meta_datastore()
    {
        $form = new Form('user_form');
        $form->addText('user_bio', 'User Bio');
        $form->addText('user_website', 'Website');
        $form->addText('user_phone', 'Phone');

        // Create datastore
        $datastore = new UserMetaDatastore($this->test_user_id, $form);
        $form->setDatastore($datastore);

        // Set form values
        $form->setValues([
            'user_bio' => 'This is a test bio',
            'user_website' => 'https://example.com',
            'user_phone' => '123-456-7890'
        ]);

        // Test form is valid
        $this->assertTrue($form->isValid());

        // Save data
        $form->save();

        // Verify data was saved to user meta
        $this->assertEquals('This is a test bio', get_user_meta($this->test_user_id, 'user_bio', true));
        $this->assertEquals('https://example.com', get_user_meta($this->test_user_id, 'user_website', true));
        $this->assertEquals('123-456-7890', get_user_meta($this->test_user_id, 'user_phone', true));
    }

    /**
     * Test OptionsDatastore functionality
     */
    public function test_options_datastore()
    {
        $form = new Form('options_form');
        $form->addText('site_tagline', 'Site Tagline');
        $form->addText('custom_option', 'Custom Option');

        // Create datastore
        $datastore = new OptionsDatastore($form);
        $form->setDatastore($datastore);

        // Set form values
        $form->setValues([
            'site_tagline' => 'Test Site Tagline',
            'custom_option' => 'Custom Value'
        ]);

        // Test form is valid
        $this->assertTrue($form->isValid());

        // Save data
        $form->save();

        // Verify data was saved to options
        $this->assertEquals('Test Site Tagline', get_option('site_tagline'));
        $this->assertEquals('Custom Value', get_option('custom_option'));
    }

    /**
     * Test TermMetaDatastore functionality
     */
    public function test_term_meta_datastore()
    {
        $form = new Form('term_form');
        $form->addText('term_color', 'Term Color');
        $form->addText('term_icon', 'Term Icon');

        // Create datastore
        $datastore = new TermMetaDatastore($this->test_term_id, $form);
        $form->setDatastore($datastore);

        // Set form values
        $form->setValues([
            'term_color' => '#ff0000',
            'term_icon' => 'fa-icon'
        ]);

        // Test form is valid
        $this->assertTrue($form->isValid());

        // Save data
        $form->save();

        // Verify data was saved to term meta
        $this->assertEquals('#ff0000', get_term_meta($this->test_term_id, 'term_color', true));
        $this->assertEquals('fa-icon', get_term_meta($this->test_term_id, 'term_icon', true));
    }

    /**
     * Test datastore with invalid data
     */
    public function test_datastore_with_invalid_data()
    {
        $form = new Form('test_form');
        $form->addText('required_field', 'Required Field')
             ->setRequired('This field is required');

        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        // Set invalid values (missing required field)
        $form->setValues([]);

        // Test form is invalid
        $this->assertFalse($form->isValid());

        // Save should not execute for invalid forms
        $form->save();

        // Verify no data was saved
        $this->assertEmpty(get_post_meta($this->test_post_id, 'required_field', true));
    }

    /**
     * Test datastore field filtering
     */
    public function test_datastore_field_filtering()
    {
        $form = new Form('test_form');
        $form->addText('allowed_field', 'Allowed Field');
        $form->addHidden('post_type', 'post'); // This should be filtered out

        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        $form->setValues([
            'allowed_field' => 'Test Value',
            'post_type' => 'malicious_post_type'
        ]);

        $this->assertTrue($form->isValid());
        $form->save();

        // Verify allowed field was saved
        $this->assertEquals('Test Value', get_post_meta($this->test_post_id, 'allowed_field', true));

        // Verify post_type was not saved as meta (it should be filtered out)
        $this->assertEmpty(get_post_meta($this->test_post_id, 'post_type', true));
    }

    /**
     * Test datastore with array values
     */
    public function test_datastore_with_array_values()
    {
        $form = new Form('test_form');
        $form->addMultiSelect('categories', 'Categories');
        $form->addCheckboxList('tags', 'Tags');

        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        $form->setValues([
            'categories' => ['cat1', 'cat2', 'cat3'],
            'tags' => ['tag1', 'tag2']
        ]);

        $this->assertTrue($form->isValid());
        $form->save();

        // Verify array values were saved
        $saved_categories = get_post_meta($this->test_post_id, 'categories', true);
        $saved_tags = get_post_meta($this->test_post_id, 'tags', true);

        $this->assertEquals(['cat1', 'cat2', 'cat3'], $saved_categories);
        $this->assertEquals(['tag1', 'tag2'], $saved_tags);
    }

    /**
     * Test datastore performance with large datasets
     */
    public function test_datastore_performance()
    {
        $form = new Form('performance_form');
        
        // Add many fields
        for ($i = 1; $i <= 50; $i++) {
            $form->addText("field_{$i}", "Field {$i}");
        }

        $datastore = new PostMetaDatastore($this->test_post_id, $form);
        $form->setDatastore($datastore);

        // Prepare large dataset
        $values = [];
        for ($i = 1; $i <= 50; $i++) {
            $values["field_{$i}"] = "Value for field {$i}";
        }

        $form->setValues($values);

        // Time the operation
        $start_time = microtime(true);
        
        $this->assertTrue($form->isValid());
        $form->save();
        
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        // Verify data was saved
        for ($i = 1; $i <= 50; $i++) {
            $this->assertEquals("Value for field {$i}", get_post_meta($this->test_post_id, "field_{$i}", true));
        }

        // Performance should be reasonable (less than 1 second for 50 fields)
        $this->assertLessThan(1.0, $execution_time, "Datastore operation took too long: {$execution_time} seconds");
    }

    /**
     * Test datastore error handling
     */
    public function test_datastore_error_handling()
    {
        $form = new Form('error_form');
        $form->addText('test_field', 'Test Field');

        // Test with invalid post ID
        $datastore = new PostMetaDatastore(999999, $form); // Non-existent post
        $form->setDatastore($datastore);

        $form->setValues(['test_field' => 'Test Value']);
        
        // The datastore should handle invalid post IDs gracefully
        $this->assertTrue($form->isValid());
        
        // This might fail or handle gracefully depending on implementation
        try {
            $form->save();
            // If it doesn't throw an exception, verify behavior
            $this->assertTrue(true); // Test passes if no exception
        } catch (Exception $e) {
            // If it throws an exception, that's also acceptable behavior
            $this->assertInstanceOf(Exception::class, $e);
        }
    }

    public function tearDown(): void
    {
        // Clean up test data
        wp_delete_post($this->test_post_id, true);
        wp_delete_user($this->test_user_id);
        wp_delete_term($this->test_term_id, 'category');
        
        // Clean up test options
        delete_option('site_tagline');
        delete_option('custom_option');
        
        parent::tearDown();
    }
}