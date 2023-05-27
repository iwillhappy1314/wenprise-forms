/*! For license information please see bootstrap-button.b09c2d.js.LICENSE.txt */
!function(){var t={5311:function(t){"use strict";t.exports=jQuery}},e={};function o(n){var i=e[n];if(void 0!==i)return i.exports;var s=e[n]={exports:{}};return t[n](s,s.exports,o),s.exports}!function(){var t=o(5311);function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},e(t)}if(void 0===t)throw new Error("Bootstrap's JavaScript requires jQuery");!function(t){"use strict";var e=t.fn.jquery.split(" ")[0].split(".");if(e[0]<2&&e[1]<9||1==e[0]&&9==e[1]&&e[2]<1||e[0]>3)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4")}(t),function(t){"use strict";var o=function e(o,n){this.$element=t(o),this.options=t.extend({},e.DEFAULTS,n),this.isLoading=!1};function n(n){return this.each((function(){var i=t(this),s=i.data("bs.button"),r="object"==e(n)&&n;s||i.data("bs.button",s=new o(this,r)),"toggle"==n?s.toggle():n&&s.setState(n)}))}o.VERSION="3.3.7",o.DEFAULTS={loadingText:"loading..."},o.prototype.setState=function(e){var o="disabled",n=this.$element,i=n.is("input")?"val":"html",s=n.data();e+="Text",null==s.resetText&&n.data("resetText",n[i]()),setTimeout(t.proxy((function(){n[i](null==s[e]?this.options[e]:s[e]),"loadingText"==e?(this.isLoading=!0,n.addClass(o).attr(o,o).prop(o,!0)):this.isLoading&&(this.isLoading=!1,n.removeClass(o).removeAttr(o).prop(o,!1))}),this),0)},o.prototype.toggle=function(){var t=!0,e=this.$element.closest('[data-toggle="buttons"]');if(e.length){var o=this.$element.find("input");"radio"==o.prop("type")?(o.prop("checked")&&(t=!1),e.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==o.prop("type")&&(o.prop("checked")!==this.$element.hasClass("active")&&(t=!1),this.$element.toggleClass("active")),o.prop("checked",this.$element.hasClass("active")),t&&o.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var i=t.fn.button;t.fn.button=n,t.fn.button.Constructor=o,t.fn.button.noConflict=function(){return t.fn.button=i,this},t(document).on("click.bs.button.data-api",'[data-toggle^="button"]',(function(e){var o=t(e.target).closest(".btn");n.call(o,"toggle"),t(e.target).is('input[type="radio"], input[type="checkbox"]')||(e.preventDefault(),o.is("input,button")?o.trigger("focus"):o.find("input:visible,button:visible").first().trigger("focus"))})).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',(function(e){t(e.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(e.type))}))}(t)}()}();