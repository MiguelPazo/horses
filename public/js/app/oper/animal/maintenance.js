$(document).ready(function () {
    var fillPrefix = true;
    var prefix = '';
    var catSelected = [];
    var xhr;

    if ($('#prefix').val() != '') {
        fillPrefix = false;
    }

    if ($('#categories').val() != '') {
        catSelected = $('#categories').val().split(',');
    }

    $('#categories_name').multiselect({
        enableFiltering: true,
        nonSelectedText: 'Ninguna categoría seleccionada',
        buttonWidth: '100%',
        buttonClass: 'btn btn-default btn_categories',
        maxHeight: 200,
        enableCaseInsensitiveFiltering: true,
        numberDisplayed: 4,
        allSelectedText: 'Todas las categorías seleccionadas...',
        selectAllText: 'Seleccionar todas',
        filterPlaceholder: 'Buscar...',
        nSelectedText: ' - Categorías seleccionadas',
        delimiterText: '; ',
        onChange: function (option, checked, select) {
            if (checked) {
                catSelected.push(option.val());
            } else {
                catSelected.splice(catSelected.indexOf(option.val()), 1);
            }

            $('#categories').val(catSelected.toString());
        }
    });

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

    $('#formAnimal').submit(function (e) {
        e.preventDefault();

        var form = $(this);
        var method = form.attr('method');
        var methodElement = form.find("input[name='_method']");

        if (methodElement.length != 0) {
            method = methodElement.val();
        }

        if (validateForm(form)) {
            var withcomma = true;
            var parents = true;
            var sameParents = false;

            form.find('.namewlast').each(function (i, item) {
                var val = $.trim($(item).val());

                if (val != '') {
                    var pos = val.indexOf(',');

                    if (pos === -1) {
                        withcomma = false;
                    }
                }
            });

            form.find('.parents').each(function (i, item) {
                var prefix = $.trim($('#prefix').val());
                prefix = (prefix == '') ? '' : '(' + prefix + ') ';
                var name = prefix + $.trim($('#name').val());
                var val = $.trim($(item).val());
                var count = 0;

                if (val == name) {
                    parents = false;
                }

                if (val != '') {
                    form.find('.parents').each(function (i2, item2) {
                        var val2 = $.trim($(item2).val());

                        if (val == val2) {
                            count++;
                        }

                        sameParents = (count > 1) ? true : sameParents;
                    });
                }
            });

            if (withcomma) {
                if (parents) {
                    if (!sameParents) {
                        $.ajax({
                            url: form.attr('action'),
                            method: method,
                            dataType: 'json',
                            data: form.serialize(),
                            success: function (response) {
                                if (response.success) {
                                    location.href = response.url;
                                } else {
                                    openPopup('Error', response.message, 1, null);
                                }
                            },
                            error: function (response) {
                                generalError();
                            }
                        });
                    } else {
                        openPopup('Error', 'El nombre del padre y madre no pueden ser iguales.', 1, null);
                    }
                } else {
                    openPopup('Error', 'El nombre del animal no puede ser el mismo que el de los padres.', 1, null);
                }
            } else {
                openPopup('Error', 'Separe los nombres y apellidos por ",". Ejemplo: Miguel Rodrigo, Pazo Sánchez', 1, null);
            }
        } else {
            openPopup('Error', 'Debe llenar el nombre del animal.', 1, null);
        }
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