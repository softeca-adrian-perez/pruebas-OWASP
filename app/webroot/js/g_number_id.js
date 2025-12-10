$(document).ready(function () {
    var gNumberInput = $('#g-number-input');
    $.ajax({
        url: gNumberInput.data('url'),
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data && data.new_g_number) {
                gNumberInput.val(data.new_g_number);
            }
        }
    });
});
