/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/scripts/modules/jq-signature.js":
/*!************************************************!*\
  !*** ./assets/scripts/modules/jq-signature.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var jq_signature__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jq-signature */ \"./node_modules/.pnpm/jq-signature@2.0.0/node_modules/jq-signature/jq-signature.js\");\n/* harmony import */ var jq_signature__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jq_signature__WEBPACK_IMPORTED_MODULE_0__);\n/* provided dependency */ var $ = __webpack_require__(/*! jquery */ \"jquery\");\n\n$.each($('.rs-form--signature input'), function (index, el) {\n  var _el = $(el),\n    pad = $('#js-' + _el.data('id'));\n  pad.jqSignature(_el.data('settings'));\n  pad.on('jq.signature.changed', function () {\n    _el.val(pad.jqSignature('getDataURL'));\n  });\n  _el.parents('.rs-form--signature').find('.rs-clear-signature').click(function () {\n    pad.jqSignature('clearCanvas');\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvc2NyaXB0cy9tb2R1bGVzL2pxLXNpZ25hdHVyZS5qcyIsIm1hcHBpbmdzIjoiOzs7O0FBQXNCO0FBRXRCQSxDQUFDLENBQUNDLElBQUksQ0FBQ0QsQ0FBQyxDQUFDLDJCQUEyQixDQUFDLEVBQUUsVUFBU0UsS0FBSyxFQUFFQyxFQUFFLEVBQUU7RUFDekQsSUFBSUMsR0FBRyxHQUFHSixDQUFDLENBQUNHLEVBQUUsQ0FBQztJQUNYRSxHQUFHLEdBQUdMLENBQUMsQ0FBQyxNQUFNLEdBQUdJLEdBQUcsQ0FBQ0UsSUFBSSxDQUFDLElBQUksQ0FBQyxDQUFDO0VBRXBDRCxHQUFHLENBQUNFLFdBQVcsQ0FBQ0gsR0FBRyxDQUFDRSxJQUFJLENBQUMsVUFBVSxDQUFDLENBQUM7RUFDckNELEdBQUcsQ0FBQ0csRUFBRSxDQUFDLHNCQUFzQixFQUFFLFlBQVc7SUFDeENKLEdBQUcsQ0FBQ0ssR0FBRyxDQUFDSixHQUFHLENBQUNFLFdBQVcsQ0FBQyxZQUFZLENBQUMsQ0FBQztFQUN4QyxDQUFDLENBQUM7RUFFRkgsR0FBRyxDQUFDTSxPQUFPLENBQUMscUJBQXFCLENBQUMsQ0FBQ0MsSUFBSSxDQUFDLHFCQUFxQixDQUFDLENBQUNDLEtBQUssQ0FBQyxZQUFXO0lBQzlFUCxHQUFHLENBQUNFLFdBQVcsQ0FBQyxhQUFhLENBQUM7RUFDaEMsQ0FBQyxDQUFDO0FBQ0osQ0FBQyxDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vd2VucHJpc2UtZnJvbnRlbmQtdG9vbC8uL2Fzc2V0cy9zY3JpcHRzL21vZHVsZXMvanEtc2lnbmF0dXJlLmpzPzkzMzciXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0ICdqcS1zaWduYXR1cmUnO1xuXG4kLmVhY2goJCgnLnJzLWZvcm0tLXNpZ25hdHVyZSBpbnB1dCcpLCBmdW5jdGlvbihpbmRleCwgZWwpIHtcbiAgdmFyIF9lbCA9ICQoZWwpLFxuICAgICAgcGFkID0gJCgnI2pzLScgKyBfZWwuZGF0YSgnaWQnKSk7XG5cbiAgcGFkLmpxU2lnbmF0dXJlKF9lbC5kYXRhKCdzZXR0aW5ncycpKTtcbiAgcGFkLm9uKCdqcS5zaWduYXR1cmUuY2hhbmdlZCcsIGZ1bmN0aW9uKCkge1xuICAgIF9lbC52YWwocGFkLmpxU2lnbmF0dXJlKCdnZXREYXRhVVJMJykpO1xuICB9KTtcblxuICBfZWwucGFyZW50cygnLnJzLWZvcm0tLXNpZ25hdHVyZScpLmZpbmQoJy5ycy1jbGVhci1zaWduYXR1cmUnKS5jbGljayhmdW5jdGlvbigpIHtcbiAgICBwYWQuanFTaWduYXR1cmUoJ2NsZWFyQ2FudmFzJyk7XG4gIH0pO1xufSk7Il0sIm5hbWVzIjpbIiQiLCJlYWNoIiwiaW5kZXgiLCJlbCIsIl9lbCIsInBhZCIsImRhdGEiLCJqcVNpZ25hdHVyZSIsIm9uIiwidmFsIiwicGFyZW50cyIsImZpbmQiLCJjbGljayJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./assets/scripts/modules/jq-signature.js\n");

/***/ }),

/***/ "./node_modules/.pnpm/jq-signature@2.0.0/node_modules/jq-signature/jq-signature.js":
/*!*****************************************************************************************!*\
  !*** ./node_modules/.pnpm/jq-signature@2.0.0/node_modules/jq-signature/jq-signature.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

eval("/* provided dependency */ var jQuery = __webpack_require__(/*! jquery */ \"jquery\");\n(function(window, document, $) {\n\t'use strict';\n  // Get a regular interval for drawing to the screen\n  window.requestAnimFrame = (function (callback) {\n    return window.requestAnimationFrame ||\n      window.webkitRequestAnimationFrame ||\n      window.mozRequestAnimationFrame ||\n      window.oRequestAnimationFrame ||\n      window.msRequestAnimaitonFrame ||\n      function (callback) {\n        window.setTimeout(callback, 1000/60);\n      };\n  })();\n\n  /*\n  * Plugin Constructor\n  */\n\n  var pluginName = 'jqSignature',\n\t    defaults = {\n\t      lineColor: '#222222',\n\t      lineWidth: 1,\n\t      border: '1px dashed #AAAAAA',\n\t      background: '#FFFFFF',\n\t      width: 300,\n\t      height: 100,\n\t      autoFit: false\n\t    },\n\t    canvasFixture = '<canvas></canvas>',\n\t\t\tidCounter = 0;\n\n  function Signature(element, options) {\n    // DOM elements/objects\n    this.element = element;\n    this.$element = $(this.element);\n    this.canvas = false;\n    this.$canvas = false;\n    this.ctx = false;\n    // Drawing state\n    this.drawing = false;\n    this.currentPos = {\n      x: 0,\n      y: 0\n    };\n    this.lastPos = this.currentPos;\n    // Determine plugin settings\n    this._data = this.$element.data();\n    this.settings = $.extend({}, defaults, options, this._data);\n    // Initialize the plugin\n    this.init();\n  }\n\n  Signature.prototype = {\n\n    // Initialize the signature canvas\n    init: function() {\n\t\t\tthis.id = 'jq-signature-canvas-' + (++idCounter);\n\n      // Set up the canvas\n      this.$canvas = $(canvasFixture).appendTo(this.$element);\n      this.$canvas.attr({\n        width: this.settings.width,\n        height: this.settings.height\n      });\n      this.$canvas.css({\n        boxSizing: 'border-box',\n        width: this.settings.width + 'px',\n        height: this.settings.height + 'px',\n        border: this.settings.border,\n        background: this.settings.background,\n        cursor: 'crosshair'\n      });\n      this.$canvas.attr('id', this.id);\n\n      // Fit canvas to width of parent\n      if (this.settings.autoFit === true) {\n        this._resizeCanvas();\n        // TODO - allow for dynamic canvas resizing\n        // (need to save canvas state before changing width to avoid getting cleared)\n        // var timeout = false;\n        // $(window).on('resize', $.proxy(function(e) {\n        //   clearTimeout(timeout);\n        //   timeout = setTimeout($.proxy(this._resizeCanvas, this), 250);\n        // }, this));\n      }\n      this.canvas = this.$canvas[0];\n      this._resetCanvas();\n\n\t\t\t// Listen for pointer/mouse/touch events\n\t\t\t// TODO - PointerEvent isn't fully supported, but eventually do something like this:\n\t\t\t// if (window.PointerEvent) {\n\t\t\t// \tthis.$canvas.parent().css('-ms-touch-action', 'none');\n\t\t\t// \tthis.$canvas.on(\"pointerdown MSPointerDown\", $.proxy(this._downHandler, this));\n      //   this.$canvas.on(\"pointermove MSPointerMove\", $.proxy(this._moveHandler, this));\n\t\t\t// \tthis.$canvas.on(\"pointerup MSPointerUp\", $.proxy(this._upHandler, this));\n      // }\n      // else {\n      //   this.$canvas.on('mousedown touchstart', $.proxy(this._downHandler, this));\n      //   this.$canvas.on('mousemove touchmove', $.proxy(this._moveHandler, this));\n      //   this.$canvas.on('mouseup touchend', $.proxy(this._upHandler, this));\n      // }\n      this.$canvas.on('mousedown touchstart', $.proxy(this._downHandler, this));\n      this.$canvas.on('mousemove touchmove', $.proxy(this._moveHandler, this));\n      this.$canvas.on('mouseup touchend', $.proxy(this._upHandler, this));\n\n      // Start drawing\n      var that = this;\n      (function drawLoop() {\n        window.requestAnimFrame(drawLoop);\n        that._renderCanvas();\n      })();\n    },\n\n    // Clear the canvas\n    clearCanvas: function() {\n      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);\n      this._resetCanvas();\n    },\n\n    // Get the content of the canvas as a base64 data URL\n    getDataURL: function() {\n      return this.canvas.toDataURL();\n    },\n\n\t\t// Handle the start of a signature\n\t\t_downHandler: function (e) {\n\t\t\tthis.drawing = true;\n\t\t\tthis.lastPos = this.currentPos = this._getPosition(e);\n\t\t\t// Prevent scrolling, etc\n\t\t\t$('body').css('overflow', 'hidden');\n\t\t\te.preventDefault();\n\t\t},\n\n\t\t// Handle mouse/touch moves during a signature\n\t\t_moveHandler: function (e) {\n\t\t\tthis.currentPos = this._getPosition(e);\n\t\t\te.preventDefault();\n\t\t},\n\n\t\t// Handle the end of a signature\n\t\t_upHandler: function (e) {\n\t\t\tthis.drawing = false;\n\t\t\t// Trigger a change event\n\t\t\tvar changedEvent = $.Event('jq.signature.changed');\n\t\t\tthis.$element.trigger(changedEvent);\n\t\t\t// Allow scrolling again\n\t\t\t$('body').css('overflow', 'auto');\n\t\t\te.preventDefault();\n\t\t},\n\n    // Get the position of the mouse/touch\n    _getPosition: function (event) {\n      var xPos, yPos, rect;\n      rect = this.canvas.getBoundingClientRect();\n      if (event.originalEvent)\n          event = event.originalEvent;\n\n      // Touch event\n      if (event.type.indexOf('touch') !== -1) { // event.constructor === TouchEvent\n        xPos = event.touches[0].clientX - rect.left;\n        yPos = event.touches[0].clientY - rect.top;\n      }\n      // Mouse event\n      else {\n        xPos = event.clientX - rect.left;\n        yPos = event.clientY - rect.top;\n      }\n      return {\n        x: xPos,\n        y: yPos\n      };\n    },\n\n    // Render the signature to the canvas\n    _renderCanvas: function() {\n        if (this.drawing) {\n        this.ctx.beginPath();\n        this.ctx.moveTo(this.lastPos.x, this.lastPos.y);\n        this.ctx.lineTo(this.currentPos.x, this.currentPos.y);\n        this.ctx.stroke();\n        this.lastPos = this.currentPos;\n      }\n    },\n\n    // Reset the canvas context\n    _resetCanvas: function() {\n      this.ctx = this.canvas.getContext(\"2d\");\n      this.ctx.strokeStyle = this.settings.lineColor;\n      this.ctx.lineWidth = this.settings.lineWidth;\n    },\n\n    // Resize the canvas element\n    _resizeCanvas: function() {\n      var width = this.$element.outerWidth();\n      this.$canvas.attr('width', width);\n      this.$canvas.css('width', width + 'px');\n    }\n\n  };\n\n  /*\n  * Plugin wrapper and initialization\n  */\n\n  $.fn[pluginName] = function ( options ) {\n    var args = arguments;\n    if (options === undefined || typeof options === 'object') {\n      return this.each(function () {\n        if (!$.data(this, 'plugin_' + pluginName)) {\n          $.data(this, 'plugin_' + pluginName, new Signature( this, options ));\n        }\n      });\n    }\n    else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {\n      var returns;\n      this.each(function () {\n        var instance = $.data(this, 'plugin_' + pluginName);\n        if (instance instanceof Signature && typeof instance[options] === 'function') {\n          returns = instance[options].apply( instance, Array.prototype.slice.call( args, 1 ) );\n        }\n        if (options === 'destroy') {\n          $.data(this, 'plugin_' + pluginName, null);\n        }\n      });\n      return returns !== undefined ? returns : this;\n    }\n  };\n\n})(window, document, jQuery);\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9ub2RlX21vZHVsZXMvLnBucG0vanEtc2lnbmF0dXJlQDIuMC4wL25vZGVfbW9kdWxlcy9qcS1zaWduYXR1cmUvanEtc2lnbmF0dXJlLmpzIiwibWFwcGluZ3MiOiI7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHOztBQUVIO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxNQUFNO0FBQ047QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLCtCQUErQjtBQUMvQjtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxPQUFPO0FBQ1A7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxPQUFPO0FBQ1A7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsWUFBWTtBQUNaO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsT0FBTztBQUNQLEtBQUs7O0FBRUw7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLOztBQUVMO0FBQ0E7QUFDQTtBQUNBLEtBQUs7O0FBRUw7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHOztBQUVIO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsR0FBRzs7QUFFSDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHOztBQUVIO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLGdEQUFnRDtBQUNoRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLOztBQUVMO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7O0FBRUw7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7O0FBRUw7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLE9BQU87QUFDUDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsT0FBTztBQUNQO0FBQ0E7QUFDQTs7QUFFQSxDQUFDLG9CQUFvQixNQUFNIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vd2VucHJpc2UtZnJvbnRlbmQtdG9vbC8uL25vZGVfbW9kdWxlcy8ucG5wbS9qcS1zaWduYXR1cmVAMi4wLjAvbm9kZV9tb2R1bGVzL2pxLXNpZ25hdHVyZS9qcS1zaWduYXR1cmUuanM/YWNlYiJdLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24od2luZG93LCBkb2N1bWVudCwgJCkge1xuXHQndXNlIHN0cmljdCc7XG4gIC8vIEdldCBhIHJlZ3VsYXIgaW50ZXJ2YWwgZm9yIGRyYXdpbmcgdG8gdGhlIHNjcmVlblxuICB3aW5kb3cucmVxdWVzdEFuaW1GcmFtZSA9IChmdW5jdGlvbiAoY2FsbGJhY2spIHtcbiAgICByZXR1cm4gd2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZSB8fFxuICAgICAgd2luZG93LndlYmtpdFJlcXVlc3RBbmltYXRpb25GcmFtZSB8fFxuICAgICAgd2luZG93Lm1velJlcXVlc3RBbmltYXRpb25GcmFtZSB8fFxuICAgICAgd2luZG93Lm9SZXF1ZXN0QW5pbWF0aW9uRnJhbWUgfHxcbiAgICAgIHdpbmRvdy5tc1JlcXVlc3RBbmltYWl0b25GcmFtZSB8fFxuICAgICAgZnVuY3Rpb24gKGNhbGxiYWNrKSB7XG4gICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGNhbGxiYWNrLCAxMDAwLzYwKTtcbiAgICAgIH07XG4gIH0pKCk7XG5cbiAgLypcbiAgKiBQbHVnaW4gQ29uc3RydWN0b3JcbiAgKi9cblxuICB2YXIgcGx1Z2luTmFtZSA9ICdqcVNpZ25hdHVyZScsXG5cdCAgICBkZWZhdWx0cyA9IHtcblx0ICAgICAgbGluZUNvbG9yOiAnIzIyMjIyMicsXG5cdCAgICAgIGxpbmVXaWR0aDogMSxcblx0ICAgICAgYm9yZGVyOiAnMXB4IGRhc2hlZCAjQUFBQUFBJyxcblx0ICAgICAgYmFja2dyb3VuZDogJyNGRkZGRkYnLFxuXHQgICAgICB3aWR0aDogMzAwLFxuXHQgICAgICBoZWlnaHQ6IDEwMCxcblx0ICAgICAgYXV0b0ZpdDogZmFsc2Vcblx0ICAgIH0sXG5cdCAgICBjYW52YXNGaXh0dXJlID0gJzxjYW52YXM+PC9jYW52YXM+Jyxcblx0XHRcdGlkQ291bnRlciA9IDA7XG5cbiAgZnVuY3Rpb24gU2lnbmF0dXJlKGVsZW1lbnQsIG9wdGlvbnMpIHtcbiAgICAvLyBET00gZWxlbWVudHMvb2JqZWN0c1xuICAgIHRoaXMuZWxlbWVudCA9IGVsZW1lbnQ7XG4gICAgdGhpcy4kZWxlbWVudCA9ICQodGhpcy5lbGVtZW50KTtcbiAgICB0aGlzLmNhbnZhcyA9IGZhbHNlO1xuICAgIHRoaXMuJGNhbnZhcyA9IGZhbHNlO1xuICAgIHRoaXMuY3R4ID0gZmFsc2U7XG4gICAgLy8gRHJhd2luZyBzdGF0ZVxuICAgIHRoaXMuZHJhd2luZyA9IGZhbHNlO1xuICAgIHRoaXMuY3VycmVudFBvcyA9IHtcbiAgICAgIHg6IDAsXG4gICAgICB5OiAwXG4gICAgfTtcbiAgICB0aGlzLmxhc3RQb3MgPSB0aGlzLmN1cnJlbnRQb3M7XG4gICAgLy8gRGV0ZXJtaW5lIHBsdWdpbiBzZXR0aW5nc1xuICAgIHRoaXMuX2RhdGEgPSB0aGlzLiRlbGVtZW50LmRhdGEoKTtcbiAgICB0aGlzLnNldHRpbmdzID0gJC5leHRlbmQoe30sIGRlZmF1bHRzLCBvcHRpb25zLCB0aGlzLl9kYXRhKTtcbiAgICAvLyBJbml0aWFsaXplIHRoZSBwbHVnaW5cbiAgICB0aGlzLmluaXQoKTtcbiAgfVxuXG4gIFNpZ25hdHVyZS5wcm90b3R5cGUgPSB7XG5cbiAgICAvLyBJbml0aWFsaXplIHRoZSBzaWduYXR1cmUgY2FudmFzXG4gICAgaW5pdDogZnVuY3Rpb24oKSB7XG5cdFx0XHR0aGlzLmlkID0gJ2pxLXNpZ25hdHVyZS1jYW52YXMtJyArICgrK2lkQ291bnRlcik7XG5cbiAgICAgIC8vIFNldCB1cCB0aGUgY2FudmFzXG4gICAgICB0aGlzLiRjYW52YXMgPSAkKGNhbnZhc0ZpeHR1cmUpLmFwcGVuZFRvKHRoaXMuJGVsZW1lbnQpO1xuICAgICAgdGhpcy4kY2FudmFzLmF0dHIoe1xuICAgICAgICB3aWR0aDogdGhpcy5zZXR0aW5ncy53aWR0aCxcbiAgICAgICAgaGVpZ2h0OiB0aGlzLnNldHRpbmdzLmhlaWdodFxuICAgICAgfSk7XG4gICAgICB0aGlzLiRjYW52YXMuY3NzKHtcbiAgICAgICAgYm94U2l6aW5nOiAnYm9yZGVyLWJveCcsXG4gICAgICAgIHdpZHRoOiB0aGlzLnNldHRpbmdzLndpZHRoICsgJ3B4JyxcbiAgICAgICAgaGVpZ2h0OiB0aGlzLnNldHRpbmdzLmhlaWdodCArICdweCcsXG4gICAgICAgIGJvcmRlcjogdGhpcy5zZXR0aW5ncy5ib3JkZXIsXG4gICAgICAgIGJhY2tncm91bmQ6IHRoaXMuc2V0dGluZ3MuYmFja2dyb3VuZCxcbiAgICAgICAgY3Vyc29yOiAnY3Jvc3NoYWlyJ1xuICAgICAgfSk7XG4gICAgICB0aGlzLiRjYW52YXMuYXR0cignaWQnLCB0aGlzLmlkKTtcblxuICAgICAgLy8gRml0IGNhbnZhcyB0byB3aWR0aCBvZiBwYXJlbnRcbiAgICAgIGlmICh0aGlzLnNldHRpbmdzLmF1dG9GaXQgPT09IHRydWUpIHtcbiAgICAgICAgdGhpcy5fcmVzaXplQ2FudmFzKCk7XG4gICAgICAgIC8vIFRPRE8gLSBhbGxvdyBmb3IgZHluYW1pYyBjYW52YXMgcmVzaXppbmdcbiAgICAgICAgLy8gKG5lZWQgdG8gc2F2ZSBjYW52YXMgc3RhdGUgYmVmb3JlIGNoYW5naW5nIHdpZHRoIHRvIGF2b2lkIGdldHRpbmcgY2xlYXJlZClcbiAgICAgICAgLy8gdmFyIHRpbWVvdXQgPSBmYWxzZTtcbiAgICAgICAgLy8gJCh3aW5kb3cpLm9uKCdyZXNpemUnLCAkLnByb3h5KGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgLy8gICBjbGVhclRpbWVvdXQodGltZW91dCk7XG4gICAgICAgIC8vICAgdGltZW91dCA9IHNldFRpbWVvdXQoJC5wcm94eSh0aGlzLl9yZXNpemVDYW52YXMsIHRoaXMpLCAyNTApO1xuICAgICAgICAvLyB9LCB0aGlzKSk7XG4gICAgICB9XG4gICAgICB0aGlzLmNhbnZhcyA9IHRoaXMuJGNhbnZhc1swXTtcbiAgICAgIHRoaXMuX3Jlc2V0Q2FudmFzKCk7XG5cblx0XHRcdC8vIExpc3RlbiBmb3IgcG9pbnRlci9tb3VzZS90b3VjaCBldmVudHNcblx0XHRcdC8vIFRPRE8gLSBQb2ludGVyRXZlbnQgaXNuJ3QgZnVsbHkgc3VwcG9ydGVkLCBidXQgZXZlbnR1YWxseSBkbyBzb21ldGhpbmcgbGlrZSB0aGlzOlxuXHRcdFx0Ly8gaWYgKHdpbmRvdy5Qb2ludGVyRXZlbnQpIHtcblx0XHRcdC8vIFx0dGhpcy4kY2FudmFzLnBhcmVudCgpLmNzcygnLW1zLXRvdWNoLWFjdGlvbicsICdub25lJyk7XG5cdFx0XHQvLyBcdHRoaXMuJGNhbnZhcy5vbihcInBvaW50ZXJkb3duIE1TUG9pbnRlckRvd25cIiwgJC5wcm94eSh0aGlzLl9kb3duSGFuZGxlciwgdGhpcykpO1xuICAgICAgLy8gICB0aGlzLiRjYW52YXMub24oXCJwb2ludGVybW92ZSBNU1BvaW50ZXJNb3ZlXCIsICQucHJveHkodGhpcy5fbW92ZUhhbmRsZXIsIHRoaXMpKTtcblx0XHRcdC8vIFx0dGhpcy4kY2FudmFzLm9uKFwicG9pbnRlcnVwIE1TUG9pbnRlclVwXCIsICQucHJveHkodGhpcy5fdXBIYW5kbGVyLCB0aGlzKSk7XG4gICAgICAvLyB9XG4gICAgICAvLyBlbHNlIHtcbiAgICAgIC8vICAgdGhpcy4kY2FudmFzLm9uKCdtb3VzZWRvd24gdG91Y2hzdGFydCcsICQucHJveHkodGhpcy5fZG93bkhhbmRsZXIsIHRoaXMpKTtcbiAgICAgIC8vICAgdGhpcy4kY2FudmFzLm9uKCdtb3VzZW1vdmUgdG91Y2htb3ZlJywgJC5wcm94eSh0aGlzLl9tb3ZlSGFuZGxlciwgdGhpcykpO1xuICAgICAgLy8gICB0aGlzLiRjYW52YXMub24oJ21vdXNldXAgdG91Y2hlbmQnLCAkLnByb3h5KHRoaXMuX3VwSGFuZGxlciwgdGhpcykpO1xuICAgICAgLy8gfVxuICAgICAgdGhpcy4kY2FudmFzLm9uKCdtb3VzZWRvd24gdG91Y2hzdGFydCcsICQucHJveHkodGhpcy5fZG93bkhhbmRsZXIsIHRoaXMpKTtcbiAgICAgIHRoaXMuJGNhbnZhcy5vbignbW91c2Vtb3ZlIHRvdWNobW92ZScsICQucHJveHkodGhpcy5fbW92ZUhhbmRsZXIsIHRoaXMpKTtcbiAgICAgIHRoaXMuJGNhbnZhcy5vbignbW91c2V1cCB0b3VjaGVuZCcsICQucHJveHkodGhpcy5fdXBIYW5kbGVyLCB0aGlzKSk7XG5cbiAgICAgIC8vIFN0YXJ0IGRyYXdpbmdcbiAgICAgIHZhciB0aGF0ID0gdGhpcztcbiAgICAgIChmdW5jdGlvbiBkcmF3TG9vcCgpIHtcbiAgICAgICAgd2luZG93LnJlcXVlc3RBbmltRnJhbWUoZHJhd0xvb3ApO1xuICAgICAgICB0aGF0Ll9yZW5kZXJDYW52YXMoKTtcbiAgICAgIH0pKCk7XG4gICAgfSxcblxuICAgIC8vIENsZWFyIHRoZSBjYW52YXNcbiAgICBjbGVhckNhbnZhczogZnVuY3Rpb24oKSB7XG4gICAgICB0aGlzLmN0eC5jbGVhclJlY3QoMCwgMCwgdGhpcy5jYW52YXMud2lkdGgsIHRoaXMuY2FudmFzLmhlaWdodCk7XG4gICAgICB0aGlzLl9yZXNldENhbnZhcygpO1xuICAgIH0sXG5cbiAgICAvLyBHZXQgdGhlIGNvbnRlbnQgb2YgdGhlIGNhbnZhcyBhcyBhIGJhc2U2NCBkYXRhIFVSTFxuICAgIGdldERhdGFVUkw6IGZ1bmN0aW9uKCkge1xuICAgICAgcmV0dXJuIHRoaXMuY2FudmFzLnRvRGF0YVVSTCgpO1xuICAgIH0sXG5cblx0XHQvLyBIYW5kbGUgdGhlIHN0YXJ0IG9mIGEgc2lnbmF0dXJlXG5cdFx0X2Rvd25IYW5kbGVyOiBmdW5jdGlvbiAoZSkge1xuXHRcdFx0dGhpcy5kcmF3aW5nID0gdHJ1ZTtcblx0XHRcdHRoaXMubGFzdFBvcyA9IHRoaXMuY3VycmVudFBvcyA9IHRoaXMuX2dldFBvc2l0aW9uKGUpO1xuXHRcdFx0Ly8gUHJldmVudCBzY3JvbGxpbmcsIGV0Y1xuXHRcdFx0JCgnYm9keScpLmNzcygnb3ZlcmZsb3cnLCAnaGlkZGVuJyk7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0fSxcblxuXHRcdC8vIEhhbmRsZSBtb3VzZS90b3VjaCBtb3ZlcyBkdXJpbmcgYSBzaWduYXR1cmVcblx0XHRfbW92ZUhhbmRsZXI6IGZ1bmN0aW9uIChlKSB7XG5cdFx0XHR0aGlzLmN1cnJlbnRQb3MgPSB0aGlzLl9nZXRQb3NpdGlvbihlKTtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR9LFxuXG5cdFx0Ly8gSGFuZGxlIHRoZSBlbmQgb2YgYSBzaWduYXR1cmVcblx0XHRfdXBIYW5kbGVyOiBmdW5jdGlvbiAoZSkge1xuXHRcdFx0dGhpcy5kcmF3aW5nID0gZmFsc2U7XG5cdFx0XHQvLyBUcmlnZ2VyIGEgY2hhbmdlIGV2ZW50XG5cdFx0XHR2YXIgY2hhbmdlZEV2ZW50ID0gJC5FdmVudCgnanEuc2lnbmF0dXJlLmNoYW5nZWQnKTtcblx0XHRcdHRoaXMuJGVsZW1lbnQudHJpZ2dlcihjaGFuZ2VkRXZlbnQpO1xuXHRcdFx0Ly8gQWxsb3cgc2Nyb2xsaW5nIGFnYWluXG5cdFx0XHQkKCdib2R5JykuY3NzKCdvdmVyZmxvdycsICdhdXRvJyk7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0fSxcblxuICAgIC8vIEdldCB0aGUgcG9zaXRpb24gb2YgdGhlIG1vdXNlL3RvdWNoXG4gICAgX2dldFBvc2l0aW9uOiBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgIHZhciB4UG9zLCB5UG9zLCByZWN0O1xuICAgICAgcmVjdCA9IHRoaXMuY2FudmFzLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgICAgaWYgKGV2ZW50Lm9yaWdpbmFsRXZlbnQpXG4gICAgICAgICAgZXZlbnQgPSBldmVudC5vcmlnaW5hbEV2ZW50O1xuXG4gICAgICAvLyBUb3VjaCBldmVudFxuICAgICAgaWYgKGV2ZW50LnR5cGUuaW5kZXhPZigndG91Y2gnKSAhPT0gLTEpIHsgLy8gZXZlbnQuY29uc3RydWN0b3IgPT09IFRvdWNoRXZlbnRcbiAgICAgICAgeFBvcyA9IGV2ZW50LnRvdWNoZXNbMF0uY2xpZW50WCAtIHJlY3QubGVmdDtcbiAgICAgICAgeVBvcyA9IGV2ZW50LnRvdWNoZXNbMF0uY2xpZW50WSAtIHJlY3QudG9wO1xuICAgICAgfVxuICAgICAgLy8gTW91c2UgZXZlbnRcbiAgICAgIGVsc2Uge1xuICAgICAgICB4UG9zID0gZXZlbnQuY2xpZW50WCAtIHJlY3QubGVmdDtcbiAgICAgICAgeVBvcyA9IGV2ZW50LmNsaWVudFkgLSByZWN0LnRvcDtcbiAgICAgIH1cbiAgICAgIHJldHVybiB7XG4gICAgICAgIHg6IHhQb3MsXG4gICAgICAgIHk6IHlQb3NcbiAgICAgIH07XG4gICAgfSxcblxuICAgIC8vIFJlbmRlciB0aGUgc2lnbmF0dXJlIHRvIHRoZSBjYW52YXNcbiAgICBfcmVuZGVyQ2FudmFzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKHRoaXMuZHJhd2luZykge1xuICAgICAgICB0aGlzLmN0eC5iZWdpblBhdGgoKTtcbiAgICAgICAgdGhpcy5jdHgubW92ZVRvKHRoaXMubGFzdFBvcy54LCB0aGlzLmxhc3RQb3MueSk7XG4gICAgICAgIHRoaXMuY3R4LmxpbmVUbyh0aGlzLmN1cnJlbnRQb3MueCwgdGhpcy5jdXJyZW50UG9zLnkpO1xuICAgICAgICB0aGlzLmN0eC5zdHJva2UoKTtcbiAgICAgICAgdGhpcy5sYXN0UG9zID0gdGhpcy5jdXJyZW50UG9zO1xuICAgICAgfVxuICAgIH0sXG5cbiAgICAvLyBSZXNldCB0aGUgY2FudmFzIGNvbnRleHRcbiAgICBfcmVzZXRDYW52YXM6IGZ1bmN0aW9uKCkge1xuICAgICAgdGhpcy5jdHggPSB0aGlzLmNhbnZhcy5nZXRDb250ZXh0KFwiMmRcIik7XG4gICAgICB0aGlzLmN0eC5zdHJva2VTdHlsZSA9IHRoaXMuc2V0dGluZ3MubGluZUNvbG9yO1xuICAgICAgdGhpcy5jdHgubGluZVdpZHRoID0gdGhpcy5zZXR0aW5ncy5saW5lV2lkdGg7XG4gICAgfSxcblxuICAgIC8vIFJlc2l6ZSB0aGUgY2FudmFzIGVsZW1lbnRcbiAgICBfcmVzaXplQ2FudmFzOiBmdW5jdGlvbigpIHtcbiAgICAgIHZhciB3aWR0aCA9IHRoaXMuJGVsZW1lbnQub3V0ZXJXaWR0aCgpO1xuICAgICAgdGhpcy4kY2FudmFzLmF0dHIoJ3dpZHRoJywgd2lkdGgpO1xuICAgICAgdGhpcy4kY2FudmFzLmNzcygnd2lkdGgnLCB3aWR0aCArICdweCcpO1xuICAgIH1cblxuICB9O1xuXG4gIC8qXG4gICogUGx1Z2luIHdyYXBwZXIgYW5kIGluaXRpYWxpemF0aW9uXG4gICovXG5cbiAgJC5mbltwbHVnaW5OYW1lXSA9IGZ1bmN0aW9uICggb3B0aW9ucyApIHtcbiAgICB2YXIgYXJncyA9IGFyZ3VtZW50cztcbiAgICBpZiAob3B0aW9ucyA9PT0gdW5kZWZpbmVkIHx8IHR5cGVvZiBvcHRpb25zID09PSAnb2JqZWN0Jykge1xuICAgICAgcmV0dXJuIHRoaXMuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgIGlmICghJC5kYXRhKHRoaXMsICdwbHVnaW5fJyArIHBsdWdpbk5hbWUpKSB7XG4gICAgICAgICAgJC5kYXRhKHRoaXMsICdwbHVnaW5fJyArIHBsdWdpbk5hbWUsIG5ldyBTaWduYXR1cmUoIHRoaXMsIG9wdGlvbnMgKSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cbiAgICBlbHNlIGlmICh0eXBlb2Ygb3B0aW9ucyA9PT0gJ3N0cmluZycgJiYgb3B0aW9uc1swXSAhPT0gJ18nICYmIG9wdGlvbnMgIT09ICdpbml0Jykge1xuICAgICAgdmFyIHJldHVybnM7XG4gICAgICB0aGlzLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICB2YXIgaW5zdGFuY2UgPSAkLmRhdGEodGhpcywgJ3BsdWdpbl8nICsgcGx1Z2luTmFtZSk7XG4gICAgICAgIGlmIChpbnN0YW5jZSBpbnN0YW5jZW9mIFNpZ25hdHVyZSAmJiB0eXBlb2YgaW5zdGFuY2Vbb3B0aW9uc10gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICByZXR1cm5zID0gaW5zdGFuY2Vbb3B0aW9uc10uYXBwbHkoIGluc3RhbmNlLCBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbCggYXJncywgMSApICk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG9wdGlvbnMgPT09ICdkZXN0cm95Jykge1xuICAgICAgICAgICQuZGF0YSh0aGlzLCAncGx1Z2luXycgKyBwbHVnaW5OYW1lLCBudWxsKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgICByZXR1cm4gcmV0dXJucyAhPT0gdW5kZWZpbmVkID8gcmV0dXJucyA6IHRoaXM7XG4gICAgfVxuICB9O1xuXG59KSh3aW5kb3csIGRvY3VtZW50LCBqUXVlcnkpO1xuIl0sIm5hbWVzIjpbXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./node_modules/.pnpm/jq-signature@2.0.0/node_modules/jq-signature/jq-signature.js\n");

/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = jQuery;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./assets/scripts/modules/jq-signature.js");
/******/ 	
/******/ })()
;