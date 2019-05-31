function minutesToHours(minutes) {
    let hour = Math.floor(minutes/60);
    let strHour = (hour >= 10) ? hour : '0' + hour;
    let minute = minutes - hour*60;
    let strMinute = (minute >= 10) ? minute : '0' + minute;
    return (hour > 0) ? strHour + ":" + strMinute : minutes;
}

function formatMoney(money) {
    let re = '\\d(?=(\\d{' + (3) + '})+' + ('$') + ')';
    return parseFloat(money).toFixed(0).replace(new RegExp(re, 'g'), '$&,');
}

function noti(type, title, message, position){
    toastr[type](message,title,{
        "positionClass": position,
        timeOut: 5000,
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false

    })
}