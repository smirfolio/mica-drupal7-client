<?php
include_once('includes/mica_client_dataset_resource.inc');

function mica_client_variable_page_detail($variable_id) {
  drupal_add_js(drupal_get_path('module', 'mica_client_dataset') . '/js/mica_client_dataset_get_ajax_variables.js', 'file');
  $variable_resource = new MicaDatasetResource();
  $variable = $variable_resource->get_variable($variable_id);
  if (empty($variable)){
    drupal_set_title(t('Variable not found'));
    return '';
  }
  $variable->label = _mica_client_variable_attributes_detail($variable, 'label');
  $variable->description = _mica_client_variable_attributes_detail($variable, 'description');
  $variable->comment = _mica_client_variable_attributes_detail($variable, 'comment');

  $title = $variable->name;
  if ($variable->variableType == 'Harmonized') {
    $title .= ' -- ' . (mica_client_commons_get_localized_field($variable->studySummaries[0], 'acronym')) . '';
  }

  drupal_set_title($title);

  $dataset_type = $variable->variableType == 'Study' ? 'study-dataset' : 'harmonization-dataset';

  drupal_set_breadcrumb(array(
    l('Home', '<front>'),
    l(t('Datasets'), 'mica/datasets'),
    l(($dataset_type == 'study-dataset' ? t('Study Datasets') : t('Harmonization Datasets')), 'mica/datasets/' . $dataset_type . 's'),
    l(mica_client_commons_get_localized_field($variable, 'datasetName'), 'mica/' . mica_client_variable_dataset_type($variable)
      . '/' . $variable->datasetId)
  ));

  return theme('mica_client_variable-detail', array(
    'variable_dto' => $variable,
    'variable_harmonization' => mica_client_variable_get_harmonization($variable),
  ));
}

function mica_client_variable_prepare_prettify() {
  static $paths = array();
  if (mica_client_commons_is_bootstrap_based_theme()) {
    $dir = drupal_get_path('theme', 'micado_bootstrap');
    $paths['css'] = $dir . '/css/prettify.css';
    $paths['js'] = $dir . '/js/prettify.js';
  }
  if (isset($paths['css'])) {
    drupal_add_css($paths['css']);
  }
  else {
    throw new Exception('prettify.css library not found!');
  }

  if (isset($paths['js'])) {
    drupal_add_js($paths['js']);
  }
  else {
    throw new Exception('prettify.js library not found!');
  }
  drupal_add_js(drupal_get_path('module', 'mica_client_dataset') . '/js/mica_client_prettify.js', 'file');
}

function mica_client_variable_get_harmonizations($variable) {
  mica_client_variable_prepare_prettify();
  $rows = array();
  $variable_resource = new MicaDatasetResource();

  $variable_harmonizations = $variable_resource->get_variable_harmonizations($variable->id);

  if (!empty($variable_harmonizations->datasetVariableSummaries)) {
    foreach ($variable_harmonizations->datasetVariableSummaries as $variable_harmonization) {
      $variable_resolver = $variable_harmonization->resolver;
      $study_id = $variable_resolver->studyId;
      $harmonized_variable_id = $variable->datasetId . ':' . $variable->name . ':Harmonized:' . $study_id;

      $rows[] = array(
        l($variable->name, 'mica/variable/' . $harmonized_variable_id . '/' . mica_client_commons_to_slug($variable->name)),
        l(_mica_client_variable_acronym_study($variable, $study_id), 'mica/study/' . $study_id),
        _mica_client_variable_attributes_detail($variable_harmonization, 'status'),
        _mica_client_variable_html_truncated(
          _mica_client_variable_attributes_detail($variable_harmonization, 'comment'), t('comment'), 200, FALSE),
        _mica_client_variable_html_truncated(
          _mica_client_variable_attributes_detail($variable_harmonization, 'script'), t('script'), 100, TRUE)
      );
    }
  }

  if (!empty($rows)) {
    $headers = array(t('Harmonized Variable'), t('Study'), t('Status'), t('Comment'), t('Script'));
    return theme('table', array(
      'header' => $headers,
      'rows' => $rows,
      'empty' => t('No variable found'),
      'sticky' => FALSE,
      'attributes' => array('id' => 'variables_overview')
    ));
  }

}

function mica_client_variable_get_harmonization($harmonized_variable) {
  $rows = array();

  if (_mica_client_variable_attributes_detail($harmonized_variable, 'status') ||
    _mica_client_variable_attributes_detail($harmonized_variable, 'comment') ||
    _mica_client_variable_attributes_detail($harmonized_variable, 'script')
  ) {
    $rows = array(
      'status' => _mica_client_variable_attributes_detail($harmonized_variable, 'status'),
      'comment' => _mica_client_variable_attributes_detail($harmonized_variable, 'comment'),
      'script' => '<pre class="prettyprint lang-js">' . _mica_client_variable_attributes_detail($harmonized_variable, 'script') . '</pre>',
    );
  }

  return $rows;
}

function _mica_client_variable_html_truncated($text, $label, $size = 200, $is_code = FALSE, $pre_class = '') {
  $htmlTruncate = '';
  if (!empty($text)) {

    // split to respect line size
    if ($is_code) {
      $htmlTruncate = "<pre class='prettyprint $pre_class'>";
      // split at ).
      $lines = explode(").", $text);
      $text = implode(")\n.", $lines);
    }
    else {
      $lines = str_split($text, 60);
      $text = implode("\n", $lines);
    }

    if (strlen($text) > $size) {
      $htmlTruncate .= check_markup(truncate_utf8($text, $size, FALSE));

      if ($is_code) {
        $htmlTruncate .= "</pre>";
      }

      $htmlTruncate .= "[" . $label . " " . t("truncated") . "]";
    }
    else {
      $htmlTruncate .= check_markup($text);

      if ($is_code) {
        $htmlTruncate .= "</pre>";
      }
    }
  }
  else {
    $htmlTruncate = '-';
  }
  return $htmlTruncate;
}

function mica_client_variable_get_categories($categories = NULL) {
  $rows = array();
  if (!empty($categories)) {
    foreach ($categories as $category) {
      $rows[] = array(
        $category->name,
        _mica_client_variable_attributes_detail($category, 'label'),
        $category->missing ? '<span class="glyphicon glyphicon-ok"></span>' : NULL
      );
    }
    return theme(
      'table',
      array(
        'header' => array(t('Name'), t('Label'), t('Missing')),
        'rows' => $rows,
        'empty' => t('No category found'),
        'sticky' => FALSE,
        'attributes' => array('id' => 'variable_categories')
      )
    );
  }

  return FALSE;
}



function mica_client_dataset_variable_get_ajax_statistics($variable_id = NULL) {
  $variable_resource = new MicaDatasetResource();
  $variable = $variable_resource->get_variable($variable_id);
  $variable_stat = $variable_resource->get_variable_facet($variable_id);
  if ($variable->variableType != 'Dataschema') {
    $variable_stat = array($variable_stat);
  }
  if (!empty($variable_stat)) {
    $stat_tab = _mica_client_variable_table_stat($variable, $variable_stat);
  }
  print $stat_tab;
}

function _mica_client_variable_table_stat($variable, $variable_stat) {
  switch ($variable->nature) {
    case 'CONTINUOUS':
      return _mica_client_variable_table_stat_continuous($variable, $variable_stat);
    case 'CATEGORICAL':
      return _mica_client_variable_table_stat_categorical($variable, $variable_stat);
  }
  return t('No statistics available');
}

function _mica_client_variable_table_stat_continuous($variable, $variable_stat) {
  $rows = array();
  if ($variable->variableType == 'Dataschema') {

  }
  foreach ($variable->studyIds as $key => $study_id) {
    $row = array();
    if ($variable->variableType == 'Dataschema') {
      $row = array(l(_mica_client_variable_acronym_study($variable, $study_id), 'mica/study/' . $study_id));
    }
    if (!empty($variable_stat[$key]->facets)) {
      $statistics = $variable_stat[$key]->facets[0]->statistics;
      array_push(
        $row,
        !empty($statistics->min) ? round($statistics->min, 3) : 'NaN',
        !empty($statistics->max) ? round($statistics->max, 3) : 'NaN',
        !empty($statistics->mean) ? round($statistics->mean, 3) : 'NaN',
        !empty($statistics->stdDeviation) ? round($statistics->stdDeviation, 3) : 'NaN',
        $statistics->count
      );
    } else {
      array_push(
        $row,
        'NaN',
        'NaN',
        'NaN',
        'NaN',
        $variable_stat[$key]->totalHits
      );
    }
    $rows[] = $row;
  }
  $headers = array();
  if ($variable->variableType == 'Dataschema') {
    $headers = array(t('Study'));
  }
  array_push($headers, t('Min'), t('Max'), t('Mean'), t('Std. Dev'), t('Count'));
  return theme('table', array('header' => $headers, 'rows' => $rows, 'empty' => t('No statistics available')));
}

function _mica_client_variable_table_stat_categorical($variable, $variable_stat) {
  $stats = _mica_client_variable_rebuild_stat($variable_stat, $variable);
  $headers = array('Value');
  if ($variable->variableType == 'Dataschema') {
    foreach ($stats as $study_id => $stat) {
      $headers[] = l(_mica_client_variable_acronym_study($variable, $study_id), 'mica/study/' . $study_id);
    }
  }
  else {
    array_push($headers, t('Frequency'));
  }
  $rows = array();
  // Get total of non-missing categories
  $totals = array();
  foreach ($variable->categories as $category) {
    if (!$category->missing) {
      $category_name = $category->name;
      foreach ($stats as $study_id => $stat) {
        $s = isset($stat[$category_name]) ? $stat[$category_name] : 0;
        $totals[$study_id] = isset($totals[$study_id]) ? $totals[$study_id] + $s : $s;
      }
    }
  }

  foreach ($variable->categories as $category) {
    $category_name = $category->name;
    $category_label = _mica_client_variable_attributes_detail($category, 'label');;
    $missing = $category->missing;
    $row = array($category_label);
    foreach ($stats as $study_id => $stat) {
      if (!$missing && isset($stat[$category_name]) && $totals[$study_id] > 0) {
        $percent = round(($stat[$category_name] / $totals[$study_id]) * 100, 1);
        $row[] = isset($stat[$category_name]) ? $stat[$category_name] . '<br>(' . $percent . '%)' : 0;
      }
      else {
        $row[] = isset($stat[$category_name]) ? $stat[$category_name] : 0;
      }
    }
    $rows[] = $row;
  }
  $row = array(
    array(
      'data' => t('All'),
      'class' => array('active')
    )
  );
  foreach ($stats as $study_id => $stat) {
    $row[] = array(
      'data' => isset($stat['_all']) ? $stat['_all'] : 0,
      'class' => array('active')
    );
  }
  $rows[] = $row;
  return theme('table', array('header' => $headers, 'rows' => $rows, 'empty' => t('No statistics available')));

}

/**
 * Get the study acronym from the study summary found in the variable. If not found, fetch the
 * study summary directly.
 * @param $variable
 * @param $study_id
 * @return the
 */
function _mica_client_variable_acronym_study($variable, $study_id) {
  foreach ($variable->studySummaries as $study) {
    if ($study->id == $study_id) {
      return mica_client_commons_get_localized_field($study, 'acronym');
    }
  }
  // not found in the dto, then fetch it
  $study_resource = new MicaStudyResource();
  $study = $study_resource->study($study_id);
  return mica_client_commons_get_localized_field($study, 'acronym');
}

function _mica_client_variable_rebuild_stat($variable_stat, $variable) {
  $stats = array();
  foreach ($variable->studyIds as $key_study => $study_id) {
    foreach ($variable_stat as $key_stud => $stat) {
      if ($key_stud == $key_study) {
        $stats[$study_id]['_all'] = $stat->totalHits;
        foreach ($stat->facets[0]->frequencies as $key_attrib => $frequency) {
          $stats[$study_id][$key_attrib] = $frequency->count;
        }
      }
    }
  }
  return $stats;
}

function mica_client_variable_dataset_type($variable) {
  return $variable->variableType == 'Study' ? 'study-dataset' : 'harmonization-dataset';
}