# wenprise-forms

为 WordPress 场景扩展 Nette Forms。

基础用法：[Nette Form](https://doc.nette.org/en/forms/standalone)

## 功能特性

- 同时支持 Composer 库模式和 WordPress 插件模式
- 在 Nette Forms 基础上扩展适配 WordPress 的字段与 UI 组件
- 内置表单验证、条件显示、数据存储集成与 AJAX 友好输入组件

## 环境要求

- PHP `>=8.3`
- `ext-json`
- `nette/forms ^3.2`
- `nette/utils ^4.1`
- WordPress `>=6.0`（推荐）

## 安装

### 方式 A：作为 Composer 库安装

```bash
composer require wenprise/forms
```

### 方式 B：作为 WordPress 插件安装

1. 将本仓库克隆或下载到 `wp-content/plugins/wenprise-forms`。
2. 在插件目录中运行 `composer install`。
3. 在 WordPress 后台启用 `Wenprise Forms`。

## 运行模式

### Composer 库模式

- 由你的项目负责加载 Composer autoload。
- 由你的业务代码决定何时何地初始化表单对象。

### WordPress 插件模式

- 插件入口文件 `wenprise-forms.php` 会自动加载 `vendor/autoload.php`。
- 插件通过 `new \Wenprise\Forms\Init();` 完成启动。

## 快速开始（最小可运行示例）

Forms 内置自己的 HTTP 请求处理流程。请在 `head_sent` 前实例化，通常放在 `init` action 中。

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

## 用法

### 快速开始
Forms 内置自己的 HTTP 请求处理流程，因此需要在 `head_sent` 前实例化，通常放在 `init` action 中。

```php
add_action('init', function ()
{
    global $form;

    // Get forms object
    $form = Helpers::get_form();
});

```
创建 Form 实例

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

## 前端资源说明

部分字段依赖 JavaScript/CSS 组件（例如：`Slider`、`Chosen`、`Date Picker`、`Signature`、`Star Rating`、`Autocomplete`、`Chained Select`）。

- 请确保在主题或插件运行时正确 enqueue 这些资源。
- 集成到现有主题时，建议一次只接入并测试一个高级字段。

## 安全建议

- 公开表单务必添加 CSRF 保护（`$form->addCsrf(...)`）。
- AJAX 端点在处理输入前应校验 nonce 与 capability。
- 请对输入做清洗，并在模板输出时做转义。
- 不要只依赖前端校验；服务端必须保留验证规则。

### 设置必填

```php
$form->addText('first_name', 'First Name')
     ->setRequired();
```

### 添加验证规则

```php
$form->addPassword('re_password', 'Password again:')
     ->addRule($form::EQUAL, 'Password mismatch', $form['password']);
```

[Rule Documentation](https://doc.nette.org/en/forms/validation)


### 添加字段说明

```php
$form->addText('first_name', 'First Name')
     ->setOption('description', 'This is your first name.');
```

使用 HTML

```php
$form->addTextArea('description', Html::el('p')
	->setHtml('This number remains hidden. <a href="...">Terms of service.</a>')
	);
```

### 设置条件显示

```php
$form->addColorPicker('first_name3', 'First Name')
     ->setHtmlAttribute('data-cond', '[name=first_name2] == 2');
```

### 设置字段宽度

`setWidth()` 同时支持 Bootstrap 风格 renderer 和 `TailwindGridFormRender`。

```php
$form->addText('first_name', 'First Name')
     ->setWidth(4);
```

也可以按断点分别设置：

```php
$form->addText('first_name', 'First Name')
     ->setWidth(6, 'base')
     ->setWidth(3, 'md')
     ->setWidth(4, 'lg');
```

或者使用简写：

```php
$form->addText('first_name', 'First Name')
     ->setWidth(6, 3, 4);
```

上面的简写等价于：

- `base = 6`
- `md = 3`
- `lg = 4`

在 `DefaultFormRender('vertical')` 和 `InlineFormRender()` 下，`setWidth()` 会映射为 `rs-col-md-4` 这类 Bootstrap 列类。

在 `TailwindGridFormRender('vertical')` 下，`setWidth()` 会映射为 `rs-grid-form__span-md-4` 这类 grid span 类。

`DefaultFormRender('horizontal')` 会保持每个字段整行显示，因此这里会忽略宽度设置。

### Repeater

使用 `addRepeater()` 可以创建可重复的字段组。

```php
use Wenprise\Forms\Renders\GridFormRender;

$form = new \Wenprise\Forms\Form('contacts_form');
$form->setRenderer(new GridFormRender('vertical'));

$form->addRepeater('contacts', 'Contacts', function (\Wenprise\Forms\Container $row): void {
    $row->addText('name', 'Name')->setWidth(6, 3, 4);
    $row->addText('phone', 'Phone')->setWidth(6, 3, 4);
    $row->addText('email', 'Email')->setWidth(12, 6, 4);
}, 1, 5);

$form->addSubmit('send', 'Save');
```

`addRepeater()` 参数说明：

- `$name`：repeater 字段名
- `$label`：repeater 标签
- `$factory`：用于定义每一行容器的回调
- `$copy_count`：初始行数
- `$max_copies`：可选的最大行数

提交后的值会以嵌套数组结构返回，并可通过内置 datastore 保存。


### 多提交按钮

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

### 在标签或说明中使用 HTML

```php
$confirm = Html::el('span')->setHtml('I agree the <a href="#">Terms of service.</a>');

$form->addCheckbox('confirm', $confirm)->setOption('description', $confirm);
```

### 设置 Datastore

```php
$form->setDatastore(new \Wenprise\Forms\Datastores\PostMetaDatastore(1));

$form->save();
```

### 自定义设置页面示例

你可以使用 `OptionsDatastore` 快速构建 WordPress 后台设置页。

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

### Tab 分组

你可以用 Tab 分组把长表单拆成多个面板。

```php
$form = new \Wenprise\Forms\Form();

$form->addTab('basic', '基础信息', true);
$form->addText('first_name', 'First Name');
$form->addText('email', 'Email');

$form->addTab('advanced', '高级设置');
$form->addTextArea('notes', 'Notes');
$form->endTab();

// 这个提交按钮在 Tab 分组之外。
$form->addSubmit('send', 'Save');

// 模板中直接输出：
echo $form;
```

Tab 会自动走现有渲染流程（`$form->render()` / `echo $form`），不需要额外调用新的渲染方法。

在 Tab 分组内创建的提交按钮会自动渲染到 Tab 面板外层，确保保存操作始终可见。

也可以使用下划线方法名：

```php
$form->add_tab('basic', '基础信息', true);
echo $form;
```

### Stepper 分组

你可以用 Stepper 分组渲染带进度条的分步表单，并自动带有上一步/下一步操作。

```php
$form = new \Wenprise\Forms\Form();

$form->addStep('campaign', '选择主活动设置', true);
$form->addText('campaign_name', '活动名称');
$form->endStep();

$form->addStep('ad_group', '创建广告组');
$form->addText('group_name', '广告组名称');
$form->endStep();

$form->addStep('ad', '创建广告');
$form->addTextArea('ad_content', '广告内容');
$form->endStep();

$form->addSubmit('send', '保存');

echo $form;
```

也可以使用下划线方法名：

```php
$form->add_step('campaign', '活动', true);
```

如果是在 shortcode 回调中渲染，请返回 HTML，不要直接 echo：

```php
add_shortcode('wenprise_form_demo', function () use ($form) {
    return (string) $form;
});
```

## 字段类型

### nonce 字段

````php
$form->addCsrf('postform', 'Nonce invalid');
````

### WordPress TinyMCE 编辑器

[Settings](https://codex.wordpress.org/Function_Reference/wp_editor)

````php
$form->addEditor('post_extra', 'Extra content', []);
````

### Ajax 上传器

```php
$form->addAjaxUpload('photos', 'Photos', true, )
             ->setUrl(  admin_url( 'admin-ajax.php?action=upload' ) );
```

#### 上传后端示例

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

### Slider 输入

[配置](http://ionden.com/a/plugins/ion.rangeSlider/en.html) 

````php
$form->addSlider('price', 'Price', []);
````

### 日期选择器

[配置](https://jqueryui.com/datepicker/)

````php
$form->AddBirthdaypicker('_birthday', 'Date of Birth', [
    'format' => 'YYYY-MM-DD', 
    'template' => 'YYYY-MM-DD',
    'minYear' => '1900',
    'maxYear' => date("Y")
]);
````

### 颜色选择器

[配置](http://automattic.github.io/Iris/)

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

#### 多选 Chosen

````php
$form->addMultiChosen('post_tags', 'Tags', $choices);
````

### 签名字段

````php
$options = [
   'width'      => '500',
   'height'     => '250',
   'border'     => '#999',
   'background' => '#f3f3f3',
];

$form->addSignature('first_name9', 'First Name', $options);
````

### 星级评分输入

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

### 图片选择

````php
$options = [
    'light' => 'https://via.placeholder.com/64/EEEEEE/000000/?text=Light',
    'dark'  => 'https://via.placeholder.com/64/000000/FFFFFF/?text=Dark',
];

$form->addImagePicker('theme', 'Theme', $options);
````

### 自动补全输入

数据源可以是数组，也可以是返回数组的 URL。

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

如果数据源是 AJAX URL，后端需要按如下格式返回数据。

```php
suggestions: [
  { "value": "United Arab Emirates", "data": "AE" },
  { "value": "United Kingdom",       "data": "UK" },
  { "value": "United States",        "data": "US" }
]
```

### 表格输入

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

### 组合输入

为文本输入设置前缀或后缀。

````php
$form->addGroupInput('day1', 'Day')
     ->setPrefix('Email')
     ->setSuffix('gmail.com');
````

### 短信输入

发送短信验证码

```php
$form->addSmsInput('phone', 'Cellphone', )
             ->setUrl(admin_url('admin-ajax.php?action=validate_cellpone'));
```

后端示例

```php

/**
 * 发送短信验证码。
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

	// 发送短信前先保存验证码。
	$code       = PhoneCode::query()->firstOrCreate( [ 'phone' => $phone ] );
	$code->code = $random;
	$code->save();

	$msg = Helper::send_sms( $phone, $code->code );

	wp_send_json( $msg, '200' );
};

/**
 * 短信后端示例：使用云片 API。
 */
function send_sms( $mobile, $content )
{
    $config = [
        'apikey' => 'xxxxx',
        'tpl_id' => '123456',
    ];

    // 模板接口地址
    $url = "https://sms.yunpian.com/v2/sms/tpl_single_send.json";

    $args = [
        'body' => [
            'apikey'    => $config[ 'apikey' ],
            'mobile'    => $mobile,

            // 模板短信参数
            'tpl_id'    => $config[ 'tpl_id' ],
            'tpl_value' => "#code#=$content",
        ],
    ];

    $result = json_decode( wp_remote_retrieve_body( wp_remote_post( $url, $args ) ) );

    // 根据网关返回结果生成消息。
    return [
        'code' => $result->code,
        'msg'  => $result->msg,
    ];
}
```

### Captcha 输入

````php
 $form->AddCaptcha('captcha', 'Captcha')
             ->setUrl(admin_url('admin-ajax.php?action=get_captcha'));
````
### 级联选择

````php
$form->addChainedSelect('chained', 'Chained Select', [
        'url'        => get_theme_file_uri('cityData.min.json'),
        'selects'    => ['province', 'city', 'area'],
        'emptyStyle' => 'none',
    ], ['province', 'city', 'area'])->setDefaultValue([001, 002, 003]);
````

### Captcha 后端示例

````php
/**
 * Captcha 后端示例。
 * WordPress 核心不使用 session，请改用 cookie 与 transient 
 *
 * 运行 `composer require gregwar/captcha` 安装依赖
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
// 验证 captcha
$captcha_id      = $_COOKIE[ 'wprs-security-captcha-id' ];
$session_captcha = get_transient($captcha_id);
````
