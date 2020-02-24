<?php
/**
 * @package     CODERS.Repository
 * @subpackage  com_coderepo
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/**
 * @since  0.0.1
 */
//class CodeRepoViewCodeRepo extends JViewLegacy
class CodeRepoViewCodeRepo extends \CODERS\Repository\RendererBase {
    /**
     * @return ARRAY
     */
    protected function list_resources(){
        return $this->get('Resources');
    }
    /**
     * @return array
     */
    protected function list_collections() {
        return $this->get( 'Collections' );
    }
    /**
     * @return HTML|String
     */
    protected function display_sidebar() {
        
        $collections = array();

        foreach( $this->list_collections() as $item ){
            $collections[] = self::__html('a',array(
                'href'=>'#' . $item ,
                'target'=>'_self' ,
                'class'=>''
                ),$item );
        }
        
        return self::__html('li',
                array('class'=>'nav nav-list'),
                $collections );
    }
    /**
     * Display the Hello World view
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * @return  void
     */
    function display($tpl = null) {
        
        $this->getModel()->collection = 'test';

        $this->registerSetting('sidebar',TRUE)
                ->registerScript('https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js')
                ->registerStyle('https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js')
                ->set('title','Collection');

        // Display the view
        parent::display($tpl);
    }
}




