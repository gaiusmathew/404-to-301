<div class="wrap">
    <form method="post" action="options.php">
        <p>
            <?php settings_fields('i4t3_gnrl_options'); ?>
            <?php $options = get_option('i4t3_gnrl_options'); ?>
            <?php
            // To show/hide options
            $cp_style = 'style="display: none;"';
            $cl_style = 'style="display: none;"';
            switch ($options['redirect_to']) {
                case 'page':
                    $cp_style = '';
                    break;

                case 'link':
                    $cl_style = '';
                    break;

                default:
                    break;
            }
            if( get_option( 'i4t3_agreement', 2 ) == 2 ) {
                include_once '404-to-301-admin-agreement-tab.php';
            }
        ?>
        <table class="form-table">
            <tbody>

                <tr>
                    <th><?php _e('Redirect type', I4T3_DOMAIN); ?></th>
                    <td>
                        <select name='i4t3_gnrl_options[redirect_type]'>
                            <option value='301' <?php selected($options['redirect_type'], '301'); ?>>301 <?php _e('Redirect (SEO)', I4T3_DOMAIN); ?></option>
                            <option value='302' <?php selected($options['redirect_type'], '302'); ?>>302 <?php _e('Redirect', I4T3_DOMAIN); ?></option>
                            <option value='307' <?php selected($options['redirect_type'], '307'); ?>>307 <?php _e('Redirect', I4T3_DOMAIN); ?></option>
                        </select>
                        <p class="description"><a target="_blank" href="https://moz.com/learn/seo/redirection"><strong><?php _e('Learn more', I4T3_DOMAIN); ?></strong></a> <?php _e('about these redirect types', I4T3_DOMAIN); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Redirect to', I4T3_DOMAIN); ?></th>
                    <td>
                        <select name='i4t3_gnrl_options[redirect_to]' id='i4t3_redirect_to'>
                            <option value='page' <?php selected($options['redirect_to'], 'page'); ?>><?php _e('Existing Page', I4T3_DOMAIN); ?></option>
                            <option value='link' <?php selected($options['redirect_to'], 'link'); ?>><?php _e('Custom URL', I4T3_DOMAIN); ?></option>
                            <option value='none' <?php selected($options['redirect_to'], 'none'); ?>><?php _e('No Redirect', I4T3_DOMAIN); ?></option>
                        </select>
                        <p class="description"><strong><?php _e('Existing Page', I4T3_DOMAIN); ?>:</strong> <?php _e('Select any WordPress page as a 404 page', I4T3_DOMAIN); ?>.</p>
                        <p class="description"><strong><?php _e('Custom URL', I4T3_DOMAIN); ?>:</strong> <?php _e('Redirect 404 requests to a specific URL', I4T3_DOMAIN); ?>.</p>
                        <p class="description"><strong><?php _e('No Redirect', I4T3_DOMAIN); ?>:</strong> <?php _e('To disable redirect', I4T3_DOMAIN); ?>.</p>
                        <p class="description i4t3-green"><strong><?php _e('You can override this by setting individual custom redirects from error logs list.', I4T3_DOMAIN); ?></strong></p>
                    </td>
                </tr>
                <tr id="custom_page" <?php echo $cp_style; ?>>
                    <th><?php _e('Select the page', I4T3_DOMAIN); ?></th>
                    <td>
                        <select name='i4t3_gnrl_options[redirect_page]'>
                            <?php foreach ($pages as $page) { ?>
                                <option value='<?php echo $page->ID; ?>' <?php selected($options['redirect_page'], $page->ID); ?>><?php echo $page->post_title; ?></option>
                            <?php } ?>
                        </select>
                        <p class="description"><?php _e('The default 404 page will be replaced by the page you choose in this list', I4T3_DOMAIN); ?>.</p>
                    </td>
                </tr>
                <tr id="custom_url"<?php echo $cl_style; ?>>
                    <th><?php _e('Custom URL', I4T3_DOMAIN); ?></th>
                    <td>
                        <input type="text" placeholder="<?php echo home_url(); ?>" name="i4t3_gnrl_options[redirect_link]" value="<?php echo $options['redirect_link']; ?>">
                        <p class="description"><?php _e('Enter any url', I4T3_DOMAIN); ?> (<?php _e('including', I4T3_DOMAIN); ?> http://)</p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Log 404 Errors', I4T3_DOMAIN); ?></th>
                    <td>
                        <select name='i4t3_gnrl_options[redirect_log]'>
                            <option value='1' <?php selected($options['redirect_log'], 1); ?>><?php _e('Enable Error Logs', I4T3_DOMAIN); ?></option>
                            <option value='0' <?php selected($options['redirect_log'], 0); ?>><?php _e('Disable Error Logs', I4T3_DOMAIN); ?></option>
                        </select>
                        <p class="description"><?php _e('Enable/Disable Logging', I4T3_DOMAIN); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Email notifications', I4T3_DOMAIN); ?></th>
                    <td>
                        <?php $email_notify = 0;
                        if (isset($options['email_notify'])) {
                            $email_notify = $options['email_notify'];
                        } ?>
                        <input type="checkbox" name="i4t3_gnrl_options[email_notify]" value="1" <?php checked($email_notify, 1); ?> />
                        <p class="description"><?php _e('If you check this, an email will be sent on every 404 log on the admin email account', I4T3_DOMAIN); ?>.</p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Email address', I4T3_DOMAIN); ?></th>
                    <td>
                    <?php $notify_address = ( isset($options['email_notify_address']) ) ? $options['email_notify_address'] : get_option('admin_email'); ?>
                        <input type="text" placeholder="<?php echo get_option('admin_email'); ?>" name="i4t3_gnrl_options[email_notify_address]" value="<?php echo $notify_address; ?>">
                        <p class="description"><?php _e('Change the recipient email address for error log notifications', I4T3_DOMAIN); ?>.</p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Exclude paths', I4T3_DOMAIN); ?></th>
                    <td>
                        <textarea rows="5" cols="50" placeholder="wp-content/plugins/abc-plugin/css/" name="i4t3_gnrl_options[exclude_paths]"><?php echo $options['exclude_paths']; ?></textarea>
                        <p class="description"><?php _e('If you want to exclude few paths from error logs, enter here. One per line.', I4T3_DOMAIN); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
<?php submit_button(__('Save All Changes', I4T3_DOMAIN)); ?>
        </p>
    </form>
</div>