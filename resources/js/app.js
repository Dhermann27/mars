import '@fortawesome/fontawesome-pro/js/all.min.js';
import * as mdb from 'mdb-ui-kit';
require ('./campcost.js');

window.addEvent = function (el, type, handler) {
    if (el.attachEvent) el.attachEvent('on' + type, handler); else el.addEventListener(type, handler);
}

window.getAjax = function (url, success) {
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    xhr.open('GET', url);
    xhr.onreadystatechange = function () {
        if (xhr.readyState > 3 && xhr.status === 200) success(JSON.parse(xhr.responseText));
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
    return xhr;
}

if (typeof(window.runOnLoad) == "function") {
// in case the document is already rendered
    if (document.readyState !== 'loading') runOnLoad();
// modern browsers
    else if (document.addEventListener) document.addEventListener('DOMContentLoaded', runOnLoad);
// IE <= 8
    else document.attachEvent('onreadystatechange', function () {
            if (document.readyState === 'complete') runOnLoad();
        });
}
