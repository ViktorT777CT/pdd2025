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
        // Очищаем сессию (если нужно)
        session()->forget(['answers', 'time_left', 'timer_started']);
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
        // Получаем выбранные параметры из сессии
        $selectedCategoryId = session('selected_category_id');
        $selectedTicketNumberId = session('selected_ticket_number_id');

        // Если параметры не переданы, возвращаем ошибку
        if (!$selectedCategoryId || !$selectedTicketNumberId) {
            abort(400, 'Категория или номер билета не выбраны');
        }

        // Получаем все вопросы для текущего билета и категории
        $tickets = Ticket::with('answers')
            ->where('category_id', $selectedCategoryId)
            ->where('ticket_number_id', $selectedTicketNumberId)
            ->get();

        // Находим текущий вопрос по question_number_id
        $currentQuestion = $tickets->firstWhere('question_number_id', $questionNumberId);

        // Если вопрос не найден, возвращаем ошибку
        if (!$currentQuestion) {
            abort(404, 'Вопрос не найден');
        }

        // Инициализируем таймер в сессии (если он еще не инициализирован)
        if (!session()->has('timer_started')) {
            session(['timer_started' => true]);
            session(['time_left' => 20 * 60]); // 20 минут в секундах
        }

        // Определяем индексы предыдущего и следующего вопросов
        $currentIndex = $tickets->search(function ($item) use ($currentQuestion) {
            return $item->question_number_id === $currentQuestion->question_number_id;
        });

        $previousQuestion = $tickets[$currentIndex - 1] ?? null;
        $nextQuestion = $tickets[$currentIndex + 1] ?? null;

        // Получаем общее количество вопросов
        $totalQuestions = $tickets->count();

        // Получаем количество ответов, сохраненных в сессии
        $userAnswers = session('answers', []);
        $answeredQuestions = count($userAnswers);

        // Передаем данные в представление
        return view('test', compact(
            'currentQuestion',
            'previousQuestion',
            'nextQuestion',
            'totalQuestions',
            'answeredQuestions',
            'tickets', // Передаем все вопросы для пагинации
            'userAnswers' // Передаем ответы для подсветки кнопок
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
    public function updateTimer(Request $request)
    {
        // Валидация данных
        $request->validate([
            'time_left' => 'required|integer|min:0',
        ]);

        // Сохраняем оставшееся время в сессии
        session(['time_left' => $request->time_left]);

        return response()->json(['success' => true]);
    }

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

        // Очищаем сессию (если нужно)
        session()->forget(['answers', 'time_left', 'timer_started']);

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
