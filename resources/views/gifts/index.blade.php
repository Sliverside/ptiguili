<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Accueil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('wallets.useCoin') }}" method="post">
                        @csrf
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Utiliser un Coin') }} ({{ Auth::user()->wallet->coins }})
                        </button>
                    </form>
                    <h2 class="text-xl mt-5">Mon butin</h2>
                    <x-table>
                        <x-table.head :headings="['Cadeau', 'gagné', 'disponibles', 'actions']" />
                        <x-table.body>
                            @foreach ($wonGifts as $gift)
                            <x-table.row>
                                <x-table.cell :link="route('gifts.show', $gift)">{{ $gift->name }}</x-table.cell>
                                <x-table.cell :link="route('gifts.show', $gift)">{{ $gift->count_wins }} fois</x-table.cell>
                                <x-table.cell :link="route('gifts.show', $gift)">{{ $gift->count_wins_pending }}</x-table.cell>
                                <x-table.cell class="flex gap-2">
                                    <x-primary-button :link="route('gifts.show', $gift)">voir</x-primary-button>
                                    {{-- <form action="{{ route('gifts.request', $gift) }}" method="post">
                                        @csrf
                                        @if($gift->count_wins_pending)
                                            <x-primary-button class="bg-lime-500 hover:bg-lime-400">Utiliser ({{ $gift->count_wins_pending }}&nbsp;dispo)</x-primary-button>
                                        @endif
                                    </form> --}}
                                </x-table.cell>
                            </x-table.row>
                            @endforeach
                        </x-table.body>
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
