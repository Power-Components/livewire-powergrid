/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/components/index.js":
/*!******************************************!*\
  !*** ./resources/js/components/index.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _pg_multi_select__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./pg-multi-select */ "./resources/js/components/pg-multi-select.js");
/* harmony import */ var _pg_toggleable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./pg-toggleable */ "./resources/js/components/pg-toggleable.js");
/* harmony import */ var _pg_multi_select_bs5__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./pg-multi-select-bs5 */ "./resources/js/components/pg-multi-select-bs5.js");
/* harmony import */ var _pg_flat_pickr__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./pg-flat-pickr */ "./resources/js/components/pg-flat-pickr.js");
/* harmony import */ var _pg_editable__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./pg-editable */ "./resources/js/components/pg-editable.js");
/* harmony import */ var _pg_copy_to_clipboard__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./pg-copy-to-clipboard */ "./resources/js/components/pg-copy-to-clipboard.js");






window.pgMultiSelect = _pg_multi_select__WEBPACK_IMPORTED_MODULE_0__["default"];
window.pgToggleable = _pg_toggleable__WEBPACK_IMPORTED_MODULE_1__["default"];
window.pgMultiSelectBs5 = _pg_multi_select_bs5__WEBPACK_IMPORTED_MODULE_2__["default"];
window.pgFlatPickr = _pg_flat_pickr__WEBPACK_IMPORTED_MODULE_3__["default"];
window.pgEditable = _pg_editable__WEBPACK_IMPORTED_MODULE_4__["default"];
document.addEventListener('alpine:init', function () {
  window.Alpine.data('pgMultiSelect', _pg_multi_select__WEBPACK_IMPORTED_MODULE_0__["default"]);
  window.Alpine.data('pgToggleable', _pg_toggleable__WEBPACK_IMPORTED_MODULE_1__["default"]);
  window.Alpine.data('pgMultiSelectBs5', _pg_multi_select_bs5__WEBPACK_IMPORTED_MODULE_2__["default"]);
  window.Alpine.data('pgFlatPickr', _pg_flat_pickr__WEBPACK_IMPORTED_MODULE_3__["default"]);
  window.Alpine.data('phEditable', _pg_editable__WEBPACK_IMPORTED_MODULE_4__["default"]);
  window.Alpine.plugin(_pg_copy_to_clipboard__WEBPACK_IMPORTED_MODULE_5__["default"]);
});

/***/ }),

/***/ "./resources/js/components/pg-copy-to-clipboard.js":
/*!*********************************************************!*\
  !*** ./resources/js/components/pg-copy-to-clipboard.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

var onCopy = function onCopy() {};

function PgClipboard(Alpine) {
  Alpine.magic('pgClipboard', function () {
    return function (target) {
      if (typeof target === 'function') {
        target = target();
      }

      if (_typeof(target) === 'object') {
        target = JSON.stringify(target);
      }

      return window.navigator.clipboard.writeText(target).then(onCopy);
    };
  });
}

PgClipboard.configure = function (config) {
  if (config.hasOwnProperty('onCopy') && typeof config.onCopy === 'function') {
    onCopy = config.onCopy;
  }

  return PgClipboard;
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (PgClipboard);

/***/ }),

/***/ "./resources/js/components/pg-editable.js":
/*!************************************************!*\
  !*** ./resources/js/components/pg-editable.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$tableName, _params$id, _params$dataField;

  return {
    editable: false,
    tableName: (_params$tableName = params.tableName) !== null && _params$tableName !== void 0 ? _params$tableName : null,
    id: (_params$id = params.id) !== null && _params$id !== void 0 ? _params$id : null,
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    content: params.content,
    save: function save() {
      document.getElementsByClassName('message')[0].style.display = "none";
      this.$wire.emit('pg:editable-' + this.tableName, {
        id: this.id,
        value: this.$el.value,
        field: this.dataField
      });
      this.editable = false;
      this.content = this.htmlSpecialChars(this.$el.value);
    },
    htmlSpecialChars: function htmlSpecialChars(string) {
      var el = document.createElement('div');
      el.innerText = string;
      return el.innerHTML;
    }
  };
});

/***/ }),

/***/ "./resources/js/components/pg-flat-pickr.js":
/*!**************************************************!*\
  !*** ./resources/js/components/pg-flat-pickr.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$label, _params$locale, _params$onlyFuture, _params$noWeekEnds, _params$customConfig;

  return {
    dataField: params.dataField,
    tableName: params.tableName,
    filterKey: params.filterKey,
    label: (_params$label = params.label) !== null && _params$label !== void 0 ? _params$label : null,
    locale: (_params$locale = params.locale) !== null && _params$locale !== void 0 ? _params$locale : 'en',
    onlyFuture: (_params$onlyFuture = params.onlyFuture) !== null && _params$onlyFuture !== void 0 ? _params$onlyFuture : false,
    noWeekEnds: (_params$noWeekEnds = params.noWeekEnds) !== null && _params$noWeekEnds !== void 0 ? _params$noWeekEnds : false,
    customConfig: (_params$customConfig = params.customConfig) !== null && _params$customConfig !== void 0 ? _params$customConfig : null,
    init: function init() {
      var _this = this;

      var options = _objectSpread(_objectSpread({
        mode: 'range',
        defaultHour: 0
      }, this.locale), this.customConfig);

      if (this.onlyFuture) {
        options.minDate = 'today';
      }

      if (this.noWeekEnds) {
        options.disable = [function (date) {
          return date.getDay() === 0 || date.getDay() === 6;
        }];
      }

      options.onClose = function (selectedDates, dateStr, instance) {
        if (selectedDates.length > 0) {
          window.livewire.emit('pg:datePicker-' + _this.tableName, {
            selectedDates: selectedDates,
            field: _this.dataField,
            values: _this.filterKey,
            label: _this.label,
            dateStr: dateStr,
            enableTime: options.enableTime === undefined ? false : options.enableTime
          });
        }
      };

      if (this.$refs.rangeInput) {
        flatpickr(this.$refs.rangeInput, options);
      }
    }
  };
});

/***/ }),

/***/ "./resources/js/components/pg-multi-select-bs5.js":
/*!********************************************************!*\
  !*** ./resources/js/components/pg-multi-select-bs5.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$dataField, _params$tableName;

  return {
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    tableName: (_params$tableName = params.tableName) !== null && _params$tableName !== void 0 ? _params$tableName : null,
    init: function init() {
      var _this = this;

      var element = '[x-ref="select_picker_' + _this.dataField + '"]';
      $(function () {
        $(element).selectpicker();
      });
      $(element).selectpicker();
      $(element).on('change', function () {
        var selected = $(this).find("option:selected");
        var arrSelected = [];
        selected.each(function () {
          arrSelected.push($(this).val());
        });
        window.livewire.emit('pg:multiSelect-' + _this.tableName, {
          id: _this.dataField,
          values: arrSelected
        });
        $(element).selectpicker('refresh');
      });
    }
  };
});

/***/ }),

/***/ "./resources/js/components/pg-multi-select.js":
/*!****************************************************!*\
  !*** ./resources/js/components/pg-multi-select.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$tableName, _params$columnField, _params$value, _params$text, _params$dataField;

  return {
    tableName: (_params$tableName = params.tableName) !== null && _params$tableName !== void 0 ? _params$tableName : null,
    columnField: (_params$columnField = params.columnField) !== null && _params$columnField !== void 0 ? _params$columnField : null,
    value: (_params$value = params.value) !== null && _params$value !== void 0 ? _params$value : null,
    text: (_params$text = params.text) !== null && _params$text !== void 0 ? _params$text : null,
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    options: [],
    data: params.data,
    selected: [],
    show: false,
    init: function init() {
      var _this = this;

      var self = this;
      var options = this.data;
      options.forEach(function (option) {
        var value = option.value[self.value];
        var text = option.value[self.text];

        _this.options.push({
          value: value,
          text: text,
          selected: false
        });
      });
      JSON.parse(params.selected).forEach(function (value) {
        self.options.map(function (option) {
          if (option.value === value) {
            option.selected = true;
            self.selected.push(option);
          }
        });
      });
    },
    selectedValues: function selectedValues() {
      var selected = [];
      this.selected.forEach(function (item) {
        selected.push(item.value);
      });
      return selected;
    },
    select: function select(value) {
      var self = this;
      var options = this.options.filter(function (option) {
        if (option.value === value && !option.selected) {
          option.selected = true;
          return option;
        }
      });
      this.selected.push(options[0]);
      this.show = false;
      this.$wire.emit('pg:multiSelect-' + self.tableName, {
        id: this.dataField,
        values: this.selectedValues()
      });
    },
    remove: function remove(value) {
      this.selected = this.selected.filter(function (item) {
        return item.value !== value;
      });
      this.options = this.options.map(function (option) {
        if (option.value === value) {
          option.selected = false;
        }

        return option;
      });
      this.$wire.emit('pg:multiSelect-' + this.tableName, {
        id: this.dataField,
        values: this.selectedValues()
      });
    }
  };
});

/***/ }),

/***/ "./resources/js/components/pg-toggleable.js":
/*!**************************************************!*\
  !*** ./resources/js/components/pg-toggleable.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$field, _params$tableName, _params$enabled;

  return {
    field: (_params$field = params.field) !== null && _params$field !== void 0 ? _params$field : null,
    tableName: (_params$tableName = params.tableName) !== null && _params$tableName !== void 0 ? _params$tableName : null,
    enabled: (_params$enabled = params.enabled) !== null && _params$enabled !== void 0 ? _params$enabled : false,
    id: params.id,
    trueValue: params.trueValue,
    falseValue: params.falseValue,
    toggle: params.toggle,
    save: function save() {
      var value = this.toggle === 0 ? this.toggle = 1 : this.toggle = 0;
      document.getElementsByClassName('message')[0].style.display = "none";
      this.$wire.emit('pg:toggleable-' + this.tableName, {
        id: this.id,
        field: this.field,
        value: value
      });
    }
  };
});

/***/ }),

/***/ "./resources/js/index.js":
/*!*******************************!*\
  !*** ./resources/js/index.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ "./resources/js/components/index.js");


/***/ }),

/***/ "./resources/css/style.css":
/*!*********************************!*\
  !*** ./resources/css/style.css ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/powergrid": 0,
/******/ 			"powergrid": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunklivewire_powergrid"] = self["webpackChunklivewire_powergrid"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["powergrid"], () => (__webpack_require__("./resources/js/index.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["powergrid"], () => (__webpack_require__("./resources/css/style.css")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;