# wenprise-forms

Extend Nette Forms for WordPress use.

Basic usage: [Nette Form](https://doc.nette.org/en/2.4/forms)

## Usage


````php
use Wenprise\Forms\Form;

$form = new Form;
wprs_form( $form );

// Set form method
$form->setMethod( 'POST' );

// Set form field
$form->addText('first_name', 'First Name');
$form->addSubmit( 'send', 'Save' );

// Validate form and get form data
if ( $form->isSuccess() ) {

	$values = $form->getValues();
	
	$first_name = $values->first_name;
			
}	
````

### nonce field

````php
$form->addCsrf('post-form', 'Nonce invaliadte');
````

### WordPress Tinymce editor

Settings: https://codex.wordpress.org/Function_Reference/wp_editor

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

Setting: http://ionden.com/a/plugins/ion.rangeSlider/en.html

````php
$form->addSlider('price', 'Price', []);
````

### Data Picker

Setting: https://jqueryui.com/datepicker/

````php
$form->AddBirthdaypicker('_birthday', 'Date of Birth', [
    'format' => 'YYYY-MM-DD', 
    'template' => 'YYYY-MM-DD',
    'minYear' => '1900',
    'maxYear' => date("Y")
]);
````

### Color Picker

Setting: http://automattic.github.io/Iris/

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


###Group Input
Set a prefix or suffix for text input.

````php
$form->addGroupInput('day1', 'Day')
     ->setPrefix('Email')
     ->setSuffix('gmail.com');
````