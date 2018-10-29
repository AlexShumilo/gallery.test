$(document).ready(function () {

    $("a.gallery-img").live('click', function(e){
        e.preventDefault();

        $("a.gallery-img").fancybox({                                       // подключение библиотеки Fancybox
        'hideOnContentClick': true
        });
    });

    $('#filter-form').submit(function (e) {                             // обработка запроса фильтра
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $('#filter-form').serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function (result) {
            	var errors = $('#filter-form .alert.alert-danger');
                errors.hide();
                var elements = $(result).find('#gallery').html();
                $('#gallery').html(elements);

            },
            error: function(data){
                var errors = JSON.parse(data.response).errors;
                var $errors = $('#filter-form .alert.alert-danger p');
                $errors.empty();
                for (var prop in errors) {
                    $errors.append(errors[prop].toString());
                }
                $('#filter-form .alert.alert-danger').show();
            }
        });
    });

    $('#show-all').live('click', function(e) {                          // обработка запроса с загрузкой всех изображений
        e.preventDefault();

        var showAllLink = $(this).attr('href');

        $.ajax({
            type: 'GET',
            url: showAllLink,
            success: function (result) {
                var elements = $(result).find('#gallery').html();
                var cleanedForm = $(result).find('#filter-form').html();
                $('#gallery').html(elements);
                $('#filter-form').html(cleanedForm);
            }
        })
    });

    $('a.page-link').live('click', function(e) {                           // обработка запроса ссылок пагинатора
        e.preventDefault();

        var paginatorLink = $(this).attr('href');

        $.ajax({
            type: 'GET',
            url: paginatorLink,
            success: function (result) {
                var elements = $(result).find('#gallery').html();
                $('#gallery').html(elements);
            }
        })
    });

    $('#count-form').submit(function(e) {                  // обработка запроса формы количества изображений на странице
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $('#count-form').serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function (result) {
                var elements = $(result).find('#gallery').html();
                var cleanedForm = $(result).find('#filter-form').html();
                $('#gallery').html(elements);
                $('#filter-form').html(cleanedForm);
            }
        })
    });

    $('a.img-link').live('click', function(e) {                           // добавление просмотров изображения
        e.preventDefault();

        var imageId = $(this).attr('data-toggle');

        $.ajax({
            type: 'GET',
            url: '/',
            data: {imageId:imageId},
            success: function (result) {
                //console.log(result);
            }
        })
    });
});