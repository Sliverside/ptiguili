<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ptiguli</title>
    @vite(['resources/css/ptiguili.css', 'resources/js/app.js'])
</head>
<body>
    <header id="mainHeader">
        <img src="logo.svg" alt="Ptiguili">
        <span class="totalCoins">{{ Auth::user()->wallet->coins }}</span>
    </header>
    <hr>

    @if (count($wonGifts))
    <h2 class="h1">Tes derniers gains</h1>
    <div class="tableLikeList">
        <ul>
            @foreach ($wonGifts as $gift)
                <li class="tableLikeList__item">{{ $gift->name }} <span class="tableLikeList__itemAside"><button class="btn no-wrap">Utiliser (100 <span class="coin"></span>)</button></span></li>
            @endforeach
        </ul>
        <hr>
        <div class="tableLikeList__item">
            <button class="btn w-100">Voir tout (12)</button>
        </div>
    </div>
    <hr>
    @endif


    <form action="{{ route('wallets.useCoin') }}" method="post">
        @csrf
        <button class="btn btn--big">
            Jouer (50 <span class="coin"></span>) !
        </button>
    </form>
</body>
</html>
