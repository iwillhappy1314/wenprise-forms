# wenprise-forms

Extend Nette Forms for WordPress use.

Basic usage: [Nette Form](https://doc.nette.org/en/2.4/forms)

## Usage

### Quick start
Forms 类带有自己的HTTP请求处理方法，需要在head_sent之前实例化，可以把实例化的过程放在 init action 中。

```php
add_action('init', function ()
{
    global $form;

    // Get forms object
    $form = Helpers::get_form();
});

```
创建Form实例

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

[Rule Documation](https://doc.nette.org/en/3.0/form-validation)


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
$form->setDatastore(new \Wenprise\Forms\Datastores\PostMetaDatastore(1, $form));

$form->save();
```

## Fields

### nonce field

````php
$form->addCsrf('postform', 'Nonce invaliadte');
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

### Signature Filed

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
        'display' => '商品名称',
        'type'    => 'text',
    ],
    [
        'name'    => 'quantity',
        'display' => '数量',
        'type'    => 'text',
    ],
    [
        'name'    => 'price',
        'display' => '单价',
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
 * 发送短信验证码
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

	// 发送验证码之前，保存验证码到数据库
	$code       = PhoneCode::query()->firstOrCreate( [ 'phone' => $phone ] );
	$code->code = $random;
	$code->save();

	$msg = Helper::send_sms( $phone, $code->code );

	wp_send_json( $msg, '200' );
};

/**
 * 发送短信后端示例，使用云片网API
 */
function send_sms( $mobile, $content )
{
    $config = [
        'apikey' => 'xxxxx',
        'tpl_id' => '123456',
    ];

    // 模板接口网关
    $url = "https://sms.yunpian.com/v2/sms/tpl_single_send.json";

    $args = [
        'body' => [
            'apikey'    => $config[ 'apikey' ],
            'mobile'    => $mobile,

            // 如果是模板短信
            'tpl_id'    => $config[ 'tpl_id' ],
            'tpl_value' => "#code#=$content",
        ],
    ];

    $result = json_decode( wp_remote_retrieve_body( wp_remote_post( $url, $args ) ) );

    // 根据网关返回的数据返回消息
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
// 验证Captcha
$captcha_id      = $_COOKIE[ 'wprs-security-captcha-id' ];
$session_captcha = get_transient($captcha_id);
````