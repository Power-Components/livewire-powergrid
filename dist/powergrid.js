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



window.pgMultiSelect = _pg_multi_select__WEBPACK_IMPORTED_MODULE_0__["default"];
window.pgToggleable = _pg_toggleable__WEBPACK_IMPORTED_MODULE_1__["default"];
window.pgMultiSelectBs5 = _pg_multi_select_bs5__WEBPACK_IMPORTED_MODULE_2__["default"];
document.addEventListener('alpine:init', function () {
  window.Alpine.data('pgMultiSelect', _pg_multi_select__WEBPACK_IMPORTED_MODULE_0__["default"]);
  window.Alpine.data('pgToggleable', _pg_toggleable__WEBPACK_IMPORTED_MODULE_1__["default"]);
  window.Alpine.data('pgMultiSelectBs5', _pg_multi_select_bs5__WEBPACK_IMPORTED_MODULE_2__["default"]);
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