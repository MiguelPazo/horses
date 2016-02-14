$(document).ready(function () {
    var totalSelected = $('#total_present').val();
    var actualMax = $('#last_pos').val();
    var step = 1;
    var catalogNumbers = $('#lst_catalog').val().split(',');

    $('#count_sel').html(totalSelected);

    $(".datepicker").datepicker({
        'dateFormat': 'dd-mm-yy',
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true
    });

    $('#pane_stage .btn_competitor').click(function () {
        if ($(this).hasClass('btn-success')) {
            totalSelected--;
            $(this).removeClass('btn-success');
        } else {
            totalSelected++;
            $(this).addClass('btn-success');
        }

        $('#count_sel').html(totalSelected);
    });

    $('#btn_confirm').click(function () {
        $('#form').submit();
    });

    $('#form').submit(function (e) {
        prepareForm();
    });

    $('#btn_add').click(function () {
        step = 1;
        $('#step_1').show();
        $('#step_2').hide();
        $('.contextual_message').hide();
        $('#num_catalog').val('');
        $('#formAnimal').trigger("reset");
        disableButtons(false);
        disableForm(false);
        $('#modal_new_animal').modal('show');
        $('#name').focus();
    });

    $('#formAnimal').submit(function (e) {
        e.preventDefault();
        $('#error_message').hide();
        disableButtons(true);

        if (idSelected == 0) {
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
                                        $('#modal_new_animal').modal('hide');
                                        //////////////////// more
                                    } else {
                                        showErrorMessage(response.message);
                                        disableButtons(false);
                                    }
                                },
                                error: function (response) {
                                    $('#modal_new_animal').modal('hide');
                                    generalError();
                                }
                            });
                        } else {
                            showErrorMessage('El nombre del padre y madre no pueden ser iguales.');
                        }
                    } else {
                        showErrorMessage('El nombre del animal no puede ser el mismo que el de los padres.');
                    }
                } else {
                    showErrorMessage('Separe los nombres y apellidos por ",". Ejemplo: Miguel Rodrigo, Pazo Sánchez');
                }
            } else {
                showErrorMessage('Debe llenar el nombre del animal.');
            }
        } else {
            $('#modal_new_animal').modal('hide');
            console.log(infoSelected);//recuperar catalogo
            //////////////////// more
        }
    });


    $('#btn_next').click(function () {
        switch (step) {
            case 1:
                var numCatalog = $('#num_catalog').val();
                if (numCatalog != '') {
                    var tournament = $('#tournament').val();

                    $.get(BASE_URL + 'admin/catalog/info/' + tournament + '/catalog/' + numCatalog, null, function (response) {
                        if (response.success) {
                            infoSelected = response;
                            fillAnimalInfo(response, true, true);
                        }

                        step++;
                        $('#step_1').hide();
                        $('#step_2').show();
                    });
                } else {
                    step++;
                    $('#step_1').hide();
                    $('#step_2').show();
                }
                break;
            case 2:
                $('#formAnimal').submit();
                break;
        }
    });

    $('#num_catalog').keypress(function (e) {
        if (e.which == 13) {
            $('#btn_next').click();
        }
    });

    var prepareForm = function () {
        $('#pane_stage .btn_competitor').each(function (i, e) {
            if ($(e).hasClass('btn-success')) {
                $(e).next('input').val('1');
            } else {
                $(e).next('input').val('0');
            }
        });
    };

    var showErrorMessage = function (message) {
        if (message != undefined && message != '') {
            disableButtons(false);
            $('#error_message').text(message);
            $('#error_message').show();
        }
    };
});