require('./bootstrap');

import '@fortawesome/fontawesome-pro/js/all.min.js';

window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

'use strict'

const validators = {
  'data-hs-validation-equal-field': field => {
    const equalTarget = document.querySelector(field.getAttribute('data-hs-validation-equal-field'))

    field.addEventListener('input', e => {
      equalTarget.value.toString().toLocaleLowerCase() !== e.target.value.toString().toLocaleLowerCase() ? field.setCustomValidity('qual-field') : field.setCustomValidity('')
      HSBsValidation.updateFieldStete(field)
    })

    equalTarget.addEventListener('input', e => {
      field.value.toString().toLocaleLowerCase() !== e.target.value.toString().toLocaleLowerCase() ? field.setCustomValidity('qual-field') : field.setCustomValidity('')
      HSBsValidation.updateFieldStete(field)
    })
  }
}

const HSBsValidation = {
  init(selector, options) {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll(selector)

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach((form) => {
        for (const validator in validators) {
          Array.prototype.slice.call(form.querySelectorAll(`[${validator}]`))
            .forEach(validators[validator])
        }

        this.addVlidationListners(form.elements)

        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            this.checkFieldsState(form.elements)
          } else {
            this.onSubmit({event, form, options})
          }

          form.classList.add('was-validated')
        }, false)
      })

    return this
  },

  addVlidationListners(elements) {
    Array.prototype.slice.call(elements)
      .forEach(field => {
        const target = field.closest('[data-hs-validation-validate-class]')
        if (!target) return

        field.addEventListener('input', e => this.updateFieldStete(e.target))
        field.addEventListener('focus', e => target.classList.add('focus'))
        field.addEventListener('blur', e => target.classList.remove('focus'))
      })
  },

  checkFieldsState(elements) {
    Array.prototype.slice.call(elements)
      .forEach(field => this.updateFieldStete(field))
  },

  updateFieldStete(field) {
    const target = field.closest('[data-hs-validation-validate-class]')
    if (!target) return

    if (field.checkValidity()) {
      target.classList.add('is-valid')
      target.classList.remove('is-invalid')
    } else {
      target.classList.add('is-invalid')
      target.classList.remove('is-valid')
    }
  },

  onSubmit(data) {
    if (!data.options || typeof data.options.onSubmit !== 'function') return false
    return data.options.onSubmit(data)
  }
}

/*
* HSCore
* @version: 2.0.0 (27 April, 2021)
* @author: HtmlStream
* @event-namespace: .HSCore
* @license: Htmlstream Libraries (https://htmlstream.com/licenses)
* Copyright 2021 Htmlstream
*/
'use strict';

const HSCore = {
	init: () => {
		// Botostrap Tootltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})

		// Bootstrap Popovers
		var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
			return new bootstrap.Popover(popoverTriggerEl)
		})
	},
	components: {}
}

HSCore.init()

/**
 * List JS wrapper.
 *
 * @author Htmlstream
 * @version 2.0
 *
 */


HSCore.components.HSList = {
  dataAttributeName: 'data-hs-list-options',
  defaults: {
    searchMenu: false,
    searchMenuDelay: 300,
    searchMenuOutsideClose: true,
    searchMenuInsideClose: true,
    clearSearchInput: true,
    keyboard: false,
    empty: false
  },
  collection: [],

  init: function (el, options, id) {
    const that = this;
    let elems;

    if (el instanceof HTMLElement) {
      elems = [el]
    } else if (el instanceof Object) {
      elems = el
    } else {
      elems = document.querySelectorAll(el)
    }

    for (let i = 0; i < elems.length; i += 1) {
      that.addToCollection(elems[i], options, id || elems[i].id)
    }

    if (!that.collection.length) {
      return false
    }

    // initialization calls
    that._init()

    return this
  },

  initializeHover: function ($el, settings, newList) {
    const that = this
    var searchFiled = $el.querySelector(`.${newList.searchClass}`),
      selected = false

    /* Start : Keboard Support */
    searchFiled.addEventListener('keydown', e => {
      if (e.which === 40) {
        e.preventDefault()

        that.searchMenuShow($el, settings, newList)

        var activeItem = newList.list.querySelector('.active')

        if (!activeItem) {
          selected = newList.list.firstChild
          selected.classList.add('active')
        } else {
          if (activeItem.nextElementSibling) {
            var newActive = activeItem.nextElementSibling
            newActive.classList.add('active')
            selected.classList.remove('active')
            selected = newActive

            if (newList.list.offsetHeight < newActive.getBoundingClientRect().top) {
              newList.list.scrollTop = newActive.getBoundingClientRect().top + newList.list.scrollTop
            }
          }
        }
      } else if (e.which === 38) {
        e.preventDefault()

        var activeItem = newList.list.querySelector('.active')

        if (!activeItem) {
          selected = newList.list.firstChild.parentNode
          selected.classList.add('active')
        } else {
          if (activeItem.previousElementSibling) {
            var newActive = activeItem.previousElementSibling
            newActive.classList.add('active')
            selected.classList.remove('active')
            selected = newActive

            if (0 > newActive.getBoundingClientRect().top) {
              newList.list.scrollTop = newActive.getBoundingClientRect().top + newList.list.scrollTop - newList.list.offsetHeight
            }
          }
        }
      } else if (e.which == 13 && searchFiled.value.length > 0) {
        e.preventDefault()

        const href = selected.querySelector('a').getAttribute('href')
        if (href) {
          window.location = href
        }
      }
    })
  },

  searchMenu: function ($el, settings, newList) {
    const that = this
    if ($el.querySelector(`.${newList.searchClass}`).value.length === 0 || newList.visibleItems.length === 0 && !settings.empty) {
      that.helpers.fadeOut(newList.list, settings.searchMenuDelay)
      return that.helpers.hide(settings.empty)
    }

    that.searchMenuShow($el, settings, newList)
  },

  searchMenuShow: function ($el, settings, newList) {
    const that = this

    that.helpers.fadeIn(newList.list, settings.searchMenuDelay)

    if (!newList.visibleItems.length) {
      var empty = that.helpers.show(document.querySelector(settings.empty).cloneNode(true))
      newList.list.innerHTML = empty.outerHTML
    }
  },

  searchMenuHide: function ($el, settings, newList) {
    const that = this
    var searchFiled = $el.querySelector(`.${newList.searchClass}`)

    if (settings.searchMenuOutsideClose) {
      document.addEventListener('click', () => {
        that.helpers.fadeOut(newList.list, settings.searchMenuDelay)

        if (settings.clearSearchInput) {
          searchFiled.value = ''
        }
      })
    }

    if (!settings.searchMenuInsideClose) {
      newList.list.addEventListener('click', event => {
        event.stopPropagation()

        if (settings.clearSearchInput) {
          searchFiled.val('')
        }
      })
    }
  },

  emptyBlock: function ($el, settings, newList) {
    const that = this

    if ($el.querySelector(`.${newList.searchClass}`).value.length === 0 || newList.visibleItems.length === 0 && !settings.empty) {
      that.helpers.hide(settings.empty)
    } else {
      that.helpers.fadeIn(newList.list, settings.searchMenuDelay)

      if (!newList.visibleItems.length) {
        var empty = document.querySelector(settings.empty).clone()
        that.helpers.show(empty)
        newList.list.innerHTML = empty.outerHTML
      }
    }
  },

  // Helpers functions
  helpers: {
    fadeIn: (el, time) => {
      if (!el || el.offsetParent !== null) return el
      el.style.opacity = 0
      el.style.display = 'block'

      var last = +new Date()
      var tick = function () {
        el.style.opacity = +el.style.opacity + (new Date() - last) / time
        last = +new Date()

        if (+el.style.opacity < 1) {
          (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16)
        }
      }

      tick()
    },

    fadeOut: (el, time) => {
      if (!el || el.offsetParent === null) return el

      if (!time) {
        return el.style.display = 'none'
      }

      var intervalID = setInterval(function () {

        if (!el.style.opacity) {
          el.style.opacity = 1
        }

        if (el.style.opacity > 0) {
          el.style.opacity -= 0.1
        } else {
          clearInterval(intervalID)
          el.style.display = 'none'
        }

      }, time / 10)
    },

    hide: el => {
      el = typeof el === 'object' ? el : document.querySelector(el)
      el ? el.style.display = 'none' : el
      return el
    },

    show: el => {
      el = typeof el === 'object' ? el : document.querySelector(el)
      el ? el.style.display = 'block' : el
      return el
    }
  },

  addToCollection(item, options, id) {
    const that = this

    this.collection.push({
      $el: item,
      id: id || null,
      options: Object.assign(
        {},
        that.defaults,
        item.hasAttribute(that.dataAttributeName)
          ? JSON.parse(item.getAttribute(that.dataAttributeName))
          : {},
        options,
      ),
    })
  },

  _init() {
    const that = this;

    for (let i = 0; i < that.collection.length; i += 1) {
      let _$el
      let _options

      if (that.collection[i].hasOwnProperty('$initializedEl')) {
        continue
      }

      _$el = that.collection[i].$el;
      _options = that.collection[i].options

      that.collection[i].$initializedEl = new List(
        _$el,
        _options,
        _options.values
      )

      if (_options.searchMenu) {
        that.helpers.hide(that.collection[i].$initializedEl.list)
      }

      that.collection[i].$initializedEl.on('searchComplete', () => {
        if (_options.searchMenu) {
          that.searchMenu(_$el, _options, that.collection[i].$initializedEl)
          that.searchMenuHide(_$el, _options, that.collection[i].$initializedEl)
        }

        if (!_options.searchMenu && _options.empty) {
          that.emptyBlock(_$el, _options, that.collection[i].$initializedEl)
        }
      })

      if (_options.searchMenu && _options.keyboard) {
        that.initializeHover(_$el, _options, that.collection[i].$initializedEl)
      }
    }
  },

  getItem(item) {
    if (typeof item === 'number') {
      return this.collection[item].$initializedEl;
    } else {
      return this.collection.find(el => {
        return el.id === item;
      }).$initializedEl;
    }
  }
}

/**
 * HSTypedJS wrapper.
 *
 * @author Htmlstream
 * @version 3.0
 *
 */

HSCore.components.HSTyped = {
  dataAttributeName: 'data-hs-typed-options',
  defaults: {},

  collection: [],

  init(el, options, id) {
    const that = this;
    let elems;

    if (el instanceof HTMLElement) {
      elems = [el]
    } else if (el instanceof Object) {
      elems = el
    } else {
      elems = document.querySelectorAll(el)
    }

    for (let i = 0; i < elems.length; i += 1) {
      that.addToCollection(elems[i], options, id || elems[i].id)
    }

    if (!that.collection.length) {
      return false
    }

    // initialization calls
    that._init()
  },

  addToCollection(item, options, id) {
    const that = this

    this.collection.push({
      $el: item,
      id: id || null,
      options: Object.assign(
        {},
        that.defaults,
        item.hasAttribute(that.dataAttributeName)
          ? JSON.parse(item.getAttribute(that.dataAttributeName))
          : {},
        options
      )
    })
  },

  _init: function () {
    const that = this;

    for (let i = 0; i < that.collection.length; i += 1) {
      let _$el
      let _options

      if (that.collection[i].hasOwnProperty('$initializedEl')) {
        continue
      }

      _$el = that.collection[i].$el
      _options = that.collection[i].options

      that.collection[i].$initializedEl = new Typed(
        _$el,
        _options
      )
    }
  }
}
