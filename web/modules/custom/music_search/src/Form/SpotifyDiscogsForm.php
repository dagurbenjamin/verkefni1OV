<?php

namespace Drupal\music_search\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\spotify_lookup\SpotifyService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SpotifyDiscogsForm extends FormBase {

  protected $injection;

  public function __construct(SpotifyService $injection) {
    $this->injection = $injection;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_lookup.spotify_service')
    );
  }


  public function getFormId()
  {
    return 'spotify_discogs_search';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $album = \Drupal::state()->get('requested_search');
    $number = \Drupal::state()->get('types');

    $the_desired_albums = $this->injection = search_album($album);

    $form['SearchBar'] = [
      '#type' => 'textfield',
      '#title' => $this->t($the_desired_albums),
      '#description' => $this->t($the_desired_albums),
      ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
  }
}
