<?php



require_once(__DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Zend'.DIRECTORY_SEPARATOR.'Loader.php');

set_include_path(__DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR);

Zend_Loader::loadClass('Zend_Gdata');

Zend_Loader::loadClass('Zend_Gdata_AuthSub');

Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

Zend_Loader::loadClass('Zend_Gdata_Docs');

Zend_Loader::loadClass('Zend_Gdata_App_MediaFileSource');

require_once('GDocsZendStreamMediaSource.class.php');

require_once('GDocsSchema.class.php');

class GDocsAPIDataStore extends BaseAPIDataStore {
	protected $_username;
	protected $_password;
	
	protected $_client;
	
	public function __construct($config_values) {
		$schema = new GDocsSchema();
		
		parent::__construct($schema, $config_values);
		if(!isset($config_values['username']))
			throw new Exception('Need a username');
		else
			$this->_username = $config_values['username'];
			
		if(!isset($config_values['password'])) 
			throw new Exception('Need a password');
		else
			$this->_password = $config_values['password'];
	}
	
	public function connect() {
		$httpClient = Zend_Gdata_ClientLogin::getHttpClient($this->_username, $this->_password, Zend_Gdata_Docs::AUTH_SERVICE_NAME);
		$this->_client = new Zend_Gdata_Docs($httpClient);
	}
	
	public function disconnect() {
		$this->_client = null;
	}
	
	public function create($obj) {
		// Set the URI to which the file will be uploaded.
		$uri = Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI;

        // Create the media source which describes the file.
        $entry = $this->_objToMediaSource($obj);

		return $this->_parseObj($this->_client->insertDocument($entry, $uri));
	}
	
	public function update($obj) {
		$this->_client->getDocument($obj->id);
	}
	
	public function findAll() {
		$ret = array();
		$feed = $this->_client->getDocumentListFeed('http://docs.google.com/feeds/documents/private/full/-/document');
		foreach($feed->entries as $entry) {
			$ret[] = $this->_parseObj($entry);
		}
		return $ret;
	}
	
	public function query($args, $order, $limit) {
		if(is_string($args)) { //assume its an id and return the doc based on that
			$entry = $this->_client->getDocument($args);
			$obj = $this->_parseObj($entry);
			if($entry->content && $entry->content->src)
				$obj->content = $this->_getContent($entry->content->src);
			return $obj;
		}
	}
	
	protected function _objToMediaEntry($obj) {
		if(is_a($obj, "Zend_Gdata_App_Entry")) {
			return $obj;
		}
	}
	
	protected function _objToMediaSource($obj) {
		if(is_a($obj, "Zend_Gdata_App_Entry") || is_a($obj, "Zend_Gdata_App_MediaSource")) {
			return $obj;
		}
		
		$entry = new GDocsZendStreamMediaSource($obj->content);
		$entry->setSlug($obj->title);
		$entry->setContentType($obj->type);
		return $entry;
	}
	
	protected function _parseObj($entry) {
		$obj = $this->_schema->getObjInstance();
		$obj->id = $this->_parseId((string)$entry->id);
		$obj->title = $entry->title;
		return $obj;
	}
	
	protected function _parseId($id) {
		$ret = str_ireplace("http://docs.google.com/feeds/documents/private/full/document%3A", "", $id);
		$ret = str_ireplace("https://docs.google.com/feeds/documents/private/full/document%3A", "", $ret);
		return $ret;
	}
	
	protected function _getContent($url) {
		$ret = (string)$this->_client->get($url.'&exportFormat=txt')->getBody();
		return $ret;
	}
}