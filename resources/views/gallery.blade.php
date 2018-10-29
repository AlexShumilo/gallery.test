<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gallery</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="\css\jquery.fancybox-1.3.4.css" type="text/css" media="screen">
    <link rel="stylesheet" href="\css\style.css">

</head>
<body>
<div class="container">
    <div class="file-container">
        <h2>Выберите изображение</h2>
        @if($errors->has('image'))
            <div class="alert alert-danger">
                <p>{{ $errors->first() }}</p>
            </div>
        @endif
        <form method="post" id="image-form" action="{{ route('gallery') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="exampleFormControlFile1">Файл должен быть не более 5Мб и разрешением не более
                    1920х1080px</label>
                <input type="file" name="image" class="form-control-file" id="exampleFormControlFile1">
                <input type="submit" class="btn btn-primary file-btn" value="Отправить">
            </div>
        </form>

    </div>

    <div>
        <h2>Фильтр</h2>
        <form id="filter-form" method="post" action="{{ route('gallery') }}">
            {{ csrf_field() }}
                <div style="display: none" class="alert alert-danger"></div>
            <input type="hidden" name="hidden" value="1">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputMinPx">Минимальное разрешение</label>
                    <div class="row">
                        <div class="col">
                            <input type="number" class="form-control" name="min_width" placeholder="Ширина, px"
                                   value="{{ old('min_width') }}">
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" name="min_height" placeholder="Высота, px"
                                   value="{{ old('min_height') }}">
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputMaxPx">Максимальное разрешение</label>
                    <div class="row">
                        <div class="col">
                            <input type="number" class="form-control" name="max_width" placeholder="Ширина, px"
                                   value="{{ old('max_width') }}">
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" name="max_height" placeholder="Высота, px"
                                   value="{{ old('max_height') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputMinSize">Минимальный размер</label>
                    <input type="number" name="min_size" class="form-control" id="inputMinSize" placeholder="kb">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputMaxSize">Максимальный размер</label>
                    <input type="number" name="max_size" class="form-control" id="inputMaxSize" placeholder="kb">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputMinViews">Просмотры, от</label>
                    <input type="number" name="min_views" class="form-control" id="inputMinSize" placeholder="шт">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputMaxViews">Просмотры, до</label>
                    <input type="number" name="max_views" class="form-control" id="inputMaxViews" placeholder="шт">
                </div>
            </div>
            <button type="submit" name="filter_submit" class="btn btn-primary">Отфильтровать</button>
            <a href="{{ route('gallery') }}" id="show-all" class="btn btn-primary">Посмотреть все
                изображения</a>
        </form>
        <hr>
    </div>


    <div id="gallery" class="gallery">
        <p class="countPerPage">Количество на странице: {{ session('count', 20) }}</p>
        <div class="gallery-inner">
        @if(count($images) > 0)
            @foreach($images as $image)
                <a class="gallery-img img-link" rel="group" data-toggle="{{ $image->id }}"
                   href="\img\{{ $image->name }}">
                    <img src="\img\{{ $image->name }}" class="gallery-img">
                </a>
            @endforeach
        @else
            <p>Выбранные изображения отсутствуют</p>
        @endif
        </div>
        <div>
            {{ $images->fragment('gallery')->onEachSide(2)->links() }}
        </div>

    </div>

    <div class="footer">

        <form id="count-form" action="{{ route('gallery') }}" method="post">
                {{ csrf_field() }}
                <label for="exampleFormControlSelect1">Выбрать количество на странице</label>
                <select class="form-control" name="count" id="exampleFormControlSelect1">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                </select>
                <input type="submit" class="btn btn-primary file-btn" value="Применить">
        </form>
    </div>

</div>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script src="/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script src="/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script src="/js/main.js"></script>
</body>
</html>