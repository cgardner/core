<h1>Users</h1>

<p><a href="/tracker">&laquo; Back</a></p>

<?php
foreach($this->users AS $user) 
{  
?>
 <p>
   USER #<?php echo $user->id; ?> <br />
   <?php echo $user->name; ?>
</p>
<?php
}
?>

<!-- some testing debug stuff -->
<div style="margin:2em;padding:1em;background:#eee;">
  Doctrine debug stuff
  <?php 
  $queries = $this->em->getConfiguration()->getSQLLogger()->queries;
  foreach ($queries as $query) {
    echo '<p style="padding:3px;background:#fff;">';
    echo '[SQL] '.$query['sql'].'<br />';
    echo '[PARAMS] '.serialize($query['params']).'<br />';
    echo '[TIME] '.$query['executionMS'];
    echo '</p>';
  }
  ?>
</div>