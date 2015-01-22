<?php

/**
 * @file
 * MicaStudyResource class
 */

$path_module_protobuf = drupal_get_path('module', 'obiba_protobuf');
$path_module_protos = drupal_get_path('module', 'obiba_mica_model');

include_once($path_module_protobuf . '/protobuf/Protobuf.php');
use \DrSlump\Protobuf;

Protobuf::autoload();

include_once($path_module_protos . '/protos/Mica.php');
include_once($path_module_protos . '/protos/MicaSearch.php');

/**
 * Class MicaSearchResource
 */
class MicaSearchResource extends MicaClient {

  public function __construct($mica_url = NULL) {
    parent::__construct($mica_url);
    Protobuf::setDefaultCodec(new Protobuf\Codec\Json());
  }

  public function taxonomies_coverage($parameters = NULL, $default_dto_search = NULL) {
    return $this->_aggregation_resource('coverage', $parameters, $default_dto_search);
  }

  private function _search_resource($parameters = NULL) {
    return $this->_aggregation_resource('search', $parameters);
  }

  private function _get_dto_query_param_from_url($parts = NULL, $type, $parameters = NULL) {
    $from = empty($parts['page']) ? 0 : $this->pagination_list_search_parameters($_GET['page'], $type, $this::SIZE_RESPONSE);
    $q = empty($parts['query']) ? (empty($parameters) ? NULL : $parameters) : $parts['query'];
    $group_by = empty($parts['group-by']) ? NULL : $parts['group-by'];
    return $this->dto_filter_construct($type, $q, $from, $group_by);
  }

  /*
   * $parameters : json query hard coded
   * */
  private function  _aggregation_resource($aggregation_type = 'search', $parameters = NULL, $default_dto_search = NULL) {
    if (!empty($_SESSION['errorsQuery'])) {
      unset($_SESSION['errorsQuery']);
    }
    $parts = NULL;
    $url_parm = explode('?', request_uri());
    $type = 'variables';
    if (!empty($url_parm[1])) {
      parse_str($url_parm[1], $parts);
      if (!empty($parts['type'])) {
        $type = $parts['type'];
      }
    }
    if (!empty($default_dto_search)) {
      if (empty($parts)) {
        $parts = $default_dto_search;
      }
      else {
        $parts = array_merge($default_dto_search, $parts);
      }
    }
    if (empty($parameters) || $parameters != 'empty_request') {
      $dto_param = $this->_get_dto_query_param_from_url($parts, $type, $parameters);
    }
    //dpm($dto_param);
    $url = $this->micaUrl . '/' . $type . '/_' . $aggregation_type;

    $request = new HttpClientRequest($url, array(
      'method' => HttpClientRequest::METHOD_POST,
      'headers' => $this->authorizationHeader(array(
            'Accept' => array(parent::HEADER_JSON),
            'Content-Type' => array(parent::HEADER_JSON)
          )
        ),
      'data' => !empty($dto_param) ? $dto_param : $this->dto_filter_construct($type, NULL, 0, NULL),
    ));

    $client = $this->client();
    try {
      $data = $client->execute($request);
      $this->setLastResponse($client->lastResponse);
      return json_decode($data);
    }
    catch (HttpClientException $e) {
      watchdog('Mica Client', 'Connection to server fail,  Error serve code : @code, message: @message',
        array('@code' => $e->getCode(), '@message' => $e->getMessage()), WATCHDOG_WARNING);
      return array();
    }

  }

  public function get_stats_entities_count($dto, $dto_data_response) { //dpm($dto_data_response);
    return array(
      'data' => $dto['data'],
      'study_totalHits' => empty($dto_data_response->studyResultDto) ? 0 : $dto_data_response->studyResultDto->totalHits,
      'study_totalCount' => empty($dto_data_response->studyResultDto) ? 0 : $dto_data_response->studyResultDto->totalCount,
      'variable_totalHits' => empty($dto_data_response->variableResultDto) ? 0 : $dto_data_response->variableResultDto->totalHits,
      'variable_totalCount' => empty($dto_data_response->variableResultDto) ? 0 : $dto_data_response->variableResultDto->totalCount,
      'network_totalHits' => !empty($dto_data_response->networkResultDto->totalHits) ?
          $dto_data_response->networkResultDto->totalHits : 0,
      'network_totalCount' => !empty($dto_data_response->networkResultDto->totalCount) ?
          $dto_data_response->networkResultDto->totalCount : 0,
      'dataset_totalHits' => !empty($dto_data_response->datasetResultDto->totalHits) ?
          $dto_data_response->datasetResultDto->totalHits : 0,
      'dataset_totalCount' => !empty($dto_data_response->datasetResultDto->totalCount) ?
          $dto_data_response->datasetResultDto->totalCount : 0
    );
  }

  public function search_variables() {
    return $this->search_documents(JoinQueryResponseWrapper::VARIABLES);
  }

  public function search_studies() {
    return $this->search_documents(JoinQueryResponseWrapper::STUDIES);
  }

  public function search_networks() {
    return $this->search_documents(JoinQueryResponseWrapper::NETWORKS);
  }

  public function search_datasets() {
    return $this->search_documents(JoinQueryResponseWrapper::DATASETS);
  }

  private function search_documents($type) {
    $join_query_response = new JoinQueryResponseWrapper($this->_search_resource());
    $response = $join_query_response->getResponseWrapper($type);

    if (!$response->isEmpty()) {
      $_SESSION['request-search-response'] = 'no-empty';
      $this->session_facets_builder($join_query_response->getResponseDto());
    }
    else {
      $_SESSION['request-search-response'] = 'empty';
    }

    return $join_query_response;
  }

}