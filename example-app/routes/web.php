<?php

use App\Http\Controllers\TestController;

Route::get('/', [TestController::class, 'index'])->name('index');

// Обработка выбора категории и номера билета
Route::post('/select', [TestController::class, 'storeSelection'])->name('test.storeSelection');
// Страница выбора категории и номера билета
Route::get('/select', [TestController::class, 'select'])->name('test.test-a');

// Перенаправление, если зашли на `/test` без параметра
Route::get('/test', function () {
    return redirect()->route('test.test-a');
});


// Показываем конкретный вопрос по `question_number_id`
Route::get('/test/{question_number_id}', [TestController::class, 'show'])->name('test.show');

Route::post('/test/save-answer', [TestController::class, 'saveAnswer'])->name('test.saveAnswer');


// Обработка завершения теста
/*Route::post('/test', [TestController::class, 'store'])->name('test.store');*/

Route::post('/test/complete', [TestController::class, 'complete'])->name('test.complete');
Route::get('/result', [TestController::class, 'result'])->name('result');

Route::post('/test/update-timer', [TestController::class, 'updateTimer'])->name('test.updateTimer');

