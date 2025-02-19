<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @yield('styles')
</head>
<body>
<div>
    <header>
        <nav class="wrap">
            <div class="nav">
                <div>
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('images/pdd.png') }}" width="60" height="57" alt="Билеты ПДД">
                    </a>
                </div>
                <input type="checkbox" id="burger-checkbox" class="burger-checkbox">
                <label for="burger-checkbox" class="burger"></label>
                <ul class="menu-list">

                    <li class="nav-item">
                        <a href="">
                            ПДД 2024
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">
                                    Общие правила дорожного движения
                                </a>
                            </li>
                            <li>
                                <a href="#">ПДД для водителей</a>
                            </li>
                            <li>
                                <a href="#">ПДД для пешеходов</a>
                            </li>
                            <li>
                                <a href="#">Правила для велосипедистов</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="">
                            Билеты ПДД
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">Категолрия ABM</a>
                            </li>
                            <li>
                                <a href="#">Категолрия DC</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="">
                            Сдать экзамен
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">Категолрия ABM</a>
                            </li>
                            <li>
                                <a href="#">Категолрия DC</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

        </nav>
    </header>
    <main>
        @yield('content')
    </main>
</div>

<footer>
    <div class="wrap">
        <div>
            <div class="footer__logo">
                <a href="">
                    <img src="{{ asset('images/pdd.png') }}" width="60" height="57" alt="Билеты ПДД">
                </a>
            </div>
            <div class="copyright">
                <p>Copyright © 2024 | <a href="mailto:p_viktor91@mail.ru"><b>p_viktor91@mail.ru</b></a></p>
            </div>
        </div>
        <div class="footer__nav">
            <a href="#">
                <h4>ПДД 2024</h4>
            </a>
            <ul>
                <li>
                    <a href="#">
                        Общие правила дорожного движения
                    </a>
                </li>
                <li>
                    <a href="#">ПДД для водителей</a>
                </li>
                <li>
                    <a href="#">ПДД для пешеходов</a>
                </li>
                <li>
                    <a href="#">Правила для велосипедистов</a>
                </li>
            </ul>
        </div>
        <div class="footer__nav">
            <a href="#">
                <h4>Билеты ПДД</h4>
            </a>
            <ul>
                <li>
                    <a href="#">Категолрия ABM</a>
                </li>
                <li>
                    <a href="#">Категолрия DC</a>
                </li>
            </ul>
        </div>
        <div class="footer__nav">
            <a href="#">
                <h4>Сдать Экзамен</h4>
            </a>
            <ul>
                <li>
                    <a href="#">Категолрия ABM</a>
                </li>
                <li>
                    <a href="#">Категолрия DC</a>
                </li>
            </ul>
        </div>
        <div class=" footer__nav footer__nav-pdd">
            <p>Данные ПДД и билеты ПДД <br> взяты с официального сайта <br> <a href="https://xn--80aebkobnwfcnsfk1e0h.xn--p1ai/"><b>ГОСАВТОИНСПЕКЦИИ</b></a>
                <br> и регулярно обновляются</p>
        </div>
    </div>
</footer>
</body>
</html>
