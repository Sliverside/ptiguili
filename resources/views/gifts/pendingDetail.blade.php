<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Requète de cadeau') }}
      </h2>
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
        <h3>Nom : {{ $gift->name }}</h3>
        <p>Description : {{ $gift->description }}</p>
        <p>Gagné le : {{ $win->created_at->format('d/m/y à H\hi') }}</p>
      </div>
      <div class="mt-5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
        <p>Confirmer la requète</p>
        <form action="{{ route('gifts.confirm', $win) }}" method="POST">
          @method('patch')
          @csrf()
          <input type="file">
          <x-primary-button class="bg-lime-500 hover:bg-lime-400">{{ __('Confirmer l\'éxexcution') }}</x-primary-button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
