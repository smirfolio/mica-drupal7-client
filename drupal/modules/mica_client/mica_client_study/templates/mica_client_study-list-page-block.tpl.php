<?php //dpm($list_studies->studySummaries); ?>
<?php //dpm($list_studies); ?>

<div class="row lg-bottom-margin">
  <div class="col-md-2 col-xs-2 text-center">
    <?php if (!empty($logo_url)): ?>
      <img src="<?php print $logo_url ?>"
           class="listImageThumb"/>
    <?php else : ?>
      <h1 class="big-character">
        <span class="t_badge color_light i-obiba-S"></span>
      </h1>
    <?php endif; ?>
  </div>
  <div class="col-md-10 col-xs-10">
    <div class="panel panel-default">
      <div class="panel-heading">
        <a
          href="study/<?php print $study->id ?>">
          <?php print mica_client_commons_get_localized_field($study, 'acronym') . ' - ' . mica_client_commons_get_localized_field($study, 'name'); ?>
        </a>
      </div>
      <div class="panel-body">
        <p class="lg-top-margin">
          <?php print truncate_utf8(mica_client_commons_get_localized_field($study, 'objectives'), 300, TRUE, TRUE); ?>
        </p>
      </div>
    </div>
  </div>
</div>

