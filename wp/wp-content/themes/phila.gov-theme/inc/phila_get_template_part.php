<?php
/*
 * Smk Theme View

 * -------------------------------------------------------------------------------------
 * @Author: Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright: (c) 2014 Smartik. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 * @Date:   2014-06-20 03:40:47
 * @Last Modified by:   Smartik
 * @Last Modified time: 2014-06-23 22:46:40
 *
 */

################################################################################

/**
 * Theme View
 *
 * Include a file and(optionally) pass arguments to it.
 *
 * @param string $file The file path, relative to theme root
 * @param array $args The arguments to pass to this file. Optional.
 * Default empty array.
 *
 * @return object Use render() method to display the content.
 */

    class phila_ThemeView{
        private $args;
        private $file;

        public function __get($name) {
            return $this->args[$name];
        }

        public function __construct($file, $args = array()) {
            $this->file = $file.'.php';
            $this->args = $args;
        }

        public function __isset($name){
            return isset( $this->args[$name] );
        }

        public function render() {
            if( locate_template($this->file) ){
                include( locate_template($this->file) );//Theme Check free. Child themes support.
            }
        }
    }


################################################################################

/**
 * phila Get Template Part
 *
 * An alternative to the native WP function `get_template_part`
 *
 * @see PHP class Phila_ThemeView
 * @param string $file The file path, relative to theme root
 * @param array $args The arguments to pass to this file. Optional.
 * Default empty array.
 *
 * @return string The HTML from $file
 */

    function phila_get_template_part($file, $args = array()){
        $template = new Phila_ThemeView($file, $args);
        $template->render();
    }


