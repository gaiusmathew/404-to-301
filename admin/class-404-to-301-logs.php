<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * WP_List_Table is marked as private by WordPress. So they may change it.
 * Details here - https://codex.wordpress.org/Class_Reference/WP_List_Table
 * So we have copied this class and using independently to avoid future issues. 
 */
if ( ! class_exists('WP_List_Table_404') ) {
    
    // Get current WordPress version.
    global $wp_version;
    // There are changes in list table class since WP 4.4
    // So we will load separate class for 4.4 above and below versions.
    if( $wp_version >= 4.4 ) {
        include_once I4T3_PLUGIN_DIR . '/admin/core/class-wp-list-table-4.4.php';
    } else {
        include_once I4T3_PLUGIN_DIR . '/admin/core/class-wp-list-table-old.php';
    }
}

/**
 * The listing page class for error logs.
 *
 * This class defines all the methods to output the error logs display table using
 * WordPress listing table class.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage ErrorLogsTable
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _404_To_301_Logs extends WP_List_Table_404 {

    /**
     * Initialize the class and set its properties.
     *
     * @since  2.0.0
     * @access public
     * @var    string $table The name of the table of plugin
     * 
     * @return void
     */
    public function __construct() {

        parent::__construct(
            array(
                'singular' => __( '404 Error Log', I4T3_DOMAIN ),
                'plural' => __( '404 Error Logs', I4T3_DOMAIN ),
                'ajax' => false
            )
        );
    }
    
    /**
     * Main function to output the listing table using WP_List_Table class
     *
     * As name says, this function is used to prepare the lsting table based
     * on the custom rules and filters that we have given.
     * This function extends the lsiting table class and uses our custom data
     * to list in the table.
     * Here we set pagination, columns, sorting etc.
     * $this->items - Push our custom log data to the listing table.
     *
     * @global object $wpdb WP DB object
     * @since  2.0.0
     * @access public
     * @uses   hide_errors() To hide if there are SQL query errors.
     * 
     * @return void
     */
    public function prepare_items() {
        
        // Get grouping field id set by user.
        $top = $this->filter_columns( $this->get_request_string( 'group_by_top', '' ) );
        $bottom = $this->filter_columns( $this->get_request_string( 'group_by_bottom', '' ) );
        $group_by = ( ! empty( $top ) ) ? $top : $bottom;
        
        // Set column headers.
        $this->_column_headers = $this->get_column_info();
        
        // Process bulk actions.
        $this->process_bulk_action();
        
        // Get the items per page in listing table.
        $per_page = $this->get_items_per_page( 'logs_per_page', 20 );
        
        // Get current page no.
        $current_page = $this->get_pagenum();
        
        // Get total error logs count.
        $total_items = $this->record_count( $group_by );
        
        // Setting pagination arguments.
        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page' => $per_page
            )
        );
        
        // Process everything and get the listing data.
        $this->items = $this->log_data( $per_page, $current_page, $group_by );
    }
    
    /**
     * Error log data to be displayed.
     *
     * Getting the error log data from the database and converts it to
     * the required structure.
     *
     * @param int    $per_page    Items per page.
     * @param int    $page_number Page number of the list.
     * @param string $group       Group by field name.
     *
     * @since  2.0.0
     * @access public
     * @global object        $wpdb WP DB object
     * @uses   apply_filters
     * 
     * @return array $error_data Array of error log data.
     */
    private function log_data( $per_page = 20, $page_number = 1, $group_by = '' ) {

        global $wpdb;
        
        // Get query offset based on page number.
        $offset = ( $page_number - 1 ) * $per_page;
        
        $count = empty( $group_by ) ? '' : ',count(id) as count ';
        
        // Get group by query if set.
        if ( ! empty( $group_by ) ) {
            $group_by = ' GROUP BY ' . $group_by;
        }

        // Get order by parameter from url.
        // Default order to date.
        $orderby = $this->filter_columns( $this->get_request_string( 'orderby', 'date' ), 'date' );

        // Get order by parameter from url.
        // Default order is ASC.
        $order = ( 'desc' == $this->get_request_string( 'order', 'ASC' ) ) ? 'DESC' : 'ASC';

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * " . $count . " FROM " . I4T3_TABLE . $group_by . " ORDER BY $orderby $order LIMIT %d OFFSET %d", array( $per_page, $offset )
            ),
            'ARRAY_A'
        );

        return $result;
    }
    
    /**
     * Add extra action dropdown for grouping the error logs.
     * 
     * @param string $which Top or Bottom.
     * 
     * @since  3.0.0
     * @access protected
     * 
     * @return void
     */
    protected function extra_tablenav( $which ) {
        
        $name = ( $which == 'top' ) ? 'group_by_top' : 'group_by_bottom';

        echo '<div class="alignleft actions bulkactions">';
        echo '<select name="' . $name . '" class="404_group_by">';
        echo '<option value="">' . __( 'Group by', I4T3_DOMAIN ) . '</option>';
        echo '<option value="url">' . __( '404 Path', I4T3_DOMAIN ) . '</option>';
        echo '<option value="ref">' . __( 'From', I4T3_DOMAIN ) . '</option>';
        echo '<option value="ip">' . __( 'IP Address', I4T3_DOMAIN ) . '</option>';
        echo '</select>';
        
        // Group by action button.
        submit_button( __( 'Apply', I4T3_DOMAIN ), 'button 404-group-submit', '', false );
        
        echo '</div>';
    }

    /**
     * Filter the sorting parameters.
     *
     * This is used to filter the sorting parameters in order
     * to prevent SQL injection atacks. We will accept only our
     * required values. Else we will assign a default value.
     * 
     * @param string $column Value from url.
     *
     * @since  2.0.3
     * @access public
     * 
     * @return string $filtered_column.
     */
    private function filter_columns( $column, $default = '' ) {

        $allowed_columns = array( 'date', 'url', 'ref', 'ip' );

        if ( in_array( $column, $allowed_columns ) ) {
            return esc_sql( $column );
        }
        
        return $default;
    }

    /**
     * Delete a single record from table.
     *
     * This function is used to clear the selected errors
     * from error logs table.
     * This function is used to clear the error logs table.
     * This action can not be undone. So we will ask user for
     * confirmation with a warning in cloient side.
     * 
     * @param int $id ID of error log.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return void
     */
    private function delete_log($id) {
        
        global $wpdb;

        $wpdb->delete( I4T3_TABLE, array( 'id' => $id ), array( '%d' ) );
    }

    /**
     * Delete all records at once from database.
     *
     * This function is used to clear the error logs table.
     * This action can not be undone. So we will ask user for
     * confirmation with a warning in cloient side.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return void
     */
    private function delete_all_logs() {

        global $wpdb;
        
        // Delete all from logs table.
        $wpdb->query( "DELETE FROM " . I4T3_TABLE . "");
    }

    /**
     * Get the count of total records in table.
     * 
     * @param string $distinct If grouping is clcked.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return mixed
     */
    private function record_count( $distinct = '' ) {

        global $wpdb;

        $sql = "SELECT COUNT(id) FROM " . I4T3_TABLE;
        
        // If group by is set, take count of distinct.
        if ( ! empty( $distinct ) ) {
            $sql = "SELECT COUNT(DISTINCT " . $distinct . ") FROM " . I4T3_TABLE;
        }

        return $wpdb->get_var( $sql );
    }

    /**
     * Empty record text.
     *
     * Custom text to display where there is nothing to display in error
     * log table.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    public function no_items() {
        
        _e( 'Ulta pulta..! Seems like you had no errors to log.', I4T3_DOMAIN );
    }

    /**
     * Default columns in list table.
     *
     * To show columns in error log list table. If there is nothing
     * for switch, printing the whole array.
     * 
     * @param array  $item        Column data
     * @param string $column_name Column name
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array
     */
    public function column_default( $item, $column_name ) {
        
        switch( $column_name ) {
            case 'date':
            case 'url':
            case 'ref':
            case 'ip':
            case 'ua':
            case 'redirect':
                return $item[ $column_name ];
                break;
            default:
                //Show the whole array for troubleshooting purposes.
                return print_r( $item, true );
                break;
        }
    }

    /**
     * To output checkbox for bulk actions.
     *
     * This function is used to add new checkbox for all entries in
     * the listing table. We use this checkbox to perform bulk actions.
     * 
     * @param array $item Column data
     *
     * @since  2.1.0
     * @access public
     * 
     * @return string
     */
    public function column_cb( $item ) {

        return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s"/>', $item['id'] );
    }
    
    /**
     * To output redirect option of the link.
     *
     * This function is used to add custom column for setting
     * custom redirect for a 404 link.
     * 
     * @param array $item Column data
     *
     * @since  2.1.1
     * @access public
     * 
     * @return  string
     */
    public function column_redirect( $item ) {

        $title = ( ! empty( $item['redirect'] ) ) ? $item['redirect'] : __( 'Default', I4T3_DOMAIN );
        
        return '<a href="javascript:void(0)" title="' .  __( 'Customize', I4T3_DOMAIN ) . '" class="i4t3_redirect_thickbox" url_404="' . $item['url'] . '">' . $title . '</a>';
    }

    /**
     * To modify the date column data
     *
     * This function is used to modify the column data for date in listing table.
     * We can change styles, texts etc. using this function.
     *
     * @param array $item Column data
     * 
     * @since  2.0.0
     * @access public
     * 
     * @return string $date_data Date column text data.
     */
    function column_date( $item ) {

        $nonce = wp_create_nonce( 'i4t3_delete_log' );

        $title = apply_filters( 'i4t3_log_list_date_column', date( "j M Y, g:i a", strtotime( $item['date'] ) ) );
        
        $confirm = __( 'Are you sure you want to delete this item? The custom redirect set for this log will also be deleted.', I4T3_DOMAIN );
        
        $actions = array(
            'delete' => sprintf( '<a href="?page=%s&action=%s&log=%s&_wpnonce=%s" onclick="return confirm(\'%s\');">' . __( 'Delete', I4T3_DOMAIN ) . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $nonce, $confirm )
        );

        return $title . $this->row_actions( $actions );
    }

    /**
     * To modify the url column data
     *
     * This function is used to modify the column data for url in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * Apply filter - i4t3_log_list_url_column.
     * 
     * @param array $item Column data
     *
     * @since  2.0.0
     * @access public
     * 
     * @return string $url_data Url column text data.
     */
    public function column_url( $item ) {
        
        // If grouped, show the count of each different errors.
        $count = empty( $item['count'] ) ? "" : " (<strong>" . intval( $item['count'] ) . "</strong>)";

        return apply_filters( 'i4t3_log_list_url_column', $this->get_empty_text( '<p><span class="i4t3-url-p">' . wp_strip_all_tags( $item['url'] ) . '</span>' .  $count . '</p>' ) );
    }

    /**
     * To modify the ref column data
     *
     * This function is used to modify the column data for ref in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * Apply filter - i4t3_log_list_ref_column.
     * 
     * @param array $item Column data
     *
     * @since  2.0.0
     * @access public
     * 
     * @return string $ref_data Ref column text data.
     */
    public function column_ref( $item ) {

        return apply_filters( 'i4t3_log_list_ref_column', $this->get_empty_text( '<a href="' . wp_strip_all_tags( $item['ref'] ) . '" target="_blank">' . wp_strip_all_tags( $item['ref'] ) . '</a>', wp_strip_all_tags( $item['ref'] ) ) );
    }

    /**
     * To modify the user agent column data
     *
     * This function is used to modify the column data for user agent in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * Apply filter - i4t3_log_list_ref_column.
     * 
     * @param array $item Column data.
     *
     * @since  2.0.9
     * @access public
     * 
     * @return string $ua_data Ref column text data.
     */
    public function column_ua( $item ) {

        return apply_filters( 'i4t3_log_list_ua_column', $this->get_empty_text( wp_strip_all_tags( $item['ua'] ) ) );
    }

    /**
     * To modify the ip column data
     *
     * This function is used to modify the column data for ip in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * Apply filter - i4t3_log_list_ref_column.
     * 
     * @param array $item Column data.
     *
     * @since  2.0.9
     * @access public
     * 
     * @return string $ip Ref column text data.
     */
    public function column_ip( $item ) {

        return apply_filters( 'i4t3_log_list_ip_column', $this->get_empty_text( wp_strip_all_tags( $item['ip'] ) ) );
    }

    /**
     * Column titles
     *
     * Custom column titles to be displayed in listing table.
     * You can change this to anything.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $columns Array of cloumn titles.
     */
    public function get_columns() {

        $columns = array(
            'cb' => '<input type="checkbox" style="width: 5%;" />',
            'date' => __( 'Date', I4T3_DOMAIN ),
            'url' => __( '404 Path', I4T3_DOMAIN ),
            'ref' => __( 'From', I4T3_DOMAIN ),
            'ip' => __( 'IP Address', I4T3_DOMAIN ),
            'ua' => __('User Agent', I4T3_DOMAIN ),
            'redirect' => __( 'Redirect', I4T3_DOMAIN ),
        );

        return $columns;
    }

    /**
     * Make columns sortable
     *
     * To make our custom columns in list table sortable. We have included
     * 4 columns except 'User Agent' column here.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $sortable_columns Array of columns to enable sorting.
     */
    public function get_sortable_columns() {

        $sortable_columns = array(
            'date' => array( 'date', true ),
            'url' => array( 'url', false ),
            'ref' => array( 'ref', false ),
            'ip' => array( 'ip', false )
        );

        return $sortable_columns;
    }

    /**
     * Bulk actions drop down.
     *
     * Options to be added to the bulk actions drop down for users
     * to select. We have added 'Delete' actions.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $actions Options to be added to the action select box.
     */
    public function get_bulk_actions() {

        $actions = array(
            'bulk-delete' => __( 'Delete Selected', I4T3_DOMAIN ),
            'bulk-all-delete' => __( 'Delete All', I4T3_DOMAIN )
        );

        return $actions;
    }

    /**
     * To perform bulk actions.
     *
     * This function is used to check if bulk action is set in post.
     * If set it will call the required functions to perform the task.
     *
     * @since  2.1.0
     * @access public
     * @uses   wp_verify_nonce To verify if the request is from WordPress.
     * 
     * @return void
     */
    public function process_bulk_action() {

        // Detect when a bulk action is being triggered.
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'i4t3_delete_log' ) ) {
                wp_die( __( 'Cheating Huh?', I4T3_DOMAIN ) );
            }
            
            $this->delete_log( absint( $_GET['log'] ) );
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }

        $this->process_bulk_actions();
    }

    /**
     * To perform bulk actions.
     *
     * This function is used to perform the bulk actions.
     * We will verify the security tokens before proceeding
     * with the actions.
     *
     * @since  2.1.0
     * @access public
     * @uses   wp_verify_nonce  To verify if the request is from WordPress.
     * 
     * @return void
     */
    private function process_bulk_actions() {

        $post = $_POST;
        
        if ( isset( $post['_wpnonce'] ) ) {

            $nonce = '';
            $action = '';
            // Get nonce security token.
            if ( ! empty( $post['_wpnonce'] ) ) {
                $nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];
            }
            // Security check using nonce.
            if ( ! wp_verify_nonce( $nonce, $action ) ) {
                wp_die( __( 'Cheating Huh?', I4T3_DOMAIN ) );
            }
            
            $this->delete_actions( $post );
        }
    }
    
    /**
     * Select delete action in bulk actions.
     * 
     * @param array $post POST data from form.
     * 
     * @since  3.0.0
     * @access private
     * 
     * @return void
     */
    private function delete_actions( $post ) {
        
        // Delete selected error logs.
        if ( ( isset( $post['action']) && $post['action'] == 'bulk-delete' )
            || ( isset( $post['action2']) && $post['action2'] == 'bulk-delete' )
        ) {
            $delete_ids = esc_sql( $post['bulk-delete'] );
            // Loop over the array of record IDs and delete them.
            foreach( $delete_ids as $id ) {
                $this->delete_log( $id );
            }
            // Refresh te error logs page.
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
        
        // Delete all error logs.
        if ( ( isset( $post['action'] ) && $post['action'] == 'bulk-all-delete' )
            || ( isset( $post['action2']) && $post['action2'] == 'bulk-all-delete' )
        ) {
            $this->delete_all_logs();
            // Refresh te error logs page.
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }

    /**
     * To make clear error text if value is N/A.
     *
     * This function is used to show the N/A text in red colour if the field value
     * is not available.
     * 
     * @param string $data Column data.
     * @param string $na   Should we show N/A.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return string
     */
    public function get_empty_text( $data, $na = '' ) {

        if ( empty( $data ) || 'N/A' == $na ) {
            return '<p class="i4t3-url-p">' . __( 'N/A', I4T3_DOMAIN ) . '</p>';
        }

        return $data;
    }
    
    /**
     * Retrive query paramater from url or post.
     * 
     * We need to get the url paramater and post data
     * multiple times. Using this function we can validate
     * the request and get the proper trimmed out put.
     * Use this function to get parameter from urls or post.
     * 
     * @param string $key Key to get.
     * 
     * @since 3.0.0
     * @access private
     * 
     * @return mixed array/string. 
     */
    private function get_request_string( $key = '', $default = '' ) {
        
        // Proceed only if required key is given.
        if ( empty( $key ) || ! is_string( $key ) ) {
            return false;
        }
        
        if ( ! isset( $_REQUEST[ $key ] ) ) {
            return $default;
        }
        
        // Trim the output.
        if ( is_string( $_REQUEST[ $key ] ) ) {
            return trim( $_REQUEST[ $key ] );
        } elseif ( is_array( $_REQUEST[ $key ] ) ) {
            return array_map( 'trim', $_REQUEST[ $key ] );
        }
        
        return $default;
    }
}
