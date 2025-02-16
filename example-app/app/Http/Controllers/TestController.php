<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketNumber;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function select()
    {
        // Получаем список всех категорий и номеров билетов
        $categories = Category::all();
        $ticketNumbers = TicketNumber::all();

        // Отображаем страницу выбора
        return view('select', compact('categories', 'ticketNumbers'));
    }

    public function storeSelection(Request $request)
    {
        // Валидация выбора
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'ticket_number_id' => 'required|exists:ticket_numbers,id',
        ]);

        // Сохраняем выбранные параметры в сессии
        session([
            'selected_category_id' => $request->input('category_id'),
            'selected_ticket_number_id' => $request->input('ticket_number_id'),
        ]);

        // Перенаправляем на первый вопрос
        return redirect()->route('test.show', 1);
    }

    public function show(Request $request, $questionNumberId)
    {
        $selectedCategoryId = session('selected_category_id');
        $selectedTicketNumberId = session('selected_ticket_number_id');

        if (!$selectedCategoryId || !$selectedTicketNumberId) {
            abort(400, 'Категория или номер билета не выбраны');
        }

        // Проверяем, установлена ли начальная метка времени
        if (!session()->has('test_start_time')) {
            session(['test_start_time' => now()]);
        }

        // Проверяем время, прошедшее с начала теста
        $startTime = session('test_start_time');
        $elapsedTime = now()->diffInSeconds($startTime); // Прошедшее время в секундах

        // Время, которое осталось (20 минут - прошедшее время)
        $remainingTime = max(0, 20 * 60 - $elapsedTime);

        $tickets = Ticket::with('answers')
            ->where('category_id', $selectedCategoryId)
            ->where('ticket_number_id', $selectedTicketNumberId)
            ->get();

        $currentQuestion = $tickets->firstWhere('question_number_id', $questionNumberId);
        if (!$currentQuestion) {
            abort(404, 'Вопрос не найден');
        }

        $currentIndex = $tickets->search(fn($item) => $item->question_number_id === $currentQuestion->question_number_id);
        $previousQuestion = $tickets[$currentIndex - 1] ?? null;
        $nextQuestion = $tickets[$currentIndex + 1] ?? null;
        $totalQuestions = $tickets->count();
        $userAnswers = session('answers', []);
        $answeredQuestions = count($userAnswers);

        return view('test', compact(
            'currentQuestion', 'previousQuestion', 'nextQuestion',
            'totalQuestions', 'answeredQuestions', 'tickets', 'userAnswers', 'remainingTime'
        ));
    }

    public function saveAnswer(Request $request)
    {
        // Валидация данных
        $request->validate([
            'question_id' => 'required|exists:tickets,id',
            'answer_id' => 'required|exists:answers,id',
        ]);

        // Сохраняем ответ в сессии
        session(["answers.{$request->question_id}" => $request->answer_id]);

        // Получаем текущий вопрос
        $currentQuestion = Ticket::find($request->question_id);

        // Получаем все вопросы для текущего билета и категории
        $tickets = Ticket::where('category_id', session('selected_category_id'))
            ->where('ticket_number_id', session('selected_ticket_number_id'))
            ->get();

        // Находим индекс текущего вопроса
        $currentIndex = $tickets->search(function ($item) use ($currentQuestion) {
            return $item->id === $currentQuestion->id;
        });

        // Получаем следующий вопрос
        $nextQuestion = $tickets[$currentIndex + 1] ?? null;

        // Если следующий вопрос существует, перенаправляем на него
        if ($nextQuestion) {
            return redirect()->route('test.show', $nextQuestion->question_number_id);
        }

        // Если следующего вопроса нет, остаемся на текущей странице
        return redirect()->route('test.show', $currentQuestion->question_number_id);
    }


/*    public function store(Request $request)
    {
        // Получаем выбранные параметры из сессии
        $selectedCategoryId = session('selected_category_id');
        $selectedTicketNumberId = session('selected_ticket_number_id');

        // Если параметры не переданы, возвращаем ошибку
        if (!$selectedCategoryId || !$selectedTicketNumberId) {
            abort(400, 'Категория или номер билета не выбраны');
        }

        // Фильтруем вопросы по выбранным параметрам
        $ticketsQuery = Ticket::with('answers')
            ->where('category_id', $selectedCategoryId)
            ->where('ticket_number_id', $selectedTicketNumberId);

        // Получаем все вопросы для текущего билета и категории
        $tickets = $ticketsQuery->get();

        // Подсчет правильных и неправильных ответов
        $score = 0;
        $errors = [];

        foreach ($request->input('answers', []) as $questionId => $answerId) {
            $answer = Answer::find($answerId);
            if ($answer && $answer->true_answer) {
                $score++;
            } else {
                $errors[] = $questionId;
            }
        }

        // Отображаем страницу с результатами
        return view('result', [
            'score' => $score,
            'total' => $tickets->count(), // Общее количество вопросов в выбранной категории и билете
            'errors' => $errors,
        ]);
    }*/


    public function complete(Request $request)
    {
        // Получаем все ответы из сессии
        $userAnswers = session('answers', []);

        // Инициализируем счетчики
        $correctCount = 0;
        $incorrectCount = 0;

        // Проверяем каждый ответ
        foreach ($userAnswers as $questionId => $answerId) {
            // Находим ответ в таблице `answers`
            $answer = Answer::find($answerId);

            // Проверяем, верный ли ответ
            if ($answer && $answer->true_answer == 1) {
                $correctCount++;
            } else {
                $incorrectCount++;
            }
        }

        // Получаем затраченное время из формы
        $timeSpent = $request->input('time_spent'); // В секундах

        // Сохраняем результаты в сессии
        session([
            'results' => [
                'correct' => $correctCount,
                'incorrect' => $incorrectCount,
                'time_spent' => $timeSpent, // Добавляем затраченное время
            ],
        ]);

        // Очищаем ответы из сессии (если нужно)
        /*session()->forget('answers');*/

        // Перенаправляем на страницу с результатами
        return redirect()->route('result');
    }
    public function result()
    {
        // Получаем результаты из сессии
        $results = session('results', [
            'correct' => 0,
            'incorrect' => 0,
        ]);
        // Очищаем ответы из сессии (если нужно)
        session()->forget('answers');
        /*dd($results);*/
        // Отображаем страницу с результатами
        return view('result', compact('results'));
    }
}
