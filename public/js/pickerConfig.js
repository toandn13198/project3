$(document).ready(function (e) {
    $('input[name="datefilter"]').daterangepicker({
        convertFormat : true,
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format : 'DD/MM/YYYY'
        }
    });
    $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
    $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    $('.timepicker').timepicker({
        format: 'HH:MM'
    });
});
