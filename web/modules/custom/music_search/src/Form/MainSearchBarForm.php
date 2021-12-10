<?php

namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;

class MainSearchBarForm extends FormBase {

  public function getFormId()
  {
    return "music_search";
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   *
   *
   */

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['SearchBar'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Please put in a song, album or artist of your choice'),
      '#description' => $this->t('Can´t think of one? Maybe Zara Larsson'),

    ];
    $form['Radio'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(
        0 => $this
          ->t('Song'),
        1 => $this
          ->t('Album'),
        2 => $this
          ->t('Artist')
      ),
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this -> t('Search'),
    );
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
  \Drupal::state()->setMultiple(['requested_search' => $form_state->getValue('SearchBar'), 'types' => $form_state->getValue('Radio')]);
  $form_state->setRedirect('music_search.limiter');
  }
}
