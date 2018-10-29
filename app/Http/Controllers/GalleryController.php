<?php

namespace App\Http\Controllers;

use App\Image;
use App\Attribute;
use Illuminate\Http\Request;
use function Sodium\increment;

class GalleryController extends Controller
{
    public function show(Request $request) {

        if($request->isMethod('post')) {
            // в случае, если запрос пришёл с методом POST, то обрабатываем его в зависимости от следующих условий
            if($request->file('image')) {
                // обработка запроса в случае загрузки файла
                $this->addImage($request, $request->file('image'));

            } elseif ($request->filled('count')) {
                // обработка запроса в случае изменения количества вывода изображений на странице
                $request->session()->put('count', $request->count);

            } elseif ($request->has('hidden')) {
                // обработка запроса в случае фильтрации
                return $this->imagesFilter($request);
            }
        };

        // добавление просмотров изображения
        if($request->has('imageId')) {
            $attribute = Attribute::where('image_id', $request->imageId)->first();

            $attribute->increment('views');
        }


        /* получение количества изображений на странице, которые установил пользователь
            если не установлено никакого значения в сессии, то стандартное количество - 20 шт. */
        $countPerPage = $request->session()->get('count', 20);

        /* выборка из базы данных объектов всех изображений с выводом на страницу в количестве, установленном
        в сессии или, в случае отсутствия значения в сесиии, в стандартном количестве*/
        $images = Image::paginate($countPerPage);
        return view('gallery', ['images'=>$images]);
    }


    private function addImage($request, $requestFile) {
        // устанавка правил для фильтрации
        $rules = [
            'image' => 'file|image|max:5000|dimensions:max_width=1920,max_height=1080'
        ];
        // переопределение стандартных сообщений об ошибках
        $messages = [
            'file' => 'Перед отправкой выберите файл изображения!',
            'image' => 'Файл изображения должее иметь один из форматов jpeg, png, bmp, gif, svg!',
            'max' => 'Размер файла изображения не должен превышать 5Мб!',
            'dimensions' => 'Разрешение изображения не должно превышать 1920х1080px'
        ];

        // валидация входящего запроса (файла)
        $this->validate($request, $rules, $messages);

        // в случае успешной валидации выполняется слеующий код
        $file = $requestFile;
        // добавляем свою "соль", для случаев, если будут загружену изображения с одинаковым названием
        $fileName = time() . $file->getClientOriginalName();

        // создание записи в таблице в соответствии с загруженным файлом
        Image::create([
            'name' => $fileName
        ]);

        $fileSize = $file->getClientSize();                             // размер файла
        $destinationPath = 'img';                                       // маршрут для сохранения

        $file->move($destinationPath, $fileName);                       // сохранение загруженного файла по заданному маршруту

        /*получение последнего загруженного файла из БД для последующего сохранения его атрибутов, таких как:
        идентификатор, высота и ширина изображения (разрешение)*/
        $image = Image::latest()->first();

        $attributes = (getimagesize('img\\' . $image->name));   // получение массива с данными об изображении

        Attribute::create([                                             // запись в таблицу атрибутов изображения
            'height' => $attributes[1],
            'width' => $attributes[0],
            'size' => $fileSize,
            'views' => 0,
            'image_id' => $image->id
        ]);
    }

    private function imagesFilter($request) {
        // метод для фильтрации изображений по входящим параметрам фильтра
        $rules = [
            'min_height' => 'required_with:min_width',
            'min_width' => 'required_with:min_height',
            'max_height' => 'required_with:max_width',
            'max_width' => 'required_with:max_height'
        ];
        $messages = [
            'required_with' => 'Заполните второй параметр разрешения!'
        ];

        $this->validate($request, $rules, $messages);

        $images = Image::with('attribute');

        // в зависимости от полученных параметров дополняем запрос
        if($request->filled('min_width') && $request->filled('min_height')){
            $images = $images->whereHas('attribute', function($query) use ($request) {
                $query->where([
                    ['height', '>', $request->min_height],
                    ['width', '>', $request->min_width]
                ]);
            });
        }

        if($request->filled('max_width') && $request->filled('max_height')) {
            $images = $images->whereHas('attribute', function($query) use ($request) {
                $query->where([
                    ['height', '<', $request->max_height],
                    ['width', '<', $request->max_width]
                ]);
            });
        }

        if($request->filled('min_size')){
            $images = $images->whereHas('attribute', function($query) use ($request) {
                $query->where('size', '>', ($request->min_size * 1000));
            });
        }

        if($request->filled('max_size')){
            $images = $images->whereHas('attribute', function($query) use ($request) {
                $query->where('size', '<', ($request->max_size * 1000));
            });
        }

        if($request->filled('min_views')){
            $images = $images->whereHas('attribute', function($query) use ($request) {
                $query->where('views', '>', $request->min_views);
            });
        }

        if($request->filled('max_views')){
            $images = $images->whereHas('attribute', function($query) use ($request) {
                $query->where('views', '<', $request->max_views);
            });
        }

        $countPerPage = $request->session()->get('count', 20);                    // получение количества изображений на странице
        $resultImages = $images->paginate($countPerPage);                         // окончательно сформированный запрос

        return view('gallery', ['images'=>$resultImages]);
    }
}
