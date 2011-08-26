<?php echo $this->instance('Session')->warning; ?>
<?php echo $this->instance('Session')->notice; ?>
<h1>Login</h1>

<?php $form = $this->instance('FormHelper'); ?>
<?php echo $form->formTag('/user/authenticate', 'user-form'); ?>
<div><?php echo $form->labelFor('Username', 'user-form-username').$form->textFieldTag('username'); ?></div>
<div><?php echo $form->labelFor('Password', 'user-form-password').$form->passwordFieldTag('password'); ?></div>
<?php echo $form->submitTag('Login'); ?>
<?php echo $form->formEnd(); ?>