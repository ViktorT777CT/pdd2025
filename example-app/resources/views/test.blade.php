@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Таймер -->
        <div class="text-end mb-3">
            <h3>Осталось времени: <span id="timer"></span></h3>
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
                    <button class="btn btn-secondary" disabled>Завершить тест</button>
                    <div class="alert alert-info mt-3">
                        Ответьте на все вопросы, чтобы завершить тест. Осталось ответить на {{ $totalQuestions - $answeredQuestions }} вопросов.
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Скрипт для таймера -->
    <script>
        const timerElement = document.getElementById('timer');
        const timeSpentInput = document.getElementById('timeSpent');
        const startTime = 20 * 60; // 20 минут в секундах
        let timerInterval;

        // Получаем текущее время из localStorage или устанавливаем начальное
        let timeLeft = localStorage.getItem('timeLeft') ? parseInt(localStorage.getItem('timeLeft')) : startTime;

        // Немедленно отобразить оставшееся время при загрузке страницы
        function displayTime() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        // Функция обновления таймера
        function updateTimer() {
            if (timeLeft > 0) {
                timeLeft--; // Уменьшаем оставшееся время
                localStorage.setItem('timeLeft', timeLeft); // Сохраняем оставшееся время в localStorage
            }

            // Обновляем отображение времени
            displayTime();

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                localStorage.removeItem('timeLeft'); // Очистка времени в localStorage, если тест завершен
                alert('Время вышло! Тест завершен.');
                document.getElementById('completeForm').submit(); // Автоматически завершить тест
            }
        }

        // Запускаем таймер при загрузке страницы
        displayTime(); // Отображаем время на экране сразу
        timerInterval = setInterval(updateTimer, 1000); // Начинаем отсчет времени

        // Обработка завершения теста
        document.getElementById('completeForm')?.addEventListener('submit', () => {
            clearInterval(timerInterval); // Останавливаем таймер
            localStorage.removeItem('timeLeft'); // Очищаем localStorage
            const timeSpent = startTime - timeLeft; // Считаем затраченное время
            timeSpentInput.value = timeSpent; // Заполняем скрытое поле перед отправкой
        });
    </script>
@endsection
