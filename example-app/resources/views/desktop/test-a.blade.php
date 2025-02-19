@extends('layouts.desktop-app')

@section('styles')
    <!-- Подключаем специфический CSS для этой страницы -->
    @vite(['resources/css/desktop/test-a.css'])

@endsection

@section('content')
    <section class="test__head">
        <div class="wrap">
            <div>
                <h1>Тестовая сдача экзамена</h1>
            </div>
            <div>
                <form action="{{ route('test.storeSelection') }}" method="POST">
                    @csrf
                    <div>
                        <div class="form__block">
                            <p>Выберете категорию:</p>
                            <label>
                                <select name="category_id" id="category_id" required>

                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>





                        {{--<div class="form__block-check">
                            <input class="input__check" type="checkbox" id="random" name="random" checked/>
                            <label for="random">Случайный билет</label>
                        </div>--}}


                        <div class="form__block">
                            <p>Выберите номер билета:</p>
                            <div class="dropdown">
                                <label>
                                    <input class="text-box" type="text" name="ticket_number_id" id="ticket_number_id" value="1" readonly>
                                </label>
                                <div class="options">
                                    @foreach($ticketNumbers as $ticketNumber)
                                        <div onclick="show('{{ $ticketNumber->id }}')">{{ $ticketNumber->number }}</div>
                                    @endforeach

                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="button">
                        <button class="button" type="submit">Продолжить</button>
                    </div>

                </form>
            </div>
        </div>
    </section>



    <section class="content__1">
        <div class="wrap">
            <h2>Правила сдачи теоретического экзамена Госавтоинспекции</h2>
            <p>Согласно требованиям Административного регламента, при сдаче теоретического
                экзамена на получение водительских прав, кандидаты, которые допустили
                одну ошибку или две ошибки, но в разных тематических блоках
                экзаменационных вопросов, получают возможность ответить на дополнительные
                5 вопросов за каждую ошибку. Это правило позволяет повысить шансы
                на успешную сдачу экзамена, предоставляя дополнительную
                возможность продемонстрировать свои знания и навыки.</p>
        </div>
    </section>
    <script>
        function show(value) {
            document.querySelector(".text-box").setAttribute('value', value);
        }

        let dropdown = document.querySelector(".dropdown")
        dropdown.onclick = function () {
            dropdown.classList.toggle("active")
        }



    </script>
@endsection



