@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Выберите категорию и номер билета</h1>

        <form action="{{ route('test.storeSelection') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="category_id">Категория:</label>
                <select name="category_id" id="category_id" class="form-control" required>

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="ticket_number_id">Номер билета:</label>
                <select name="ticket_number_id" id="ticket_number_id" class="form-control" required>

                    @foreach($ticketNumbers as $ticketNumber)
                        <option value="{{ $ticketNumber->id }}">{{ $ticketNumber->number }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Продолжить</button>
        </form>
    </div>
@endsection
