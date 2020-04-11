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
        'name'=>'',
        'type'=>'',
        'size' => 0,
        'collection'=>'default',
        'status' => self::STATUS_CREATED,
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
        switch( TRUE ){
            case (preg_match('/^is_/', strtolower( $name))):
                $has = sprintf('is%s',substr($name, 3));
                return method_exists($this, $has) ? $this->$has() : FALSE;
            case !array_key_exists( $name , $this->_meta):
                $get = sprintf('get%s', preg_replace('/_/', '', $name) );
                return method_exists($this, $get) ? $this->$get() : '';
            default:
                return array_key_exists( $name , $this->_meta ) ?
                    strval( $this->_meta[$name] ) : '';
        }
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
     * @return boolean
     */
    public final function isAttachment(){

        //any non attachment types should fall here
        return !in_array($this->_meta['type'], array(
            'image/png',
            'image/bmp',
            'image/jpg',
            'image/jpeg',
            'image/gif',
            //case 'text/plain',
            //case 'text/html',
            //case 'text/json',
            //'application/json',
        ));
    }
    /**
     * @return boolean
     */
    public final function isImage(){
        //any non attachment types should fall here
        return in_array($this->_meta['type'], array(
            'image/png',
            'image/bmp',
            'image/jpg',
            'image/jpeg',
            'image/gif',
        ));
    }
    /**
     * @return string
     */
    public final function getClassType(){
        
        $class = array(
            $this->isImage() ? 'icon-image' : 'icon-attachment',
            preg_replace('/\//', '-', $this->_meta['type'] )
        );
        
        return implode(' ', $class);
    }
    /**
     * @return string
     */
    public final function getPath(){

        $root = Repository::instance()->root( $this->collection );

        return sprintf('%s/%s', $root , $this->ID );
    }
    /**
     * @return String|URL
     */
    public final function getUrl(){
        return Repository::url($this->_meta['ID']);
    }
    /**
     * @return boolean
     */
    public final function validate(){
        return strlen( $this->_meta['ID'] )  > 0;
    }
    /**
     * @return boolean
     */
    public final function exists(){
        return file_exists($this->getPath());
    }
    /**
     * @param string|stream $buffer
     * @return int|boolean
     */
    public final function write( $buffer ){
        
        return file_put_contents($this->getPath(), $buffer);
    }
    /**
     * @return string|Boolean
     */
    public final function read(){
        
        return $this->exists() ? file_get_contents($this->getPath()) : FALSE;
    }
    /**
     * @return array
     */
    public final function meta(){

        return $this->_meta;
    }
    /**
     * @return String|JSON
     */
    public final function json(){
        
        return json_encode( $this->meta() );
    }
    /**
     * @global \wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function register( \CODERS\Repository\Resource $R ){
        
        $db = \JFactory::getDbo();
        $fields = array_keys( $R->_meta );
        $values = array();
        
        foreach( $R->_meta as $val ){
            switch( TRUE ){
                case is_array($val):
                    $values[] = $db->quote( implode('|', $val) );
                    break;
                case is_string($val):
                    $values[] = $db->quote($val);
                    break;
                default:
                    $values[] = $val;
                    break;
            }
        }
        
        $insert = $db->getQuery( true );
        $insert->insert($db->quoteName('#__coder_repo'))
                ->columns($db->quoteName($fields))
                ->values(implode(',', $values));
        $db->setQuery($insert);
        $db->execute();
        
        return TRUE;
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
     * @param array $data
     * @return \CODERS\Repository\Resource | FALSE
     */
    public static final function import( array $data ){
        
        $R = new Resource( $data );

        return $R->validate() ? $R : FALSE;
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
            
            $R->_meta['status'] = self::STATUS_SAVED;
            
            return self::register($R) ? $R : FALSE;
        }
        catch (\Exception $ex) {
            print( $ex->getMessage() );
        }
        return FALSE;
    }
}


