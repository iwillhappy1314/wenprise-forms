/*!
 * 
 * wenpriseForms
 * 
 * @author 
 * @version 0.1.0
 * @link UNLICENSED
 * @license UNLICENSED
 * 
 * Copyright (c) 2020 
 * 
 * This software is released under the UNLICENSED License
 * https://opensource.org/licenses/UNLICENSED
 * 
 * Compiled with the help of https://wpack.io
 * A zero setup Webpack Bundler Script for WordPress
 */
(window.wpackiowenpriseFormsappJsonp=window.wpackiowenpriseFormsappJsonp||[]).push([[12],{152:function(e,t,n){n(3),n(153),e.exports=n(155)},153:function(e,t,n){"use strict";n.r(t);n(154);jQuery(document).ready((function(e){var t=e(".js-uploader"),n=e(".js-uploader-message"),i=t.data("settings"),o={url:e(".js-uploader .rs-uploader__shadow").data("url"),type:"POST",dataType:"json",maxFileSize:2e6,auto:!0,queue:!1,multiple:!0===t.data("multiple"),onBeforeUpload:function(){n.empty()},onDragEnter:function(){t.addClass("active")},onDragLeave:function(){t.removeClass("active")},onUploadSuccess:function(e,n){var i=t.data("name"),o=!0===t.data("multiple"),s='<button type="button" class="rs-uploader__close" data-value='+n.id+'><svg t="1575261098184" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3639" width="12" height="12"><path d="M49.6 158.4l104-108.8 358.4 352 356.8-352 105.6 105.6-352 356.8 352 355.2-102.4 107.2L512 620.8 155.2 974.4l-105.6-105.6L406.4 512z" p-id="3640" fill="#ffffff"></path></svg></button>',r='<img src="'+n.thumb+'" alt="Thumbnail">';n.thumb||(r='<svg t="1540194811704" class="icon" style="" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2457" xmlns:xlink="http://www.w3.org/1999/xlink" width="128" height="128"><defs><style type="text/css"></style></defs><path d="M768 426.666667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM341.333333 324.266667h170.666667a17.066667 17.066667 0 1 0 0-34.133334H341.333333a17.066667 17.066667 0 1 0 0 34.133334zM768 563.2H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM768 699.733333H341.333333a17.066667 17.066667 0 1 0 0 34.133334h426.666667a17.066667 17.066667 0 1 0 0-34.133334zM768 836.266667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333z" p-id="2458" fill="#666666"></path><path d="M836.266667 248.9344V0H102.4v938.666667h85.333333v85.333333h733.866667V334.267733l-85.333333-85.333333z m-153.6-105.335467l153.6 153.6L863.3344 324.266667H682.666667V143.598933zM136.533333 904.533333V34.133333h665.6v180.667734L672.6656 85.333333H187.733333v819.2H136.533333z m85.333334 85.333334V119.466667h426.666666v238.933333h238.933334v631.466667H221.866667z" p-id="2459" fill="#666666"></path></svg><br>'+n.title),t.find("input:text").filter((function(){return""===this.value})).remove(),o?(t.find(".rs-uploader__value").append('<input type="hidden" name="'+i+'" value="'+n.id+'">'),t.find(".rs-uploader__preview").show().append('<div class="rs-uploader__thumbnail">'+s+r+"</div>")):(t.find(".rs-uploader__text").hide(),t.find(".rs-uploader__button").hide(),t.find(".rs-uploader__value").empty().append('<input type="hidden" name="'+i+'" value="'+n.id+'">'),t.find(".rs-uploader__preview").empty().show().append('<div class="rs-uploader__thumbnail">'+s+r+"</div>"))},onUploadError:function(e,t,n,i){},onUploadComplete:function(e){},onUploadCanceled:function(e){n.html("您已取消上传文件")},onUploadProgress:function(e,t){n.addClass("js-progress").css("width",t+"%")},onFileTypeError:function(e){n.html("文件类型错误")},onFileSizeError:function(e){n.html("文件尺寸超过限制")},onFileExtError:function(e){n.html("文件类型错误")}};e.extend(o,i),t.dmUploader(o),e(".rs-uploader__close").live("click",(function(){var t=e(this).data("value"),n=e(this).closest(".js-uploader");!0===n.data("multiple")?n.find("input[value="+t+"]").remove():(e(".rs-uploader__value input").attr("value",""),n.show(),n.find(".rs-uploader__text").show(),n.find(".rs-uploader__button").show()),e(this).parent().remove()})),e("input[name=js-input-shadow]").each((function(){var t=e(this).closest(".js-uploader"),n=!0===t.data("multiple"),i=t.find(".rs-uploader__preview").children().length;!n&&i>0&&(t.find(".rs-uploader__text").hide(),e(this).parent().hide())}))}))},154:function(e,t,n){var i,o,s;
/*
 * dmUploader - jQuery Ajax File Uploader Widget
 * https://github.com/danielm/uploader
 *
 * Copyright Daniel Morales <daniel85mg@gmail.com>
 * Released under the MIT license.
 * https://github.com/danielm/uploader/blob/master/LICENSE.txt
 *
 * @preserve
 */!function(r){"use strict";o=[n(4)],void 0===(s="function"==typeof(i=function(e){var t="dmUploader",n=0,i=1,o=2,s=3,r=4,a={auto:!0,queue:!0,dnd:!0,hookDocument:!0,multiple:!0,url:document.URL,method:"POST",extraData:{},headers:{},dataType:null,fieldName:"file",maxFileSize:0,allowedTypes:"*",extFilter:null,onInit:function(){},onComplete:function(){},onFallbackMode:function(){},onNewFile:function(){},onBeforeUpload:function(){},onUploadProgress:function(){},onUploadSuccess:function(){},onUploadCanceled:function(){},onUploadError:function(){},onUploadComplete:function(){},onFileTypeError:function(){},onFileSizeError:function(){},onFileExtError:function(){},onDragEnter:function(){},onDragLeave:function(){},onDocumentDragEnter:function(){},onDocumentDragLeave:function(){}},l=function(e,t){this.data=e,this.widget=t,this.jqXHR=null,this.status=n,this.id=Math.random().toString(36).substr(2)};l.prototype.upload=function(){var t=this;if(!t.canUpload())return t.widget.queueRunning&&t.status!==i&&t.widget.processQueue(),!1;var n=new FormData;n.append(t.widget.settings.fieldName,t.data);var o=t.widget.settings.extraData;return"function"==typeof o&&(o=o.call(t.widget.element,t.id)),e.each(o,(function(e,t){n.append(e,t)})),t.status=i,t.widget.activeFiles++,t.widget.settings.onBeforeUpload.call(t.widget.element,t.id),t.jqXHR=e.ajax({url:t.widget.settings.url,type:t.widget.settings.method,dataType:t.widget.settings.dataType,data:n,headers:t.widget.settings.headers,cache:!1,contentType:!1,processData:!1,forceSync:!1,xhr:function(){return t.getXhr()},success:function(e){t.onSuccess(e)},error:function(e,n,i){t.onError(e,n,i)},complete:function(){t.onComplete()}}),!0},l.prototype.onSuccess=function(e){this.status=o,this.widget.settings.onUploadSuccess.call(this.widget.element,this.id,e)},l.prototype.onError=function(e,t,n){this.status!==r&&(this.status=s,this.widget.settings.onUploadError.call(this.widget.element,this.id,e,t,n))},l.prototype.onComplete=function(){this.widget.activeFiles--,this.status!==r&&this.widget.settings.onUploadComplete.call(this.widget.element,this.id),this.widget.queueRunning?this.widget.processQueue():this.widget.settings.queue&&0===this.widget.activeFiles&&this.widget.settings.onComplete.call(this.element)},l.prototype.getXhr=function(){var t=this,n=e.ajaxSettings.xhr();return n.upload&&n.upload.addEventListener("progress",(function(e){var n=0,i=e.loaded||e.position,o=e.total||e.totalSize;e.lengthComputable&&(n=Math.ceil(i/o*100)),t.widget.settings.onUploadProgress.call(t.widget.element,t.id,n)}),!1),n},l.prototype.cancel=function(e){e=void 0!==e&&e;var t=this.status;return!!(t===i||e&&t===n)&&(this.status=r,this.widget.settings.onUploadCanceled.call(this.widget.element,this.id),t===i&&this.jqXHR.abort(),!0)},l.prototype.canUpload=function(){return this.status===n||this.status===s};var u=function(t,n){return this.element=e(t),this.settings=e.extend({},a,n),this.checkSupport()?(this.init(),this):(e.error("Browser not supported by jQuery.dmUploader"),this.settings.onFallbackMode.call(this.element),!1)};u.prototype.checkSupport=function(){return void 0!==window.FormData&&(!new RegExp("/(Android (1.0|1.1|1.5|1.6|2.0|2.1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle/(1.0|2.0|2.5|3.0))/").test(window.navigator.userAgent)&&!e('<input type="file" />').prop("disabled"))},u.prototype.init=function(){var t=this;this.queue=[],this.queuePos=-1,this.queueRunning=!1,this.activeFiles=0,this.draggingOver=0,this.draggingOverDoc=0;var n=t.element.is("input[type=file]")?t.element:t.element.find("input[type=file]");return n.length>0&&(n.prop("multiple",this.settings.multiple),n.on("change.dmUploader",(function(n){var i=n.target&&n.target.files;i&&i.length&&(t.addFiles(i),e(this).val(""))}))),this.settings.dnd&&this.initDnD(),0!==n.length||this.settings.dnd?(this.settings.onInit.call(this.element),this):(e.error("Markup error found by jQuery.dmUploader"),null)},u.prototype.initDnD=function(){var t=this;t.element.on("drop.dmUploader",(function(e){e.preventDefault(),t.draggingOver>0&&(t.draggingOver=0,t.settings.onDragLeave.call(t.element));var n=e.originalEvent&&e.originalEvent.dataTransfer;if(n&&n.files&&n.files.length){var i=[];t.settings.multiple?i=n.files:i.push(n.files[0]),t.addFiles(i)}})),t.element.on("dragenter.dmUploader",(function(e){e.preventDefault(),0===t.draggingOver&&t.settings.onDragEnter.call(t.element),t.draggingOver++})),t.element.on("dragleave.dmUploader",(function(e){e.preventDefault(),t.draggingOver--,0===t.draggingOver&&t.settings.onDragLeave.call(t.element)})),t.settings.hookDocument&&(e(document).off("drop.dmUploader").on("drop.dmUploader",(function(e){e.preventDefault(),t.draggingOverDoc>0&&(t.draggingOverDoc=0,t.settings.onDocumentDragLeave.call(t.element))})),e(document).off("dragenter.dmUploader").on("dragenter.dmUploader",(function(e){e.preventDefault(),0===t.draggingOverDoc&&t.settings.onDocumentDragEnter.call(t.element),t.draggingOverDoc++})),e(document).off("dragleave.dmUploader").on("dragleave.dmUploader",(function(e){e.preventDefault(),t.draggingOverDoc--,0===t.draggingOverDoc&&t.settings.onDocumentDragLeave.call(t.element)})),e(document).off("dragover.dmUploader").on("dragover.dmUploader",(function(e){e.preventDefault()})))},u.prototype.releaseEvents=function(){this.element.off(".dmUploader"),this.element.find("input[type=file]").off(".dmUploader"),this.settings.hookDocument&&e(document).off(".dmUploader")},u.prototype.validateFile=function(t){if(this.settings.maxFileSize>0&&t.size>this.settings.maxFileSize)return this.settings.onFileSizeError.call(this.element,t),!1;if("*"!==this.settings.allowedTypes&&!t.type.match(this.settings.allowedTypes))return this.settings.onFileTypeError.call(this.element,t),!1;if(null!==this.settings.extFilter){var n=t.name.toLowerCase().split(".").pop();if(e.inArray(n,this.settings.extFilter)<0)return this.settings.onFileExtError.call(this.element,t),!1}return new l(t,this)},u.prototype.addFiles=function(e){for(var t=0,n=0;n<e.length;n++){var i=this.validateFile(e[n]);if(i)!1!==this.settings.onNewFile.call(this.element,i.id,i.data)&&(this.settings.auto&&!this.settings.queue&&i.upload(),this.queue.push(i),t++)}return 0===t?this:(this.settings.auto&&this.settings.queue&&!this.queueRunning&&this.processQueue(),this)},u.prototype.processQueue=function(){return this.queuePos++,this.queuePos>=this.queue.length?(0===this.activeFiles&&this.settings.onComplete.call(this.element),this.queuePos=this.queue.length-1,this.queueRunning=!1,!1):(this.queueRunning=!0,this.queue[this.queuePos].upload())},u.prototype.restartQueue=function(){this.queuePos=-1,this.queueRunning=!1,this.processQueue()},u.prototype.findById=function(e){for(var t=!1,n=0;n<this.queue.length;n++)if(this.queue[n].id===e){t=this.queue[n];break}return t},u.prototype.cancelAll=function(){var e=this.queueRunning;this.queueRunning=!1;for(var t=0;t<this.queue.length;t++)this.queue[t].cancel();e&&this.settings.onComplete.call(this.element)},u.prototype.startAll=function(){if(this.settings.queue)this.restartQueue();else for(var e=0;e<this.queue.length;e++)this.queue[e].upload()},u.prototype.methods={start:function(t){if(this.queueRunning)return!1;var i=!1;return void 0===t||(i=this.findById(t))?i?(i.status===r&&(i.status=n),i.upload()):(this.startAll(),!0):(e.error("File not found in jQuery.dmUploader"),!1)},cancel:function(t){var n=!1;return void 0===t||(n=this.findById(t))?n?n.cancel(!0):(this.cancelAll(),!0):(e.error("File not found in jQuery.dmUploader"),!1)},reset:function(){return this.cancelAll(),this.queue=[],this.queuePos=-1,this.activeFiles=0,!0},destroy:function(){this.cancelAll(),this.releaseEvents(),this.element.removeData(t)}},e.fn.dmUploader=function(n){var i=arguments;if("string"!=typeof n)return this.each((function(){e.data(this,t)||e.data(this,t,new u(this,n))}));this.each((function(){var o=e.data(this,t);o instanceof u?"function"==typeof o.methods[n]?o.methods[n].apply(o,Array.prototype.slice.call(i,1)):e.error("Method "+n+" does not exist in jQuery.dmUploader"):e.error("Unknown plugin data found by jQuery.dmUploader")}))}})?i.apply(t,o):i)||(e.exports=s)}()},155:function(e,t,n){},3:function(e,t,n){"use strict";var i="wenpriseFormsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(i)]},4:function(e,t){e.exports=jQuery}},[[152,0]]]);
//# sourceMappingURL=uploader-ba0e0eff.js.map