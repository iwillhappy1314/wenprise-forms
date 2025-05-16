<?php

/**
 * Meta Box API 使用示例
 * 
 * 这个文件展示了如何使用类似 Nette Forms 的 API 来快速添加 post meta box 和 user metabox
 */

use Wenprise\Forms\MetaBox;

/**
 * 添加文章元数据
 */
function wprs_register_post_meta_fields()
{
    // 创建一个文章 Meta Box
    $post_meta = MetaBox::post('wprs_page_settings', '页面设置')
        ->where('post_type', '=', 'page')
        ->setContext('normal')
        ->setPriority('high');

    // 添加文本字段
    $post_meta->addText('page_subtitle', '页面副标题')
        ->setOption('description', '这将显示在页面标题下方')
        ->setRequired('请输入页面副标题');

    // 添加颜色选择器
    $post_meta->addColorPicker('page_color', '页面主色调')
        ->setOption('description', '选择页面的主色调');

    // 添加选择框
    $post_meta->addSelect('page_layout', '页面布局', [
        'default' => '默认布局',
        'full-width' => '全宽布局',
        'sidebar-left' => '左侧边栏',
        'sidebar-right' => '右侧边栏',
    ])->setOption('description', '选择页面布局')->setDefaultValue('not-in-option-array');

    // 添加复选框
    $post_meta->addCheckbox('show_breadcrumbs', '显示面包屑导航')
        ->setOption('description', '是否在页面顶部显示面包屑导航');

    // 添加文本编辑器
    $post_meta->addEditor('page_footer_content', '页脚内容', [
        'media_buttons' => true,
        'textarea_rows' => 5,
    ])->setOption('description', '这将显示在页面底部');
}
add_action('init', 'wprs_register_post_meta_fields');

/**
 * 添加用户元数据
 */
function wprs_register_user_meta_fields()
{
    // 创建一个用户 Meta Box
    $user_meta = MetaBox::user('wprs_user_settings', '用户附加信息');

    // 添加文本字段
    $user_meta->addText('user_phone', '手机号码')
        ->setOption('description', '请输入您的手机号码')
        ->setRequired('手机号码不能为空')
        ->addRule('PATTERN', '请输入正确的手机号码', '/^1[3-9]\d{9}$/');

    // 添加文本域
    $user_meta->addTextArea('user_bio', '个人简介')
        ->setOption('description', '简短介绍自己')
        ->setHtmlAttribute('rows', 5);

    // 添加日期选择器
    $user_meta->addDatePicker('user_birthday', '出生日期', [
        'format' => 'YYYY-MM-DD',
    ])->setOption('description', '请选择您的出生日期');

    // 添加单选框列表
    $user_meta->addRadioList('user_gender', '性别', [
        'male' => '男',
        'female' => '女',
        'other' => '其他',
    ])->setOption('description', '请选择您的性别');

    // 添加复选框列表
    $user_meta->addCheckboxList('user_interests', '兴趣爱好', [
        'reading' => '阅读',
        'music' => '音乐',
        'sports' => '运动',
        'travel' => '旅行',
        'cooking' => '烹饪',
    ])->setOption('description', '请选择您的兴趣爱好');
}
add_action('init', 'wprs_register_user_meta_fields');

/**
 * 添加分类元数据
 */
function wprs_register_term_meta_fields()
{
    // 创建一个分类 Meta Box
    $term_meta = MetaBox::term('wprs_category_settings', '分类设置')
        ->where('taxonomy', '=', 'category');

    // 添加文本字段
    $term_meta->addText('category_subtitle', '分类副标题')
        ->setOption('description', '这将显示在分类标题下方');

    // 添加上传字段
    $term_meta->addUpload('category_image', '分类图片')
        ->setOption('description', '上传分类图片');

    // 添加颜色选择器
    $term_meta->addColorPicker('category_color', '分类颜色')
        ->setOption('description', '选择分类颜色');
}
add_action('init', 'wprs_register_term_meta_fields');

/**
 * 添加选项页
 */
function wprs_register_options_fields()
{
    // 创建一个选项页
    $options = MetaBox::options('wprs_site_settings', '网站设置');

    // 添加文本字段
    $options->addText('site_copyright', '版权信息')
        ->setOption('description', '网站底部显示的版权信息')
        ->setRequired('请输入版权信息');

    // 添加文本域
    $options->addTextArea('site_footer_scripts', '页脚脚本')
        ->setOption('description', '添加到网站页脚的自定义脚本')
        ->setHtmlAttribute('rows', 5);

    // 添加颜色选择器
    $options->addColorPicker('site_primary_color', '主色调')
        ->setOption('description', '网站的主色调');

    // 添加复选框
    $options->addCheckbox('enable_analytics', '启用网站分析')
        ->setOption('description', '是否启用网站分析功能');
}
add_action('init', 'wprs_register_options_fields');
