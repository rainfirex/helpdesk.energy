<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PDF</title>
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/bootstrap.min.css') }}">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>
<div>
    <h2 class="text-center">Заявка на подключение к информационным ресурсам</h2>
    <hr>
    <div>
        <!--Информация о сотруднике-->
        <div class="mb-5">
            <h4 class="text-center mb-3">Информация о сотруднике</h4>
            <!--Сотрудник-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Сотрудник: {{ $user->name}}</label>
                </div>
            </div>
            <!--Руководитель подразделения-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Руководитель подразделения: {{ $user->leader }}</label>
                </div>
            </div>
            <!--Должность-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Должность: {{ $user->function }}</label>
                </div>
            </div>
            <!--Подразделение-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Подразделение: {{ $user->unit }}</label>
                </div>
            </div>
            <!--Адрес (место работы)-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Адрес (место работы): {{ $user->address }}</label>
                </div>
            </div>
            <!--Кабинет-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Кабинет: {{ $user->cabinet }}</label>
                </div>
            </div>
            <!--Телефон-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-4 col-form-label">
                    <label>Телефон: {{ $user->phone }}</label>
                </div>
            </div>
            <!--Период-->
            <div class="row form-group">
                <div class="offset-md-1 col-md-2 col-form-label">
                    <label>Период: c {{ $user->perStart }} по {{ $user->perEnd }}</label>
                </div>
            </div>
        </div>
        <!--Информационные ресурсы-->
        <div class="mb-5">
            <h4 class="text-center mb-3">Доступ к информационным ресурсам</h4>
            <div class="row form-group box-check d-md-flex flex-md-wrap justify-content-md-center">
                <!--Доступ к ресурсам ЛВС (учетная запись)-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isLogin)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        Доступ к ресурсам ЛВС (учетная запись)
                    </label>
                </div>
                <!--1С УПП-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->is1CUPP)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        1С УПП
                    </label>
                </div>
                <!--1С Зарплата и Управление персоналом-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->is1CZPP)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        1С Зарплата и Управление персоналом
                    </label>
                </div>
                <!--АСУСЭ-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isAsuse)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        АСУСЭ
                    </label>
                </div>
                <!--ОМНИУС-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isOmnius)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        ОМНИУС
                    </label>
                </div>
                <!--USB-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isUSB)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        USB
                    </label>
                </div>
                <!--Электронная почта-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isEmail)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        Электронная почта
                    </label>
                </div>
                <!--Интернет-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isInternet)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        Интернет
                    </label>
                </div>
                <!--Консультант плюс-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isConsult)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        Консультант плюс
                    </label>
                </div>
                <!--Папка обмен-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isFolderObmen)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        Папка обмен
                    </label>
                </div>
                <!--Work от УТД-->
                <div class="form-check">
                    <label class="form-check-label">
                        @if($user->access->isWorkFromUTD)
                            <input class="form-check-input" type="checkbox" checked>
                        @else
                            <input class="form-check-input" type="checkbox">
                        @endif
                        Work от УТД
                    </label>
                </div>
            </div>
        </div>
        <!--Доступ к сетевым ресурсам-->
        <div class="mb-5">
            @if($user->access->lanResource)
            <h4 class="text-center mb-3">Доступ к сетевым ресурсам</h4>
            <div class="form-group offset-md-1 col-md-10">
                <textarea class="form-control" rows="3">{{$user->access->lanResource}}</textarea>
            </div>
            @endif
        </div>
        <!--Другие программные продукты-->
        <div class="mb-5">
            @if($user->access->otherProgram)
            <h4 class="text-center mb-3">Другие программные продукты</h4>
            <div class="form-group offset-md-1 col-md-10">
                <textarea class="form-control" rows="3" v-model="">{{ $user->access->otherProgram }}</textarea>
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>