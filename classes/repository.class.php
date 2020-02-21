<?php namespace CODERS\CodeRepo;
defined('_JEXEC') or die;
/**
 * 
 */
final class Repository{
    
    const ENDPOINT = 'repository';
    /**
     * @var \CodersRepo
     */
    private static $_INSTANCE = NULL; 
    /**
     * 
     */
    private final function __construct() {
        
    }
    /**
     * @param string $collection
     * @return string
     */
    public static final function base( $collection = '' ){
        
        $base = sprintf('%s%s',
                preg_replace('/\\\\/', '/', ABSPATH),
                get_option( 'coders_repo_base' , self::ENDPOINT ));
        
        if(strlen($collection)){
            $base . '/' . $collection;
        }
        
        return $base;
    }
    /**
     * @param string $rid Resource ID
     * @return URL
     */
    public static final function url( $rid ){
        
        return \JURI::base();
        
        //return sprintf('%s?template=%s&rid=%s', get_site_url(),self::ENDPOINT,$rid);
    }
    /**
     * @param array $selection
     * @return \CODERS\CodeRepo\Resource[]
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
    public static final function readCollection( ){
        
        $output = array();
        $root = self::base( );
        //var_dump(self::base());
        //var_dump(scandir(self::base()));
        foreach(scandir($root) as $item ){
            if( is_dir($root . '/' . $item ) && $item !== '.' && $item !== '..' ){
                $output[] = $item;
            }
        }
        
        return $output;
    }
    /**
     * @param string $collection
     * @return \CODERS\CodeRepo\Resource[]
     */
    public static final function collection( $collection ){
        return self::query( array( 'storage' => $collection ) );
    }
    /**
     * @param string $collection
     * @return boolean
     */
    public static final function checkCollection( $collection ){
        
        $path = Repository::base($collection);
        
        return ( filetype( $path ) === 'dir' ) ? TRUE : mkdir($path);
    }
    /**
     * @return array
     */
    public static final function collections(){
        
        return [];
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
     * @return \CODERS\CodeRepo\Resource|Boolean
     */
    public static final function import( $public_id ){
        
        return \CODERS\CodeRepo\Resource::import($public_id);
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
                base64_encode( $file->read( ) ) :
                FALSE;
    }
    /**
     * @param String $file_id
     * @return String
     */
    public final function attach( $file_id ){
        
        $file = self::import( $file_id );
        
        if($file !== FALSE ){
        
            return base64_encode( $file->load( ) );
        }
        
        return '';
    }
    /**
     * @return \CodersRepo
     */
    static final function instance(){
        
        if(is_null(self::$_INSTANCE)){
            self::$_INSTANCE = new \CodersRepo ();
        }
        
        return self::$_INSTANCE;
    }
}

//CodersRepo::instance();
