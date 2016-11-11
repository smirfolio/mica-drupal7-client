<?php
/**
 * Copyright (c) 2016 OBiBa. All rights reserved.
 *
 * This program and the accompanying materials
 * are made available under the terms of the GNU Public License v3.0.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @file
 * MicaClient class
 */

namespace Obiba\ObibaMicaClient\MicaClient\DrupalMicaClient;

use Obiba\ObibaMicaClient\MicaCache as MicaCache;
use Obiba\ObibaMicaClient\MicaConfigurations as MicaConfig;
use Obiba\ObibaMicaClient\MicaWatchDog as MicaWatchDog;
use Obiba\ObibaMicaClient\MicaHttpClient as MicaHttpClient;

/**
 * Class DrupalMicaClient
 */
class MicaClient {
  private $request;
  private static $responsePageSizeSmall = 10;
  private static $responsePageSize = 20;


  /**
   * Instance initialisation.
   *
   * Mica client from a given url or from the one retrieved from 'mica_url'
   * variable.
   *
   * @param string $micaUrl
   * @param MicaCache\MicaCacheInterface $micaCache
   * @param MicaConfig\MicaConfigInterface $micaConfig
   * @param MicaWatchDog\MicaWatchDogInterface $micaWatchDog
   *   IF given,  the Mica server url, the  default value is a a setting
   *   parameter
   *
   */
  public function __construct($micaUrl = NULL,
                              MicaCache\MicaCacheInterface $micaCache,
                              MicaConfig\MicaConfigInterface $micaConfig,
                              MicaWatchDog\MicaWatchDogInterface $micaWatchDog) {
    $this->drupalCache = $micaCache;
    $this->drupalConfig = $micaConfig;
    $this->drupalWatchDog = $micaWatchDog;
    $this->micaUrl = (isset($micaUrl) ? $micaUrl : $this->drupalConfig->MicaGetConfig('mica_url'));
    self::$responsePageSize = $this->drupalConfig->MicaGetConfig('mica_response_page_size');
    self::$responsePageSizeSmall = $this->drupalConfig->MicaGetConfig('mica_response_page_size_small');
    $this->request = new MicaHttpClient\DrupalMicaHttpClient($this->micaUrl);
  }
public function getResponseRequest(){
  return $this->request;
}
  public function DrupalhttpGetLastResponse(){
    return $this->request->drupalMicaClientGetLastResponse();
  }
  public function sendGet($resource, $acceptType, $ajax = NULL, $parameters = NULL){
    $this->request->httpGet($resource)
      ->httpSetAcceptHeaders(MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($acceptType))
      ->httpAuthorizationHeader();
    if(!empty($parameters['headers']['Content-Type'])){
      $this->request->httpSetContentTypeHeaders(
        MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($parameters['headers']['Content-Type']));
      unset($parameters['headers']);
    }
    return  $this->request->send($parameters, $ajax);
  }

  public function sendPost($resource, $acceptType, $ajax = NULL, $parameters = NULL){
      $this->request->httpPost($resource,$parameters)
      ->httpSetAcceptHeaders(MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($acceptType))
      ->httpAuthorizationHeader();
    if(!empty($parameters['headers']['Content-Type'])){
      $this->request->httpSetContentTypeHeaders(
        MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($parameters['headers']['Content-Type']));
      unset($parameters['headers']);
    }
    return  $this->request->send($parameters, $ajax);
  }

  public function sendPut($resource, $acceptType, $ajax = NULL, $parameters = NULL){
    $this->request->httpPut($resource)
      ->httpSetAcceptHeaders(MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($acceptType))
      ->httpAuthorizationHeader();
    if(!empty($parameters['headers']['Content-Type'])){
      $this->request->httpSetContentTypeHeaders(
        MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($parameters['headers']['Content-Type']));
      unset($parameters['headers']);
    }
    return  $this->request->send($parameters, $ajax);
  }


  public function sendDelete($resource, $acceptType){
    $this->request->httpDelete($resource)
      ->httpSetAcceptHeaders(MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($acceptType))
      ->httpAuthorizationHeader();
    return  $this->request->send();
  }

  public function downloadFileGet($resource, $acceptType){
    $this->request->httpGet($resource)
      ->httpSetAcceptHeaders(MicaHttpClient\AbstractMicaHttpClient::acceptHeaderArray($acceptType))
      ->httpAuthorizationHeader();
    $response = $this->request->download();
    if (!empty($response)) {
      return $response;
    }
    return FALSE;
  }

  /**
   * Make sure we are not using previous session state.
   */
  public static function clear() {
    unset($_SESSION[MicaHttpClient\DrupalMicaHttpClient::MICA_COOKIE]);
  }

  /**
   * Send a logout request to Mica and clean drupal client cookies.
   */
  public function logout() {
    $response = $this->sendGet('/auth/session/_current', 'HEADER_JSON');
    if (!empty($response)) {
      unset($_SESSION[MicaHttpClient\AbstractMicaHttpClient::getRequestConst('MICA_COOKIE')]);
    }
    else {
      unset($_SESSION[MicaHttpClient\AbstractMicaHttpClient::getRequestConst('MICA_COOKIE')]);
    }
  }

  /**
   * GEt the setting of the samll page size response (for pagination).
   *
   * @return int
   *   The page size.
   */
  public
  static function getResponsePageSizeSmall() {
    return self::$responsePageSizeSmall;
  }

  /**
   * GEt the setting of the page size response (for pagination).
   *
   * @return int
   *   The page size.
   */
  public
  static function getResponsePageSize() {
    $size = empty($_GET['size']) ? self::$responsePageSize : $_GET['size'];
    return $size;
  }

  /**
   * The search list pagination parameters.
   *
   * @param int $current_page
   *   The current page number
   * @param string $type
   *   The entity type.
   * @param int $size
   *   The size server response.
   *
   * @return float|int
   *   The next page offset.
   */
  public function paginationListSearchParameters($current_page = 0, $type = NULL, $size = NULL) {
    return self::nextPageOffset(
      empty($current_page) ? 0 : $current_page,
      empty($size) ? MicaClient::getResponsePageSize() : $size,
      @$_SESSION[strtolower($type)]['aggregations']['total_hits']
    );
  }

  /**
   * Calculate the next offset page.
   *
   * @param int $current_page
   *   The current page number.
   * @param int $size
   *   The size server response.
   * @param int $total
   *   The total in server.
   *
   * @return int
   *   The next page offset.
   */
  public static function nextPageOffset($current_page = 0, $size = NULL, $total = NULL) {
    $nb_pages = ceil($total / $size);
    $from = 0;
    if (!empty($current_page)) {
      $actual_page = intval($current_page);
      if ($actual_page > $nb_pages) {
        $actual_page = $nb_pages;
      }
      $from = ($actual_page) * $size;
    }
    return $from;
  }

  /**
   * Deal with downloadable resources from server (Images, attachments).
   *
   * @param string $entity_type
   *   Study, dce, network ...
   * @param string $entity_id
   *   The id of the entity.
   * @param string $file_id
   *   The id of the stored file on the server.
   *
   * @return array
   *   containing :
   *    data : The raw file to download
   *    filename : The real file name of the file
   *    raw_header_array : the raw of header response
   *    or in case of error
   *    code : the error code
   *    message : the error message
   */
  public function downloadFile($entity_type, $entity_id, $file_id) {
    $resource_query = "/" . $entity_type . "/" . $entity_id . "/file/" . $file_id . "/_download";
    $response = $this->downloadFileGet($resource_query, array('HEADER_BINARY'));
    if (!empty($response)) {
      return $response;
    }
  }

  public static function DrupalMicaErrorHandler($force = NULL,  $message_parameters = NULL){
    $current_path = current_path();
    if($force){
      drupal_set_message(t($message_parameters['message'],
        $message_parameters['placeholders']),
        $message_parameters['severity']);
      throw new MyException('Server Error');
      header('Location: ' . HIDE_PHP_FATAL_ERROR_URL . '?destination=' . $current_path, FALSE);
    }
    if((!is_null($error = error_get_last()) && $error['type'] === E_ERROR)) {
      header('Location: ' . HIDE_PHP_FATAL_ERROR_URL . '?destination=' . $current_path);

      // We need to reuse the code from _drupal_error_handler_real() to
      // force the maintenance page.
      require_once DRUPAL_ROOT . '/includes/errors.inc';

      $types = drupal_error_levels();
      list($severity_msg, $severity_level) = $types[$error['type']];

      if (!function_exists('filter_xss_admin')) {
        require_once DRUPAL_ROOT . '/includes/common.inc';
      }

      // We consider recoverable errors as fatal.
      $error = array(
        '%type' => isset($types[$error['type']]) ? $severity_msg : 'Unknown error',
        // The standard PHP error handler considers that the error messages
        // are HTML. We mimick this behavior here.
        '!message' => filter_xss_admin($error['message']),
        '%file' => $error['file'],
        '%line' => $error['line'],
        'severity_level' => $severity_level,
      );

      watchdog('php', '%type: !message (line %line of %file).', $error, $error['severity_level']);
      exit;
    }
  }

}
