@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Результаты теста</h1>

        <div class="card mb-3">
            <div class="card-header">Ваши результаты</div>
            <div class="card-body">
                <p>Верных ответов: <strong>{{ $results['correct'] }}</strong></p>
                <p>Неверных ответов: <strong>{{ $results['incorrect'] }}</strong></p>
                <p>Затраченное время: <strong>{{ gmdate('i:s', $results['time_spent']) }}</strong></p>
            </div>
        </div>


    </div>
@endsection
