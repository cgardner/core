<h1>Bugs</h1>

<p><a href="/tracker">&laquo; Back</a></p>

<?php
foreach($this->bugs AS $bug) 
{  
  $products = array();
  foreach ($bug->getProducts() as $product) {
    $products[] = $product->name;
  }
  $products = implode(', ', $products);
?>
 <p>
   ISSUE #<?php echo $bug->id; ?> <br />
   <?php echo $bug->description; ?> <br />
   Reported: <?php echo $bug->created->format('Y-m-d H:i:s'); ?> <br />
   Reported by: <?php echo $bug->getReporter()->name; ?> <br />
   Assigned to: <?php echo $bug->getEngineer()->name; ?> <br />
   Products: <?php echo $products; ?>
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