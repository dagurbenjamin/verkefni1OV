<?php

namespace Drupal\music_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\spotify_lookup\SpotifyService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MusicSearchController extends ControllerBase {

  /**
   * @var \Drupal\spotify_lookup\SpotifyService
   */

  protected $injection;

  public function __construct(SpotifyService $injection) {
    $this->injection = $injection;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_lookup.spotify_service')
    );
  }

  public function getConnection(string $albumName) {
    return [
      '#markup' => $this->injection->search_album($albumName)
    ];
  }
}

