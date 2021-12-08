<?php
namespace Drupal\hello_world;

use Drupal\Core\StringTranslation\StringTranslationTrait;

class HelloWorldSalutation {
  use StringTranslationTrait;

  public function getSalutation() {
    $time = new \DateTime();
    if ((int) $time->format('G') >= 0 && (int)$time->format('G') < 12) {
      return $this->t('Good morning everyone');
    }
    if ((int) $time->format('G') >= 12 && (int)$time->format('G') < 18) {
      return $this->t('Good afternoon everyone');
    }
    if ((int) $time->format('G') >= 18) {
      return $this->t('Good evening everyone');
    }
  }
}
