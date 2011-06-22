<?php
/**
 * It should not be necessary to edit below this line.
 */
require_once('doctrine_init.php');

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
  'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),
  'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection())
));

