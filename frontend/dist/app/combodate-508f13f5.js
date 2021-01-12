/*!
 * 
 * wenpriseForms
 * 
 * @author 
 * @version 0.1.0
 * @link UNLICENSED
 * @license UNLICENSED
 * 
 * Copyright (c) 2021 
 * 
 * This software is released under the UNLICENSED License
 * https://opensource.org/licenses/UNLICENSED
 * 
 * Compiled with the help of https://wpack.io
 * A zero setup Webpack Bundler Script for WordPress
 */
(window.wpackiowenpriseFormsappJsonp=window.wpackiowenpriseFormsappJsonp||[]).push([[3],{160:function(t,e,i){i(3),t.exports=i(161)},161:function(t,e,i){"use strict";i.r(e);var o,s,n=i(2);o=window.jQuery,(s=function(t,e){this.$element=o(t),this.$element.is("input")?(this.options=o.extend({},o.fn.combodate.defaults,e,this.$element.data()),this.init()):o.error("Combodate should be applied to INPUT element")}).prototype={constructor:s,init:function(){this.map={day:["D","date"],month:["M","month"],year:["Y","year"],hour:["[Hh]","hours"],minute:["m","minutes"],second:["s","seconds"],ampm:["[Aa]",""]},this.$widget=o('<span class="combodate"></span>').html(this.getTemplate()),this.initCombos(),this.datetime=null,this.$widget.on("change","select",o.proxy((function(t){this.$element.val(this.getValue()).change(),this.options.smartDays&&(o(t.target).is(".month")||o(t.target).is(".year"))&&this.fillCombo("day")}),this)),this.$widget.find("select").css("width","auto"),this.$element.hide().after(this.$widget),this.setValue(this.$element.val()||this.options.value)},getTemplate:function(){var t=this.options.template,e=this.$element.prop("disabled"),i=this.options.customClass;return o.each(this.map,(function(e,i){i=i[0];var o=new RegExp(i+"+"),s=i.length>1?i.substring(1,2):i;t=t.replace(o,"{"+s+"}")})),t=t.replace(/ /g,"&nbsp;"),o.each(this.map,(function(o,s){var n=(s=s[0]).length>1?s.substring(1,2):s;t=t.replace("{"+n+"}",'<select class="'+o+" "+i+'"'+(e?' disabled="disabled"':"")+"></select>")})),t},initCombos:function(){for(var t in this.map){var e=this.$widget.find("."+t);this["$"+t]=e.length?e:null,this.fillCombo(t)}},fillCombo:function(t){var e=this["$"+t];if(e){var i=this["fill"+t.charAt(0).toUpperCase()+t.slice(1)](),o=e.val();e.empty();for(var s=0;s<i.length;s++)e.append('<option value="'+i[s][0]+'">'+i[s][1]+"</option>");e.val(o)}},fillCommon:function(t){var e,i=[];if("name"===this.options.firstItem){var o="function"==typeof(e=moment.localeData?moment.localeData()._relativeTime:moment.relativeTime||moment.langData()._relativeTime)[t]?e[t](1,!0,t,!1):e[t];o=o.split(" ").reverse()[0],i.push(["",o])}else"empty"===this.options.firstItem&&i.push(["",""]);return i},fillDay:function(){var t,e,i=this.fillCommon("d"),o=-1!==this.options.template.indexOf("DD"),s=31;if(this.options.smartDays&&this.$month&&this.$year){var n=parseInt(this.$month.val(),10),a=parseInt(this.$year.val(),10);isNaN(n)||isNaN(a)||(s=moment([a,n]).daysInMonth())}for(e=1;e<=s;e++)t=o?this.leadZero(e):e,i.push([e,t]);return i},fillMonth:function(){var t,e,i=this.fillCommon("M"),o=-1!==this.options.template.indexOf("MMMMMM"),s=-1!==this.options.template.indexOf("MMMMM"),n=-1!==this.options.template.indexOf("MMMM"),a=-1!==this.options.template.indexOf("MMM"),r=-1!==this.options.template.indexOf("MM");for(e=0;e<=11;e++)t=o?moment().date(1).month(e).format("MM - MMMM"):s?moment().date(1).month(e).format("MM - MMM"):n?moment().date(1).month(e).format("MMMM"):a?moment().date(1).month(e).format("MMM"):r?this.leadZero(e+1):e+1,i.push([e,t]);return i},fillYear:function(){var t,e,i=[],o=-1!==this.options.template.indexOf("YYYY");for(e=this.options.maxYear;e>=this.options.minYear;e--)t=o?e:(e+"").substring(2),i[this.options.yearDescending?"push":"unshift"]([e,t]);return i=this.fillCommon("y").concat(i)},fillHour:function(){var t,e,i=this.fillCommon("h"),o=-1!==this.options.template.indexOf("h"),s=(this.options.template.indexOf("H"),-1!==this.options.template.toLowerCase().indexOf("hh")),n=o?12:23;for(e=o?1:0;e<=n;e++)t=s?this.leadZero(e):e,i.push([e,t]);return i},fillMinute:function(){var t,e,i=this.fillCommon("m"),o=-1!==this.options.template.indexOf("mm");for(e=0;e<=59;e+=this.options.minuteStep)t=o?this.leadZero(e):e,i.push([e,t]);return i},fillSecond:function(){var t,e,i=this.fillCommon("s"),o=-1!==this.options.template.indexOf("ss");for(e=0;e<=59;e+=this.options.secondStep)t=o?this.leadZero(e):e,i.push([e,t]);return i},fillAmpm:function(){var t=-1!==this.options.template.indexOf("a");return this.options.template.indexOf("A"),[["am",t?"am":"AM"],["pm",t?"pm":"PM"]]},getValue:function(t){var e,i={},s=this,n=!1;return o.each(this.map,(function(t,e){var o;if("ampm"!==t)return s["$"+t]?i[t]=parseInt(s["$"+t].val(),10):(o=s.datetime?s.datetime[e[1]]():"day"===t?1:0,i[t]=o),isNaN(i[t])?(n=!0,!1):void 0})),n?"":(this.$ampm&&(12===i.hour?i.hour="am"===this.$ampm.val()?0:12:i.hour="am"===this.$ampm.val()?i.hour:i.hour+12),e=moment([i.year,i.month,i.day,i.hour,i.minute,i.second]),this.highlight(e),null===(t=void 0===t?this.options.format:t)?e.isValid()?e:null:e.isValid()?e.format(t):"")},setValue:function(t){if(t){var e="string"==typeof t?moment(t,this.options.format,!0):moment(t),i=this,s={};e.isValid()?(o.each(this.map,(function(t,i){"ampm"!==t&&(s[t]=e[i[1]]())})),this.$ampm&&(s.hour>=12?(s.ampm="pm",s.hour>12&&(s.hour-=12)):(s.ampm="am",0===s.hour&&(s.hour=12))),o.each(s,(function(t,e){i["$"+t]&&("minute"===t&&i.options.minuteStep>1&&i.options.roundTime&&(e=n(i["$"+t],e)),"second"===t&&i.options.secondStep>1&&i.options.roundTime&&(e=n(i["$"+t],e)),i["$"+t].val(e))})),this.options.smartDays&&this.fillCombo("day"),this.$element.val(e.format(this.options.format)).change(),this.datetime=e):this.datetime=null}function n(t,e){var i={};return t.children("option").each((function(t,s){var n,a=o(s).attr("value");""!==a&&(n=Math.abs(a-e),(void 0===i.distance||n<i.distance)&&(i={value:a,distance:n}))})),i.value}},highlight:function(t){t.isValid()?this.options.errorClass?this.$widget.removeClass(this.options.errorClass):this.$widget.find("select").css("border-color",this.borderColor):this.options.errorClass?this.$widget.addClass(this.options.errorClass):(this.borderColor||(this.borderColor=this.$widget.find("select").css("border-color")),this.$widget.find("select").css("border-color","red"))},leadZero:function(t){return t<=9?"0"+t:t},destroy:function(){this.$widget.remove(),this.$element.removeData("combodate").show()}},o.fn.combodate=function(t){var e,i=Array.apply(null,arguments);return i.shift(),"getValue"===t&&this.length&&(e=this.eq(0).data("combodate"))?e.getValue.apply(e,i):this.each((function(){var e=o(this),a=e.data("combodate"),r="object"==Object(n.a)(t)&&t;a||e.data("combodate",a=new s(this,r)),"string"==typeof t&&"function"==typeof a[t]&&a[t].apply(a,i)}))},o.fn.combodate.defaults={format:"DD-MM-YYYY HH:mm",template:"D / MMM / YYYY   H : mm",value:null,minYear:1970,maxYear:2015,yearDescending:!0,minuteStep:5,secondStep:1,firstItem:"empty",errorClass:null,customClass:"",roundTime:!0,smartDays:!1}},2:function(t,e,i){"use strict";function o(t){return(o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}i.d(e,"a",(function(){return o}))},3:function(t,e,i){"use strict";var o="wenpriseFormsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(o)]}},[[160,0]]]);
//# sourceMappingURL=combodate-508f13f5.js.map