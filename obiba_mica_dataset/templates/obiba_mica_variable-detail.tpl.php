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
 * Code for the obiba_mica_dataset modules.
 */

?>

<article class="bordered-article">
  <section>
    <div class="row">
      <div class="col-lg-6 col-xs-12 ">
        <h2><?php print $localize->getTranslation('client.label.overview') ?></h2>

        <table class="table table-striped">
          <tbody>

          <?php if (!empty($variable_dto->label)): ?>
            <tr>
              <th><?php print $localize->getTranslation('search.variable.label') ?></th>
              <td><?php print $variable_dto->label; ?></td>
            </tr>
          <?php endif; ?>

          <?php if (!empty($variable_dto->description)): ?>
            <tr>
              <th><?php print $localize->getTranslation('description') ?></th>
              <td><p><?php print obiba_mica_commons_markdown($variable_dto->description); ?></p></td>
            </tr>
          <?php endif; ?>

          <?php if ($variable_dto->variableType == 'Dataschema' && variable_get_value('variable_show_networks') && !empty($variable_dto->networkSummaries)): ?>
            <tr>
              <th>
                <?php print $localize->getTranslation('networks'); ?>
              </th>
              <td>
                <ul class="list-unstyled">
                  <?php foreach ($variable_dto->networkSummaries as $network_summary): ?>
                    <li><?php print MicaClientAnchorHelper::network($network_summary); ?></li>
                  <?php endforeach ?>
                </ul>
              </td>
            </tr>
          <?php endif; ?>

          <?php if (variable_get_value('variable_show_studies')): ?>
            <?php if ((!empty($variable_dto->studySummaries) || !empty($variable_dto->networkSummaries))): ?>
                  <tr>
                      <th>
                        <?php if ($variable_dto->variableType == 'Dataschema') :
                          print $localize->getTranslation('studies');
                        elseif (!empty($variable_dto->studySummaries)) :
                          print $localize->getTranslation('study.label');
                        else:
                          print $localize->getTranslation('dataset.network.title');
                        endif;
                        ?>
                      </th>
                      <td>
                        <?php if ($variable_dto->variableType == 'Dataschema'): ?>
                            <ul class="list-unstyled">
                              <?php foreach ($variable_dto->studySummaries as $study_summary): ?>
                                  <li><?php print MicaClientAnchorHelper::study($study_summary); ?></li>
                              <?php endforeach ?>
                            </ul>
                        <?php elseif (!empty($variable_dto->studySummaries)):
                          print MicaClientAnchorHelper::study($variable_dto->studySummaries[0]);
                        elseif (!empty($variable_dto->networkSummaries)):
                          print MicaClientAnchorHelper::network($variable_dto->networkSummaries[0]);
                        endif ?>
                      </td>
                  </tr>
            <?php elseif (!empty($variable_dto->studySummary)): ?>
                  <tr>
                      <th>
                        <?php if ($variable_dto->studySummary->studyResourcePath == DrupalMicaStudyResource::HARMONIZATION_STUDY): ?>
                          <?php print $localize->getTranslation('global.harmonization-study'); ;?>
                          <?php else: ?>
                          <?php print $localize->getTranslation('study.label'); ;?>
                        <?php endif; ?>
                      </th>
                      <td>
                        <?php
                          print MicaClientAnchorHelper::study($variable_dto->studySummary);
                       ?>
                      </td>
                  </tr>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (!empty($variable_dto->datasetId)): ?>
            <tr>
              <th><?php print $localize->getTranslation('dataset.details'); ?></th>
              <td>
                <p>
                  <?php print MicaClientAnchorHelper::variableDataset($variable_dto); ?>
                </p>
              </td>
            </tr>
          <?php endif; ?>

          <tr>
            <th><?php print $localize->getTranslation('client.label.variable.value-type'); ?></th>
            <td>
              <p><?php print $localize->getTranslation('variable_taxonomy.vocabulary.valueType.term.' . $variable_dto->valueType . '.title'); ?></p>
            </td>
          </tr>

          <?php if (!empty($variable_dto->unit)): ?>
            <tr>
              <th><?php print $localize->getTranslation('client.label.variable.unit'); ?></th>
              <td>
                <p><?php print t('@varUnit', array('@varUnit' => $variable_dto->unit)); ?></p>
              </td>
            </tr>
          <?php endif; ?>

          <tr>
            <th><?php print $localize->getTranslation('client.label.variable.variable-type'); ?></th>
            <td>
              <p>
                <?php $variable_type = $variable_dto->variableType?>
                <?php if (variable_get_value('mica_all_variables_dataschema')  && stristr($variable_type,'study') === FALSE): ?>
                  <?php $variable_type = $localize->getTranslation('search.variable.dataschema'); ?>
                <?php endif; ?>
                <?php print t('@type Variable', array('@type' =>
                  $variable_type)); ?>
                <?php if ($variable_dto->variableType == 'Harmonized'): ?>
                  <?php print '(' . MicaClientAnchorHelper::variableHarmonized($variable_dto) . ')'; ?>
                <?php endif; ?>
              </p>
            </td>
          </tr>

          </tbody>
        </table>
      </div>
      <div class="col-lg-6 col-xs-12">
        <!-- Taxonomy terms -->
        <?php if (!empty($variable_dto->termAttributes)): ?>
          <h2><?php print $localize->getTranslation('classifications.title') ?></h2>
          <table class="table table-striped">
            <tbody>
            <?php foreach ($variable_dto->termAttributes as $term_attributes) : ?>
              <tr>
                <td colspan="2" data-toggle="tooltip"
                  title="<?php print obiba_mica_commons_get_localized_field($term_attributes->taxonomy, 'descriptions'); ?>">
                  <p class="help-block">
                    <?php print obiba_mica_commons_get_localized_field($term_attributes->taxonomy, 'titles'); ?>
                  </p>
                </td>
              </tr>
              <?php foreach ($term_attributes->vocabularyTerms as $term_attribute) : ?>
                <tr>

                  <th data-toggle="tooltip"
                    title="<?php print obiba_mica_commons_get_localized_field($term_attribute->vocabulary, 'descriptions'); ?>">
                    <?php print obiba_mica_commons_get_localized_field($term_attribute->vocabulary, 'titles'); ?>
                  </th>
                  <td>
                    <?php if (count($term_attribute->terms == 1)): ?>
                      <p data-toggle="tooltip"
                        title="<?php print obiba_mica_commons_get_localized_field($term_attribute->terms[0], 'descriptions'); ?>">
                        <?php print obiba_mica_commons_get_localized_field($term_attribute->terms[0], 'titles'); ?>
                      </p>
                    <?php else: ?>
                      <ul class="list-unstyled">
                        <?php foreach ($term_attribute->terms as $term) : ?>
                          <li data-toggle="tooltip"
                            title="<?php print obiba_mica_commons_get_localized_field($term, 'descriptions'); ?>">
                            <?php print obiba_mica_commons_get_localized_field($term, 'titles'); ?>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endforeach; ?>

            </tbody>
          </table>
        <?php endif; ?>

      </div>
    </div>

  </section>

  <!-- CATEGORIES -->
  <?php if (!empty($variable_dto->categories)): ?>
    <section>
      <h2><?php print $localize->getTranslation('client.label.variable.categories') ?></h2>

      <div class="row">
        <div class="col-md-6 col-sm-12">
          <?php print obiba_mica_dataset_variable_get_categories($variable_dto->categories); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- STATISTICS -->
  <?php if (variable_get_value('mica_statistics')): ?>
    <section id="section-statistics">
      <h2>
        <?php print $localize->getTranslation('client.label.variable.statistics') ?>
        <?php if (strcasecmp($variable_dto->nature, 'CATEGORICAL') === 0 && $variable_dto->variableType !== 'Harmonized'): ?>
          <span class="pull-right">
        <?php if (variable_get_value('mica_statistics')) :
          print MicaClientAnchorHelper::variableCrosstab($variable_dto, TRUE);
        endif;
        ?>
      </span>
        <?php endif; ?>
      </h2>
      <?php
      $column_for_detail_statistics = 6;
      if (!variable_get_value('dataset_detailed_var_stats') && $variable_dto->variableType == 'Dataschema') : ?>
        <p><?php print t('Cumulative Summary of all Studies:') ?></p>
      <?php endif; ?>
      <?php if ($variable_dto->variableType == 'Dataschema' && variable_get_value('dataset_detailed_var_stats')): ?>
        <div class="scroll-content-tab">
          <div class="table-statistic-var">
            <div id="param-statistics"
              var-id="<?php print $variable_dto->id; ?>"
              <?php if (!variable_get_value('dataset_detailed_var_stats')) : ?> class="statistic-tab"<?php endif; ?> >
              <div id="toempty">
                <img
                  src="<?php print base_path() . drupal_get_path('theme', obiba_mica_commons_get_current_theme()) ?>/img/spin.gif">
              </div>
            </div>
          </div>
        </div>
        <div ng-controller="VariableCoverageChartController">
          <div ng-repeat="d3Config in d3Configs">
            <obiba-nv-chart chart-config="d3Config"></obiba-nv-chart>
          </div>
        </div>
      <?php else: ?>
        <div class="row">
          <div
            class="col-md-6 col-sm-12">
            <div id="param-statistics"
              var-id="<?php print $variable_dto->id; ?>"
              <?php if (!variable_get_value('dataset_detailed_var_stats')) : ?> class="statistic-tab"<?php endif; ?> >
              <div id="toempty">
                <img
                  src="<?php print base_path() . drupal_get_path('theme', obiba_mica_commons_get_current_theme()) ?>/img/spin.gif">
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div ng-controller="VariableCoverageChartController">
              <div ng-repeat="d3Config in d3Configs">
                <obiba-nv-chart chart-config="d3Config"></obiba-nv-chart>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <?php if ($variable_dto->variableType != DrupalMicaStudyResource::TYPE_COLLECTION_STUDY): ?>
    <section>
      <h2><?php print $localize->getTranslation('client.label.variable.harmonization') ?></h2>
      <?php print render($harmonization_table_legend); ?>
      <?php if ($variable_dto->variableType == 'Dataschema'): ?>

        <div id="variable-harmonization-table">
          <div class="row">
            <div class="col-lg-12 col-xs-12">
              <table class="table table-striped"
                id="table-variable-harmonization"></table>
            </div>
          </div>
        </div>

        <div class="scroll-content-tab">
          <?php obiba_mica_dataset_variable_get_harmonizations($variable_dto); ?>
        </div>
        <?php if (!empty($variable_harmonization_algorithms)): ?>
          <?php if (!empty(variable_get_value('variable_algorithm'))): ?>
          <button id="harmo-algo"
            data-loading-text="<?php print $localize->getTranslation('loading') ?>"
            type="button"
            class="btn btn-success md-bottom-margin margin-top-20"
            data-toggle="collapse"
            data-target="#harmo-algo"
            aria-expanded="true"
            aria-controls="harmo-algo"
            var-id="<?php print $variable_dto->id; ?>"
            title-button-var="<?php print $localize->getTranslation('client.label.variable.harmonization-algo'); ?>"
            >

              <?php print $localize->getTranslation('client.label.variable.show-harmo-algo') ?>
            </button>
            <div id="harmo-algo" class="collapse">

            </div>
          <?php endif; ?>

        <?php endif; ?>
      <?php else: ?>
        <div class="row">
          <div class="col-md-6 col-xs-12 ">

            <table class="table table-striped">
              <tbody>
              <tr>
                <th><?php print $localize->getTranslation('status'); ?></th>
                <td>
                  <?php if (empty($variable_harmonization['status'])): ?>
                    <span
                      class="glyphicon glyphicon-question-sign alert-warning" ></span>
                  <?php elseif ($variable_harmonization['status'] == 'complete'): ?>
                    <span class="glyphicon glyphicon-ok alert-success"></span>
                    <?php
                  elseif ($variable_harmonization['status'] == 'impossible'): ?>
                    <span
                      class="glyphicon <?php print ObibaDatasetConstants::getIcon(); ?>" ></span>
                    <?php
                  elseif ($variable_harmonization['status'] == 'undetermined'): ?>
                    <span
                      class="glyphicon glyphicon-question-sign alert-warning"></span>
                  <?php endif ?>
                </td>
              </tr>
              <?php if (!empty($variable_harmonization['status_detail'])): ?>
              <tr>
                <th><?php print $localize->getTranslation('client.label.variable.status-detail'); ?></th>
                <td>
                  <span><?php print t(ucfirst($variable_harmonization['status_detail'])) ?></span>
                </td>
              </tr>
              <?php endif ?>
              <tr>
                <th><?php print $localize->getTranslation('comment-label'); ?></th>
                <td>
                  <p><?php print empty($variable_harmonization['comment']) ? '<i>None</i>' : obiba_mica_commons_markdown(filter_xss($variable_harmonization['comment'], obiba_mica_commons_allowed_filter_xss_tags())); ?></p>
                </td>
              </tr>
              </tbody>
            </table>

          </div>
        </div>
        <?php if (!empty(variable_get_value('variable_algorithm'))): ?>
          <?php if ($variable_harmonization['status'] == 'complete'): ?>
            <?php if (!empty($variable_harmonization['algorithm'])): ?>
              <h2><?php print $localize->getTranslation('config.keys.algo') ?></h2>
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <?php print $variable_harmonization['algorithm']; ?>
                </div>
              </div>
            <?php else: ?>
              <h4><?php print $localize->getTranslation('client.label.variable.script'); ?></h4>
              <pre class="prettyprint lang-js">
            <?php print $variable_harmonization['script']; ?>
          </pre>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>

      <?php endif; ?>
    </section>
  <?php endif; ?>
</article>
