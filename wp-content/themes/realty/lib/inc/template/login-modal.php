<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
  <div class="modal-dialog login-modal-content">
    <div class="modal-content">    	
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	
      	<ul class="nav nav-tabs" role="tablist">
					<li class="active"><a href="#tab-login" role="tab" data-toggle="tab"><?php _e( 'Login', 'tt' ); ?></a></li>
					<?php if( get_option('users_can_register') ) { ?>
					<li><a href="#tab-registration" role="tab" data-toggle="tab"><?php _e( 'Register', 'tt' ); ?></a></li>
					<?php } ?>
	    	</ul>
      </div>
      <div class="modal-body">

				<div class="tab-content">
						
					<div class="tab-pane active" id="tab-login">
						<?php if ( !is_user_logged_in() ) { ?>
		        <p id="msg-login-to-add-favorites" class="alert alert-info hide"><small><?php _e( 'You have to be logged in to use this feature.', 'tt' ); ?></small></p>
		        <?php } ?>
						<?php	if( isset($_GET['login']) && $_GET['login'] == 'failed' ) { ?>
						<p id="login-error" class="text-danger"><small><?php _e( 'Incorrect login details. Please enter your username and password, and submit again.', 'tt' ); ?></small></p>
						<?php } 
						wp_login_form( array( "id_submit" => "wp-submit-login", "label_username" => __( 'Username / Email', 'tt' ) ) ); ?>
					</div>

					<?php if( get_option('users_can_register') ) { ?>
					<div class="tab-pane" id="tab-registration">
						<form name="registerform" id="registerform" action="<?php echo wp_registration_url(); ?>" method="post">
							<div class="form-group">
								<label for="user_login"><?php _e( 'Username', 'tt' ); ?></label>
								<input type="text" name="user_login" id="user_login" class="form-control" title="<?php _e( 'Please enter your username.', 'tt' ); ?>"></label>
							</div>
							<div class="form-group">
								<label for="user_email"><?php _e( 'E-mail', 'tt' ); ?></label>
								<input type="text" name="user_email" id="user_email" class="form-control" title="<?php _e( 'Please enter your email.', 'tt' ); ?>"></label>
							</div>
							<p id="reg_passmail"><?php _e( 'A password will be e-mailed to you.', 'tt' ); ?></p>					
							<input type="hidden" name="redirect_to" value="<?php echo site_url(); ?>?user=registered">
							<input type="submit" name="wp-submit-registration" id="wp-submit-registration" value="Register">
						</form>
					</div>
					<?php } ?>
					
				</div>
				
      </div>
    </div>
  </div>
</div>