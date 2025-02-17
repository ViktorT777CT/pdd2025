@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Таймер -->
        <div class="text-end mb-3">
            <h3>Осталось времени: <span id="timer">{{ gmdate('i:s', session('time_left', 20 * 60)) }}</span></h3>
        </div>

        <h1>Вопрос #{{ $currentQuestion->question_number_id }}</h1>

        <!-- Форма для сохранения ответа -->
        <form action="{{ route('test.saveAnswer') }}" method="POST">
            @csrf
            <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">

            <div class="card mb-3">
                <div class="card-header">{{ $currentQuestion->question }}</div>
                <div class="card-body">
                    @foreach($currentQuestion->answers as $answer)
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="answer_id"
                                   id="answer{{ $answer->id }}"
                                   value="{{ $answer->id }}"
                                   {{ session("answers.{$currentQuestion->id}") == $answer->id ? 'checked' : '' }}
                                   onchange="this.form.submit()"> <!-- Отправка формы при выборе ответа -->
                            <label class="form-check-label" for="answer{{ $answer->id }}">
                                {{ $answer->answer }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>

        <!-- Пагинация -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach($tickets as $ticket)
                <a href="{{ route('test.show', $ticket->question_number_id) }}"
                   class="btn btn-sm {{ isset($userAnswers[$ticket->id]) ? 'btn-success' : 'btn-outline-secondary' }}">
                    {{ $ticket->question_number_id }}
                </a>
            @endforeach
        </div>

        <!-- Кнопки навигации -->
        <div class="d-flex justify-content-between">
            @if($previousQuestion)
                <a href="{{ route('test.show', $previousQuestion->question_number_id) }}" class="btn btn-primary">Назад</a>
            @else
                <button class="btn btn-secondary" disabled>Назад</button>
            @endif

            @if($nextQuestion)
                <a href="{{ route('test.show', $nextQuestion->question_number_id) }}" class="btn btn-primary">Далее</a>
            @else
                <!-- Кнопка "Завершить тест" -->
                @if($answeredQuestions == $totalQuestions)
                    <form action="{{ route('test.complete') }}" method="POST" id="completeForm">
                        @csrf
                        <input type="hidden" name="time_spent" id="timeSpent">
                        <button type="submit" class="btn btn-danger">Завершить тест</button>
                    </form>
                @else

                @endif
            @endif
        </div>
    </div>

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
