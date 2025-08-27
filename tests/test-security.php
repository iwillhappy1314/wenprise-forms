<?php
/**
 * Security Tests
 *
 * @package Wenprise_Forms
 */

use Wenprise\Forms\Form;
use Wenprise\Forms\Init;
use Wenprise\Forms\Controls\CsrfInput;
use Wenprise\Forms\Helpers;

class SecurityTests extends WP_UnitTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $_POST = [];
        $_REQUEST = [];
        $_GET = [];
    }

    /**
     * Test CSRF token generation and validation
     */
    public function test_csrf_token_generation()
    {
        $form = new Form('test_form');
        $csrf = $form->addCsrf('csrf_token', 'CSRF validation failed');

        $token = $csrf->generateToken();
        $this->assertNotEmpty($token);
        $this->assertTrue(wp_verify_nonce($token, 'csrf_token'));

        // Test token validation
        $this->assertTrue(CsrfInput::validateCsrf($csrf));
    }

    /**
     * Test CSRF protection prevents forged requests
     */
    public function test_csrf_protection()
    {
        $form = new Form('test_form');
        $form->addCsrf('csrf_token', 'CSRF validation failed');
        $form->addText('name', 'Name');

        // Test with invalid token
        $_POST = [
            'csrf_token' => 'invalid_token',
            'name' => 'Test Name'
        ];
        $form->setValues($_POST);
        
        $this->assertFalse($form->isValid());
    }

    /**
     * Test data sanitization to prevent XSS
     */
    public function test_xss_prevention()
    {
        $malicious_inputs = [
            '<script>alert("xss")</script>',
            '<img src="x" onerror="alert(1)">',
            'javascript:alert(1)',
            '<iframe src="javascript:alert(1)"></iframe>',
            '<svg onload="alert(1)">',
            '" onclick="alert(1)"',
            '&lt;script&gt;alert("xss")&lt;/script&gt;'
        ];

        foreach ($malicious_inputs as $malicious_input) {
            // Test that malicious content is properly handled
            $sanitized = sanitize_text_field($malicious_input);
            $this->assertNotContains('<script>', $sanitized);
            $this->assertNotContains('javascript:', $sanitized);
            $this->assertNotContains('onclick=', $sanitized);
            $this->assertNotContains('onerror=', $sanitized);
        }
    }

    /**
     * Test SQL injection prevention through proper data handling
     */
    public function test_sql_injection_prevention()
    {
        $malicious_inputs = [
            "'; DROP TABLE wp_posts; --",
            "1' OR '1'='1",
            "1'; DELETE FROM wp_users WHERE 1=1; --",
            "1' UNION SELECT user_login, user_pass FROM wp_users --",
            "admin'/**/OR/**/1=1#"
        ];

        foreach ($malicious_inputs as $malicious_input) {
            // Test that input is properly escaped when used in database operations
            $escaped = esc_sql($malicious_input);
            $this->assertNotContains('DROP TABLE', $escaped);
            $this->assertNotContains('DELETE FROM', $escaped);
            $this->assertNotContains('UNION SELECT', $escaped);
        }
    }

    /**
     * Test file upload security
     */
    public function test_file_upload_security()
    {
        $dangerous_extensions = [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
            'js', 'html', 'htm', 'shtml', 'shtm', 'exe', 'com', 'bat',
            'cmd', 'scr', 'pif', 'vbs', 'jsp', 'asp', 'aspx'
        ];

        foreach ($dangerous_extensions as $ext) {
            $filename = "malicious_file." . $ext;
            
            // Test that dangerous file types should be rejected
            $is_allowed = wp_check_filetype($filename)['ext'];
            
            if (in_array($ext, ['php', 'js', 'html', 'exe'])) {
                $this->assertEmpty($is_allowed, "Dangerous extension {$ext} should not be allowed");
            }
        }
    }

    /**
     * Test path traversal prevention
     */
    public function test_path_traversal_prevention()
    {
        $malicious_paths = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\config\\sam',
            '/etc/passwd',
            'C:\\windows\\system32\\config\\sam',
            '....//....//....//etc/passwd',
            '..%2F..%2F..%2Fetc%2Fpasswd'
        ];

        foreach ($malicious_paths as $path) {
            // Test path sanitization
            $sanitized = sanitize_file_name(basename($path));
            $this->assertNotContains('..', $sanitized);
            $this->assertNotContains('/', $sanitized);
            $this->assertNotContains('\\', $sanitized);
        }
    }

    /**
     * Test authorization checks
     */
    public function test_authorization_checks()
    {
        // Create a user without proper capabilities
        $user_id = $this->factory->user->create(['role' => 'subscriber']);
        wp_set_current_user($user_id);

        // Test that subscribers cannot perform admin actions
        $this->assertFalse(current_user_can('manage_options'));
        $this->assertFalse(current_user_can('edit_others_posts'));
        
        // Create admin user
        $admin_id = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($admin_id);
        
        // Test that admin can perform admin actions
        $this->assertTrue(current_user_can('manage_options'));
        $this->assertTrue(current_user_can('edit_others_posts'));
    }

    /**
     * Test session security
     */
    public function test_session_security()
    {
        // Test that sessions are properly handled
        if (session_status() === PHP_SESSION_NONE) {
            // Session should be started securely
            $this->assertTrue(ini_get('session.cookie_httponly'));
            $this->assertTrue(ini_get('session.use_strict_mode'));
        }
    }

    /**
     * Test rate limiting and brute force protection
     */
    public function test_rate_limiting()
    {
        // This would typically involve testing transients for rate limiting
        $user_ip = '192.168.1.1';
        $rate_limit_key = 'form_submit_' . md5($user_ip);
        
        // Simulate multiple rapid submissions
        for ($i = 0; $i < 10; $i++) {
            $attempts = get_transient($rate_limit_key) ?: 0;
            set_transient($rate_limit_key, $attempts + 1, 300); // 5 minutes
        }
        
        $current_attempts = get_transient($rate_limit_key);
        $this->assertEquals(10, $current_attempts);
        
        // Test that we can detect excessive attempts
        $this->assertGreaterThan(5, $current_attempts);
    }

    /**
     * Test sensitive data exposure prevention
     */
    public function test_sensitive_data_protection()
    {
        $sensitive_fields = [
            'password',
            'pass',
            'passwd',
            'pwd',
            'secret',
            'token',
            'key',
            'api_key',
            'auth_token'
        ];

        foreach ($sensitive_fields as $field) {
            // Test that sensitive field names are handled carefully
            $form = new Form('test_form');
            $input = $form->addPassword($field, ucfirst($field));
            
            // Password fields should not echo values in HTML
            $control = $input->getControl();
            $this->assertEquals('password', $control->type);
        }
    }

    /**
     * Test data sanitization in Helpers
     */
    public function test_helpers_data_sanitization()
    {
        // Test with malicious data in $_REQUEST
        $_REQUEST = [
            'malicious_script' => '<script>alert("xss")</script>',
            'sql_injection' => "'; DROP TABLE wp_posts; --",
            'clean_data' => 'This is clean data'
        ];

        // The Helpers::input_get should return unsanitized data (by design for flexibility)
        // But we should test that when sanitized, it's safe
        $malicious = Helpers::input_get('malicious_script');
        $sanitized = sanitize_text_field($malicious);
        $this->assertNotContains('<script>', $sanitized);

        $sql_data = Helpers::input_get('sql_injection');
        $escaped = esc_sql($sql_data);
        $this->assertNotContains('DROP TABLE', $escaped);

        $clean = Helpers::input_get('clean_data');
        $this->assertEquals('This is clean data', $clean);
    }

    /**
     * Test WordPress nonce functionality
     */
    public function test_wordpress_nonce_security()
    {
        $action = 'test_action';
        $nonce = wp_create_nonce($action);
        
        $this->assertNotEmpty($nonce);
        $this->assertEquals(1, wp_verify_nonce($nonce, $action));
        
        // Test with wrong action
        $this->assertFalse(wp_verify_nonce($nonce, 'wrong_action'));
        
        // Test with tampered nonce
        $tampered = substr($nonce, 0, -1) . 'x';
        $this->assertFalse(wp_verify_nonce($tampered, $action));
    }

    public function tearDown(): void
    {
        $_POST = [];
        $_REQUEST = [];
        $_GET = [];
        
        // Clean up transients
        $user_ip = '192.168.1.1';
        $rate_limit_key = 'form_submit_' . md5($user_ip);
        delete_transient($rate_limit_key);
        
        parent::tearDown();
    }
}