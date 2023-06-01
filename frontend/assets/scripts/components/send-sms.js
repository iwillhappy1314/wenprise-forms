(function($) {

  // 开始上手
  $.send_sms = function(element, options) {

    // 插件默认配置
    // 私有属性只有被插件内部使用
    var defaults = {
      count         : 60,
      name          : 'phone',
      get_again_text: 'Get Again',
      countdown_text: ' Minutes Later Get Again',
      url           : wenpriseFormSettings.ajax_url + '?action=get_sms_code',
    };

    // 避免变量名冲突， 为当前变量实例对象定义别名
    var plugin = this;

    // 将合并默认配置与用户配置参数
    // 插件内部通过settings.属性访问，在外部可通过插件绑定元素的element.data('pluginName').settings.属性配置。
    plugin.settings = {};

    var $element = $(element);    // jQuery对象

    var InterValObj; //timer 变量，控制时间
    var current_count;//当前剩余秒数

    // 初始化化函数，构造函数实例化会调用init函数
    plugin.init = function() {

      // 插件最终配置文件
      plugin.settings = $.extend({}, defaults, options);

      $element.click(function() {
        $.ajax({
          type      : 'POST',
          dataType  : 'json',
          url       : plugin.settings.url,
          data      : {
            'phone': $('input[name=' + plugin.settings.name + ']').val(),
          },
          beforeSend: function() {
            $(this).addClass('loading');
          },
          success   : function(data) {
            if (data.success === true) {
              // 验证码发送成功后，启动计时器
              current_count = plugin.settings.count;

              // 设置button效果，开始计时
              $element.prop('disabled', true);
              $element.val(current_count + plugin.settings.countdown_text);

              InterValObj = window.setInterval(set_count_down, 1000); //启动计时器，1秒执行一次
              $(this).removeClass('loading');
            }
          },
          error     : function(data) {
            $(this).removeClass('loading');
            alert(data.message);
          },
        });
      });

    };

    // 公共函数
    // 内部通过plugin.methodName(arg1, arg2, ... argn)访问
    // 外部通过element.data('pluginName').publicMethod(arg1, arg2, ... argn)调用
    // foo_public_method只是示范，可以自己定义且定义多个
    plugin.aaa = function() {

      // code goes here

    };

    // 私有方法
    // 只能在插件内部使用
    // foo_private_method只是示范，可以自己定义且定义多个
    var set_count_down = function() {

      if (current_count === 0) {
        window.clearInterval(InterValObj);//停止计时器
        $element.removeAttr('disabled');//启用按钮
        $element.val(plugin.settings.get_again_text);
      } else {
        current_count--;
        $element.val(current_count + plugin.settings.countdown_text);
      }

    };

    // 在实例化时初始化插件
    plugin.init();

  };

  // 为jQuery.fn对象添加插件
  $.fn.send_sms = function(options) {

    // 遍历选择器绑定插件
    return this.each(function() {

      // 判断对象是否绑定插件
      if (undefined == $(this).data('send_sms')) {

        // 创建一个实例化对象并传入配置参数
        var plugin = new $.send_sms(this, options);

        // 用data存储实例化对象
        // 通过element.data('pluginName').publicMethod(arg1, arg2, ... argn) 或element.data('pluginName').settings.propertyName使用对象和其公开访问
        $(this).data('send_sms', plugin);

      }

    });

  };

})(jQuery);