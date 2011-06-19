<h1>Login</h1>
<?php echo $this->form->formTag('/user/process', 'user-form'); ?>
<?php echo $this->form->textFieldTag('email'); ?>
<?php echo $this->form->passwordFieldTag('password'); ?>
<?php echo $this->form->submitTag('Login'); ?>
<?php echo $this->form->formEnd(); ?>
<?php echo $this->linkTo('Register', '/user/register'); ?>