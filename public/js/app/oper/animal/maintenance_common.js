var idSelected = 0;
var valSelected = '';
var infoSelected = null;
var wDisable = true;

$(document).ready(function () {
    var fillPrefix = true;
    var prefix = '';

    wDisable = ($('#name').attr('rel') == 'disable') ? true : false;

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

    $('#name').devbridgeAutocomplete({
        serviceUrl: BASE_URL + 'oper/animal/list-parents',
        minChars: 3,
        onSelect: function (suggestion) {
            $('#name').val(suggestion.show);

            $.get(BASE_URL + 'oper/animal/info-animal/' + suggestion.data, null, function (response) {
                if (response.success) {
                    infoSelected = response;
                    fillAnimalInfo(response, false, wDisable);
                }
            });
        }
    });

    $('#name').keyup(function () {
        if (valSelected != '' && valSelected != $(this).val()) {
            idSelected = 0;
            valSelected = '';

            if (wDisable) {
                disableForm(false);
            }
        }
    });
});

var fillAnimalInfo = function (data, wName, wDisable) {
    idSelected = data.id;
    valSelected = data.name;

    if (wName) {
        $('#name').val(data.name);
    }

    $('#birthdate').val(data.birthdate);
    $('#code').val(data.code);
    $('#owner_name').val(data.owner);
    $('#breeder_name').val(data.breeder);
    $('#prefix').val(data.prefix);
    $('#dad_name').val(data.dad);
    $('#mom_name').val(data.mom);

    if (wDisable) {
        disableForm(true);
    }
};

var disableForm = function (disable) {
    $('#formAnimal').find('input').each(function (i, item) {
        if (!$(item).hasClass('no_disable')) {
            if (disable) {
                $(item).attr('disabled', 'disabled');
            } else {
                $(item).val('');
                $(item).removeAttr('disabled');
            }
        }
    });
};

var getPrefixBreeder = function (breeder) {
    var pos = breeder.indexOf(' ');
    var rightText = breeder.substring(pos + 1);
    prefix += rightText.substring(0, 1);

    pos2 = rightText.indexOf(' ');

    if (pos2 !== -1) {
        getPrefixBreeder(rightText);
    }
};