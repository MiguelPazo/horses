$(document).ready(function () {
    var catSelected = [];

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

    $('#formAnimal').submit(function (e) {
        e.preventDefault();

        var form = $(this);

        if (validateForm(form)) {
            if (catSelected.length > 0) {
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
                            disableButtons(true);
                            var url = form.attr('action');
                            var data = form.serialize();

                            if (idSelected == 0) {
                                $.post(url, data, function (response) {
                                    if (response.success) {
                                        location.href = response.url;
                                    } else {
                                        openPopup('Error', response.message, 1, null);
                                        disableButtons(false);
                                    }
                                });
                            } else {
                                $.ajax({
                                    url: BASE_URL + '/oper/animal/' + idSelected,
                                    method: 'PUT',
                                    dataType: 'json',
                                    data: data,
                                    success: function (response) {
                                        if (response.success) {
                                            location.href = response.url;
                                        } else {
                                            openPopup('Error', response.message, 1, null);
                                            disableButtons(false);
                                        }
                                    }
                                });
                            }
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
                openPopup('Error', 'Debe seleccionar por lo menos una categoría.', 1, null);
            }
        } else {
            openPopup('Error', 'Debe llenar el nombre del animal.', 1, null);
        }
    });
});