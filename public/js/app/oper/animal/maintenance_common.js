$(document).ready(function () {
    var fillPrefix = true;
    var prefix = '';
    var catSelected = [];

    if ($('#prefix').val() != '') {
        fillPrefix = false;
    }

    $(".datepicker").datepicker({
        'dateFormat': 'dd-mm-yy',
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true
    });

    $('#breeder_name').blur(function (e) {
        if (fillPrefix) {
            var breeder = $.trim($(this).val()).replace(',', '');
            prefix = breeder.substring(0, 1);

            getPrefixBreeder(breeder);
            $('#prefix').val(prefix);
        }
    });

    $('#prefix').keypress(function (e) {
        fillPrefix = false;
    });

    $('#owner_name').devbridgeAutocomplete({
        serviceUrl: BASE_URL + 'oper/agent/list-all',
        minChars: 3
    });

    $('#breeder_name').devbridgeAutocomplete({
        serviceUrl: BASE_URL + 'oper/agent/list-all',
        minChars: 3,
        onSelect: function (suggestion) {
            fillPrefix = true;

            if (suggestion.data != null) {
                $('#prefix').val(suggestion.data);
                fillPrefix = false;
            }
        }
    });

    $('#dad_name').devbridgeAutocomplete({
        serviceUrl: BASE_URL + 'oper/animal/list-parents',
        minChars: 3,
        params: {
            gender: 'male'
        }
    });

    $('#mom_name').devbridgeAutocomplete({
        serviceUrl: BASE_URL + 'oper/animal/list-parents',
        minChars: 3,
        params: {
            gender: 'female'
        }
    });

    var getPrefixBreeder = function (breeder) {
        var pos = breeder.indexOf(' ');
        var rightText = breeder.substring(pos + 1);
        prefix += rightText.substring(0, 1);

        pos2 = rightText.indexOf(' ');

        if (pos2 !== -1) {
            getPrefixBreeder(rightText);
        }
    };
});