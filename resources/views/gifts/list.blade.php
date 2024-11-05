@extends('layouts.app-v2')

@section('content')
    @if (count($wonGifts))
        <h1>Ton butin</h1>
        @foreach ($wonGifts as $gift)
            <article class="article">
                <h2 class="p-bold">{{ $gift->name }}</h2>
                {{-- <p>{{ $gift->description }}</p> --}}
                <div class="btnsGroup">
                    @if ($gift->count_wins_pending)
                        <a class="btn no-wrap" href="{{ route('gifts.show', $gift) }}">Utiliser ({{ $gift->price }} <span class="coin"></span>)</a>
                        <a class="btn no-wrap" href="#">Vendre (+{{ $gift->sell_price }} <span class="coin"></span>)</a>
                    @endif
                    <a class="btn no-wrap" href="{{ route('gifts.show', $gift) }}">Plus d'infos</a>
                </div>
                <hr>
            </article>
        @endforeach
    @endif
@endsection
