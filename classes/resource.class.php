<?php namespace CODERS\Repository;
/**
 * 
 */
final class Resource{
    
    const STATUS_CREATED = 0;
    const STATUS_SAVED = 1;
    const STATUS_REMOVED = 2;
    
    private $_meta = array(
        'ID'=>'',
        //'public_id'=>'',
        'name'=>'',
        'type'=>'',
        'size' => self::STATUS_CREATED,
        'collection'=>'default',
        'status' => 1,
        'date_created'=>NULL,
        'date_updated'=>NULL,
    );
    //private $_ID,$_public_id,$_name,$_type,$_collection,$_date_created,$_date_updated = NULL;
    /**
     * @param array $meta
     * @param string $buffer
     */
    private final function __construct( array $meta ) {
        
        //fill in resource data
        $this->populate($meta);
    }
    /**
     * @param string $name
     * @return Mixed
     */
    function __get($name) {
        
        return array_key_exists( $name , $this->_meta ) ? strval( $this->_meta[$name] ) : '';
    }
    /**
     * @param string $collection
     * @return string
     */
    private static final function GenerateID( $collection = '' ){
        return md5(uniqid(date('YmdHis').$collection,true));
    }
    /**
     * @param array $input
     * @return \CODERS\Repository\Resource
     */
    private final function populate( array $input ) {
        
        $ts = date('Y-m-d H:i:s');
        
        foreach($input as $var => $val ){
            if(array_key_exists( $var, $this->_meta)){
                $this->_meta[ $var ] = $val;
            }
        }

        if(strlen($this->_meta['date_created']) === 0){
            $this->_meta['date_created'] = $ts;
        }
        if(strlen($this->_meta['date_updated']) === 0){
            $this->_meta['date_updated'] = $ts;
        }
        if(strlen($this->_meta['ID']) === 0){
            $this->_meta['ID'] = self::GenerateID( $this->_meta['collection'] );
        }
        

        return $this;
    }
    /**
     * @return string
     */
    private final function path(){
        return sprintf('%s/%s/%s', \CodersRepo::base(),$this->collection,$this->public_id);
    }
    /**
     * 
     * @return boolean
     */
    public final function exists(){
        return file_exists($this->path());
    }
    /**
     * @param string|stream $buffer
     * @return int|boolean
     */
    private final function write( $buffer ){
        
        return file_put_contents($this->path(), $buffer);
    }
    /**
     * @return string|Boolean
     */
    public final function read(){
        
        return $this->exists() ? file_get_contents($this->path()) : FALSE;
    }
    /**
     * @global \wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function register( \CODERS\Repository\Resource $R ){
        
        global $wpdb,$table_prefix;
        
        $inserted = $wpdb->insert(sprintf('%scoders_repository',$table_prefix),$R->_meta);
        
        return $inserted !== FALSE && $inserted > 0;
    }
    /**
     * @return boolean
     */
    public final function delete(){
        
        return Repository::remove( [ 'ID' => $this->_meta['ID'] ] );
    }
    /**
     * @return array
     */
    public static final function find( $search ){
        
        $filters = is_array($search) ? $search : array('ID'=>$search);
        
        return Repository::query($filters);
    }
    /**
     * 
     * @param array $meta
     * @param string $buffer
     * @return boolean|\CODERS\Repository\Resource
     * @throws \Exception
     */
    public static final function create( array $meta , $buffer = '' ){
        
        try{
            switch( TRUE ){
                case !array_key_exists('name', $meta):
                    throw new \Exception('EMPTY_NAME_ERROR');
                    //break;
                case !array_key_exists('type', $meta):
                    throw new \Exception('EMPTY_FILETYPE_ERROR');
                    //break;
                case !array_key_exists('collection', $meta):
                    $meta['collection'] = 'default';
                    break;
            }
            
            $meta['ID'] = self::GenerateID( $meta['collection'] );
            
            $R = new Resource( $meta , $buffer );
            
            if( strlen($buffer) && !$R->exists( ) ){
                
                if( !Repository::checkCollection( $meta['collection'] ) || !$R->write($buffer) ){
                   
                    return FALSE;
                }
            }
            
            return self::register($R) ? $R : FALSE;
        }
        catch (\Exception $ex) {
            print( $ex->getMessage() );
        }
        return FALSE;
    }
    /**
     * @param string $input
     * @param string $collection
     * @return boolean
     * @throws \Exception
     */
    public static final function upload( $input , $collection = 'default' ){
        
        try{
            $destination = Repository::base($collection);
            
            $fileMeta = array_key_exists($input, $_FILES) ? $_FILES[ $input ] : array();

            if( count($fileMeta) === 0 ){
                throw new \Exception('UPLOAD_ERROR_INVALID_FILE');
            }
            
            if( strlen($destination) === 0 ){
                throw new \Exception('UPLOAD_ERROR_INVALID_DESTINATION');
            }
            
            switch( $fileMeta['error'] ){
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
            
            $buffer = file_get_contents($fileMeta['tmp_name']);
            
            unlink($fileMeta['tmp_name']);
           
            if( $buffer !== FALSE ){

                return self::create($fileMeta , $buffer);
            }
        }
        catch (\Exception $ex) {
            print( $ex->getMessage() );
        }
        
        return FALSE;
    }
    /**
     * @param string $public_id
     * @return \CodersRepoSource
     */
    public static final function import( $public_id ){
        
        $result = self::query(array('public_id'=>$public_id));

        return ( count($result)) ? new Resource( $result[0] ) : FALSE;
    }
}


