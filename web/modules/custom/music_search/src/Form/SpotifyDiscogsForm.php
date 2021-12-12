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
    $the_search = \Drupal::state()->get('requested_search');
    $number = \Drupal::state()->get('types');
    $fromArray = array();

    if ($number == 0) {
      $the_desired_track = $this->injection->search_track($the_search);
      foreach ($the_desired_track->tracks->items as $item) {
        $fromArray[] = $item->name . ' - ' . $item->artists[0]->name;
        $form['Display'] = [
          '#type' => 'radios',
          '#title' => 'Choose the desired track',
          '#options' => $fromArray
        ];
        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this -> t('Submit'),
        );
      }
    }
      elseif ($number == 1) {
        $the_desired_album = $this->injection->search_album($the_search);
        foreach ($the_desired_album->albums->items as $item) {
          $album_name = $item->name;
          $fromArray[] = $album_name . ' - ' . $item->artists[0]->name;
          $form['Display'] = [
            '#type' => 'radios',
            '#title' => 'Choose the desired album',
            '#options' => $fromArray
          ];
          $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this -> t('Submit'),
          );
        }
      }
      elseif ($number == 2) {
        $the_desired_artist = $this->injection->search_artist($the_search);
        foreach ($the_desired_artist->artists->items as $item) {
          $fromArray[] = $item->name;
          $form['Display'] = [
            '#type' => 'radios',
            '#title' => 'Choose the desired artist',
            '#options' => $fromArray
          ];
          $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this -> t('Submit'),
          );
        }
      }
    return $form;

  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    \Drupal::state()->setMultiple(['the_chosen' => $form_state->getValue('Display')]);
    \Drupal::state()->setMultiple(['the_ting' => $form_state->getValue('the_desired_artist')]);
    $form_state->setRedirect('music_search.confirmation');
  }
}
