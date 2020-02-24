<?php defined('_JEXEC') or die;
/**
 * 
 */
final class CodersFramework{
    /**
     * @var CodersFramework
     */
    private static $_INSTANCE = NULL;
    
    private $_classes = [
        'renderer',
        'dictionary',
        'repository',
        'resource',
    ];
    
    private function __construct() {
        
        $this->preload();
    }
    /**
     * @return \CodersFramework
     */
    private final function preload(){
        
        foreach( $this->_classes as $class ){
            $path = sprintf( '%s/classes/%s.class.php' , __DIR__,$class );
            //print( $path . ':'.file_exists(  $path) );
            require_once( $path );
        }
        
        return $this;
    }
    /**
     * 
     * @return \CodersFramework
     */
    public static final function instance(){
        if(is_null( self::$_INSTANCE)){
            self::$_INSTANCE = new CodersFramework();
        }
        
        return self::$_INSTANCE;
    }
}




