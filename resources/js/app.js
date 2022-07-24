import * as mdb from 'mdb-ui-kit';
import IMask from 'imask';

require('./campcost.js');
window.addEvent = function (el, type, handler) {
    if (el.attachEvent) el.attachEvent('on' + type, handler); else el.addEventListener(type, handler);
}
window.removeEvent = function (el, type, handler) {
    if (el.detachEvent) el.detachEvent('on' + type, handler); else el.removeEventListener(type, handler);
}

window.hasClass = function (el, className) {
    return el.classList ? el.classList.contains(className) : new RegExp('\\b' + className + '\\b').test(el.className);
}

window.addClass = function (el, className) {
    if (el.classList) el.classList.add(className);
    else if (!hasClass(el, className)) el.className += ' ' + className;
}

window.removeClass = function (el, className) {
    if (el.classList) el.classList.remove(className);
    else el.className = el.className.replace(new RegExp('\\b' + className + '\\b', 'g'), '');
}

window.getAjax = function (url, success) {
    const xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    xhr.open('GET', url);
    xhr.onreadystatechange = function () {
        if (xhr.readyState > 3 && xhr.status === 200) success(JSON.parse(xhr.responseText));
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
    return xhr;
}

const modal = document.getElementById('paypalModal');
if (modal) window.paypalModal = new mdb.Modal(modal);

window.setSelect = function (el, value) {
    const select = document.querySelector(el);
    if (window.hasClass(select, 'select-initialized')) {
        const singleSelectInstance = mdb.Select.getInstance(select);
        if (singleSelectInstance) singleSelectInstance.setValue(value);
    } else {
        select.value = value;
    }
}

const churchFilter = async (query) => {
    const url = `/data/churchlist?term=${encodeURI(query)}`;
    const response = await fetch(url);
    return await response.json();
};

let isDirty = false;
window.checkDirty = function (e) {
    if (!isDirty) {
        return;
    }
    return e.returnValue = 'You have unsaved changes on this page.';
}

function runOnLoad() {
    const menus = document.querySelectorAll('.navbar .dropdown-menu');
    for (let i = 0; i < menus.length; i++) {
        window.addEvent(menus[i], 'click', function (e) {
            e.stopPropagation();
        });
    }
    const inputs = document.querySelectorAll("select, textarea, input:not([type='hidden'])");
    for (let i = 0; i < inputs.length; i++) {
        window.addEvent(inputs[i], 'change', function () {
            isDirty = true;
        });
        if (window.hasClass(inputs[i], 'phone-mask')) {
            IMask(inputs[i], {
                mask: '000-000-0000'
            });
        }
        if (window.hasClass(inputs[i], 'days-mask')) {
            IMask(inputs[i], {
                mask: Number,
                scale: 0,
                min: 0,
                max: 30
            });
        }
        if (window.hasClass(inputs[i], 'amount-mask')) {
            window.lastAmountMask = IMask(inputs[i], {
                mask: Number,
                radix: '.',
                normalizeZeros: false,
                min: 0,
                max: 99999.99
            });
        }
        if (window.hasClass(inputs[i], 'church-search')) {
            new mdb.Autocomplete(inputs[i].parentNode, {
                filter: churchFilter,
                autoSelect: true,
                threshold: 2,
                displayValue: (value) => value.name + " (" + value.city + ", " + value.province.code + ")",
                itemContent: (result) => {
                    return `
                        <div class="autocomplete-custom-item-content">
                            <div class="autocomplete-custom-item-title">${result.name}</div>
                            <div class="autocomplete-custom-item-subtitle">${result.city}, ${result.province.code}</div>
                        </div>`;
                },
            });
            window.addEvent(inputs[i].parentNode, 'itemSelect.mdb.autocomplete', (event) => {
                event.target.querySelector('.autocomplete-custom-content').value = event.value.id;
            })
        }
        if (window.hasClass(inputs[i], 'fonts')) {
            window.addEvent(inputs[i], 'open.mdb.select', function (e) {
                setTimeout(function () {
                    const options = document.querySelectorAll('.select-option');
                    options[1].style.fontFamily = 'Indie Flower';
                    options[2].style.fontFamily = 'Fredericka the Great';
                    options[3].style.fontFamily = 'Mystery Quest';
                    options[4].style.fontFamily = 'Great Vibes';
                    options[5].style.fontFamily = 'Bangers';
                    options[6].style.fontFamily = 'Comic Sans MS';
                }, 100);
            });
        }

        window.addEvent(window, 'beforeunload', checkDirty);

        const forms = document.querySelectorAll('form');
        for (let i = 0; i < forms.length; i++) {
            window.addEvent(forms[i], 'submit', function () {
                window.removeEvent(window, 'beforeunload', checkDirty);
            });
        }
    }
}

// in case the document is already rendered
if (document.readyState !== 'loading') runOnLoad();
// modern browsers
else if (document.addEventListener) document.addEventListener('DOMContentLoaded', runOnLoad);
// IE <= 8
else document.attachEvent('onreadystatechange', function () {
        if (document.readyState === 'complete') runOnLoad();
    });
