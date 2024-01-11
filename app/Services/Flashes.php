<?php

namespace App\Services;

use App\Enums\FlashTypeEnum;
use App\Models\Flash;
use Illuminate\Support\Facades\Session;

class Flashes {
  protected const KEY = '___flashes';
  protected const NOW_KEY = '___now_flashes';

  /** Permet de récuperer les messages "flash" en session */
  public static function all(): array
  {
    $flashes = Session::get(self::KEY);
    $nowFlashes = Session::get(self::NOW_KEY);
    if(!is_array($flashes)) $flashes = [];
    if(!is_array($nowFlashes)) $nowFlashes = [];
    return array_merge(
      array_values($nowFlashes),
      array_values($flashes),
    );
  }

  /**
   * Permet d'ajouter un message "flash" à la requète en cours et la suivante.
   * Cette méthode est surtout utile juste avant une redirection, sinon,
   * la méthode Flashes::pushNow est plus adapté.
   * */
  public static function push(string $content, FlashTypeEnum $type = FlashTypeEnum::info)
  {
    if(!Session::has(self::KEY)) Session::flash(self::KEY, []);
    Session::push(self::KEY, new Flash($content, $type));
  }

  /**
   * Permet d'ajouter un message "flash" à la requète en cours. Si le but est
   * d'envoyer un message à une redirection il faut utiliser à la place la
   * méthode Flashes::push avant la dite redirection.
   * */
  public static function pushNow(string $content, FlashTypeEnum $type = FlashTypeEnum::info)
  {
    if(!Session::has(self::NOW_KEY)) Session::now(self::NOW_KEY, []);
    Session::push(self::NOW_KEY, new Flash($content, $type));
  }
}
