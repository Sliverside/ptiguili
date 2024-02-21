<?php

namespace App\Http\Controllers;

use App\Enums\FlashTypeEnum;
use App\Models\Gift;
use App\Models\User;
use App\Services\Flashes;
use Illuminate\Http\Request;

class WalletsController extends Controller
{
    public function useCoin(Request $request)
    {
        if ($request->user()->wallet->coins <= 0) {
            Flashes::push("tu n'as pas assez de points !", FlashTypeEnum::danger);

            return back();
        }

        $partner = User::with('giftsBag', 'gifts')
            ->where('id', $request->user()->partner_id)
            ->first();

        if (! $partner) {
            Flashes::push('Impossible de trouver ton partenaire', FlashTypeEnum::danger);

            return back();
        }

        $request->user()->wallet->coins -= 1;
        $request->user()->wallet->update();

        if ($partner->giftsBag->success) {
            $gift = Gift::randomGift($partner->gifts);
            $request->user()->wallet->wonGifts()->attach($gift);
            Flashes::push("Bravo tu as gagner : $gift->name", FlashTypeEnum::success);
        } else {
            Flashes::push("Dommage tu n'as rien gagner... :/", FlashTypeEnum::warning);
        }

        return back();
    }

    public function useAllCoins(Request $request)
    {

        $partner = User::with('giftsBag', 'gifts')
            ->where('id', $request->user()->partner_id)
            ->firstOrFail();
        $gifts = [];

        while ($request->user()->wallet->coins > 0) {
            $request->user()->wallet->coins -= 1;
            if ($partner->giftsBag->success) {
                $gifts[] = Gift::randomGift($partner->gifts);
            }
        }

        // TODO: Optimiser pour avoir une seule requète
        $request->user()->wallet->wonGifts()->saveMany($gifts);
        $request->user()->wallet->save();
    }
}
