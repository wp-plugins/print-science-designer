<?php class WC_Settings_Tab_Demo {

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_demo', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_demo', __CLASS__ . '::update_settings' );
    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_demo'] = __( 'Print Science Settings', 'woocommerce-settings-tab-demo' );
        return $settings_tabs;
    }


    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }


    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Order Mail Setting', 'woocommerce-settings-tab-demo' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_demo_section_title'
            ),
            'title' => array(
                'name' => __( 'PDF Link', 'woocommerce-settings-tab-demo' ),
				"std" => "1",
				"type" => "radio",
				"options" => array(
					'1' => 'Show',
					'0' => 'Hide'),
                'id'   => 'wc_settings_tab_demo_title',
				'desc' => __( 'PDF link show in order email or not', 'woocommerce-settings-tab-demo' )
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_demo_section_end'
            )
        );

        return apply_filters( 'wc_settings_tab_demo_settings', $settings );
    }

}

WC_Settings_Tab_Demo::init(); ?>