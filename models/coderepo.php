<?php defined('_JEXEC') or die;
/**
 * 
 */
class CodeRepoModelCodeRepo extends \Joomla\CMS\MVC\Model\BaseDatabaseModel
{
    //selected collection
    private $_settings = array(
        'collection' => 'default'
    );
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if( array_key_exists($name, $this->_settings) ){
            $this->_settings[ $name ] = $value;
        }
    }
    /**
     * @param string $name
     * @return String
     */
    public function __get($name) {
        return array_key_exists($name, $this->_settings) ? $this->_settings[ $name ] : '';
    }
    /**
     * @return array
     */
    public function getResources(){

        $list = \CODERS\Repository\Repository::collection($this->collection);
                
        return $list;
    }
    /**
     * @return int
     */
    public function getUploadSize(){
        return 256*256*10;
    }
    /**
     * @return string
     */
    public function getCollection(){
        return $this->_settings['collection'];
    }
    /**
     * @return Int
     */
    public function getCount(){
        
        return count( $this->getResources());
    }
    /**
     * @return array
     */
    public function getCollections(){
        return \CODERS\Repository\Repository::instance()->collections();
    }
}

