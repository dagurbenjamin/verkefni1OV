<?php
namespace Drupal\hello_world\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\hello_world\HelloWorldSalutation;

/**
 * Hello World Salutation block.
 *
 * @Block(
 *  id = "hello_world_salutation_block",
 *  admin_label = @Translation("Hello world salutation"),
 * )
 */

class HelloWorldSalutationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\hello_world\HelloWorldSalutation
   */

  protected $salutation;

  /**
   * Constructs a HelloWorldSalutationBlock.
   *
   * @param array $configuration
   *  A config array containing info about the plugin instance.
   * @param string $plugin_id
   *  The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *  The plugin implementation definition.
   * @param \Drupal\hello_world\HelloWorldSalutation $salutation
   *  The salutation service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, HelloWorldSalutation $salutation) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->salutation = $salutation;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('hello_world.salutation')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->salutation->getSalutation(),
    ];
  }
}
