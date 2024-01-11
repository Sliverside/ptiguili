<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Ma hote de cadeaux') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-3 text-gray-900 dark:text-gray-100">
            <div class="p-3">

              @if ($errors->hasBag('giftsBag'))
              <p>
                Le cadeau n'a pas été mis à jour :
                  <ul>
                      @foreach ($errors->getBag('giftsBag')->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
                </p>
              @endif
              <h2>Probabilité de donner un cadeau en % :</h2>
              <form class="flex items-end gap-3 mt-1" method="POST" action="{{ route('giftsBag.update', $giftsBag) }}">
                @csrf
                @method("patch")
                <x-text-input class="block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" type="text" placeholder="Probabilité" name="probability" value="{{$giftsBag->probability}}"/>
                <x-primary-button>Mettre à jour</x-primary-button>
              </form>
            </div>
          </div>
        </div>
        <div class="mt-5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-3 text-gray-900 dark:text-gray-100">
            <div class="p-3">
              <h2>Créé un nouveau cadeau</h2>
              <form class="flex items-end gap-3 mt-1" method="POST" action="{{ route('gifts.store') }}">
                @csrf
                <label>
                  <x-input-label :value="__('Nom')" />
                  <x-text-input type="text" name="name" value="{{old('name')}}"/>
                </label>
                <label>
                  <x-input-label :value="__('Description')" />
                  <x-text-input type="text" name="description" value="{{old('description')}}"/>
                </label>
                <label>
                  <x-input-label :value="__('Probabilité relative en %')" />
                  <x-text-input
                    type="text"
                    name="relative_probability"
                    value="{{old('relative_probability')}}"/>
                </label>
                <x-primary-button>Enregistré</x-primary-button>
              </form>
            </div>
          </div>
        </div>
        @if ($gifts->isNotEmpty())
          <div class="mt-5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-3 text-gray-900 dark:text-gray-100">
                @foreach ($gifts as $gift)
                  <div id="gift_form_{{$gift->id}}" @class([
                    'p-3',
                    'bg-blue-400' => session('giftNoUpdate' . $gift->id) == true,
                    'bg-green-400' => session('giftUpdate' . $gift->id) == true,
                    'bg-red-400' => $errors->hasBag('gift' . $gift->id),
                  ])>
                    @if ($errors->hasBag('gift' . $gift->id))
                      <p>
                        Le cadeau n'a pas été mis à jour :
                          <ul>
                              @foreach ($errors->getBag('gift' . $gift->id)->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                        </p>
                    @endif
                    <p>
                      créé le&nbsp;: {{$gift->created_at->format('d/m/y à H\hi')}} et modifié le&nbsp;: {{$gift->updated_at->format('d/m/y à H\hi')}}
                    </p>
                    <form class="flex items-end gap-3 mt-1" method="POST" action="{{ route('gifts.update', $gift) }}#gift_form_{{$gift->id}}">
                      @method("patch")
                      @csrf
                      <label>
                        <x-input-label :value="__('Nom')" />
                        <x-text-input type="text" name="name" value="{{$gift->name}}"/>
                      </label>
                      <label>
                        <x-input-label :value="__('Description')" />
                        <x-text-input type="text" name="description" value="{{$gift->description}}"/>
                      </label>
                      <label>
                        <x-input-label :value="__('Probabilité relative en %')" />
                        <x-text-input class="block w-full" type="text" name="relative_probability" value="{{$gift->relative_probability}}"/>
                      </label>
                      @if ($gift->probability)
                        <label>
                          <x-input-label :value="__('Probabilité en %')" />
                          <x-text-input class="block w-full" type="text" disabled value="{{round($gift->probability, 2)}}"/>
                        </label>
                      @endif
                      <x-primary-button class="whitespace-nowrap">Mettre à jour</x-primary-button>
                      <x-danger-button
                        type="button"
                        onclick="document.getElementById('deleteGift{{$gift->id}}').submit()"
                      >Supprimer</x-primary-button>
                    </form>
                    <form id="deleteGift{{$gift->id}}" method="POST" action="{{ route('gifts.delete', $gift) }}">
                      @method("delete")
                      @csrf
                    </form>
                  </div>
                @endforeach
              </div>
          </div>
        @endif
      </div>
  </div>
</x-app-layout>
