<?php

namespace App\Http\Controllers;

use App\Models\GiftsBag;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class GiftsBagsController extends Controller
{
    public function index()
    {
        $giftsBag = GiftsBag::whereBelongsTo(Auth::user())->with([
            'gifts' => fn (HasMany $query) => $query->withProbability()->latest('gifts.id'),
        ])->first();

        return view('giftsBags.index', [
            'giftsBag' => $giftsBag,
            'gifts' => $giftsBag->gifts,
        ]);
    }

    public function update(Request $request, GiftsBag $giftsBag)
    {
        $giftsBag->update($request->validateWithBag('giftsBag', [
            'probability' => 'required|numeric|between:0,100',
        ]));

        return Redirect::route('giftsBag');
    }
}
