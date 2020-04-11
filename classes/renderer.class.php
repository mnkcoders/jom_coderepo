<?php namespace CODERS\Repository;
/**
 * @package     CODERS.Repository
 * @subpackage  com_coderepo
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 
/**
 * 
 */
abstract class RendererBase extends \JViewLegacy{
    /**
     * @var array
     */
    private $_settings = array(
        //define here all required view settings
    );
    /**
     * @param string $style
     * @return \CODERS\Repository\RendererBase
     */
    protected final function registerStyle( $style ){
        
        \JFactory::getDocument()->addStyleSheet( $style );

        return $this;
    }
    /**
     * @param string $script
     * @return \CODERS\Repository\RendererBase
     */
    protected final function registerScript( $script ){

        \JFactory::getDocument()->addScript( $script );

        return $this;
    }
    /**
     * @param string $setting
     * @param boolean $value
     * @return \CODERS\Repository\RendererBase
     */
    protected final function registerSetting( $setting , $value = FALSE ){
        $this->_settings[ $setting ] = $value;
        return $this;
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        if (preg_match('/^input_/', $name)) {
            return $this->__input(substr($name, 6));
        }
        elseif (preg_match('/^value_/', $name)) {
            return $this->__value(substr($name, 6));
        }
        elseif (preg_match('/^display_/', $name)) {
            return $this->__display(substr($name, 8));
        }
        elseif (preg_match('/^has_/', $name)) {
            $this->has( substr($name, 4) );
        }
        elseif (preg_match('/^list_/', $name)) {
            return $this->__list(substr($name, 5));
        }
        elseif (preg_match('/^get_/', $name)) {
            $get = substr($name, 4);
            return $this->get( $get , $name );
        }
        elseif (preg_match('/^label_/', $name) ) {
            return $this->__label(substr($name, 6));
        }
        return sprintf('<!-- invalid element [%s] -->', $name);
    }
    /**
     * @param string $setting
     * @return boolean
     */
    protected final function has( $setting ){
        return array_key_exists($setting, $this->_settings) ?
                $this->_settings[ $setting ] :
                FALSE;
    }
    /**
     * @param string $tag
     * @param mixed $attributes
     * @param mixed $content
     * @return String|HTML HTML output
     */
    protected static function __html($tag, $attributes = array(), $content = NULL) {
        if (isset($attributes['class'])) {
            if (is_array($attributes['class'])) {
                $attributes['class'] = implode(' ', $attributes['class']);
            }
        }
        $serialized = array();
        foreach ($attributes as $att => $val) {
            $serialized[] = sprintf('%s="%s"', $att, $val);
        }
        if (!is_null($content)) {
            if (is_object($content)) {
                $content = strval($content);
            }
            elseif (is_array($content)) {
                $content = implode(' ', $content);
            }
            return sprintf('<%s %s>%s</%s>', $tag,
                    implode(' ', $serialized), strval($content),
                    $tag);
        }
        return sprintf('<%s %s />', $tag, implode(' ', $attributes));
    }
    /**
     * @param string $name
     * @return string|HTML
     */
    protected function __input($name) {
        $model = $this->getModel();
        if (!is_null($model)) {
            return $model->$name();
        }
        return sprintf('<!-- INPUT %s NOT FOUND -->', $name);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function __value($name) {
        return sprintf('<!-- DATA %s NOT FOUND -->', $name);
    }

    /**
     * @param string $list
     * @return array
     */
    protected function __list($list) {
        $override = 'list_' . $list;
        if (method_exists($this, $override)) {
            return $this->$override();
        }
        elseif (!is_null($model = $this->getModel())) {
            if (method_exists($model, 'listOptions')) {
                return $model->listOptions($list);
            }
        }
        return array();
    }
    /**
     * @param string $name
     * @return string
     */
    protected function __label($name) {
        return $name;
    }
    /**
     * @param string $display
     * @return string
     */
    public function __display($display) {
        $callback = sprintf('display_%s', $display);
        print method_exists($this, $callback) ?
                        $this->$callback() :
                        $this->loadTemplate($display);
    }
}