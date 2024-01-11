<?php

namespace App\Http\Controllers;

use App\Enums\FlashTypeEnum;
use App\Enums\WonGiftStatusEnum;
use App\Models\Gift;
use App\Models\WonGift;
use App\Notifications\GiftRequest;
use App\Services\Flashes;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class GiftsController extends Controller
{
    public function index()
    {
        $gifts = Gift::wonBy(Auth::user())
            ->withCountWins(Auth::user())
            ->withCountWins(Auth::user(), WonGiftStatusEnum::pending)
            ->get();
        return view('gifts/index', ['wonGifts' => $gifts]);
    }

    public function show(int $id)
    {
        $gift = Gift::withCountWins(Auth::user())
        ->withCountWins(Auth::user(), WonGiftStatusEnum::pending)
        ->withCountWins(Auth::user(), WonGiftStatusEnum::used)
            ->findOrFail($id);

        $wonGift = $gift->oldestWon(Auth::user(), WonGiftStatusEnum::pending);

        if($wonGift) {
            $ownerLink = route('gifts.pendingDetail', $wonGift);

            $qrcode = (new QRCode(new QROptions([
                'outputType' => QROutputInterface::GDIMAGE_WEBP,
                'scale' => 8
                ])))->render($ownerLink);
        }

        return view('gifts/show', ['gift' => $gift, 'qrcode' => $qrcode ?? null, 'ownerLink' => $ownerLink ?? null]);
    }

    public function update(Request $request, Gift $gift)
    {
        $errorsBag = 'gift' . $gift->id;

        try {
            $gift->fill($request->validate([
                'name' => 'required',
                'description' => 'required',
                'relative_probability' => 'required|numeric|between:0,100',
            ]));
        } catch (ValidationException $e) {
            Flashes::push("Les donnés que vous avez envoyé ne sont pas correctes. Le cadeau \"$gift->name\" n'a pas été mis à jour");
            $e->errorBag = $errorsBag;
            throw $e;
        }

        if(!$gift->isDirty()) {
            Flashes::push("Le cadeau \"$gift->name\" n'a pas été mis à jour car aucun changement n'a été detecté");
            return Redirect::route('giftsBag')->with('giftNoUpdate' . $gift->id, true);
        }

        $gift->save();

        Flashes::push("le cadeau \"$gift->name\" à bien été mis à jour");
        return Redirect::route('giftsBag')->with('giftUpdate' . $gift->id, true);
    }

    public function delete(Gift $gift)
    {
        $gift->delete();
        return Redirect::route('giftsBag');
    }

    public function store(Request $request)
    {
        $request->user()->giftsBag->gifts()->create($request->validateWithBag('giftCreate',[
            'name' => 'required',
            'description' => 'required',
            'relative_probability' => 'required|numeric|between:0,100',
        ]));

        return Redirect::route('giftsBag')
            ->with('giftCreate', true);
    }

    public function request(Request $request, int $id)
    {
        /** @var ?Gift */
        $gift = Gift::wonBy($request->user())->find($id);

        $oldestWonPending = null;

        if($gift) $oldestWonPending = $gift->oldestWon($request->user(), WonGiftStatusEnum::pending);

        if(!$gift || !$oldestWonPending) {
            Flashes::push("tu n'as pas de cadeau \"$gift->name\" en attente", FlashTypeEnum::danger);
            return back();
        }

        $giftOwner = $gift->giftsBag->user;
        $giftOwner->notify(new GiftRequest($gift, $oldestWonPending, $request->user()));

        $oldestWonPending->status = WonGiftStatusEnum::pending;
        $oldestWonPending->update();

        Flashes::push("félicitation ta requète pour le cadeau \"$gift->name\" est bien été pris en compte !");
        return back();
    }

    public function confirm(Request $request, int $id)
    {
        $wonGift = WonGift::ownedBy($request->user())
            ->where("status", WonGiftStatusEnum::pending)
            ->find($id);

        if(!$wonGift) {
            Flashes::push("Cadeau introuvable", FlashTypeEnum::danger);
            return redirect()->route('giftsBag');
        }

        $wonGift->status = WonGiftStatusEnum::used;
        $wonGift->update();

        Flashes::push("L'execution du cadeau \"{$wonGift->gift->name}\" est bien été pris en compte !");
        return redirect()->route('giftsBag');
    }

    public function pending()
    {
        $pendingGifts = Gift::ownedBy(Auth::user())
            ->with('wons', function($builder) {
                $builder->where('status', WonGiftStatusEnum::pending);
            })
            ->whereHasWins(WonGiftStatusEnum::pending)
            ->get();

        // $pendingGifts->each(fn($gift) => dump($gift->wons));

        // die();

        return view('gifts/pending', ['pendingGifts' => $pendingGifts]);
    }

    public function pendingDetail(int $id)
    {
        $win = WonGift::ownedBy(Auth::user())
            ->where('status', WonGiftStatusEnum::pending)
            ->find($id);

        if(!$win) {
            Flashes::push("Erreur requète de cadeau introuvable", FlashTypeEnum::danger);
            return redirect()->route('giftsBag');
        }
        return view('gifts/pendingDetail', [
            'win' => $win,
            'gift' => $win->gift,
        ]);
    }
}
