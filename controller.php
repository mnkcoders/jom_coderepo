<?php defined('_JEXEC') or die;
/**
 * @package     CODERS.Repository
 * @subpackage  com_coders_repository
 */
class CodeRepoController extends JControllerLegacy {

    public function dragDrop() {
        
        $output = array();
        
        $uploads = \CODERS\Repository\Repository::upload( 'upload', 'default' );
        
        foreach( $uploads as $id => $resource ){
            if( $id !== 'error' ){
                //import JSON ready
                $output[ $id ] = $resource->meta();
            }
        }
        
        header('Content-Type: application/json');

        json_encode($output);
        
        exit;
    }
    
    public function upload() {
        
        $uploads = \CODERS\Repository\Repository::upload( 'upload', 'default' );

        //var_dump($uploads);
        if( count($uploads)){
            //
        }
        
        return $this->display();
    }
}
