@extends('layouts.app-v2')

@section('content')
    @if (count($wonGifts))
        <h2 class="h1">Tes derniers gains</h2>
        <div class="tableLikeList">
            <ul>
                @foreach ($wonGifts as $gift)
                    <li class="tableLikeList__item">
                        <a href="{{ route('gifts.show', $gift) }}">{{ $gift->name }}</a>
                        <span class="tableLikeList__itemAside">
                            <a class="btn no-wrap" href="{{ route('gifts.show', $gift) }}">Utiliser ({{ $gift->price }} <span
                                    class="coin"></span>)</a>
                        </span>
                    </li>
                @endforeach
            </ul>
            <hr>
            <div class="tableLikeList__item">
                <a class="btn w-100" href="{{ route('gifts.list') }}">Voir tout ({{ $wonGiftsCount }})</a>
            </div>
        </div>
        <hr>
    @endif

    <form action="{{ route('wallets.useCoin') }}" method="post">
        @csrf
        <button class="btn btn--big">
            Jouer ({{ config('app.wheelGamePrice') }} <span class="coin"></span>) !
        </button>
    </form>

@endsection
