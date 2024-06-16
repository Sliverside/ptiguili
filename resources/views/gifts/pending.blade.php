<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Mes cadeaux en attente d\'execution') }}
      </h2>
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      @forelse ($pendingGifts as $gift)
        <div @class([
          'bg-white',
          'dark:bg-gray-800',
          'overflow-hidden',
          'shadow-sm',
          'sm:rounded-lg',
          'p-6',
          'text-gray-900',
          'mt-4' => $loop->index !== 0
        ])>
          <h2 class="text-xl font-semibold">{{ $gift->name }}({{ $gift->wons->count() }})</h2>
          <p>{{ $gift->description }}</p>
          <h3 class="text-lg font-bold mt-3">Historique des demandes</h3>
          <x-table>
            <x-table.body>
              <x-table.head :headings="['Gagné le', 'Par', 'actions']" />
              @foreach ($gift->wons as $win)
                <x-table.row>
                  <x-table.cell>{{ $win->created_at->format('d/m/y à H\hi') }}</x-table.cell>
                  <x-table.cell>{{ $win->winner->name }}</x-table.cell>
                  <x-table.cell class="flex gap-2">
                    <x-primary-button link="{{ route('gifts.pendingDetail', $win) }}" class="bg-lime-500 hover:bg-lime-400">{{ __('Confirmer l\'éxexcution') }}</x-primary-button>
                  </x-table.cell>
                </x-table.row>
                @endforeach
            </x-table.body>
        </x-table>
        </div>
      @empty
      Aucun cadeau en attente...
      @endforelse
    </div>
  </div>
</x-app-layout>
