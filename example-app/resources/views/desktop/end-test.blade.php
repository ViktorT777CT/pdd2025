@extends('layouts.desktop-app')

@section('styles')
    <!-- Подключаем специфический CSS для этой страницы -->
    @vite(['resources/css/desktop/end-test.css'])

@endsection
@section('content')
    <section class="tickets__head">
        <div class="wrap">
            <div>
                <h1>
                    @if($results['correct'] >= 18)
                        Вы успешно сдали тестовый экзамен!!!
                    @else
                        <span class="results__red">Вы не сдали экзамен!!!</span>

                    @endif


                </h1>
            </div>
            <div class="tickets__head-new">
                <a href="{{ route('test.test-a') }}">выбрать другой билет</a>
            </div>
        </div>
    </section>
    <section>
        <div class="wrap results">
            <div>
                <p>Затраченное время: <span class="results__red"></span>{{ gmdate('i:s', $results['time_spent']) }}</p>
            </div>
            <div>
                <p>Правильных ответов: <span class="results__red"></span>{{ $results['correct'] }}</p>
            </div>
            <div>
                <p>Допущено ошибок: <span class="results__red">{{ $results['incorrect'] }}</span></p>
                {{--<ul>
                    <li>

                        <details class="details">
                            <summary class="details__title">
                                <h3>Билет 1 вопрос 7 (развернуть)</h3>
                            </summary>
                            <div class="details__content">
                                <div class="ticket">
                                    <div class="ticket__content">
                                        <div class="ticket__img">
                                            <img src="/img/AB-1-2_1_11zon.webp" alt="">
                                        </div>
                                        <div>
                                            <h2>Разрешен ли Вам поворот
                                                на дорогу с грунтовым
                                                покрытием?</h2>
                                        </div>
                                        <div class="ticket__answer">
                                            <ol>
                                                <li>
                                                    <button type="button">Разрешен.</button>
                                                </li>
                                                <li>
                                                    <button class="button__active" type="button">Разрешен только при
                                                        технической неисправности
                                                        транспортного средства.Разрешен только при
                                                        технической неисправности
                                                        транспортного средства.Разрешен только при
                                                        технической неисправности
                                                        транспортного средства.Разрешен только при
                                                        технической неисправности
                                                        транспортного средства.
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button">Запрещен.</button>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <p>
                                    Знаки 1.11.2 «Опасный поворот» и 1.34.2 «Направление поворота» указывают направление
                                    движения на закруг-
                                    лениях дороги малого радиуса с ограниченной видимостью.
                                    Эти знаки не ограничивают возможность выполнить другой
                                    поворот, в данном случае — на примыкающую справа грунтовую дорогу.
                                    <b>Ответ — 1</b>
                                </p>
                            </div>
                        </details>
                    </li>
                </ul>--}}
            </div>
        </div>
    </section>
@endsection



