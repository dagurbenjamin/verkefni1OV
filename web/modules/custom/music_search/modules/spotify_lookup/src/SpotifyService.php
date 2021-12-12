<?php
namespace Drupal\spotify_lookup;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Controller\ControllerBase;

class SpotifyService
{
  use StringTranslationTrait;

  /**
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */

  protected $configFactory;
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * @param string $album_name
   * @return array
   */

  public function search_album(string $album_name) : \stdClass {
    $uri = 'https://api.spotify.com/v1/search?type=album&include_external=audio&limit=5&q=' . $album_name;
    return $this -> _spotify_api_get_query($uri);
  }

  /**
   * @param string $track_name
   * @return array
   */

  public function search_track(string $track_name) : \stdClass {
    $uri = 'https://api.spotify.com/v1/search?type=track&include_external=audio&limit=5&q=' . $track_name;
    return $this -> _spotify_api_get_query($uri);
  }

  /**
   * @param string $artist_name
   * @return array
   */

  public function search_artist(string $artist_name) : \stdClass {
    $uri = 'https://api.spotify.com/v1/search?type=artist&include_external=audio&limit=5&q=' . $artist_name;
    return $this -> _spotify_api_get_query($uri);
  }

  /**
   * Sends a GET query to Spotify for specific URL
   *
   * @param $uri string
   *   The fully generated search string
   * @return object
   *   Returns a stdClass with the search results or an error message
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  private function _spotify_api_get_query($uri) {
    $token = $this->_spotify_api_get_auth_token();
    $token = json_decode($token);
    $options = array(
      'method' => 'GET',
      'timeout' => 3,
      'headers' => array(
        'Accept' => 'application/json',
        'Authorization' => "Bearer " . $token->access_token,
      ),
    );

    $search_results = \Drupal::httpClient()->request('GET', $uri, $options);

    if (empty($search_results->error)) {
      $search_results = json_decode($search_results->getBody());

    } else {
      $this->t('The search request resulted in the following error: @error.', array(
        '@error' => $search_results->error,
      ));

      return $search_results->error;
    }
    return $search_results;
  }

  /**
   * Gets Auth token from the Spotify API
   */
  private function _spotify_api_get_auth_token(): bool|string {
    $connection_string = "https://accounts.spotify.com/api/token";
    $config = $this->configFactory->get('spotify_lookup.api_keys');
    $api_auth = $config->get('api_auth');
    $api_secret = $config->get('api_secret');
    $key = base64_encode($api_auth . ':' . $api_secret);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $connection_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = "Authorization: Basic " . $key;
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
  }


}
