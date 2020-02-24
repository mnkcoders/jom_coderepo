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
    //test
    private $_testData = array(
        'default' => array(
            'A' => array(
                'title' => 'A',
                'type' => 'text',
            )
        )
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
        return array_key_exists($this->collection, $this->_testData) ?
                $this->_testData[ $this->collection ] :
                array();
    }
    /**
     * @return Int
     */
    public function getCount(){
        return array_key_exists($this->collection, $this->_testData) ?
                count( $this->_testData[ $this->collection ] ) :
                0;
    }
    /**
     * @return array
     */
    public function getCollections(){
        return \CODERS\Repository\Repository::instance()->collections();
    }
}

