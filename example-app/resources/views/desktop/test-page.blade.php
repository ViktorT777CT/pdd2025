@extends('layouts.desktop-app')

@section('styles')
    <!-- Подключаем специфический CSS для этой страницы -->
    @vite(['resources/css/desktop/test-page.css'])

@endsection

@section('content')
    <section  class="tickets__head">
        <div class="wrap">

            <!-- Пагинация -->
            <div class="pagination">
                @foreach($tickets as $ticket)
                    <a href="{{ route('test.show', $ticket->question_number_id) }}"
                       class=" {{ isset($userAnswers[$ticket->id]) ? 'check' : 'btn-outline-secondary' }}">
                        {{ $ticket->question_number_id }}
                    </a>
                @endforeach
            </div>

            <div class="block__nav-test">
                <!-- Кнопки навигации -->
                <div class="button__pagination">
                    @if($previousQuestion)
                        <a href="{{ route('test.show', $previousQuestion->question_number_id) }}" class="previous active">Назад</a>
                    @else
                        <a href="" class="previous">Назад</a>
                    @endif

                    @if($nextQuestion)
                        <a href="{{ route('test.show', $nextQuestion->question_number_id) }}" class="next active">Далее</a>
                    @else
                            <a href="" class="next">Далее</a>
                    @endif

                </div>





                <!-- Таймер -->
                <div class="text-end mb-3">
                    <h3>Осталось времени: <span id="timer">{{ gmdate('i:s', session('time_left', 20 * 60)) }}</span></h3>
                </div>
            </div>

            <div>
                <h1>Билет № {{ $selectedTicketNumberId }} вопрос № {{ $currentQuestion->question_number_id }}</h1>
                <div class="end__test">
                    <!-- Кнопка "Завершить тест" -->
                    @if($answeredQuestions == $totalQuestions)
                        <form action="{{ route('test.complete') }}" method="POST" id="completeForm">
                            @csrf
                            <input type="hidden" name="time_spent" id="timeSpent">
                            <button type="submit" class="button__end">Завершить тест</button>
                        </form>
                    @else

                    @endif
                </div>
            </div>
        </div>
    </section>


    <section>
        <div class="wrap">
            <form action="{{ route('test.saveAnswer') }}" method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <div class="ticket">
                    <div class="ticket__content">
                        @if($currentQuestion->image)
                            <div class="ticket__img">
                                <img src="{{ asset($currentQuestion->image) }}" alt="">
                            </div>
                        @else


                        @endif

                        <div>
                            <h2>{{ $currentQuestion->question }}</h2>
                        </div>
                        <div class="ticket__answer">

                            <ol>
                                @foreach($currentQuestion->answers as $answer)
                                    <li>
                                        <label class="radio-button">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="answer_id"
                                                   id="answer{{ $answer->id }}"
                                                   value="{{ $answer->id }}"
                                                   {{ session("answers.{$currentQuestion->id}") == $answer->id ? 'checked' : '' }}
                                                   onchange="this.form.submit()"> <!-- Отправка формы при выборе ответа -->
                                            <span class="radio-button__label">{{ $answer->answer }}</span>
                                        </label>
                                    </li>
                                @endforeach

                            </ol>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>



    <!-- Скрипт для таймера -->
    <script>
        // Время в секундах (из сессии или 20 минут по умолчанию)
        let timeLeft = {{ session('time_left', 20 * 60) }};
        const timerElement = document.getElementById('timer');
        const timeSpentInput = document.getElementById('timeSpent');
        let timerInterval;

        // Функция обновления таймера
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('Время вышло! Тест завершен.');
                document.getElementById('completeForm').submit(); // Автоматически завершаем тест
            } else {
                timeLeft--;
                // Сохраняем оставшееся время в сессии
                fetch("{{ route('test.updateTimer') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ time_left: timeLeft })
                });
            }
        }

        // Запуск таймера
        timerInterval = setInterval(updateTimer, 1000);

        // Сохраняем затраченное время при завершении теста
        document.getElementById('completeForm')?.addEventListener('submit', () => {
            clearInterval(timerInterval); // Останавливаем таймер
            const timeSpent = 20 * 60 - timeLeft; // Затраченное время в секундах
            timeSpentInput.value = timeSpent; // Передаем значение в скрытое поле формы
        });
    </script>
@endsection

