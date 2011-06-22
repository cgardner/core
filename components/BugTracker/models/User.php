<?php
namespace models\BugTracker;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="users")
 */
class User
{
  /**
   * @Id 
   * @GeneratedValue 
   * @Column(type="integer")
   */
  private $id;
  
  /**
   * @Column(type="string")
   */
  private $name;
  
  /**
   * @OneToMany(targetEntity="Bug", mappedBy="reporter")
   */
  private $reportedBugs = null;
  
  /**
   * @OneToMany(targetEntity="Bug", mappedBy="engineer")
   */
  private $assignedBugs = null;

  public function __construct()
  {
    $this->reportedBugs = new ArrayCollection();
    $this->assignedBugs = new ArrayCollection();
  }
  
  public function __set($name, $value) {
    $this->$name = $value;
  }

  public function __get($name) {
    return $this->$name;
  }
  
  public function addReportedBug($bug)
  {
    $this->reportedBugs[] = $bug;
  }

  public function assignedToBug($bug)
  {
    $this->assignedBugs[] = $bug;
  }
  
}