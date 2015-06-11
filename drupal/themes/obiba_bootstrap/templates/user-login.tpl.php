<div class="outer">

  <div class="innerdivs">
    <div class="row">
      <div class="col-md-6 text-center bg-primary"><?php print t('Sign In') ?></div>
	    <?php if (!empty($form['obiba_auth']) && $form['obiba_auth']['#value'] == 'obiba_auth_user_register_form'): ?>
		    <div class="col-md-6 text-center "><?php print l('Sign Up ', 'obiba_user/register') ?></div>
	    <?php else: ?>
		    <div class="col-md-6 text-center "><?php print l('Sign Up ', 'user/register') ?></div>
	    <?php endif; ?>

    </div>
    <p><?php print render($intro_text); ?></p>

    <div class="obiba-bootstrap-user-login-form-wrapper">
      <?php print drupal_render_children($form) ?>
    </div>

    <div class="md-top-margin">
      <?php print l('Forgot your password?', 'user/password') ?>
    </div>

  </div>

</div>