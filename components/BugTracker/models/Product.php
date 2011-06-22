<?php
namespace models\BugTracker;

/**
 * @Entity @Table(name="products")
 */
class Product
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
  private $name;
  
  public function __set($name, $value) {
    $this->$name = $value;
  }

  public function __get($name) {
    return $this->$name;
  }
}