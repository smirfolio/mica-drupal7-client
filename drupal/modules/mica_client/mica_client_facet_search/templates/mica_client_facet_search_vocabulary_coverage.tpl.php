<?php
//dpm($vocabulary_coverage);
//dpm($vocabulary_attribute);
//dpm($chart);
//dpm($bucket_names);
?>

<?php if (!empty($vocabulary_coverage->hits)): ?>
  <?php $has_coverage = TRUE; ?>
  <?php
  $term_names = array();
  foreach ($vocabulary_coverage->terms as $term_coverage) {
    if (!empty($term_coverage->hits)) {
      $term_names[] = $term_coverage->term->name;
    }
  } ?>
  <h4 id="<?php print $taxonomy->name . '-' . $vocabulary_coverage->vocabulary->name; ?>">
    <?php print mica_client_commons_get_localized_field($vocabulary_coverage->vocabulary, 'titles'); ?>
  </h4>

  <p class="help-block">
    <?php print mica_client_commons_get_localized_field($vocabulary_coverage->vocabulary, 'descriptions'); ?>
  </p>



  <div class="coverage-table">

    <table class="table table-striped fix-first-column ">
      <thead>
      <tr>
        <th
          style="<?php if (!empty($vocabulary_coverage->buckets)) {
            print 'width: 350px; min-width: 350px;';
          } ?>"><?php print t('Term'); ?></th>
        <th style="text-align: center; <?php if (empty($vocabulary_coverage->buckets)) {
          print 'width: 150px;';
        } ?>">
          <?php print t('All'); ?>
        </th>
        <?php if (!empty($vocabulary_coverage->buckets)): ?>
          <?php foreach ($vocabulary_coverage->buckets as $bucket) : ?>
            <th style="text-align: center;">
              <?php $is_link = explode('-', $bucket->value);
              if (empty($is_link[1])) {
                print l($is_link[0], 'mica/search',
                  array(
                    'query' => array(
                      'type' => 'variables',
                      'query' => MicaClient::add_parameter_dto_query_link(array(
                          'variables' => array(
                            'terms' => array(
                              $bucket->field => $is_link[0]
                            )
                          )
                        ))
                    ),
                  ));
              }
              else {
                print $is_link[0];
              }
              //
              ?>
            </th>
          <?php endforeach; ?>
        <?php endif ?>
      </tr>
      </thead>
      <tfoot>
      <tr>
        <th><?php print t('Total'); ?></th>
        <th style="text-align: center;" title="100%">
          <?php
          print l($vocabulary_coverage->hits, 'mica/search',
            array(
              'query' => array(
                'type' => 'variables',
                'query' => MicaClient::add_parameter_dto_query_link(array(
                    'variables' => array(
                      'terms' => array(
                        $vocabulary_attribute => $term_names,
                      )
                    )
                  ))
              ),
            )) ?>
        </th>
        <?php if (!empty($vocabulary_coverage->buckets)): ?>
          <?php foreach ($vocabulary_coverage->buckets as $bucket) : ?>
            <th style="text-align: center;">
              <?php if (empty($bucket->hits)): ?>
                <?php print 0; ?>
              <?php else: ?>
                <?php
                print l($bucket->hits, 'mica/search',
                  array(
                    'query' => array(
                      'type' => 'variables',
                      'query' => MicaClient::add_parameter_dto_query_link(array(
                          'variables' => array(
                            'terms' => array(
                              $bucket->field => $bucket->value,
                              $vocabulary_attribute => $term_names,
                            )
                          )
                        ))
                    ),
                  )) ?>
              <?php endif ?>
            </th>
          <?php endforeach; ?>
        <?php endif ?>
      </tr>
      </tfoot>
      <tbody>

      <?php foreach ($vocabulary_coverage->terms as $key_term => $term_coverage) : ?>
        <tr data-toggle="tooltip"
            title="<?php print mica_client_commons_get_localized_field($term_coverage->term, 'descriptions'); ?>">
          <td style="vertical-align: middle; word-wrap:break-word;" class="headcol">
            <?php if (empty($term_coverage->hits)): ?>
              <?php print mica_client_commons_get_localized_field($term_coverage->term, 'titles'); ?>
            <?php else: ?>
              <?php print l(mica_client_commons_get_localized_field($term_coverage->term, 'titles'), 'mica/search',
                array(
                  'query' => array(
                    'type' => 'variables',
                    'query' => MicaClient::add_parameter_dto_query_link(array(
                          'variables' => array(
                            'terms' => array(
                              $vocabulary_attribute => $term_coverage->term->name
                            )
                          )
                        )
                      )
                  ),
                )) ?>
            <?php endif ?>
          </td>
          <td style="text-align: center; vertical-align: middle;"
              title="<?php print empty($vocabulary_coverage->hits) ? '0' : floor($term_coverage->hits * 10000 / $vocabulary_coverage->hits) / 100; ?>%">
            <?php if (empty($term_coverage->hits)): ?>
              <?php print 0; ?>
            <?php else: ?>
              <span class="badge alert-success"><?php print $term_coverage->hits; ?></span>
            <?php endif ?>
          </td>
          <?php if (!empty($term_coverage->buckets)): ?>
            <?php foreach ($bucket_names as $bucket_name) : ?>
              <?php $found = FALSE ?>
              <?php foreach ($term_coverage->buckets as $bucket) : ?>
                <?php if ($bucket->value == $bucket_name): ?>
                  <td style="text-align: center; vertical-align: middle;">
                    <?php if (empty($bucket->hits)): ?>
                      <?php print 0; ?>
                    <?php else: ?>
                      <span class="badge alert-info">
                    <?php print l($bucket->hits, 'mica/search',
                      array(
                        'query' => array(
                          'type' => 'variables',
                          'query' => MicaClient::add_parameter_dto_query_link(array(
                              'variables' => array(
                                'terms' => array(
                                  $vocabulary_attribute => $term_coverage->term->name,
                                  $bucket->field => $bucket->value
                                )
                              )
                            ))
                        ),
                      )) ?>
                    </span>
                    <?php endif ?>
                  </td>
                  <?php $found = TRUE ?>
                <?php endif ?>
              <?php endforeach; ?>
              <?php if (!$found): ?>
                <td style="text-align: center; vertical-align: middle;">0</td>
              <?php endif ?>
            <?php endforeach; ?>
          <?php else: ?>
            <?php foreach ($bucket_names as $bucket_name) : ?>
              <td style="text-align: center; vertical-align: middle;">0</td>
            <?php endforeach; ?>
          <?php endif ?>
        </tr>
      <?php endforeach; ?>

      </tbody>
    </table>


  </div>

  <div>
    <?php //print render($chart); ?>
  </div>

<?php endif ?>
