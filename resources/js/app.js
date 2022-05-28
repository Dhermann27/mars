import '@fortawesome/fontawesome-pro/js/all.min.js';
import * as mdb from 'mdb-ui-kit';

require('./campcost.js');
window.addEvent = function (el, type, handler) {
    if (el.attachEvent) el.attachEvent('on' + type, handler); else el.addEventListener(type, handler);
}
window.removeEvent = function (el, type, handler) {
    if (el.detachEvent) el.detachEvent('on' + type, handler); else el.removeEventListener(type, handler);
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

let isDirty = false;
window.checkDirty = function(e) {
    if (!isDirty) {
        return;
    }
    return e.returnValue = 'You have unsaved changes on this page.';
}

function runOnLoad() {
    const inputs = document.querySelectorAll('input[type="checkbox"]', 'select', 'textarea', 'input[type="number"]', 'input[type="password"]', 'input[type="radio"]', 'input[type="text"]');
    for (let i = 0; i < inputs.length; i++) {
        window.addEvent(inputs[i], 'change', function () {
            isDirty = true;
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

// in case the document is already rendered
mdb.toString(); // For formatter
if (document.readyState !== 'loading') runOnLoad();
// modern browsers
else if (document.addEventListener) document.addEventListener('DOMContentLoaded', runOnLoad);
// IE <= 8
else document.attachEvent('onreadystatechange', function () {
        if (document.readyState === 'complete') runOnLoad();
    });
