# wenprise-forms

Extend Nette Forms for WordPress use.

Basic usage: [Nette Form](https://doc.nette.org/en/forms/standalone)

## Features

- Supports both Composer library mode and WordPress plugin mode
- Extends Nette Forms with WordPress-ready fields and UI widgets
- Includes form validation, conditional display, datastore integration, and AJAX-friendly inputs

## Requirements

- PHP `>=8.3`
- `ext-json`
- `nette/forms ^3.2`
- `nette/utils ^4.1`
- WordPress `>=6.0` (recommended)

## Installation

### Option A: Install as a Composer library

```bash
composer require wenprise/forms
```

### Option B: Install as a WordPress plugin

1. Clone or download this repository into `wp-content/plugins/wenprise-forms`.
2. Run `composer install` inside the plugin directory.
3. Activate `Wenprise Forms` in WordPress admin.

## Runtime Modes

### Composer library mode

- Your project is responsible for loading Composer autoload.
- Your code decides when and where to bootstrap the form objects.

### WordPress plugin mode

- Plugin entry file `wenprise-forms.php` loads `vendor/autoload.php` automatically.
- Plugin bootstraps with `new \Wenprise\Forms\Init();`.

## Quick Start (Minimal Working Example)

Forms has its own HTTP request handling flow. Instantiate it before `head_sent`, usually on `init`.

```php
<?php
use Wenprise\Forms\Form;
use Wenprise\Forms\Helpers;
use Wenprise\Forms\Renders\DefaultFormRender;

add_action('init', function () {
    global $demo_form;

    $demo_form = Helpers::get_form();
    $demo_form->setRenderer(new DefaultFormRender('horizontal'));
    $demo_form->setMethod('POST');
    $demo_form->setAction(home_url('/'));
    $demo_form->addText('first_name', 'First Name')->setRequired();
    $demo_form->addSubmit('send', 'Save');
});

add_shortcode('wenprise_form_demo', function () {
    global $demo_form;

    if ($demo_form->isSuccess()) {
        $values = $demo_form->getValues();
        $first_name = $values->first_name;
        // Save or process your value here.
    }

    return (string) $demo_form;
});
```

## Usage

### Quick start
Forms includes its own HTTP request handling flow, so instantiate it before `head_sent`, usually in the `init` action.

```php
add_action('init', function ()
{
    global $form;

    // Get forms object
    $form = Helpers::get_form();
});

```
Create a Form instance

````php
use Wenprise\Forms\Form;

$form = new Form;

$form->setRenderer(new \Wenprise\Forms\Renders\DefaultFormRender('horizontal'));

// Set form method
$form->setMethod( 'POST' );
$form->setAction('https://www.example.com');

// Set form field
$form->addText('first_name', 'First Name');

// Set submit button
$form->addSubmit( 'send', 'Save' );

// Validate form and get form data
if ( $form->isSuccess() ) {

	$values = $form->getValues();
	
	$first_name = $values->first_name;
			
}	
````

## Frontend Assets

Some fields depend on JavaScript/CSS widgets (for example: `Slider`, `Chosen`, `Date Picker`, `Signature`, `Star Rating`, `Autocomplete`, `Chained Select`).

- Ensure assets are properly enqueued in your theme/plugin runtime.
- Prefer testing one advanced field at a time when integrating into existing themes.

## Security Notes

- Always include CSRF protection for public forms (`$form->addCsrf(...)`).
- For AJAX endpoints, validate nonce and capability before processing input.
- Sanitize incoming data and escape output in templates.
- Do not trust client-side validation alone; keep validation rules on the server side.

### Set Required

```php
$form->addText('first_name', 'First Name')
     ->setRequired();
```

### Add rule

```php
$form->addPassword('re_password', 'Password again:')
     ->addRule($form::EQUAL, 'Password mismatch', $form['password']);
```

[Rule Documentation](https://doc.nette.org/en/forms/validation)


### Add field description

```php
$form->addText('first_name', 'First Name')
     ->setOption('description', 'This is your first name.');
```

With Html

```php
$form->addTextArea('description', Html::el('p')
	->setHtml('This number remains hidden. <a href="...">Terms of service.</a>')
	);
```

### Set condition display

```php
$form->addColorPicker('first_name3', 'First Name')
     ->setHtmlAttribute('data-cond', '[name=first_name2] == 2');
```


### Multiple Submit Button

```php
$form->addSubmit('save', 'Save');
$form->addSubmit('delete', 'Delete');

if ($form->isSuccess()) {
	if ($form['save']->isSubmittedBy()) {
		....
	}

	if ($form['delete']->isSubmittedBy()) {
		....
	}
}
```

### Use html in label or description

```php
$confirm = Html::el('span')->setHtml('I agree the <a href="#">Terms of service.</a>');

$form->addCheckbox('confirm', $confirm)->setOption('description', $confirm);
```

### Set Datastore

```php
$form->setDatastore(new \Wenprise\Forms\Datastores\PostMetaDatastore(1));

$form->save();
```

### Custom Settings Page Example

Use `OptionsDatastore` to build a WordPress admin settings page quickly.

```php
<?php
use Wenprise\Forms\Form;
use Wenprise\Forms\Datastores\OptionsDatastore;
use Wenprise\Forms\Renders\AdminFormRender;

add_action('admin_menu', function () {
    add_menu_page(
        'Wenprise Form Settings',
        'Form Settings',
        'manage_options',
        'wenprise-form-settings',
        'render_wenprise_form_settings_page'
    );
});

function render_wenprise_form_settings_page() {
    $form = new Form('wenprise_form_settings');
    $form->setRenderer(new AdminFormRender('vertical'));
    $form->setMethod('POST');

    $form->addText('wprs_company_name', 'Company Name')
        ->setDefaultValue(get_option('wprs_company_name', ''));

    $form->addText('wprs_support_email', 'Support Email')
        ->setRequired()
        ->addRule($form::EMAIL, 'Please enter a valid email address.')
        ->setDefaultValue(get_option('wprs_support_email', ''));

    $form->addCheckbox('wprs_enable_notifications', 'Enable Email Notifications')
        ->setDefaultValue((bool) get_option('wprs_enable_notifications', false));

    $form->addSubmit('save', 'Save Settings');

    $form->setDatastore(new OptionsDatastore());
    $form->save();

    echo '<div class="wrap"><h1>Wenprise Form Settings</h1>';
    $form->render();
    echo '</div>';
}
```

### Tab Groups

Use tab groups to split long forms into multiple panels.

```php
$form = new \Wenprise\Forms\Form();

$form->addTab('basic', 'Basic', true);
$form->addText('first_name', 'First Name');
$form->addText('email', 'Email');

$form->addTab('advanced', 'Advanced');
$form->addTextArea('notes', 'Notes');
$form->endTab();

// This submit button is outside tab groups.
$form->addSubmit('send', 'Save');

// Template context:
echo $form;
```

Tabs are rendered automatically through the existing render flow (`$form->render()` / `echo $form`), so no extra render method is required.

Submit buttons created inside tab groups are rendered outside tab panes automatically, so the save action stays visible.

You can also use underscore naming method:

```php
$form->add_tab('basic', 'Basic', true);
echo $form;
```

If you render inside a shortcode callback, return the form HTML instead of echoing:

```php
add_shortcode('wenprise_form_demo', function () use ($form) {
    return (string) $form;
});
```

## Fields

### nonce field

````php
$form->addCsrf('postform', 'Nonce invalid');
````

### WordPress Tinymce editor

[Settings](https://codex.wordpress.org/Function_Reference/wp_editor)

````php
$form->addEditor('post_extra', 'Extra content', []);
````

### Ajax uploader

```php
$form->addAjaxUpload('photos', 'Photos', true, )
             ->setUrl(  admin_url( 'admin-ajax.php?action=upload' ) );
```

#### Uploader backend sample.

````php
add_action('wp_ajax_upload', 'ajax_uploader');
add_action('wp_ajax_nopriv_upload', 'ajax_uploader');

function ajax_uploader()
{

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $attachment_id = media_handle_upload('file', 0);

    $thumb_url  = wp_get_attachment_thumb_url($attachment_id);
    $origin_url = wp_get_attachment_url($attachment_id);

    $thumb = get_post($attachment_id);

    $file_data = [
        'id'       => $attachment_id,
        'original' => $thumb->post_title,
        'size'     => $thumb->size,
        'state'    => 'SUCCESS',
        'title'    => $thumb->post_title,
        'type'     => $thumb->post_mime_type,
        'thumb'    => $thumb_url,
        'url'      => $origin_url,
    ];

    wp_send_json($file_data, 200);

    return false;
}
````

### Slider input

[Setting](http://ionden.com/a/plugins/ion.rangeSlider/en.html) 

````php
$form->addSlider('price', 'Price', []);
````

### Data Picker

[Setting](https://jqueryui.com/datepicker/)

````php
$form->AddBirthdaypicker('_birthday', 'Date of Birth', [
    'format' => 'YYYY-MM-DD', 
    'template' => 'YYYY-MM-DD',
    'minYear' => '1900',
    'maxYear' => date("Y")
]);
````

### Color Picker

[Setting](http://automattic.github.io/Iris/)

````php
$form->addColorPicker('color', 'Color', []);
````

### Chosen

````php
$choices = [
    'php'         => 'PHP',
    'javascript'  => 'JavaScript',
    'css'         => 'CSS',
    'java'        => 'Java',
];

$form->addChosen('category', 'Category', $choices);
````

#### Multi Chosen

````php
$form->addMultiChosen('post_tags', 'Tags', $choices);
````

### Signature Field

````php
$options = [
   'width'      => '500',
   'height'     => '250',
   'border'     => '#999',
   'background' => '#f3f3f3',
];

$form->addSignature('first_name9', 'First Name', $options);
````

### Star rating input

````php
$options = [
    'displayOnly' => false,
    'showClear'   => false,
    'theme'       => 'krajee-svg',
    'step'        => 1,
    'min'         => 1,
    'max'         => 5,
];

$form->addStarRating('rating', 'Rating', $options);
````

### Image Select

````php
$options = [
    'light' => 'https://via.placeholder.com/64/EEEEEE/000000/?text=Light',
    'dark'  => 'https://via.placeholder.com/64/000000/FFFFFF/?text=Dark',
];

$form->addImagePicker('theme', 'Theme', $options);
````

### Autocomplete Input

Source is an array or a url returns an array.

```php
$form->addAutocomplete('name', 'Name')->setSource([
    [
        'value' => 'aaa',
        'data'  => 'AAA',
    ],
    [
        'value' => 'bbb',
        'data'  => 'BBB',
    ],
    [
        'value' => 'ccc',
        'data'  => 'CCC',
    ],
]);
```

If Source is an ajax url, the backend need return data as bellow.

```php
suggestions: [
  { "value": "United Arab Emirates", "data": "AE" },
  { "value": "United Kingdom",       "data": "UK" },
  { "value": "United States",        "data": "US" }
]
```

### Table input

````php
$fields = [
    [
        'name'    => 'name',
        'display' => 'Product Name',
        'type'    => 'text',
    ],
    [
        'name'    => 'quantity',
        'display' => 'Quantity',
        'type'    => 'text',
    ],
    [
        'name'    => 'price',
        'display' => 'Unit Price',
        'type'    => 'text',
    ],
];

$values = [
    [
        'name'     => 'Macbook Pro',
        'quantity' => '1',
        'price'    => '8500',
    ],
    [
        'name'     => 'Pixel XL',
        'quantity' => '2',
        'price'    => '8500',
    ],
];


$form->addTableInput('table', 'Table', [], $fields)
     ->setDefaultValue($values);
````

### Clone Input

Allow input multi text value.

````php
$form->addCloneInput('photo1', 'Photo');
````

### Group Input

Set a prefix or suffix for text input.

````php
$form->addGroupInput('day1', 'Day')
     ->setPrefix('Email')
     ->setSuffix('gmail.com');
````

### SMS input

Send SMS code

```php
$form->addSmsInput('phone', 'Cellphone', )
             ->setUrl(admin_url('admin-ajax.php?action=validate_cellpone'));
```

Backend Example

```php

/**
 * Send SMS verification code.
 */
add_action('wp_ajax_validate_cellpone', 'validate_cellpone');
add_action('wp_ajax_nopriv_validate_cellpone', 'validate_cellpone');
function validate_cellpone()
{
	$phone = Input::get( 'phone', null );

	if ( ! $phone && is_user_logged_in() ) {
		$phone = OpenAuth::get_open_id( 'phone', get_current_user_id() );
	}

	$random = mt_rand( 100000, 999999 );

	// Save the verification code before sending SMS.
	$code       = PhoneCode::query()->firstOrCreate( [ 'phone' => $phone ] );
	$code->code = $random;
	$code->save();

	$msg = Helper::send_sms( $phone, $code->code );

	wp_send_json( $msg, '200' );
};

/**
 * SMS backend example using Yunpian API.
 */
function send_sms( $mobile, $content )
{
    $config = [
        'apikey' => 'xxxxx',
        'tpl_id' => '123456',
    ];

    // Template API endpoint
    $url = "https://sms.yunpian.com/v2/sms/tpl_single_send.json";

    $args = [
        'body' => [
            'apikey'    => $config[ 'apikey' ],
            'mobile'    => $mobile,

            // Template SMS payload
            'tpl_id'    => $config[ 'tpl_id' ],
            'tpl_value' => "#code#=$content",
        ],
    ];

    $result = json_decode( wp_remote_retrieve_body( wp_remote_post( $url, $args ) ) );

    // Return message based on gateway response
    return [
        'code' => $result->code,
        'msg'  => $result->msg,
    ];
}
```

### Captcha Input

````php
 $form->AddCaptcha('captcha', 'Captcha')
             ->setUrl(admin_url('admin-ajax.php?action=get_captcha'));
````
### Chained Select

````php
$form->addChainedSelect('chained', 'Chained Select', [
        'url'        => get_theme_file_uri('cityData.min.json'),
        'selects'    => ['province', 'city', 'area'],
        'emptyStyle' => 'none',
    ], ['province', 'city', 'area'])->setDefaultValue([001, 002, 003]);
````

### Captcha backend sample.

````php
/**
 * Captcha backend sample.
 * WordPress core do not use sessions, use cookie and transient instead 
 *
 * Run composer require gregwar/captcha to install requirement
 */
add_action('wp_ajax_get_captcha', 'get_captcha');
add_action('wp_ajax_nopriv_get_captcha', 'get_captcha');
function get_captcha($type)
{
    header('Content-type: image/jpeg');

    $builder    = new CaptchaBuilder();
    $captcha_id = wp_generate_uuid4();
    $expires = MINUTE_IN_SECONDS * 5;

    setcookie('wprs-security-captcha-id', $captcha_id, time() + $expires);
    set_transient($captcha_id, $builder->getPhrase(), $expires);

    $builder->build()
            ->output();
}
````

````php
// Validate captcha
$captcha_id      = $_COOKIE[ 'wprs-security-captcha-id' ];
$session_captcha = get_transient($captcha_id);
````
