<?php
namespace Drupal\hello_world;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class HelloWorldSalutation {
  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */

  protected $configFactory;

  public function __construct(ConfigFactoryInterface $config_factory){
    $this->configFactory = $config_factory;
  }

  public function getSalutation() {
    $config = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');
    if ($salutation !== "" && $salutation) {
      return $salutation;
    }

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
