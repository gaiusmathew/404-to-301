<div class="wrap">
<?php
if( get_option( 'i4t3_agreement', 2 ) == 2 ) {
    include_once '404-to-301-admin-agreement-tab.php';
}
?>
    <br>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class="hndle"><span><?php _e('About the plugin & developer', I4T3_DOMAIN); ?></span></h3>
                    <div class="inside">
                        <div class="c4p-clearfix">
                            <div class="c4p-left">
                                <img src="<?php echo I4T3_PATH . 'admin/images/foxe.png'; ?>" class="c4p-author-image" />
                            </div>
                            <div class="c4p-left" style="width: 70%">
                                <?php $uname = ( $current_user->user_firstname == '' ) ? $current_user->user_login : $current_user->user_firstname; ?>
                                <p><?php printf(__('Yo %s!', I4T3_DOMAIN), '<strong>' . $uname . '</strong>'); ?> <?php _e('Thank you for using 404 to 301', I4T3_DOMAIN); ?></p>
                                <p>
                                    <?php _e('This plugin is brought to you by', I4T3_DOMAIN); ?> <a href="https://thefoxe.com/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit author website', I4T3_DOMAIN); ?>"><strong>The Foxe</strong></a>, <?php _e('a web store developed and managed by Joel James.', I4T3_DOMAIN); ?>
                                </p>
                                <p>
                                <hr/>
                                </p>
                                <p>
                                    <?php _e('So you installed this plugin and how is it doing? Feel free to', I4T3_DOMAIN); ?> <a href="https://thefoxe.com/contact/" class="i4t3-author-link" target="_blank" title="<?php _e('Contact the developer', I4T3_DOMAIN); ?>"><?php _e('get in touch with me', I4T3_DOMAIN); ?></a> <?php _e('anytime for help. I am always happy to help.', I4T3_DOMAIN); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle"><span><?php _e('Debugging Data', I4T3_DOMAIN); ?></span></h3>
                    <div class="inside">
                        <div class="c4p-clearfix">
                            <div class="c4p-left" style="width: 70%">
                                <?php echo _404_To_301_Admin::get_debug_data(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">

                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-info"></span> <?php _e('Plugin Information', I4T3_DOMAIN); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <label><?php _e('Name', I4T3_DOMAIN); ?> : </label>
                            <span><strong><?php _e('404 to 301', I4T3_DOMAIN); ?></strong></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><?php _e('Version', I4T3_DOMAIN); ?> : v<?php echo I4T3_VERSION; ?></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><?php _e('Author', I4T3_DOMAIN); ?> : <a href="https://thefoxe.com/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit author website', I4T3_DOMAIN); ?>">The Foxe</a></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><a href="https://thefoxe.com/docs/docs/category/404-to-301/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit plugin website', I4T3_DOMAIN); ?>"><strong><?php _e('Plugin documentation', I4T3_DOMAIN); ?></strong></a></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><a href="https://thefoxe.com/products/404-to-301/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit plugin website', I4T3_DOMAIN); ?>"><strong><?php _e('More details about the plugin', I4T3_DOMAIN); ?></strong></a></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><?php _e('Need help?', I4T3_DOMAIN); ?></label>
                            <span><strong><a href="https://thefoxe.com/contact/"><?php _e('Contact support', I4T3_DOMAIN); ?></a></strong></span>
                        </div>
                        <div class="misc-pub-section">
                            <?php if( get_option( 'i4t3_agreement', 0 ) == 1 ) { ?>
                            <a class="button-secondary" href="<?php echo I4T3_HELP_PAGE; ?>&i4t3_agreement=0" id="i4t3-hide-admin-notice"><?php _e('Disable UAN', I4T3_DOMAIN); ?></a>
                            <?php } else { ?>
                            <a class="button-primary" href="<?php echo I4T3_HELP_PAGE; ?>&i4t3_agreement=1" id="i4t3-accept-terms"><?php _e('Enable UAN', I4T3_DOMAIN); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-admin-plugins"></span> <?php _e('Log Manager Addon', I4T3_DOMAIN); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <p><?php _e('Error Log Manager addon is available for 404 to 301 now. Make 404 error management more easy.', I4T3_DOMAIN); ?></p>
                            <p><span class="dashicons dashicons-backup"></span> <?php _e('Instead of email alerts on every error, get Hourly, Daily, Twice a day, Weekly, Twice a week email alerts.', I4T3_DOMAIN); ?></p>
                            <p><span class="dashicons dashicons-trash"></span> <?php _e('Automatically clear old error logs after few days to reduce db load.', I4T3_DOMAIN); ?></p>
                            <p><a class="i4t3-author-link" href="https://thefoxe.com/products/404-to-301-log-manager/" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e('See More Details', I4T3_DOMAIN); ?></a></p>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-smiley"></span> <?php _e('Like the plugin', I4T3_DOMAIN); ?>?</h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-star-filled"></span> <label><strong><a href="https://wordpress.org/support/view/plugin-reviews/404-to-301?filter=5#postform" target="_blank" title="<?php _e('Rate now', I4T3_DOMAIN); ?>"><?php _e('Rate this on WordPress', I4T3_DOMAIN); ?></a></strong></label>
                        </div>
                        <div class="misc-pub-section">
                            <label><span class="dashicons dashicons-heart"></span> <strong><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XUVWY8HUBUXY4" target="_blank" title="<?php _e('Donate now', I4T3_DOMAIN); ?>"><?php _e('Make a small donation', I4T3_DOMAIN); ?></a></strong></label>
                        </div>
                        <div class="misc-pub-section">
                            <label><span class="dashicons dashicons-admin-plugins"></span> <strong><a href="https://github.com/joel-james/404-to-301/" target="_blank" title="<?php _e('Contribute now', I4T3_DOMAIN); ?>"><?php _e('Contribute to the Plugin', I4T3_DOMAIN); ?></a></strong></label>
                        </div>
                        <div class="misc-pub-section">
                            <label><span class="dashicons dashicons-twitter"></span> <strong><a href="https://twitter.com/home?status=I%20am%20using%20404%20to%20301%20plugin%20by%20%40Joel_James%20to%20handle%20all%20404%20errors%20in%20my%20%40WordPress%20site%20-%20it%20is%20awesome!%20%3E%20https://wordpress.org/plugins/404-to-301/" target="_blank" title="<?php _e('Tweet now', I4T3_DOMAIN); ?>"><?php _e('Tweet about the Plugin', I4T3_DOMAIN); ?></a></strong></label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
