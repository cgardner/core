<?php
namespace models\BugTracker;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="bugs")
 */
class Bug
{
  /**
   * @Id 
   * @Column(type="integer") 
   * @GeneratedValue
   */
  private $id;
  
  /**
   * @Column(type="string")
   */
  private $description;
  
  /**
   * @Column(type="datetime")
   */
  private $created;
  
  /**
   * @Column(type="string")
   */
  private $status;
  
  /**
   * @ManyToOne(targetEntity="User", inversedBy="assignedBugs")
   */
  private $reporter;
  
  /**
   * @ManyToOne(targetEntity="User", inversedBy="reportedBugs")
   */
  private $engineer;
  
  /**
   * @ManyToMany(targetEntity="Product")
   */
  private $products = null;

  public function __construct()
  {
    $this->products = new ArrayCollection();
  }
  
  public function __set($name, $value) {
    $this->$name = $value;
  }

  public function __get($name) {
    return $this->$name;
  }
  
  public function assignToProduct($product)
  {
    $this->products[] = $product;
  }

  public function getProducts()
  {
    return $this->products;
  }
  
  public function setEngineer($engineer)
  {
    $engineer->assignedToBug($this);
    $this->engineer = $engineer;
  }

  public function setReporter($reporter)
  {
    $reporter->addReportedBug($this);
    $this->reporter = $reporter;
  }

  public function getEngineer()
  {
    return $this->engineer;
  }

  public function getReporter()
  {
    return $this->reporter;
  }
  
}