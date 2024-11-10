<?php

namespace App\Http\Controllers;

use App\Enums\FlashTypeEnum;
use App\Enums\WonGiftStatusEnum;
use App\Models\Gift;
use App\Models\WonGift;
use App\Notifications\GiftRequest;
use App\Services\Flashes;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class GiftsController extends Controller
{
    public function index()
    {
        $gifts = Gift::wonBy(Auth::user())
            ->withCountWins(Auth::user())
            ->whereHasWins(Auth::user(), WonGiftStatusEnum::pending)
            ->take(3)
            ->get();

        $wonGiftsCount = Gift::wonBy(Auth::user())
            ->withCountWins(Auth::user())
            ->whereHasWins(Auth::user(), WonGiftStatusEnum::pending)
            ->count();

        return view('gifts/index', ['wonGifts' => $gifts, 'wonGiftsCount' => $wonGiftsCount]);
    }

    public function list()
    {
        $gifts = Gift::wonBy(Auth::user())
            ->withCountWins(Auth::user())
            ->withCountWins(Auth::user(), WonGiftStatusEnum::pending)
            ->whereHasWins(Auth::user(), WonGiftStatusEnum::pending)
            ->get();

        return view('gifts/list', ['wonGifts' => $gifts]);
    }

    public function show(int $id)
    {
        $gift = Gift::withCountWins(Auth::user())
            ->withCountWins(Auth::user(), WonGiftStatusEnum::pending)
            ->withCountWins(Auth::user(), WonGiftStatusEnum::used)
            ->findOrFail($id);

        return view('gifts/show', ['gift' => $gift]);
    }

    public function showOwnerLinkQrcode(int $id)
    {
        $wonGift = WonGift::query()
            ->where('gift_id', $id)
            ->where('status', WonGiftStatusEnum::pending)
            ->winedBy(Auth::user())
            ->orderBy('id', 'ASC')
            ->firstOrFail();

        $ownerLink = route('gifts.pendingDetail', $wonGift);

        $options = new QROptions();

        $options->outputBase64 = false;

        $qrcode = new QRCode($options);

        return new Response($qrcode->render($ownerLink), 200, [
            'Content-type' => 'image/svg+xml'
        ]);
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

        if (! $gift->isDirty()) {
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
        $request->user()->giftsBag->gifts()->create($request->validateWithBag('giftCreate', [
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

        if ($gift) {
            $oldestWonPending = $gift->oldestWon($request->user(), WonGiftStatusEnum::pending);
        }

        if (! $gift || ! $oldestWonPending) {
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

    public function sell(Request $request, int $id)
    {
        /** @var ?Gift */
        $gift = Gift::wonBy($request->user())->find($id);

        $oldestWonPending = null;

        if ($gift) {
            $oldestWonPending = $gift->oldestWon($request->user(), WonGiftStatusEnum::pending);
        }

        if (! $gift || ! $oldestWonPending) {
            Flashes::push("tu n'as pas de cadeau \"$gift->name\" en attente", FlashTypeEnum::danger);

            return back();
        }

        if (!$gift->sell_price || $gift->sell_price <= 0) {
            Flashes::push("Le cadeau \"$gift->name\" ne peut pas être vendu...", FlashTypeEnum::danger);

            return back();
        }

        $request->user()->wallet->coins += $gift->sell_price;
        $request->user()->wallet->update();

        $oldestWonPending->status = WonGiftStatusEnum::sold;
        $oldestWonPending->update();

        Flashes::push("félicitation ton cadeau \"$gift->name\" à bien été vendu pour " . $gift->sell_price . '<span class="coin"></span> !');

        return back();
    }

    public function confirm(Request $request, int $id)
    {
        $wonGift = WonGift::ownedBy($request->user())
            ->where('status', WonGiftStatusEnum::pending)
            ->with('winner')
            ->with('gift')
            ->find($id);

        if (! $wonGift) {
            Flashes::push('Cadeau introuvable', FlashTypeEnum::danger);

            return redirect()->route('giftsBag');
        }

        $winnerWallet = $wonGift->winner->wallet;
        $giftPrice = $wonGift->gift->price;

        $winnerWallet->coins -= $giftPrice;

        if ($winnerWallet->coins < 0) {
            Flashes::push("L'execution du cadeau \"{$wonGift->gift->name}\" a échoué. Le partenaire n'a pas assez de coins", FlashTypeEnum::danger);
            return redirect()->back();
        }

        $winnerWallet->save();

        $wonGift->status = WonGiftStatusEnum::used;
        $wonGift->update();

        Flashes::push("L'execution du cadeau \"{$wonGift->gift->name}\" est bien été pris en compte !");

        return redirect()->route('giftsBag');
    }

    public function pending()
    {
        $pendingGifts = Gift::ownedBy(Auth::user())
            ->with('wons', function ($builder) {
                $builder->where('status', WonGiftStatusEnum::pending);
            })
            ->whereHasWins(Auth::user(), WonGiftStatusEnum::pending)
            ->get();

        return view('gifts/pending', ['pendingGifts' => $pendingGifts]);
    }

    public function pendingDetail(int $id)
    {
        $win = WonGift::ownedBy(Auth::user())
            ->where('status', WonGiftStatusEnum::pending)
            ->find($id);

        if (! $win) {
            Flashes::push('Erreur requète de cadeau introuvable', FlashTypeEnum::danger);

            return redirect()->route('giftsBag');
        }

        return view('gifts/pendingDetail', [
            'win' => $win,
            'gift' => $win->gift,
        ]);
    }
}
