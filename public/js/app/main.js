var openPopup = function(title, message, type){
    $('.modal_title').html(title);
    $('.modal_message').html(message);

    switch (type){
        case 1:
            $('#modal_notice').modal( 'show' );
        break;
        case 2:
            $('#modal_confirm').modal( 'show' );
        break;
    };
};

var validateForm = function(form){
    var result = true;

    form.find('.required').each(function (i,e){
        if($.trim($(e).val()) == ''){
            result = false;
        }
    });

    return result;
};

$(document).ready(function(){
    $('.integer').numeric();

    $('.formValid').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var method = form.attr('method');
        var methodElement = form.find("input[name='_method']");

        if(methodElement.length != 0){
            method = methodElement.val();
        }

        if(validateForm(form)){
            $.ajax({
                url: form.attr('action'),
                method: method,
                dataType: 'json',
                data: form.serialize(),
                success: function (response) {
                    if (response.success) {
                        location.href = response.url;
                    } else {
                        openPopup('Error', response.message, 1);
                    }
                },
                error: function (response){
                    openPopup('Error', 'Ha ocurrido un error, se recargará la página', 1);

                    setTimeout(function(){
                        location.reload();
                    },2000);
                }
            });
        }else{
            openPopup('Error', 'Debe llenar todos los campos', 1);
        }
    });

    $('.btn_link_prevent').click(function(e){
        e.preventDefault();

        var url = $(this).attr('href');

         $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    location.href = response.url;
                }else{
                    openPopup('Error', response.message, 1);
                }
            },
            error: function (response){
                openPopup('Error', 'Ha ocurrido un error, se recargará la página', 1);

                setTimeout(function(){
                    location.reload();
                },2000);
            }
        });

    });

    $(".datepicker").datepicker({
        'dateFormat': 'dd-mm-yy',
        showButtonPanel: true
    });
});
