<?php
/*
Plugin Name: Visual Hook Reference
Plugin URI: http://matty.co.za/
Description: A visual hook reference for displaying where various action hooks are executed.
Version: 1.0.1
Author: Matty, mirkolofio
Author URI: http://matty.co.za/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  Copyright 2012  WooThemes  (email : nothanks@idontwantspam.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    if ( ! defined( 'ABSPATH' ) ) exit;

    if ( is_admin() && ( 'plugins.php' == $pagenow ) ) {} else {
        function create_vhr_instance(){
            global $visual_hook_reference;
            $visual_hook_reference = new Visual_Hook_Reference();
        }
        add_action( 'plugins_loaded', 'create_vhr_instance' );
    }

	class Visual_Hook_Reference {
        private $hooks = array();

        /**
         * Constructor.
         * @since  1.0.0
         * @return  void
         */
        public function __construct () {
            $this->init_hooks();
            $this->init_display();

            add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );
            add_action( 'admin_print_styles', array( &$this, 'enqueue_styles' ) );
        } // End __construct()

        /**
         * Init the hooks for display.
         * @since  1.0.0
         * @return  void
         */
        private function init_hooks () {
           foreach ( array( 'init', 'wp', 'after_setup_theme', 'get_header', 'wp_head', 'loop_start', 'the_post', 'loop_end', 'get_sidebar', 'get_footer', 'wp_footer' ) as $k => $v ) {
                $this->add_hook( $v );
           }

           if ( is_admin() ) { $this->init_admin_hooks(); }

           do_action( 'visual_hook_reference_init_hooks', $this );
        } // End init_hooks()

        /**
         * Load admin-specific hooks, for display.
         * @since  1.0.0
         * @return array Array of hooks, with admin-specific hooks added.
         */
        private function init_admin_hooks () {
            $this->hooks = array_merge( $this->hooks, array( 'admin_init', 'admin_header', 'admin_notices', 'admin_menu', 'admin_footer' ) );
        } // End init_admin_hooks()

        /**
         * Setup the various displays of the hooks.
         * @since  1.0.0
         * @return  void
         */
        private function init_display () {
            // Add our visual references.
            foreach ( $this->hooks as $k => $v ) {
                add_action( $v, array( &$this, 'display_action' ) );
            }
        } // End init_display()

        /**
         * Add a hook to be set for display.
         * @since  1.0.0
         * @param string $hook The hook handle to be added.
         * @return  void
         */
        public function add_hook ( $hook ) {
            if ( is_array( $this->hooks ) && ! in_array( $hook, $this->hooks ) ) $this->hooks[] = strip_tags( $hook );
        } // End add_hook()

        /**
         * Update a hook to be set for display.
         * @since  1.0.0
         * @param string $hook The hook handle to be updated.
         * @param string $new_hook The updated hook handle.
         * @return  void
         */
        public function update_hook ( $hook, $new_hook ) {
            if ( is_array( $this->hooks ) && ! in_array( $hook, $this->hooks ) ) {
                foreach ( $this->hooks as $k => $v ) {
                    if ( $hook == $v ) {
                        $this->hooks[$k] = strip_tags( $new_hook );
                        break;
                    }
                }
            }
        } // End update_hook()

        /**
         * Remove a hook to be set for display.
         * @since  1.0.0
         * @param string $hook The hook handle to be removed.
         * @return  void
         */
        public function remove_hook ( $hook ) {
            if ( is_array( $this->hooks ) && ! in_array( $hook, $this->hooks ) ) {
                foreach ( $this->hooks as $k => $v ) {
                    if ( $hook == $v ) {
                        unset( $this->hooks[$k] );
                        break;
                    }
                }
            }
        } // End remove_hook()

        /**
         * Display the visual reference for the filter this is hooked onto.
         * @since  1.0.0
         * @return  void
         */
        public function display_action () {
            $args = func_get_args();
            $accepted_args = func_num_args();

            $html = '<div class="visual-hook-reference-box">' . current_filter() . ' (' . $accepted_args . ')' . "\n";
            if ( is_array( $args ) && ( 1 <= count( $args ) ) && ( '' != $args[0] ) ) {
                $html .= '<pre>' . print_r( $args, true ) . '</pre>' . "\n";
            }
            $html .= '</div><!--/.visual-hook-reference-box-->' . "\n";

            echo $html;
        } // End display_action()

        /**
         * Load styling for the hook reference boxes.
         * @since  1.0.0
         * @return  void
         */
        public function enqueue_styles () {
            echo '<style type="text/css">' . "\n" . 
                    '.visual-hook-reference-box { font-size: 0.9em; border: 1px dashed #CC0033; color: #CC0033; padding: 0.2em 0.3em; margin: 0.2em; word-wrap: break-word; }' . "\n" . 
                    '.visual-hook-reference-box:hover { background: #CC0033; color: #FFFFFF; font-weight: bold; }' . "\n" . 
                 '</style>' . "\n";
        } // End enqueue_styles()
    } // End Class
?>
