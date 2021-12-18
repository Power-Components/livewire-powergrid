/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./js/components/index.js":
/*!********************************!*\
  !*** ./js/components/index.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _pg_multi_select__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./pg-multi-select */ "./js/components/pg-multi-select.js");
/* harmony import */ var _pg_toggleable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./pg-toggleable */ "./js/components/pg-toggleable.js");
/* harmony import */ var _pg_multi_select_bs5__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./pg-multi-select-bs5 */ "./js/components/pg-multi-select-bs5.js");
/* harmony import */ var _pg_flat_pickr__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./pg-flat-pickr */ "./js/components/pg-flat-pickr.js");
/* harmony import */ var _pg_editable__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./pg-editable */ "./js/components/pg-editable.js");
/* harmony import */ var _pg_copy_to_clipboard__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./pg-copy-to-clipboard */ "./js/components/pg-copy-to-clipboard.js");






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

/***/ "./js/components/pg-copy-to-clipboard.js":
/*!***********************************************!*\
  !*** ./js/components/pg-copy-to-clipboard.js ***!
  \***********************************************/
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

/***/ "./js/components/pg-editable.js":
/*!**************************************!*\
  !*** ./js/components/pg-editable.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$id, _params$dataField;

  return {
    editable: false,
    id: (_params$id = params.id) !== null && _params$id !== void 0 ? _params$id : null,
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    content: params.content,
    save: function save() {
      document.getElementsByClassName('message')[0].style.display = "none";
      window.livewire.emit('pg:eventInputChanged', {
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

/***/ "./js/components/pg-flat-pickr.js":
/*!****************************************!*\
  !*** ./js/components/pg-flat-pickr.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$dataField, _params$filterKey, _params$label, _params$locale, _params$onlyFuture, _params$noWeekEnds, _params$customConfig;

  return {
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    filterKey: (_params$filterKey = params.filterKey) !== null && _params$filterKey !== void 0 ? _params$filterKey : null,
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
          _this.filter(selectedDates);
        }
      };

      flatpickr(this.$refs.rangeInput, options);
    },
    filter: function filter(selectedDates) {
      window.livewire.emit('pg:eventChangeDatePiker', {
        selectedDates: selectedDates,
        field: this.dataField,
        values: this.filterKey,
        label: this.label
      });
    }
  };
});

/***/ }),

/***/ "./js/components/pg-multi-select-bs5.js":
/*!**********************************************!*\
  !*** ./js/components/pg-multi-select-bs5.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$dataField;

  return {
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    init: function init() {
      var field = this.dataField;
      var element = '[x-ref="select_picker_' + field + '"]';
      $(element).selectpicker();
      document.addEventListener('DOMContentLoaded', function () {
        Livewire.hook('message.processed', function (message, component) {
          $(element).selectpicker();
        });
      });
      $(element).on('change', function () {
        var selected = $(this).find("option:selected");
        var arrSelected = [];
        selected.each(function () {
          arrSelected.push($(this).val());
        });
        window.livewire.emit('pg:eventMultiSelect', {
          id: field,
          values: arrSelected
        });
        $(element).selectpicker('refresh');
      });
    }
  };
});

/***/ }),

/***/ "./js/components/pg-multi-select.js":
/*!******************************************!*\
  !*** ./js/components/pg-multi-select.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$columnField, _params$dataField;

  return {
    columnField: (_params$columnField = params.columnField) !== null && _params$columnField !== void 0 ? _params$columnField : null,
    dataField: (_params$dataField = params.dataField) !== null && _params$dataField !== void 0 ? _params$dataField : null,
    options: [],
    data: params.data,
    selected: [],
    show: false,
    init: function init() {
      var options = this.data;

      for (var i = 0; i < options.length; i++) {
        this.options.push({
          value: options[i].value.id,
          text: options[i].value.name,
          selected: false
        });
      }
    },
    selectedValues: function selectedValues() {
      var _this = this;

      return this.selected.map(function (option) {
        return _this.options[option].value;
      });
    },
    select: function select(index, event) {
      if (!this.options[index].selected) {
        this.options[index].selected = true;
        this.options[index].element = event.target;
        this.selected.push(index);
        this.show = false;
        this.$wire.emit('pg:eventMultiSelect', {
          id: this.dataField,
          values: this.selectedValues()
        });
      } else {
        this.selected.splice(this.selected.lastIndexOf(index), 1);
        this.options[index].selected = false;
        this.show = false;
      }
    },
    remove: function remove(index, option) {
      this.options[option].selected = false;
      this.selected.splice(index, 1);
      this.$wire.emit('pg:eventMultiSelect', {
        id: this.dataField,
        values: this.selectedValues()
      });
    }
  };
});

/***/ }),

/***/ "./js/components/pg-toggleable.js":
/*!****************************************!*\
  !*** ./js/components/pg-toggleable.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (function (params) {
  var _params$field, _params$enabled;

  return {
    field: (_params$field = params.field) !== null && _params$field !== void 0 ? _params$field : null,
    enabled: (_params$enabled = params.enabled) !== null && _params$enabled !== void 0 ? _params$enabled : false,
    id: params.id,
    trueValue: params.trueValue,
    falseValue: params.falseValue,
    toggle: params.toggle,
    save: function save() {
      var value = this.toggle === 0 ? this.toggle = 1 : this.toggle = 0;
      document.getElementsByClassName('message')[0].style.display = "none";
      window.livewire.emit('pg:eventToggleChanged', {
        id: this.id,
        field: this.field,
        value: value
      });
    }
  };
});

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
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*********************!*\
  !*** ./js/index.js ***!
  \*********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components */ "./js/components/index.js");

})();

/******/ })()
;