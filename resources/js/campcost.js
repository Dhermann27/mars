
window.spinnerClick = function (e) {
    var btn = e.target.closest('button'), input, oldVal = 0, newVal = 0;
    if (btn.getAttribute('data-dir') === 'up') {
        input = btn.nextElementSibling;
        newVal = parseInt(input.value, 10) + 1;
    } else {
        input = btn.previousElementSibling;
        oldVal = parseInt(input.value, 10);
        newVal = oldVal > 0 ? oldVal - 1 : 0;
    }
    input.value = newVal;
    calcluateCampCost();
};


window.calcluateCampCost = function () {
    var total = 0.0, deposit = 0.0;
    var adults = parseInt(document.getElementById('adults').value, 10),
        yas = parseInt(document.getElementById('yas').value, 10),
        jrsrs = parseInt(document.getElementById('jrsrs').value, 10),
        children = parseInt(document.getElementById('children').value, 10),
        babies = parseInt(document.getElementById('babies').value, 10);
    var singlealert = document.getElementById('single-alert'), adultsfee = document.getElementById('adults-fee'),
        yasfee = document.getElementById('yas-fee'), childrenfee = document.getElementById('children-fee');
    switch (adults + yas + jrsrs + children + babies) {
        case 0:
            break;
        case 1:
            deposit = 200.0;
            break;
        default:
            deposit = 400.0;
    }
    singlealert.style.display = 'none';
    switch (parseInt(document.querySelector('input[name=adults-housing]:checked').value, 10)) {
        case 1:
            switch (adults + children + babies) {
                case 1:
                    rate = adults * guestsuite[0] * 6;
                    singlealert.style.display = 'block';
                    break;
                case 2:
                    rate = adults * guestsuite[1] * 6;
                    break;
                case 3:
                    rate = adults * guestsuite[2] * 6;
                    break;
                default:
                    rate = adults * guestsuite[3] * 6;
            }
            total += rate + (children * guestsuite[4] * 6);
            adultsfee.innerText = "$" + rate.toFixed(2);
            childrenfee.innerText = "$" + (children * guestsuite[4] * 6).toFixed(2);
            break;
        case 2:
            total += adults * lakewood[0] * 6 + children * lakewood[2] * 6;
            adultsfee.innerText = "$" + (adults * lakewood[0] * 6).toFixed(2);
            childrenfee.innerText = "$" + (children * lakewood[2] * 6).toFixed(2);
            break;
        case 3:
            total += adults * tentcamp[0] * 6 + children * tentcamp[2] * 6;
            adultsfee.innerText = "$" + (adults * tentcamp[0] * 6).toFixed(2);
            childrenfee.innerText = "$" + (children * tentcamp[2] * 6).toFixed(2);
            break;
    }
    switch (parseInt(document.querySelector('input[name=yas-housing]:checked').value, 10)) {
        case 2:
            total += yas * lakewood[6] * 6;
            yasfee.innerText = "$" + (yas * lakewood[6] * 6).toFixed(2);
            break;
        case 3:
            total += yas * tentcamp[6] * 6;
            yasfee.innerText = "$" + (yas * tentcamp[6] * 6).toFixed(2);
            break;
    }
    total += jrsrs * lakewood[1] * 6;
    document.getElementById('jrsrs-fee').innerText = "$" + (jrsrs * lakewood[1] * 6).toFixed(2);
    total += babies * guestsuite[6] * 6;
    document.getElementById('babies-fee').innerText = "$" + (babies * guestsuite[6] * 6).toFixed(2);
    document.getElementById('deposit').innerText = "$" + Math.min(total, deposit).toFixed(2);
    document.getElementById('arrival').innerText = "$" + Math.max(total - deposit, 0).toFixed(2);
    document.getElementById('total').innerText = "$" + total.toFixed(2);
}
