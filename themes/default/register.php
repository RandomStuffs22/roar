<?php theme_include('partials/header'); ?>

	<h3>Register</h3>

	<?php echo Form::open(base_url() . 'register'); ?>

	<fieldset>
		<p><label>Name<br>
		<?php echo Form::input('name', Input::previous('name')); ?></label></p>

		<p><label>Email<br>
		<?php echo Form::input('email', Input::previous('email')); ?></label></p>

		<p><label>Username<br>
		<?php echo Form::input('username', Input::previous('username')); ?></label></p>

		<p><label>Password<br>
		<?php echo Form::password('password'); ?></label></p>

		<?php echo Form::submit('submit', 'Register'); ?>
	</fieldset>

	<?php echo Form::close(); ?>

<?php theme_include('partials/footer'); ?>