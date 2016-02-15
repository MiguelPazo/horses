var openPopup = function (title, message, type, okFunction) {
    $('.modal_title').html(title);
    $('.modal_message').html(message);

    switch (type) {
        case 1:
            $('#modal_notice').modal('show');
            break;
        case 2:
            $('#modal_confirm').modal('show');
            $('#btn_ok').click(okFunction)
            break;
    }
    ;
};

var validateForm = function (form) {
    var result = true;

    form.find('.required').each(function (i, e) {
        if ($.trim($(e).val()) == '') {
            result = false;
        }
    });

    return result;
};

var reloadPage = function () {
    setTimeout(function () {
        location.reload();
    }, 2000);
};

var generalError = function () {
    openPopup('Error', 'Ha ocurrido un error, se recargará la página', 1, null);

    reloadPage();
};

var disableButtons = function (disable) {
    if (disable) {
        $('.btn_disable').attr('disabled', 'disabled');
    } else {
        $('.btn_disable').removeAttr('disabled');
    }
}

$(document).ready(function () {
    $('.integer').numeric();

    $('.formValid').submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var method = form.attr('method');
        var methodElement = form.find("input[name='_method']");

        if (methodElement.length != 0) {
            method = methodElement.val();
        }

        if (validateForm(form)) {
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
            openPopup('Error', 'Debe llenar todos los campos obligatorios.', 1, null);
        }
    });

    $('.btn_link_prevent').click(function (e) {
        e.preventDefault();

        var url = $(this).attr('href');
        var method = ($(this).attr('data-method') == undefined) ? 'GET' : $(this).attr('data-method');

        $.ajax({
            url: url,
            method: method,
            dataType: 'json',
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

    });

    $('.btn_print').click(function () {
        print();
    });


});

$(document).ajaxError(function (event, xhr, settings, thrownError) {
    if (xhr.status === 0 || xhr.readyState === 0) {
        return;
    }

    generalError();
});
