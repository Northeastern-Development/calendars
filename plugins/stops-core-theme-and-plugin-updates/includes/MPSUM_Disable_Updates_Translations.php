<?php
/**
 * Disables all WordPress translation updates.
 *
 * @package WordPress
 *  @since 5.0.0
 */

/**
 * Disable tranlastion updates
 * Credit - From https://wordpress.org/plugins/disable-wordpress-updates/
 */
class MPSUM_Disable_Updates_Translations {
	/**
	 * Constructor
	 */
	public function __construct() {
		/*
		 * Disable All Automatic Updates
		 * 3.7+
		 * 
		 * @author	sLa NGjI's @ slangji.wordpress.com
		 */
		add_filter( 'auto_update_translation', '__return_false' );
		
		/*
		 * Disable Theme Translations
		 * 2.8 to 3.0
		 */
		add_filter( 'transient_update_themes', array( $this, 'remove_translations' ) );

		/*
		 * 3.0
		 */
		add_filter( 'site_transient_update_themes', array( $this, 'remove_translations' ) );
		
		
		/*
		 * Disable Plugin Translations
		 * 2.8 to 3.0
		 */
		add_action( 'transient_update_plugins', array( $this, 'remove_translations' ) );

		/*
		 * 3.0
		 */
		add_filter( 'site_transient_update_plugins', array( $this, 'remove_translations' ) );
		
		
		/*
		 * Disable Core Translations
		 * 2.8 to 3.0
		 */
		add_filter( 'transient_update_core', array( $this, 'remove_translations' ) );

		/*
		 * 3.0
		 */
		add_filter( 'site_transient_update_core', array( $this, 'remove_translations' ) );
		
	} //end constructor
	
	/**
	 * Remove translations
	 *
	 * @param array $transient Transient options
	 * @return array
	 */
	public function remove_translations( $transient ) {
		
		if ( is_object( $transient ) && isset( $transient->translations ) ) {
			$transient->translations = array();	
		}
		return $transient;
	}
	
}