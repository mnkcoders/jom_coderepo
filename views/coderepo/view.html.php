<?php
/**
 * @package     CODERS.Repository
 * @subpackage  com_coderepo
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register( 'RendererBase', __DIR__ . '/../../classes/renderer.class.php');
/**
 * @since  0.0.1
 */
//class CodeRepoViewCodeRepo extends \CODERS\Repository\Admin\RendererBase
class CodeRepoViewCodeRepo extends JViewLegacy
{
        public function __get( $name ){
            
            if( preg_match('/^input_/', $name) ){
                return $this->__input(substr($name, 6));
            }
            elseif( preg_match(  '/^list_/' , $name ) ){
                return $this->__list(substr($name, 5));
            }
            elseif( preg_match(  '/^value_/' , $name ) ){
                return $this->__value(substr($name, 6));
            }
            elseif( preg_match(  '/^display_/' , $name ) ){
                return $this->__display(substr($name, 8));
            }
            elseif( preg_match(  '/^label_/' , $name ) ){
                return $this->__label(substr($name, 6));
            }

            return sprintf('<!-- invalid element [%s] -->',$name);
        }
        /**
         * @param string $tag
         * @param mixed $attributes
         * @param mixed $content
         * @return String|HTML HTML output
         */
        protected static function __html( $tag, $attributes = array( ), $content = NULL ){

            if( isset( $attributes['class'])){
                if(is_array($attributes['class'])){
                    $attributes['class'] = implode(' ', $attributes['class']);
                }
            }

            $serialized = array();

            foreach( $attributes as $att => $val ){

                $serialized[] = sprintf('%s="%s"',$att,$val);
            }

            if( !is_null($content) ){

                if(is_object($content)){
                    $content = strval($content);
                }
                elseif(is_array($content)){
                    $content = implode(' ', $content);
                }

                return sprintf('<%s %s>%s</%s>' , $tag ,
                        implode(' ', $serialized) , strval( $content ) ,
                        $tag);
            }

            return sprintf('<%s %s />' , $tag , implode(' ', $attributes ) );
        }
        /**
         * @param string $name
         * @return string|HTML
         */
        protected function __input( $name ){

            $model = $this->getModel();
            
           
            if( !is_null($model) ){
                
                return $model->$name();
            }
            return sprintf('<!-- INPUT %s NOT FOUND -->',$name);
        }
        /**
         * @param string $name
         * @return string
         */
        protected function __value( $name ){

            return sprintf('<!-- DATA %s NOT FOUND -->',$name);
        }
        /**
         * @param string $list
         * @return array
         */
        protected function __list( $list ){
            
            $override = 'list_' . $list;
            
            if(method_exists($this, $override)){
                
                return $this->$override(); 
            }
            elseif( !is_null($model = $this->getModel() )){
                
                if( method_exists($model, 'listOptions') ){
                    return $model->listOptions( $list );
                }
                
            }
            
            return [];
        }
        /**
         * @param string $name
         * @return string
         */
        protected function __label( $name ){

            return $name;
        }
        /**
         * 
         * @param string $display
         * @return string
         */
        public function __display( $display ){

            return $this->loadTemplate( $display );
        }
        /**
         * 
         * @return array
         */
        protected function list_collection(){
            
            return ['A','B','C'];
            
        }
    
    
        /**
         * 
         * @return CodeRepoViewCodeRepo
         */
        private function attachScripts(){
            
            $doc = JFactory::getDocument();
            $doc->addStyleSheet( 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js' );
            $doc->addScript( 'https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js' );
            return $this;
        }
    
	/**
	 * Display the Hello World view
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	function display( $tpl = null )
	{
                $this->attachScripts();
		// Assign data to the view
		$this->title = 'Collection';
		// Display the view
		parent::display($tpl);
	}
}


