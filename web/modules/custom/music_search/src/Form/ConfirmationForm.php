<?php

namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\spotify_lookup\SpotifyService;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ConfirmationForm extends FormBase {


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
    return 'search_confirmation';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $chosen_entity = \Drupal::state()->get('the_chosen');
    $the_search = \Drupal::state()->get('requested_search');
    $number = \Drupal::state()->get('types');



    if ($number == 0) {
      $the_desired_track = $this->injection->search_track($the_search);
      $the_track = $the_desired_track->tracks->items[$chosen_entity];
      $form['options'] = [
        '#type' => 'checkboxes',
        '#title' => t('What about your selected album would you like to save?'),
        '#options' => array(
          1 => 'Lagið: ' . $the_track->name,
          2 => 'Nafn listamanns: ' . $the_track->artists[0]->name,
          3 => 'Album sem lagið er á: ' . $the_track->album->name,
          4 => 'Útgáfudagur: ' . $the_track->album->release_date,
          5 => 'Spotify linkur á lagið: ' . $the_track->external_urls->spotify,
        ),
        '#default_value' => array(0, 1, 2, 3, 4),
      ];
      $form['photo'] = [
        '#theme' => 'image',
        '#attributes' => array(
          'width' => 250,
          'height' => 250,
          'src' => $the_track->album->images[0]->url,
        )
      ];
      $form['actions']['submit'] = array(
        '#type' => 'submit',
        '#value' => $this -> t('Submit'),
      );
    }

      elseif ($number == 1) {
        $the_desired_album = $this->injection->search_album($the_search);
        $the_album = $the_desired_album->albums->items[$chosen_entity];
        $form['options'] = [
          '#type' => 'checkboxes',
          '#title' => t('What about your selected album would you like to save?'),
          '#options' => array(
            1 => 'Nafn albums: ' . $the_album->name,
            2 => 'Nafn listamanns: ' . $the_album->artists[0]->name,
            3 => 'Útgáfudagur: ' . $the_album->release_date,
            4 => 'Spotify linkur á albumið: ' . $the_album->external_urls->spotify,
          ),
          '#default_value' => array(0, 1, 2, 3, 4),
        ];
        $form['photo'] = [
          '#theme' => 'image',
          '#attributes' => array(
            'width' => 250,
            'height' => 250,
            'src' => $the_album->images[0]->url,
          )
        ];
        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this -> t('Submit'),
        );
      }

      elseif ($number == 2) {
        $the_desired_artist = $this->injection->search_artist($the_search);
        $the_artist = $the_desired_artist->artists->items[$chosen_entity];
        $form['options'] = [
          '#type' => 'checkboxes',
          '#title' => t('What about your selected artist would you like to save?'),
          '#options' => array(
            1 => 'Nafn listamanns: ' . $the_artist->name,
            2 => 'Spotify linkur á profile listamanns: ' . $the_artist->external_urls->spotify,
          ),
          '#default_value' => array(0, 1,),
        ];
        $form['photo'] = [
          '#theme' => 'image',
          '#attributes' => array(
            'width' => 250,
            'height' => 250,
            'src' => $the_artist->images[0]->url,
          )
        ];

        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this -> t('Submit'),
        );

      }
    return $form;
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $the_search = \Drupal::state()->get('requested_search');
    $number = \Drupal::state()->get('types');
    $chosen_entity = \Drupal::state()->get('the_chosen');

    $the_desired_track = $this->injection->search_track($the_search);
    $the_desired_album = $this->injection->search_album($the_search);
    $the_desired_artist = $this->injection->search_artist($the_search);

    $the_track_pic = $the_desired_track->tracks->items[$chosen_entity]->album->images[0]->url;
    $track_pic_name = $the_desired_track->tracks->items[$chosen_entity]->name;

    $the_album_pic = $the_desired_album->albums->items[$chosen_entity]->images[0]->url;
    $album_pic_name = $the_desired_album->albums->items[$chosen_entity]->name;

    $the_artist_pic = $the_desired_artist->artists->items[$chosen_entity]->images[0]->url;
    $artist_pic_name = $the_desired_artist->artists->items[$chosen_entity]->name;

    $folder = 'Verkefni1AllarMyndir';

    $track_destination = \Drupal::config('system.file')->get('default_scheme') . '://' . $folder . '/' . basename($track_pic_name);
    $album_destination = \Drupal::config('system.file')->get('default_scheme') . '://' . $folder . '/' . basename($album_pic_name);
    $artist_destination = \Drupal::config('system.file')->get('default_scheme') . '://' . $folder . '/' . basename($artist_pic_name);

    if ($number == 0 && $the_track_pic !== null) {
      $myndir[] = $this->_save_file($the_track_pic, 'Verkefni1AllarMyndir', 'image', $track_pic_name, $track_pic_name . '.jpg' );
      \Drupal::state()->setMultiple(['the_chosen' => $form_state->getValue('Options')]);

    }
    elseif ($number == 1 && $the_album_pic !== null) {
      $myndir[] = $this->_save_file($the_album_pic, 'Verkefni1AllarMyndir', 'image', $album_pic_name, $album_pic_name . '.jpg' );
    }
    elseif ($number == 2 && $the_artist_pic !== null) {
      $myndir[] = $this->_save_file($the_artist_pic, 'Verkefni1AllarMyndir', 'image', $artist_pic_name, $artist_pic_name . '.jpg' );

      \Drupal::state()->setMultiple(['checked_boxes' => $form_state->getValue('Options')]);
      $checked_boxes = \Drupal::state()->get('checked_boxes');
      $temp_store_box = [];
      $temp_store_box[0] = $the_artist_pic;

      foreach ($checked_boxes as $item) {
        if ($item == 1) {
          $temp_store_box[1] = $artist_pic_name;
        }
        if ($item == 2) {
          $temp_store_box[2] = $the_desired_artist->artists->items[$chosen_entity]->external_urls->spotify;
        }
      }

    }
  }
  /**
   * Saves a file, based on it's type
   *
   * @param $url
   *   Full path to the image on the internet
   * @param $folder
   *   The folder where the image is stored on your hard drive
   * @param $type
   *   Type should be 'image' at all time for images.
   * @param $title
   *   The title of the image (like ALBUM_NAME - Cover), as it will appear in the Media management system
   * @param $basename
   *   The name of the file, as it will be saved on your hard drive
   *
   * @return int|null|string
   * @throws EntityStorageException
   */
  function _save_file($url, $folder, $type, $title, $basename, $uid = 1)
  {
    if (!is_dir(\Drupal::config('system.file')->get('default_scheme') . '://' . $folder)) {
      return null;
    }
    $destination = \Drupal::config('system.file')->get('default_scheme') . '://' . $folder . '/' . basename($basename);
    if (!file_exists($destination)) {
      $file = file_get_contents($url);
      $file = file_save_data($file, $destination);
    } else {
      $file = \Drupal\file\Entity\File::create([
        'uri' => $destination,
        'uid' => $uid,
        'status' => FILE_STATUS_PERMANENT
      ]);

      $file->save();
    }

    $file->status = 1;

    $media_type_field_name = 'field_media_image';

    $media_array = [
      $media_type_field_name => $file->id(),
      'name' => $title,
      'bundle' => $type,
    ];
    if ($type == 'image') {
      $media_array['alt'] = $title;
    }

    $media_object = \Drupal\media\Entity\Media::create($media_array);
    $media_object->save();

    return $media_object->id();
  }
  private function _artistNode(array &$form, FormStateInterface $form_state)
  {
    $factory = \Drupal::service('tempstore.private');
    $store = $factory->get('my_module.my_collection');
    $results = $store->get('my_key');

    $object_to_save = [];

    foreach ($results as $item) {

      $object_to_save['type'] = $item['type'];

      $value = $form_state->getValue($item["id"]);
      if (!is_null($value) and $value['name'] !== 0) {
        // name selected from $item.
        $object_to_save['name'] = $item['name'];
      }

      if (!is_null($value) and $value['link'] !== 0) {
        // link selected from $item.
        $object_to_save['link'] = $item['url'];
      }
      if (!is_null($value) and $item['type'] == 'artist' and $value['popularity'] !== 0) {
        // popularity selected from $item.
        $object_to_save['popularity'] = $item['popularity'];
      } elseif (!is_null($value) and $item['type'] !== 'artist' and $value['artist'] !== 0) {
        // artist selected from $item.
        $object_to_save['artist'] = $item['artist'];
      }
    }
    $content_type = "";
    if ($object_to_save["type"] == "artist") {
      $content_type = "listamadur";
    } elseif ($object_to_save["type"] == "track") {
      $content_type = "lag";
    } elseif ($object_to_save["type"] == "album") {
      $content_type = "plata";
    }
    $values = [
      'type' => $content_type,
      'title' => $object_to_save["name"]

    ];
    /** @var \Drupal\node\NodeInterface $node */
    $node = \Drupal::entityTypeManager()->getStorage('node')->create($values);
    $node->set('field_hlekkur', $object_to_save["link"]);
    if ($object_to_save['type'] == "artist") {
      $node->set('field_popularity', $object_to_save["popularity"]);
    } else {
      $node->set('field_artist', $object_to_save["artist"]);
    }

    $node->save();
    $nid = $node->id();
    $form_state->setRedirect('entity.node.canonical', ['node' => $nid]);


  }
}
