<?php
/**
 * Performance and Stress Tests
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Datastores\PostMetaDatastore;
use Wenprise\Forms\Datastores\UserMetaDatastore;
use Wenprise\Forms\Datastores\OptionsDatastore;

class PerformanceTests extends WP_UnitTestCase
{

    private $test_posts = [];
    private $test_users = [];

    public function setUp(): void
    {
        parent::setUp();
        
        // Create test data for performance tests
        for ($i = 0; $i < 10; $i++) {
            $this->test_posts[] = $this->factory->post->create([
                'post_title' => "Test Post {$i}",
                'post_status' => 'publish'
            ]);
            
            $this->test_users[] = $this->factory->user->create([
                'user_login' => "testuser{$i}",
                'user_email' => "test{$i}@example.com"
            ]);
        }
    }

    /**
     * Test form creation performance
     */
    public function test_form_creation_performance()
    {
        $iterations = 100;
        $start_time = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $form = new Form("test_form_{$i}");
            $form->addText('name', 'Name');
            $form->addEmail('email', 'Email');
            $form->addTextArea('message', 'Message');
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        $time_per_form = $execution_time / $iterations;

        // Each form creation should take less than 10ms
        $this->assertLessThan(0.01, $time_per_form, "Form creation too slow: {$time_per_form}s per form");
        
        // Total time for 100 forms should be under 1 second
        $this->assertLessThan(1.0, $execution_time, "Total form creation time too slow: {$execution_time}s");
    }

    /**
     * Test form validation performance with large datasets
     */
    public function test_form_validation_performance()
    {
        $form = new Form('validation_test');
        
        // Add many fields with validation rules
        for ($i = 1; $i <= 100; $i++) {
            $form->addText("field_{$i}", "Field {$i}")
                 ->setRequired("Field {$i} is required")
                 ->addRule($form::MIN_LENGTH, "Field {$i} too short", 2)
                 ->addRule($form::MAX_LENGTH, "Field {$i} too long", 100);
        }

        // Prepare valid data
        $data = [];
        for ($i = 1; $i <= 100; $i++) {
            $data["field_{$i}"] = "Valid value for field {$i}";
        }

        $start_time = microtime(true);
        
        $form->setValues($data);
        $is_valid = $form->isValid();
        
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        $this->assertTrue($is_valid);
        $this->assertLessThan(0.5, $execution_time, "Form validation too slow: {$execution_time}s");
    }

    /**
     * Test form rendering performance
     */
    public function test_form_rendering_performance()
    {
        $form = new Form('render_test');
        
        // Add various field types
        for ($i = 1; $i <= 50; $i++) {
            $form->addText("text_{$i}", "Text Field {$i}");
        }
        
        for ($i = 1; $i <= 20; $i++) {
            $form->addSelect("select_{$i}", "Select Field {$i}", [
                'opt1' => 'Option 1',
                'opt2' => 'Option 2',
                'opt3' => 'Option 3',
                'opt4' => 'Option 4',
                'opt5' => 'Option 5'
            ]);
        }
        
        for ($i = 1; $i <= 10; $i++) {
            $form->addCheckboxList("checkbox_{$i}", "Checkbox List {$i}", [
                'cb1' => 'Checkbox 1',
                'cb2' => 'Checkbox 2',
                'cb3' => 'Checkbox 3'
            ]);
        }

        $start_time = microtime(true);
        
        $rendered = (string)$form;
        
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        $this->assertGreaterThan(1000, strlen($rendered));
        $this->assertLessThan(0.3, $execution_time, "Form rendering too slow: {$execution_time}s");
    }

    /**
     * Test datastore performance with multiple saves
     */
    public function test_datastore_performance()
    {
        $form = new Form('datastore_test');
        $form->addText('name', 'Name');
        $form->addEmail('email', 'Email');
        $form->addTextArea('content', 'Content');

        $iterations = 50;
        $start_time = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $post_id = $this->test_posts[$i % count($this->test_posts)];
            $datastore = new PostMetaDatastore($post_id, $form);
            $form->setDatastore($datastore);

            $form->setValues([
                'name' => "Test Name {$i}",
                'email' => "test{$i}@example.com",
                'content' => "Test content for iteration {$i}"
            ]);

            if ($form->isValid()) {
                $form->save();
            }
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        $time_per_save = $execution_time / $iterations;

        $this->assertLessThan(0.1, $time_per_save, "Datastore save too slow: {$time_per_save}s per save");
        $this->assertLessThan(5.0, $execution_time, "Total datastore operations too slow: {$execution_time}s");
    }

    /**
     * Test memory usage during form operations
     */
    public function test_memory_usage()
    {
        $initial_memory = memory_get_usage();

        $forms = [];
        for ($i = 0; $i < 50; $i++) {
            $form = new Form("memory_test_{$i}");
            
            for ($j = 1; $j <= 20; $j++) {
                $form->addText("field_{$j}", "Field {$j}");
            }
            
            $forms[] = $form;
        }

        $peak_memory = memory_get_peak_usage();
        $memory_used = $peak_memory - $initial_memory;

        // Memory usage should be reasonable (less than 10MB for 50 forms with 20 fields each)
        $this->assertLessThan(10 * 1024 * 1024, $memory_used, "Memory usage too high: " . ($memory_used / 1024 / 1024) . "MB");

        // Clean up
        unset($forms);
        
        $final_memory = memory_get_usage();
        $memory_retained = $final_memory - $initial_memory;

        // Most memory should be freed (less than 2MB retained)
        $this->assertLessThan(2 * 1024 * 1024, $memory_retained, "Too much memory retained: " . ($memory_retained / 1024 / 1024) . "MB");
    }

    /**
     * Test concurrent form processing simulation
     */
    public function test_concurrent_processing_simulation()
    {
        $concurrent_forms = 20;
        $forms = [];
        $start_time = microtime(true);

        // Simulate multiple forms being processed simultaneously
        for ($i = 0; $i < $concurrent_forms; $i++) {
            $form = new Form("concurrent_{$i}");
            $form->addText('name', 'Name')->setRequired();
            $form->addEmail('email', 'Email')->setRequired();
            
            $post_id = $this->test_posts[$i % count($this->test_posts)];
            $datastore = new PostMetaDatastore($post_id, $form);
            $form->setDatastore($datastore);

            $form->setValues([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com"
            ]);

            $this->assertTrue($form->isValid());
            $form->save();
            
            $forms[] = $form;
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        $time_per_form = $execution_time / $concurrent_forms;

        $this->assertLessThan(0.05, $time_per_form, "Concurrent processing too slow: {$time_per_form}s per form");
        $this->assertLessThan(1.0, $execution_time, "Total concurrent processing too slow: {$execution_time}s");

        // Verify all data was saved correctly
        for ($i = 0; $i < $concurrent_forms; $i++) {
            $post_id = $this->test_posts[$i % count($this->test_posts)];
            $saved_name = get_post_meta($post_id, 'name', true);
            $saved_email = get_post_meta($post_id, 'email', true);
            
            $this->assertEquals("User {$i}", $saved_name);
            $this->assertEquals("user{$i}@example.com", $saved_email);
        }
    }

    /**
     * Test large form data handling
     */
    public function test_large_data_handling()
    {
        $form = new Form('large_data_test');
        $form->addTextArea('large_content', 'Large Content');
        $form->addText('name', 'Name');

        $large_content = str_repeat('This is a test of large content handling. ', 1000); // ~40KB
        
        $start_time = microtime(true);

        $form->setValues([
            'large_content' => $large_content,
            'name' => 'Test User'
        ]);

        $this->assertTrue($form->isValid());

        $datastore = new PostMetaDatastore($this->test_posts[0], $form);
        $form->setDatastore($datastore);
        $form->save();

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        $this->assertLessThan(0.5, $execution_time, "Large data handling too slow: {$execution_time}s");

        // Verify large content was saved correctly
        $saved_content = get_post_meta($this->test_posts[0], 'large_content', true);
        $this->assertEquals($large_content, $saved_content);
    }

    /**
     * Test form validation with many rules
     */
    public function test_complex_validation_performance()
    {
        $form = new Form('complex_validation');
        
        $input = $form->addText('complex_field', 'Complex Field')
                      ->setRequired('Field is required')
                      ->addRule($form::MIN_LENGTH, 'Too short', 5)
                      ->addRule($form::MAX_LENGTH, 'Too long', 50)
                      ->addRule($form::PATTERN, 'Invalid format', '[A-Za-z0-9]+')
                      ->addRule(function($control) {
                          return strlen($control->getValue()) % 2 === 0;
                      }, 'Length must be even')
                      ->addRule(function($control) {
                          return !in_array(strtolower($control->getValue()), ['admin', 'root', 'test']);
                      }, 'Reserved word not allowed');

        $iterations = 100;
        $start_time = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $form->setValues(['complex_field' => "ValidValue{$i}"]);
            $is_valid = $form->isValid();
            $this->assertTrue($is_valid);
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        $time_per_validation = $execution_time / $iterations;

        $this->assertLessThan(0.01, $time_per_validation, "Complex validation too slow: {$time_per_validation}s per validation");
    }

    /**
     * Test database query optimization
     */
    public function test_database_query_performance()
    {
        global $wpdb;
        
        $form = new Form('db_test');
        for ($i = 1; $i <= 20; $i++) {
            $form->addText("field_{$i}", "Field {$i}");
        }

        $datastore = new PostMetaDatastore($this->test_posts[0], $form);
        $form->setDatastore($datastore);

        $data = [];
        for ($i = 1; $i <= 20; $i++) {
            $data["field_{$i}"] = "Value {$i}";
        }

        $form->setValues($data);

        // Count queries before save
        $query_count_before = $wpdb->num_queries;
        
        $form->save();
        
        $query_count_after = $wpdb->num_queries;
        $queries_executed = $query_count_after - $query_count_before;

        // Should not execute excessive queries (reasonable threshold: 25 queries for 20 fields)
        $this->assertLessThan(25, $queries_executed, "Too many database queries: {$queries_executed}");
    }

    /**
     * Test stress test with rapid form submissions
     */
    public function test_rapid_submissions_stress()
    {
        $form = new Form('stress_test');
        $form->addText('name', 'Name');
        $form->addEmail('email', 'Email');

        $submissions = 100;
        $start_time = microtime(true);
        $successful_submissions = 0;

        for ($i = 0; $i < $submissions; $i++) {
            $post_id = $this->test_posts[$i % count($this->test_posts)];
            $datastore = new PostMetaDatastore($post_id, $form);
            $form->setDatastore($datastore);

            $form->setValues([
                'name' => "Stress Test {$i}",
                'email' => "stress{$i}@example.com"
            ]);

            if ($form->isValid()) {
                $form->save();
                $successful_submissions++;
            }
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        $this->assertEquals($submissions, $successful_submissions, "Not all submissions were successful");
        $this->assertLessThan(5.0, $execution_time, "Stress test took too long: {$execution_time}s");

        $submissions_per_second = $submissions / $execution_time;
        $this->assertGreaterThan(20, $submissions_per_second, "Throughput too low: {$submissions_per_second} submissions/second");
    }

    public function tearDown(): void
    {
        // Clean up test data
        foreach ($this->test_posts as $post_id) {
            wp_delete_post($post_id, true);
        }
        
        foreach ($this->test_users as $user_id) {
            wp_delete_user($user_id);
        }
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        parent::tearDown();
    }
}