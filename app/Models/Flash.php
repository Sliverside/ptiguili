<?php

namespace App\Models;

use App\Enums\FlashTypeEnum;

class Flash {
  readonly String $class;
  public function __construct(
    readonly string $content,
    readonly FlashTypeEnum $type = FlashTypeEnum::info,
  ) {}

  public function class()
  {
    return 'class="flash flash--' . $this->type->value . '"';
  }
}
