<?php
//dpm($network_dto);
?>

<div>
  <?php if (!empty($network_dto->description)): ?>
    <p class="md-top-margin">
      <?php if (!empty($network_dto->logo->id)): ?>
        <img
          src="<?php print obiba_mica_commons_safe_expose_server_url($network_dto->id, $network_dto->logo, 'network') ?>"
          class="imageThumb">
      <?php endif; ?>
      <?php print obiba_mica_commons_markdown(obiba_mica_commons_get_localized_field($network_dto, 'description')); ?>
    </p>
  <?php endif; ?>

  <?php if (!empty($network_dto->studyIds)): ?>
    <div class="pull-right md-bottom-margin">
      <?php
      $query_array = array("networks" => array("terms" => array("networkId" => $network_dto->id)));
      $query = MicaClient::create_query_dto_as_string($query_array);

      print l(t('Search Variables'), 'mica/search',
        array(
          'query' => array(
            'type' => 'variables',
            'query' => $query
          ),
          'attributes' => array('class' => 'btn btn-primary')
        ));
      ?>
      <?php
      print l(t('View Coverage'), 'mica/coverage',
        array(
          'query' => array(
            'type' => 'variables',
            'query' => $query
          ),
          'attributes' => array('class' => 'btn btn-primary indent')
        ));
      ?>
    </div>
  <?php endif; ?>
</div>

<div class="clearfix"></div>

<article>

  <section>
    <h3><?php print t('Overview') ?></h3>

    <div class="row">
      <div class="col-lg-6 col-xs-12">

        <table class="table table-striped">
          <tbody>
          <?php if (!empty($network_dto->acronym)): ?>
            <tr>
              <td><h5><?php print t('Acronym') ?></h5></td>
              <td><p><?php print obiba_mica_commons_get_localized_field($network_dto, 'acronym'); ?></p></td>
            </tr>
          <?php endif; ?>

          <?php if (!empty($network_dto->website)): ?>
            <tr>
              <td><h5><?php print t('Website') ?></h5></td>
              <td>
                <p><?php
                  print l(obiba_mica_commons_get_localized_field($network_dto, 'acronym') . ' ' . t('website'),
                    $network_dto->website,
                    array('attributes' => array('target' => '_blank')));
                  ?></p>
              </td>
            </tr>
          <?php endif; ?>

          <?php if (!empty($network_dto->investigators)): ?>
            <tr>
              <td><h5><?php print t('Investigators') ?></h5></td>
              <td>
                <ul>
                  <?php foreach ($network_dto->investigators as $key_investigator => $investigator) : ?>
                    <li>
                      <a href="#" data-toggle="modal"
                         data-target="#investigator_<?php print $network_dto->id ?>_<?php print $key_investigator ?>">
                        <?php print $investigator->title; ?>
                        <?php print $investigator->firstName; ?>
                        <?php print $investigator->lastName; ?>
                        <?php if (!empty($investigator->academicLevel)) {
                          print ', ' . $investigator->academicLevel;
                        } ?>
                        (<?php print obiba_mica_commons_get_localized_field($investigator->institution, 'name'); ?>)
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
            </tr>
          <?php endif; ?>

          <?php if (!empty($network_dto->contacts)): ?>
            <tr>
              <td><h5><?php print t('Contacts') ?></h5></td>
              <td>
                <ul>
                  <?php foreach ($network_dto->contacts as $key_contact => $contact) : ?>
                    <li>
                      <a href="#" data-toggle="modal"
                         data-target="#contact_<?php print $network_dto->id ?>_<?php print $key_contact ?>">
                        <?php print $contact->title; ?>
                        <?php print $contact->firstName; ?>
                        <?php print $contact->lastName; ?>
                        <?php if (!empty($contact->academicLevel)) {
                          print ', ' . $contact->academicLevel;
                        } ?>
                        (<?php print obiba_mica_commons_get_localized_field($contact->institution, 'name'); ?>)
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
            </tr>
          <?php endif; ?>

          </tbody>
        </table>

      </div>

      <?php if (!empty($network_dto->attributes)): ?>
        <div class="col-lg-6 col-xs-12">
          <h5><?php print t('Attributes') ?></h5>

          <p><?php print obiba_mica_dataset_attributes_tab($network_dto->attributes, 'maelstrom'); ?></p>
        </div>
      <?php endif; ?>
    </div>

  </section>

  <!-- STUDIES -->
  <section>
    <h3><?php print t('Studies') ?></h3>

    <div class="row">
      <div class="col-lg-12 col-xs-12">
        <div class="scroll-content-tab">
          <?php print obiba_mica_network_study_table($network_dto); ?>
        </div>
      </div>
    </div>
  </section>

  <!-- COVERAGE -->
  <?php if (!empty($coverage)): ?>
    <section>
      <h3 id="coverage"><?php print t('Classifications Coverage') ?></h3>

      <?php foreach ($coverage as $taxonomy_coverage): ?>
        <h4><?php print obiba_mica_commons_get_localized_field($taxonomy_coverage['taxonomy'], 'titles'); ?></h4>
        <p class="help-block">
          <?php print obiba_mica_commons_get_localized_field($taxonomy_coverage['taxonomy'], 'descriptions'); ?>
        </p>
        <div class="scroll-content-tab">
          <?php print render($taxonomy_coverage['chart']); ?>
        </div>
      <?php endforeach ?>
    </section>
  <?php endif; ?>


  <div><?php !empty($investigators_modal) ? print $investigators_modal : ''; ?></div>
  <div><?php !empty($contacts_modal) ? print $contacts_modal : ''; ?></div>

</article>