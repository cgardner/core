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
require_once dirname(__FILE__) . '/../interfaces/CumulaSchema.interface.php';
require_once dirname(__FILE__) . '/../interfaces/CumulaConfig.interface.php';
require_once dirname(__FILE__) . '/../interfaces/CumulaTemplater.interface.php';

//Load abstract classes
require_once dirname(__FILE__) . '/../classes/EventDispatcher.class.php';
require_once dirname(__FILE__) . '/../classes/BaseDataStore.class.php';
require_once dirname(__FILE__) . '/../classes/BaseComponent.class.php';
require_once dirname(__FILE__) . '/../classes/BaseMVCComponent.class.php';
require_once dirname(__FILE__) . '/../classes/BaseMVCController.class.php';
require_once dirname(__FILE__) . '/../classes/BaseMVCModel.class.php';
require_once dirname(__FILE__) . '/../classes/BaseSqlDataStore.class.php';
require_once dirname(__FILE__) . '/../classes/BaseAPIDataStore.class.php';
require_once dirname(__FILE__) . '/../classes/SimpleSchema.class.php';

//Load Core Classes
require_once dirname(__FILE__) . '/../classes/ComponentManager.class.php';
require_once dirname(__FILE__) . '/../classes/Router.class.php';
require_once dirname(__FILE__) . '/../classes/Response.class.php' ;
require_once dirname(__FILE__) . '/../classes/Request.class.php' ;
require_once dirname(__FILE__) . '/../classes/SystemConfig.class.php' ;
require_once dirname(__FILE__) . '/../classes/Cumula.class.php';

require_once dirname(__FILE__) . '/../libraries/ContentBlock/ContentBlock.class.php';
require_once dirname(__FILE__) . '/../libraries/SqliteDataStore/SqliteDataStore.class.php';
require_once dirname(__FILE__) . '/../libraries/StandardConfig/StandardConfig.class.php';
require_once dirname(__FILE__) . '/../libraries/YAMLDataStore/YAMLDataStore.class.php';
require_once dirname(__FILE__) . '/../libraries/GDocsAPIDataStore/GDocsAPIDataStore.class.php';






