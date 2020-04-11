<?php namespace CODERS\Repository;

defined('_JEXEC') or die;
/**
 * 
 */
final class Repository{
    
    const DEFAULT_ROOT = 'media/com_coderepo/collections';
    const REQUEST_VAR = 'resource_id';
    
    /**
     * @var \CODERS\Repository\Repository
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
     * 
     */
    private final function __construct() {
        
        $settings = \JComponentHelper::getParams('com_coderepo');
        
        foreach( array_keys( $this->_params) as $var ){
            $this->_params[ $var ] = $settings->get($var,self::DEFAULT_ROOT);
        }
    }
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
     * @param string $collection
     * @return string
     */
    public final function root( $collection = '' ){
        
        $root = sprintf( '%s/%s',
                preg_replace('/\\\\/', '/', JPATH_ROOT ),
                $this->root );
        
        if(strlen($collection)){
            
            $root .= '/' . $collection;

        }
        
        return $root;
    }
    /**
     * @param string $rid Resource ID
     * @return URL
     */
    public static final function url( $rid ){
        
        return sprintf('%s?%s=%s', \JURI::root(), self::REQUEST_VAR , $rid);
    }
    /**
     * @param array $selection
     * @return array
     */
    public static final function query( array $selection = [ ] , $order = 'ASC' ){
        
        $db = \JFactory::getDbo();
        $where = array();
        foreach( $selection as $var => $val ){
            switch( TRUE ){
                case is_string($val):
                    $where[] = sprintf("%s='%s'",$db->quoteName( $var ) , $val);
                    break;
                case is_array($val):
                    $where[] = sprintf("%s IN (%s)",$db->quoteName( $var ) , implode(',', $val));
                    break;
                default:
                    $where[] = sprintf("%s=%s",$db->quoteName( $var ) , $val);
                    break;
            }
        }
        
        $query = $db->getQuery( true );
        $query->select( '*' )
                ->from($db->quoteName('#__coder_repo'))
                ->where(implode(' AND ', $where))
                ->order('sorting ' . $order );
        $db->setQuery($query);
        $items = $db->loadAssocList();
        
        return !is_null( $items ) ? $items : array();
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
        
        $output = array();
        
        foreach( self::query( array( 'collection' => $collection ) ) as $resource ){
            $R = Resource::import( $resource );
            if( $R->validate() ){
                $output[ $R->ID ] = $R;
            }
        }
        
        return $output;
    }
    /**
     * @param string $collection
     * @return boolean
     */
    public static final function checkCollection( $collection ){
        
        $path = self::$_INSTANCE->root($collection);
        
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
     * @param string $ID
     * @return \CODERS\Repository\Resource|Boolean
     */
    public static final function load( $ID ){

        $result = self::query(array('ID'=>$ID));
        
        if( count( $result ) ){
            $R = Resource::import( $result[0] );
            if( $R->validate() ){
                return $R;
            }
        }
        
        return FALSE;
    }
    /**
     * @param string $input
     * @param string $collection
     * @return \CODERS\Repository\Resource[]
     * @throws \Exception
     */
    public static final function upload( $input , $collection = 'default' ){
        
        //list all files here
        $output = array();
            
        try{
            
            $destination = self::instance()->root($collection);
            
            $fileMeta = array_key_exists($input, $_FILES) ? $_FILES[ $input ] : array();

            if( count($fileMeta) === 0 ){
                throw new \Exception('UPLOAD_ERROR_INVALID_FILE');
            }
            
            if( strlen($destination) === 0 ){
                throw new \Exception('UPLOAD_ERROR_INVALID_DESTINATION');
            }
            
            $fileBatch = array();
            
            if( is_array( $fileMeta['name'] ) ){
                for( $i = 0 ; $i < count($fileMeta['name']) ; $i++ ){
                    $fileBatch[] = array(
                        'name' => $fileMeta['name'],
                        'tmp_name' => $fileMeta['tmp_name'],
                        'type' => $fileMeta['type'],
                        'size' => $fileMeta['size'],
                        'error' => $fileMeta['error'],
                    );
                }
            }
            else{
                $fileBatch[] = $fileMeta;
            }
            
            foreach( $fileBatch as $fileData ){
                
                switch( $fileData['error'] ){
                    case UPLOAD_ERR_CANT_WRITE:
                        throw new \Exception('UPLOAD_ERROR_READ_ONLY');
                    case UPLOAD_ERR_EXTENSION:
                        throw new \Exception('UPLOAD_ERROR_INVALID_EXTENSION');
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new \Exception('UPLOAD_ERROR_SIZE_OVERFLOW');
                    case UPLOAD_ERR_INI_SIZE:
                        throw new \Exception('UPLOAD_ERROR_CFG_OVERFLOW');
                    case UPLOAD_ERR_NO_FILE:
                        throw new \Exception('UPLOAD_ERROR_NO_FILE');
                    case UPLOAD_ERR_NO_TMP_DIR:
                        throw new \Exception('UPLOAD_ERROR_INVALID_TMP_DIR');
                    case UPLOAD_ERR_PARTIAL:
                        throw new \Exception('UPLOAD_ERROR_INCOMPLETE');
                    case UPLOAD_ERR_OK:
                        break;
                }

                $buffer = file_get_contents( $fileData['tmp_name'] );

                unlink( $fileData['tmp_name'] );

                if( $buffer !== FALSE ){

                    $resource = Resource::create( $fileData , $buffer );
                    
                    if( $resource !== FALSE ){
                        
                        $output[ $resource->ID ] = $resource;
                    }
                    else{
                        throw new \Exception(sprintf('Error on create %s',$fileData['name']));
                    }
                }
            }
        }
        catch (\Exception $ex) {
            $output['error'] = $ex->getMessage();
        }
        
        return $output;
    }
    /**
     * @deprecated since version 0.0.1 Use load instead
     * @param string $ID
     * @return \CODERS\Repository\Resource|Boolean
     */
    public static final function import( $ID ){

        return self::load($ID);
    }
    /**
     * @return string|boolean
     */
    public static final function request(){
        $input = \Joomla\CMS\Factory::getApplication()->input;
        $rid = $input->get(  self::REQUEST_VAR, '' , 'string' );
        return strlen($rid) ? $rid : FALSE;
    }
    /**
     * @param String $file_id
     * @return \CodersRepo
     */
    public static final function download( $file_id ){

        $resource = self::import( $file_id );

        if($resource !== FALSE ){

            $buffer = $resource->read();
            header('Content-Type:' . $resource->type );
            header( sprintf('Content-Disposition: %s; filename="%s"',
                    $resource->isAttachment() ? 'attachment' : 'inline',
                    $resource->name ));
            header("Content-Length: " . $resource->size );
            print $buffer;
        }
        else{
            print 'INVALID_RESOURCE';
        }
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


