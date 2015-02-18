<?php if (!empty($dataset)): ?>
  <div class="row sm-bottom-margin document-item-list flex-row">
    <div class="col-md-12  col-sm-12 col-xs-12">
      <h4>
        <?php
        $acronym = obiba_mica_commons_get_localized_field($dataset, 'acronym');
        $name = obiba_mica_commons_get_localized_field($dataset, 'name');
        print l($acronym == $name ? $acronym : $acronym . ' - ' . $name,
          'mica/' . obiba_mica_dataset_type($dataset) . '/' . $dataset->id); ?>
      </h4>
      <hr class="no-margin">
      <p class="md-top-margin">
        <small>
          <?php
          if (empty($dataset->description)) {
            print '';
          }
          else {
            $objective = obiba_mica_commons_get_localized_field($dataset, 'description');;
            if (drupal_strlen($objective) >= 300) {
              print text_summary(strip_tags(obiba_mica_commons_markdown($objective)), 'html', 300)
                . '... ' . l('Read more',
                  'mica/' . obiba_mica_dataset_type($dataset) . '/' . $dataset->id);
            }
            else {
              print $objective;
            }
          }
          ?>
        </small>
      </p>
      <div>
        <?php
        $counts = $dataset->{'obiba.mica.CountStatsDto.datasetCountStats'};
        $variables = $counts->variables;
        $vars_caption = $variables < 2 ? t('variable') : t('variables');
        $studies = $counts->studies;
        $studies_caption = $studies < 2 ? t('study') : t('studies');
        $networks = $counts->networks;
        $network_caption = $networks < 2 ? "network" : "networks";
        ?>
        <?php if (!empty($networks) && variable_get_value('datasets_column_networks')): ?>
          <span class="label label-info right-indent">
            <?php print MicaClientAnchorHelper::dataset_networks(t('@count ' . $network_caption, array('@count' => $networks)), $dataset->id) ?>
          </span>
        <?php endif ?>
        <?php if (!empty($studies) && variable_get_value('datasets_column_studies')): ?>
          <span class="label label-info right-indent">
            <?php print MicaClientAnchorHelper::dataset_studies(t('@count ' . $studies_caption, array('@count' => $studies)), $dataset->id) ?>
          </span>
        <?php endif ?>
        <?php if (!empty($variables) && variable_get_value('datasets_column_variables')): ?>
          <span class="label label-info">
            <?php print MicaClientAnchorHelper::dataset_variables(t('@count ' . $vars_caption, array('@count' => $variables)), $dataset->id) ?>
          </span>
        <?php endif ?>
      </div>
    </div>
  </div>



  <?php
  $dataset_name = obiba_mica_commons_get_localized_field($dataset, 'name');
  ?>
<?php endif; ?>