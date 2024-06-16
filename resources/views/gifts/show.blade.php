<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Mon cadeau') }}
      </h2>
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
        <h3>Nom : {{ $gift->name }}</h3>
        <p>Description: {{ $gift->description }}</p>
      </div>
      <div class="mt-5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
        <p>tu as gagner ce cadeau {{ $gift->count_wins }} foi(s), tu l'as utilisé {{ $gift->count_wins_used }} foi(s)</p>
        @if ($gift->count_wins_pending > 0)
          <img src="{{ $qrcode }}" alt="qrcode" width="296" style="max-width: 100%">
          @env('local')
            <a href="{{ $ownerLink }}">{{ $ownerLink }}</a>
          @endenv
          {{-- <form class="mt-2" action="{{ route('gifts.request', $gift) }}" method="post">
            @csrf
            <x-primary-button class="bg-lime-500 hover:bg-lime-400">Demander ce cadeau par mail</x-primary-button>
          </form> --}}
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
