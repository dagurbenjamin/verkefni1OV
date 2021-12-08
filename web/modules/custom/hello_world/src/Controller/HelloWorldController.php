<?php
namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hello_world\HelloWorldSalutation;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HelloWorldController extends ControllerBase {

  /**
   * @var \Drupal\hello_world\HelloWorldSalutation
   */

  public function __construct(HelloWorldSalutation $salutation) {
    $this->salutation = $salutation;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('hello_world.salutation')
    );
  }

  public function helloWorld() {
    return [
      '#markup' => $this->salutation->getSalutation()
    ];
  }
}

