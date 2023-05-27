/*! For license information please see ajax-uploader.3157ba.js.LICENSE.txt */
!function(){var e={6462:function(e,t,n){var i,o,s;!function(r){"use strict";o=[n(5311)],i=function(e){var t="dmUploader",n={PENDING:0,UPLOADING:1,COMPLETED:2,FAILED:3,CANCELLED:4},i={auto:!0,queue:!0,dnd:!0,hookDocument:!0,multiple:!0,url:document.URL,method:"POST",extraData:{},headers:{},dataType:null,fieldName:"file",maxFileSize:0,allowedTypes:"*",extFilter:null,onInit:function(){},onComplete:function(){},onFallbackMode:function(){},onNewFile:function(){},onBeforeUpload:function(){},onUploadProgress:function(){},onUploadSuccess:function(){},onUploadCanceled:function(){},onUploadError:function(){},onUploadComplete:function(){},onFileTypeError:function(){},onFileSizeError:function(){},onFileExtError:function(){},onDragEnter:function(){},onDragLeave:function(){},onDocumentDragEnter:function(){},onDocumentDragLeave:function(){}},o=function(e,t){this.data=e,this.widget=t,this.jqXHR=null,this.status=n.PENDING,this.id=Math.random().toString(36).substr(2)};o.prototype.upload=function(){var t=this;if(!t.canUpload())return t.widget.queueRunning&&t.status!==n.UPLOADING&&t.widget.processQueue(),!1;var i=new FormData;i.append(t.widget.settings.fieldName,t.data);var o=t.widget.settings.extraData;return"function"==typeof o&&(o=o.call(t.widget.element,t.id)),e.each(o,(function(e,t){i.append(e,t)})),t.status=n.UPLOADING,t.widget.activeFiles++,t.widget.settings.onBeforeUpload.call(t.widget.element,t.id),t.jqXHR=e.ajax({url:t.widget.settings.url,type:t.widget.settings.method,dataType:t.widget.settings.dataType,data:i,headers:t.widget.settings.headers,cache:!1,contentType:!1,processData:!1,forceSync:!1,xhr:function(){return t.getXhr()},success:function(e){t.onSuccess(e)},error:function(e,n,i){t.onError(e,n,i)},complete:function(){t.onComplete()}}),!0},o.prototype.onSuccess=function(e){this.status=n.COMPLETED,this.widget.settings.onUploadSuccess.call(this.widget.element,this.id,e)},o.prototype.onError=function(e,t,i){this.status!==n.CANCELLED&&(this.status=n.FAILED,this.widget.settings.onUploadError.call(this.widget.element,this.id,e,t,i))},o.prototype.onComplete=function(){this.widget.activeFiles--,this.status!==n.CANCELLED&&this.widget.settings.onUploadComplete.call(this.widget.element,this.id),this.widget.queueRunning?this.widget.processQueue():this.widget.settings.queue&&0===this.widget.activeFiles&&this.widget.settings.onComplete.call(this.element)},o.prototype.getXhr=function(){var t=this,n=e.ajaxSettings.xhr();return n.upload&&n.upload.addEventListener("progress",(function(e){var n=0,i=e.loaded||e.position,o=e.total||e.totalSize;e.lengthComputable&&(n=Math.ceil(i/o*100)),t.widget.settings.onUploadProgress.call(t.widget.element,t.id,n)}),!1),n},o.prototype.cancel=function(e){e=void 0!==e&&e;var t=this.status;return!!(t===n.UPLOADING||e&&t===n.PENDING)&&(this.status=n.CANCELLED,this.widget.settings.onUploadCanceled.call(this.widget.element,this.id),t===n.UPLOADING&&this.jqXHR.abort(),!0)},o.prototype.canUpload=function(){return this.status===n.PENDING||this.status===n.FAILED};var s=function(t,n){return this.element=e(t),this.settings=e.extend({},i,n),this.checkSupport()?(this.init(),this):(e.error("Browser not supported by jQuery.dmUploader"),this.settings.onFallbackMode.call(this.element),!1)};s.prototype.checkSupport=function(){return void 0!==window.FormData&&(!new RegExp("/(Android (1.0|1.1|1.5|1.6|2.0|2.1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle/(1.0|2.0|2.5|3.0))/").test(window.navigator.userAgent)&&!e('<input type="file" />').prop("disabled"))},s.prototype.init=function(){var n=this;this.queue=[],this.queuePos=-1,this.queueRunning=!1,this.activeFiles=0,this.draggingOver=0,this.draggingOverDoc=0;var i=n.element.is("input[type=file]")?n.element:n.element.find("input[type=file]");return i.length>0&&(i.prop("multiple",this.settings.multiple),i.on("change."+t,(function(t){var i=t.target&&t.target.files;i&&i.length&&(n.addFiles(i),e(this).val(""))}))),this.settings.dnd&&this.initDnD(),0!==i.length||this.settings.dnd?(this.settings.onInit.call(this.element),this):(e.error("Markup error found by jQuery.dmUploader"),null)},s.prototype.initDnD=function(){var n=this;n.element.on("drop."+t,(function(e){e.preventDefault(),n.draggingOver>0&&(n.draggingOver=0,n.settings.onDragLeave.call(n.element));var t=e.originalEvent&&e.originalEvent.dataTransfer;if(t&&t.files&&t.files.length){var i=[];n.settings.multiple?i=t.files:i.push(t.files[0]),n.addFiles(i)}})),n.element.on("dragenter."+t,(function(e){e.preventDefault(),0===n.draggingOver&&n.settings.onDragEnter.call(n.element),n.draggingOver++})),n.element.on("dragleave."+t,(function(e){e.preventDefault(),n.draggingOver--,0===n.draggingOver&&n.settings.onDragLeave.call(n.element)})),n.settings.hookDocument&&(e(document).off("drop."+t).on("drop."+t,(function(e){e.preventDefault(),n.draggingOverDoc>0&&(n.draggingOverDoc=0,n.settings.onDocumentDragLeave.call(n.element))})),e(document).off("dragenter."+t).on("dragenter."+t,(function(e){e.preventDefault(),0===n.draggingOverDoc&&n.settings.onDocumentDragEnter.call(n.element),n.draggingOverDoc++})),e(document).off("dragleave."+t).on("dragleave."+t,(function(e){e.preventDefault(),n.draggingOverDoc--,0===n.draggingOverDoc&&n.settings.onDocumentDragLeave.call(n.element)})),e(document).off("dragover."+t).on("dragover."+t,(function(e){e.preventDefault()})))},s.prototype.releaseEvents=function(){this.element.off("."+t),this.element.find("input[type=file]").off("."+t),this.settings.hookDocument&&e(document).off("."+t)},s.prototype.validateFile=function(t){if(this.settings.maxFileSize>0&&t.size>this.settings.maxFileSize)return this.settings.onFileSizeError.call(this.element,t),!1;if("*"!==this.settings.allowedTypes&&!t.type.match(this.settings.allowedTypes))return this.settings.onFileTypeError.call(this.element,t),!1;if(null!==this.settings.extFilter){var n=t.name.toLowerCase().split(".").pop();if(e.inArray(n,this.settings.extFilter)<0)return this.settings.onFileExtError.call(this.element,t),!1}return new o(t,this)},s.prototype.addFiles=function(e){for(var t=0,n=0;n<e.length;n++){var i=this.validateFile(e[n]);if(i)!1!==this.settings.onNewFile.call(this.element,i.id,i.data)&&(this.settings.auto&&!this.settings.queue&&i.upload(),this.queue.push(i),t++)}return 0===t||this.settings.auto&&this.settings.queue&&!this.queueRunning&&this.processQueue(),this},s.prototype.processQueue=function(){return this.queuePos++,this.queuePos>=this.queue.length?(0===this.activeFiles&&this.settings.onComplete.call(this.element),this.queuePos=this.queue.length-1,this.queueRunning=!1,!1):(this.queueRunning=!0,this.queue[this.queuePos].upload())},s.prototype.restartQueue=function(){this.queuePos=-1,this.queueRunning=!1,this.processQueue()},s.prototype.findById=function(e){for(var t=!1,n=0;n<this.queue.length;n++)if(this.queue[n].id===e){t=this.queue[n];break}return t},s.prototype.cancelAll=function(){var e=this.queueRunning;this.queueRunning=!1;for(var t=0;t<this.queue.length;t++)this.queue[t].cancel();e&&this.settings.onComplete.call(this.element)},s.prototype.startAll=function(){if(this.settings.queue)this.restartQueue();else for(var e=0;e<this.queue.length;e++)this.queue[e].upload()},s.prototype.methods={start:function(t){if(this.queueRunning)return!1;var i=!1;return void 0===t||(i=this.findById(t))?i?(i.status===n.CANCELLED&&(i.status=n.PENDING),i.upload()):(this.startAll(),!0):(e.error("File not found in jQuery.dmUploader"),!1)},cancel:function(t){var n=!1;return void 0===t||(n=this.findById(t))?n?n.cancel(!0):(this.cancelAll(),!0):(e.error("File not found in jQuery.dmUploader"),!1)},reset:function(){return this.cancelAll(),this.queue=[],this.queuePos=-1,this.activeFiles=0,!0},destroy:function(){this.cancelAll(),this.releaseEvents(),this.element.removeData(t)}},e.fn.dmUploader=function(n){var i=arguments;if("string"!=typeof n)return this.each((function(){e.data(this,t)||e.data(this,t,new s(this,n))}));this.each((function(){var o=e.data(this,t);o instanceof s?"function"==typeof o.methods[n]?o.methods[n].apply(o,Array.prototype.slice.call(i,1)):e.error("Method "+n+" does not exist in jQuery.dmUploader"):e.error("Unknown plugin data found by jQuery.dmUploader")}))}},void 0===(s="function"==typeof i?i.apply(t,o):i)||(e.exports=s)}()},5311:function(e){"use strict";e.exports=jQuery}},t={};function n(i){var o=t[i];if(void 0!==o)return o.exports;var s=t[i]={exports:{}};return e[i](s,s.exports,n),s.exports}n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var i in t)n.o(t,i)&&!n.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){"use strict";n(6462);var e,t,i=n(5311);t='<svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="12" height="12"><path d="M49.6 158.4l104-108.8 358.4 352 356.8-352 105.6 105.6-352 356.8 352 355.2-102.4 107.2L512 620.8 155.2 974.4l-105.6-105.6L406.4 512z" p-id="3640" fill="#ffffff"></path></svg>',(e=i).fn.wprsAjaxUploader=function(){var n=this.data("settings"),i=this,o={url:i.find(".rs-uploader__shadow").data("url"),type:"POST",dataType:"json",maxFileSize:2e6,auto:!0,queue:!1,multiple:!0===i.data("multiple"),onBeforeUpload:function(){i.find(".js-uploader-message").empty()},onDragEnter:function(){i.addClass("active")},onDragLeave:function(){i.removeClass("active")},onUploadSuccess:function(e,n){var o=i.data("name"),s=!0===i.data("multiple"),r='<button type="button" class="rs-uploader__close" data-value='+n.id+">"+t+"</button>",a='<img src="'+n.thumb+'" alt="Thumbnail">';n.thumb||(a='<svg class="icon" style="" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="128" height="128"><defs><style type="text/css"></style></defs><path d="M768 426.666667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM341.333333 324.266667h170.666667a17.066667 17.066667 0 1 0 0-34.133334H341.333333a17.066667 17.066667 0 1 0 0 34.133334zM768 563.2H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333zM768 699.733333H341.333333a17.066667 17.066667 0 1 0 0 34.133334h426.666667a17.066667 17.066667 0 1 0 0-34.133334zM768 836.266667H341.333333a17.066667 17.066667 0 1 0 0 34.133333h426.666667a17.066667 17.066667 0 1 0 0-34.133333z" p-id="2458" fill="#666666"></path><path d="M836.266667 248.9344V0H102.4v938.666667h85.333333v85.333333h733.866667V334.267733l-85.333333-85.333333z m-153.6-105.335467l153.6 153.6L863.3344 324.266667H682.666667V143.598933zM136.533333 904.533333V34.133333h665.6v180.667734L672.6656 85.333333H187.733333v819.2H136.533333z m85.333334 85.333334V119.466667h426.666666v238.933333h238.933334v631.466667H221.866667z" p-id="2459" fill="#666666"></path></svg>'+n.title),i.find("input:text").filter((function(){return""===this.value})).remove(),s?(i.find(".rs-uploader__value").append('<input type="hidden" name="'+o+'" value="'+n.id+'">'),i.find(".rs-uploader__preview").show().append('<div class="rs-uploader__thumbnail">'+r+a+"</div>")):(i.find(".rs-uploader__text").hide(),i.find(".rs-uploader__button").hide(),i.find(".rs-uploader__value").empty().append('<input type="hidden" name="'+o+'" value="'+n.id+'">'),i.find(".rs-uploader__preview").empty().show().append('<div class="rs-uploader__thumbnail">'+r+a+"</div>"))},onUploadError:function(e,t,n,o){i.find(".js-uploader-message").html(wprsUploaderL10n.error)},onUploadComplete:function(e){i.find(".js-progress").remove()},onUploadCanceled:function(e){i.find(".js-uploader-message").html(wprsUploaderL10n.canceled)},onUploadProgress:function(t,n){i.find(".js-uploader-message").html(e('<div class="js-progress">').css("width",n+"%"))},onFileTypeError:function(e){i.find(".js-uploader-message").html(wprsUploaderL10n.file_type_error)},onFileSizeError:function(e){i.find(".js-uploader-message").html(wprsUploaderL10n.file_size_error)},onFileExtError:function(e){i.find(".js-uploader-message").html(wprsUploaderL10n.file_ext_error)}},s=e.extend({},o,n);i.dmUploader(s),e("body").on("click",".rs-uploader__close",(function(){var t=e(this).data("value"),n=e(this).closest(".js-uploader");!0===n.data("multiple")?n.find("input[value="+t+"]").remove():(n.find(".rs-uploader__value input").attr("value",""),n.show(),n.find(".rs-uploader__text").show(),n.find(".rs-uploader__button").show()),e(this).parent().remove()})),e("input[name=js-input-shadow]").each((function(){var t=e(this).closest(".js-uploader"),n=!0===t.data("multiple"),i=t.find(".rs-uploader__preview").children().length;!n&&i>0&&(t.find(".rs-uploader__text").hide(),e(this).parent().hide())}))},e(".rs-wp-uploader__button").on("click",(function(n){n.preventDefault();var i,o=e(this).next().attr("id"),s=e(this).closest(".rs-wp-uploader"),r=s.data("name"),a=!0===s.data("multiple");i||((i=wp.media.frames.file_frame=wp.media({title:wprsUploaderL10n.choose_image,button:{text:wprsUploaderL10n.insert_image},multiple:a})).on("select",(function(){var n=e("#"+o).parent();i.state().get("selection").toJSON().forEach((function(e){var i='<div class="rs-uploader__thumbnail"><button type="button" class="rs-uploader__close rs-wp-uploader__close" data-value='+e.id+">"+t+'</button><img src="'+e.url+'" alt="Thumbnail"></div>',o=n.find(".rs-uploader__preview"),s=n.find(".rs-uploader__value");n.find("input:text").remove(),a?(s.append('<input type="hidden" name="'+r+'" value="'+e.id+'">'),o.append(i).show()):(s.html('<input type="hidden" name="'+r+'" value="'+e.id+'">'),o.html(i).show())}))})),e(".rs-form--wp-uploader").on("click","button.rs-wp-uploader__close",(function(t){var n=e(this).data("value"),i=e("body").find(".rs-wp-uploader__field");console.log(i.find("input[value="+n+"]")),i.find("input[value="+n+"]").remove(),e(this).parent().remove()}))),i.open()}))}()}();