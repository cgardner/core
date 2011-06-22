<?php

class TrackerController extends BaseMVCController 
{
  
  protected $em;
  
	public function __construct($component) 
  {
		parent::__construct($component);
    
    // Any controller that wants to interact with Doctrine entities will need to 
    // grab the entity manger from the component.
    $this->em = BugTracker::getInstance()->get_em();
	}
	
  
	public function startup() {
		$this->registerRoute('/tracker', 'tracker_index');
		$this->registerRoute('/tracker/users', 'list_users');
		$this->registerRoute('/tracker/bugs', 'list_bugs');
	}
  
  
  public function tracker_index($route, $router, $args)
  {
    $this->render('index');
  }
	
  
  public function list_users($route, $router, $args)
  {
    $dql = 'SELECT u 
      FROM models\BugTracker\User u';

    $query = $this->em->createQuery($dql);
    $users = $query->getResult();
    
    $this->users = $users;
    
    $this->render('list_users');
  }
  
  
  public function list_bugs($route, $router, $args)
  {
    $dql = 'SELECT b, e, r 
      FROM models\BugTracker\Bug b 
      JOIN b.engineer e 
      JOIN b.reporter r 
      ORDER BY b.created ASC';

    $query = $this->em->createQuery($dql);
    $bugs = $query->getResult();
    
    $this->bugs = $bugs;
    
    $this->render('list_bugs');
  }
  
}