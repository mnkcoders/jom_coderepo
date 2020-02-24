<?php namespace CODERS\Repository;

defined('_JEXEC') or die;
/**
 * 
 */
final class Repository{
    
    const DEFAULT_ROOT = 'media/com_coderepo/collections';
    
    /**
     * @var \CodersRepo
     */
    private static $_INSTANCE = NULL;
    /**
     *
     * @var array
     */
    private $_params = array(
        'root' => self::DEFAULT_ROOT,
    );
    /**
     * @var array
     */
    private $_collectionCache = array(
        //
    );
    /**
     * @param string $name
     * @return boolean
     */
    public final function __get( $name ){
        return array_key_exists($name, $this->_params) ?
                $this->_params[ $name ] :
                FALSE;
    }
    /**
     * 
     */
    private final function __construct() {
        
        $settings = \JComponentHelper::getParams('com_coderepo');
        
        foreach( array_keys( $this->_params) as $var ){
            $this->_params[ $var ] = $settings->get($var,self::DEFAULT_ROOT);
        }
    }
    /**
     * @param string $collection
     * @return string
     */
    public final function root( $collection = '' ){
        
        $root = sprintf('%s/%s',
                preg_replace('/\\\\/', '/', JPATH_ROOT ),
                $this->root );
        
        if(strlen($collection)){
            $root . '/' . $collection;
        }
        
        return $root;
    }
    /**
     * @param string $rid Resource ID
     * @return URL
     */
    public static final function url( $rid ){
        
        return sprintf('%s?option=com_coderepo&rid=%s',
                \JURI::root(),
                $rid);
    }
    /**
     * @param array $selection
     * @return \CODERS\Repository\Resource[]
     */
    public static final function query( array $selection = [ ] ){
        
        $db = \JFactory::getDbo();
        
        $query = $db->getQuery( true );
        
        $query->select( '*' )
                ->from($db->quoteName('#__coderepo_repository'))
                ->where($db->quoteName($selection))
                ->order('ordering ASC');

        $db->setQuery($query);

        $output = [];
        
        foreach( $db->loadResultArray() as $resource ){
            $output[ ] = new Resource( $resource );
        }

        return $output;
    }
    /**
     * @return array
     */
    public final function readRoot( ){
        
        $output = array();
        $root = $this->root( );
        //print( $root );
        //var_dump(scandir($root));
        if(file_exists( $root ) && filetype($root) === 'dir' ){
            foreach(scandir($root) as $item ){
                if( is_dir($root . '/' . $item ) && $item !== '.' && $item !== '..' ){
                    $output[] = $item;
                }
            }
        }
        
        return $output;
    }
    /**
     * @param string $collection
     * @return \CODERS\Repository\Resource[]
     */
    public static final function collection( $collection ){
        return self::query( array( 'storage' => $collection ) );
    }
    /**
     * @param string $collection
     * @return boolean
     */
    public static final function checkCollection( $collection ){
        
        $path = Repository::root($collection);
        
        return ( filetype( $path ) === 'dir' ) ? TRUE : mkdir($path);
    }
    /**
     * @return array
     */
    public final function collections(){
       
        if( count( $this->_collectionCache ) === 0 ){
            $this->_collectionCache = self::readRoot();
        }
        
        return $this->_collectionCache;
    }
    /**
     * @global wpdb $wpdb
     * @global string $table_prefix
     * @param string $id
     * @return boolean
     */
    public static final function remove( array $selection = [] ){
        
        if( count( $selection ) === 0 ){
            return FALSE;
        }
        
        $db = \JFactory::getDbo();
        
        $query = $db->getQuery( true );
        
        $query->delete( $db->quoteName('#__coderepo_repository'))
                ->where($db->quoteName($selection));
        
        return $db->setQuery( $query  )->execute();
    }
    /**
     * @global \wpdb $wpdb
     * @global string $prefix
     * @param string $public_id
     * @return \CODERS\Repository\Resource|Boolean
     */
    public static final function import( $public_id ){
        
        return \CODERS\Repository\Resource::import($public_id);
    }
    /**
     * @return string|boolean
     */
    public final function request(){
        $input = \Joomla\CMS\Factory::getApplication()->input;
        $rid = $input->get(  'coderepo_id', '' , 'string' );
        return strlen($rid) ? $rid : FALSE;
    }
    /**
     * @param String $file_id
     * @return \CodersRepo
     */
    public final function download( $file_id ){
        
        $file = self::import( $file_id );
        
        if($file !== FALSE ){
            header('Content-Type:' . $file->type );
            print $file->read();
        }
        else{
            print $file->path();
        }

        return $this;
    }
    /**
     * @param string $file_id
     * @return string
     */
    public final function encode( $file_id ){

        $file = self::import( $file_id );
        
        return ($file !== FALSE ) ?
                root64_encode( $file->read( ) ) :
                FALSE;
    }
    /**
     * @param String $file_id
     * @return String
     */
    public final function attach( $file_id ){
        
        $file = self::import( $file_id );
        
        if($file !== FALSE ){
        
            return root64_encode( $file->load( ) );
        }
        
        return '';
    }
    /**
     * @return \CODERS\Repository\Repository
     */
    static final function instance(){
        
        if(is_null(self::$_INSTANCE)){
            self::$_INSTANCE = new Repository();
        }
        
        return self::$_INSTANCE;
    }
}


