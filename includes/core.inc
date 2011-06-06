<?php 
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

//TODO: Replace with function to dynamically load everything in the core folder

//Load Events
require_once dirname(__FILE__) . '/events.inc';

//Load Constants
require_once dirname(__FILE__) . '/constants.inc';

//Load utility functions
require_once dirname(__FILE__) . '/inflect.php';
require_once dirname(__FILE__) . '/utils.php';

//Load interfaces
require_once dirname(__FILE__) . '/../interfaces/CumulaDataStore.interface.php';
require_once dirname(__FILE__) . '/../interfaces/CumulaSchema.interface.php';
require_once dirname(__FILE__) . '/../interfaces/CumulaConfig.interface.php';
require_once dirname(__FILE__) . '/../interfaces/CumulaTemplater.interface.php';

//Load abstract classes
require_once dirname(__FILE__) . '/../abstracts/EventDispatcher.class.php';
require_once dirname(__FILE__) . '/../abstracts/BaseDataStore.class.php';
require_once dirname(__FILE__) . '/../abstracts/BaseComponent.class.php';
require_once dirname(__FILE__) . '/../abstracts/BaseMVCComponent/BaseMVCComponent.class.php';
require_once dirname(__FILE__) . '/../abstracts/BaseSqlDataStore.class.php';
require_once dirname(__FILE__) . '/../abstracts/BaseSchema.class.php';

//Load Core Classes
require_once dirname(__FILE__) . '/../ComponentManager.class.php';
require_once dirname(__FILE__) . '/../Response.class.php' ;
require_once dirname(__FILE__) . '/../Request.class.php' ;
require_once dirname(__FILE__) . '/../SystemConfig.class.php' ;

require_once dirname(__FILE__) . '/../common/ContentBlock/ContentBlock.class.php';
require_once dirname(__FILE__) . '/../common/SqliteDataStore/SqliteDataStore.class.php';





