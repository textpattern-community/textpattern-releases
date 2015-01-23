<html><head></head><body><pre>&lt;?php
# install en-gb without XML-RPC requirements
$en_gb_lang = array(
	'admin' =&gt; array(
		'add_new_author' =&gt; 'Add new author',
		'and_mailed_to' =&gt; 'and mailed to',
		'a_message_will_be_sent_with_login' =&gt; 'A message will be sent with login information',
		'a_new_password_will_be_mailed' =&gt; 'A new password will be e-mailed',
		'change_email_address' =&gt; 'Change your email address',
		'change_password' =&gt; 'Change your password',
		'copy_editor' =&gt; 'Copy Editor',
		'could_not_mail' =&gt; 'Could not mail',
		'could_not_update_author' =&gt; 'Could not update author',
		'designer' =&gt; 'Designer',
		'error_adding_new_author' =&gt; 'Error adding new author',
		'freelancer' =&gt; 'Freelancer',
		'greeting' =&gt; 'Dear',
		'log_in_at' =&gt; 'Login at',
		'mail_it' =&gt; 'Mail it to me',
		'managing_editor' =&gt; 'Managing Editor',
		'new_email' =&gt; 'New email',
		'new_password' =&gt; 'New password',
		'password_changed' =&gt; 'Password changed',
		'password_sent_to' =&gt; 'Password sent to',
		'privileges' =&gt; 'Privileges',
		'publisher' =&gt; 'Publisher',
		'real_name' =&gt; 'Real Name',
		'reset_author_password' =&gt; 'Reset author password',
		'site_administration' =&gt; 'Site Administration',
		'staff_writer' =&gt; 'Staff writer',
		'writer' =&gt; 'Writer',
		'your_login_info' =&gt; 'Your login info',
		'your_login_is' =&gt; 'Your login is',
		'your_new_password' =&gt; 'Your new password',
		'your_password_is' =&gt; 'Your password is',
		'you_have_been_registered' =&gt; 'You have been registered as a contributor to the site',
	),'article' =&gt; array(
		'advanced_options' =&gt; 'Advanced Options',
		'article_deleted' =&gt; 'Article deleted!',
		'article_image' =&gt; 'Article image',
		'article_posted' =&gt; 'Article posted',
		'article_saved' =&gt; 'Article saved',
		'article_saved_draft' =&gt; 'Article saved as draft',
		'article_saved_hidden' =&gt; 'Article saved as hidden',
		'article_saved_pending' =&gt; 'Article saved as pending',
		'or_publish_at' =&gt; 'or publish at',
		'override_default_form' =&gt; 'Override form',
		'pending' =&gt; 'Pending',
		'reset_time' =&gt; 'Reset time to now',
		'set_to_now' =&gt; 'Set timestamp to now',
		'url_title' =&gt; 'URL-only title',
		'url_title_is_blank' =&gt; '(URL-only title was left blank)',
		'url_title_is_multiple' =&gt; '(The same URL-only title is used by {count} different articles)',
	),'category' =&gt; array(
		'article_category' =&gt; 'Article category',
		'article_category_name' =&gt; 'Article category name',
		'article_category_title' =&gt; 'Article category title',
		'article_head' =&gt; 'Article Categories',
		'file_category_name' =&gt; 'File Category name',
		'file_category_title' =&gt; 'File Category title',
		'file_head' =&gt; 'File Categories',
		'image_category_name' =&gt; 'Image Category name',
		'image_category_title' =&gt; 'Image category title',
		'image_head' =&gt; 'Image Categories',
		'link_category_name' =&gt; 'Link Category name',
		'link_category_title' =&gt; 'Link Category title',
		'link_head' =&gt; 'Link Categories',
		'parent' =&gt; 'Parent',
	),'common' =&gt; array(
		'are_you_sure' =&gt; 'Are you sure?',
		'auth_required' =&gt; 'Authorisation required',
		'bad_cookie' =&gt; 'Bad cookie',
		'changesection' =&gt; 'Change section',
		'changestatus' =&gt; 'Change status',
		'confirm_delete_popup' =&gt; 'Really delete?',
		'cookies_must_be_enabled' =&gt; 'Browser cookies must be enabled to use Textpattern.',
		'could_not_log_in' =&gt; 'Could not log in with that name/password',
		'delete_selected' =&gt; 'Delete selected',
		'draft' =&gt; 'Draft',
		'file_max_upload_size' =&gt; 'Max Upload File Size (bytes)',
		'hidden' =&gt; 'Hidden',
		'image_category' =&gt; 'Category',
		'link_category' =&gt; 'Link category',
		'logged_in_as' =&gt; 'Logged in as',
		'login_name' =&gt; 'Login',
		'login_to_textpattern' =&gt; 'Log in to Textpattern',
		'logout' =&gt; 'Log out',
		'log_in' =&gt; 'log in',
		'log_in_button' =&gt; 'Log in',
		'modified' =&gt; 'modified',
		'password' =&gt; 'password',
		'per_page' =&gt; 'per page',
		'range' =&gt; 'Range',
		'restricted_area' =&gt; 'Restricted area',
		'save_new' =&gt; 'Save New',
		'sort_value' =&gt; 'Sort Value',
		'stay_logged_in' =&gt; 'Remain logged in with this browser',
		'sticky' =&gt; 'Sticky',
		'structure' =&gt; 'Structure',
		'tab_admin' =&gt; 'admin',
		'tab_comments' =&gt; 'comments',
		'tab_content' =&gt; 'content',
		'tab_diagnostics' =&gt; 'diagnostics',
		'tab_extensions' =&gt; 'extensions',
		'tab_file' =&gt; 'files',
		'tab_forms' =&gt; 'forms',
		'tab_image' =&gt; 'images',
		'tab_import' =&gt; 'import',
		'tab_link' =&gt; 'links',
		'tab_list' =&gt; 'articles',
		'tab_logs' =&gt; 'logs',
		'tab_organise' =&gt; 'organise',
		'tab_pages' =&gt; 'pages',
		'tab_plugins' =&gt; 'plugins',
		'tab_preferences' =&gt; 'preferences',
		'tab_presentation' =&gt; 'presentation',
		'tab_sections' =&gt; 'sections',
		'tab_site_admin' =&gt; 'site admin',
		'tab_style' =&gt; 'style',
		'tab_view_site' =&gt; 'view site',
		'tab_write' =&gt; 'write',
		'thumbnail' =&gt; 'Thumb',
		'title_body' =&gt; 'Title &amp; Body',
		'upload_err_form_size' =&gt; 'File exceeds the maximum size specified in textpattern\'s preferences',
		'upload_err_ini_size' =&gt; 'File exceeds the upload_max_filesize directive in php.ini',
		'upload_err_no_file' =&gt; 'No file was specified',
		'upload_err_partial' =&gt; 'File was only partially uploaded',
		'upload_file' =&gt; 'Upload file',
		'use_textile' =&gt; 'Use Textile',
		'viewsite' =&gt; 'View Site',
		'visible' =&gt; 'Visible',
		'with_selected' =&gt; 'With selected:',
	),'css' =&gt; array(
		'add_declaration' =&gt; 'add declaration',
		'add_new_selector' =&gt; 'Add new selector',
		'all_stylesheets' =&gt; 'All stylesheets',
		'bulkload_existing_css' =&gt; 'Create or load new CSS',
		'cannot_delete_default_css' =&gt; 'CSS default cannot be deleted',
		'copy_css_as' =&gt; 'Copy style sheet as:',
		'create_new_css' =&gt; 'Create new CSS',
		'css_mode' =&gt; 'CSS Mode',
		'css_property_value' =&gt; 'Property : Value',
		'css_selector' =&gt; 'Selector',
		'delete_declaration' =&gt; 'Delete this declaration',
		'delete_entire_selector' =&gt; 'Delete entire selector',
		'delete_this_declaration' =&gt; 'Delete this declaration',
		'edit_css' =&gt; 'edit CSS',
		'edit_css_file' =&gt; 'edit CSS file',
		'edit_css_in_form' =&gt; 'Edit in CSS editor',
		'edit_raw_css' =&gt; 'Edit raw CSS',
		'name_for_this_style' =&gt; 'Name for this style',
		'property' =&gt; 'Property',
		'save_css_as' =&gt; 'Save style sheet as:',
		'save_this_declaration' =&gt; 'Save this declaration',
		'save_this_selector' =&gt; 'Save this selector',
		'selector' =&gt; 'Selector',
		'style_sheet' =&gt; 'Style sheet',
		'style_sheet_saved' =&gt; 'Style sheet saved',
		'you_are_editing_css' =&gt; 'You are editing CSS',
	),'diag' =&gt; array(
		'all_checks_passed' =&gt; 'All checks passed!',
		'apache_modules' =&gt; 'Apache modules',
		'apache_version' =&gt; 'Apache version',
		'cleanurl_only_apache' =&gt; 'Clean URLs are only supported for apache, use at your own risk',
		'detail' =&gt; 'Detail',
		'diagnostic_info' =&gt; 'Diagnostic info',
		'dir_not_writable' =&gt; '{dirtype} is not writable',
		'dns_lookup_fails' =&gt; 'Web Domain DNS lookup fails',
		'document_root' =&gt; 'Document root',
		'file_uploads_disabled' =&gt; 'File uploads are disabled',
		'high' =&gt; 'high',
		'htaccess_contents' =&gt; '.htaccess file contents',
		'htaccess_missing' =&gt; '.htaccess file is missing',
		'img_dir_read_only' =&gt; 'Image directory is read-only',
		'is_inaccessible' =&gt; 'is inaccessible',
		'low' =&gt; 'low',
		'magic_quotes' =&gt; 'Magic quotes',
		'missing_files' =&gt; 'Missing files',
		'mod_rewrite_missing' =&gt; 'Apache module mod_rewrite is not installed',
		'old_placeholder' =&gt; 'Old placeholder file is in the way',
		'path_to_site_inacc' =&gt; 'path_to_site is inaccessible',
		'php_extensions' =&gt; 'PHP extensions',
		'php_version' =&gt; 'PHP version',
		'preflight_check' =&gt; 'Pre-flight check',
		'register_globals' =&gt; 'Register globals',
		'server' =&gt; 'Server',
		'site_trailing_slash' =&gt; 'Site URL has a trailing slash',
		'still_exists' =&gt; 'still exists',
		'txp_path' =&gt; 'Textpattern path',
		'txp_version' =&gt; 'Textpattern version',
		'warn_register_globals_or_update' =&gt; 'Your version of PHP has security related risks. Please turn register_globals off or update to a newer PHP version.',
		'web_domain' =&gt; 'Site URL',
	),'discuss' =&gt; array(
		'ban' =&gt; 'Ban',
		'cant_ban_blank_ip' =&gt; 'Can\'t ban a blank IP address!',
		'confirm_comment_deletion' =&gt; 'confirm comment deletion',
		'displayed_comments' =&gt; 'Displayed comments',
		'edit_comment' =&gt; 'edit comment',
		'hide_comment' =&gt; 'Hide comment',
		'list_banned_ips' =&gt; 'List banned IPs',
		'no_comments_recorded' =&gt; 'No comments recorded yet',
		'no_ips_banned' =&gt; 'No IPs have been banned',
		'unban' =&gt; 'Unban',
	),'file' =&gt; array(
		'download' =&gt; 'download',
		'downloads' =&gt; 'Downloads',
		'existing_file' =&gt; 'Existing file:',
		'file' =&gt; 'File',
		'file_already_exists' =&gt; 'already exists',
		'file_category' =&gt; 'File Category',
		'file_delete_failed' =&gt; 'Failed to delete file',
		'file_dir_not_writeable' =&gt; 'Warning: cannot write to file directory&lt;br /&gt; {filedir}. &lt;br /&gt;Please change file permissions to 777.',
		'file_download_count' =&gt; 'Download Count',
		'file_name' =&gt; 'Filename',
		'file_not_found' =&gt; 'File not found',
		'file_relink' =&gt; 'Upload/Assign File',
		'file_status' =&gt; 'File Status',
		'file_status_missing' =&gt; 'Missing',
		'file_status_ok' =&gt; 'Ok',
		'file_upload_failed' =&gt; 'Failed to upload file',
		'invalid_filename' =&gt; 'Invalid filename',
		'invalid_id' =&gt; 'Invalid ID',
		'linked_to_file' =&gt; 'Linked record to file',
		'permissions' =&gt; 'Permissions',
		'private' =&gt; 'Private',
		'public' =&gt; 'Public',
		'reset' =&gt; 'reset',
		'reset_file_count_failure' =&gt; 'Failed to reset file count',
		'reset_file_count_success' =&gt; 'Successfully reset file count',
	),'form' =&gt; array(
		'all_forms' =&gt; 'All forms',
		'create_new_form' =&gt; 'Create new form',
		'delete_form_confirmation' =&gt; 'confirm form deletion',
		'edit_forms' =&gt; 'edit forms',
		'form_name' =&gt; 'Form name (required)',
		'form_type' =&gt; 'Form type (required)',
		'list_forms' =&gt; 'list forms',
		'you_are_editing_form' =&gt; 'You are editing form',
	),'image' =&gt; array(
		'cannot_write_directory' =&gt; 'Cannot write to directory',
		'choose_either_width_height_or_both' =&gt; 'Indicate width, height, or both',
		'create_thumbnail' =&gt; 'Create thumbnail',
		'image_name' =&gt; 'Image name',
		'image_save_error' =&gt; 'there was a problem saving image data',
		'img_dir_not_writeable' =&gt; 'Warning: cannot write to image directory&lt;br /&gt; {imgdir}. &lt;br /&gt;Please change file permissions to 777.',
		'invalid_width_or_height' =&gt; 'Invalid width or height',
		'keep_square_pixels' =&gt; 'Crop',
		'not_saved' =&gt; '&lt;strong&gt;not&lt;/strong&gt; saved!',
		'only_graphic_files_allowed' =&gt; ' .jpg, .gif, .png or .swf graphic files allowed',
		'replace_image' =&gt; 'Replace image',
		'save_these_settings_as_default' =&gt; 'Save settings as default',
		'thumb_height' =&gt; 'Height',
		'thumb_width' =&gt; 'Width',
		'upload_category' =&gt; 'Category',
		'upload_dir_perms' =&gt; 'directory permissions must be 777',
		'upload_thumbnail' =&gt; 'Upload thumbnail',
	),'import' =&gt; array(
		'continue' =&gt; 'Continue',
		'database_stuff' =&gt; 'Database Data',
		'import_blogid' =&gt; 'Weblog ID',
		'import_database' =&gt; 'Database name',
		'import_file_not_found' =&gt; 'Import file not found. &lt;br /&gt;Please name the file import.txt and place it in /textpattern/include/import/',
		'import_host' =&gt; 'Mysql host',
		'import_invite' =&gt; 'Default comments invite',
		'import_login' =&gt; 'Mysql user',
		'import_password' =&gt; 'Mysql password',
		'import_section' =&gt; 'Section to import into',
		'import_status' =&gt; 'Default article status',
		'import_wpprefix' =&gt; 'Tables prefix (if any)',
		'select_tool' =&gt; 'Import from',
		'txp_import' =&gt; 'Import content from other publishing tools',
	),'link' =&gt; array(
		'edit_links' =&gt; 'edit links',
		'linkcategory' =&gt; 'Link Categories',
		'links' =&gt; 'Links',
		'linktext' =&gt; 'linktext',
		'link_name' =&gt; 'Link Name',
		'link_saved' =&gt; 'Link saved',
		'link_text' =&gt; 'Link text',
	),'log' =&gt; array(
		'logs' =&gt; 'Logs',
		'no_refers_recorded' =&gt; 'No referrers recorded yet',
		'referrer' =&gt; 'Referrer',
		'visitor_logs' =&gt; 'visitor logs',
	),'page' =&gt; array(
		'all_pages' =&gt; 'All pages',
		'copy_page_as' =&gt; 'Copy page as:',
		'delete_page_confirmation' =&gt; 'confirm page deletion',
		'edit_page' =&gt; 'edit page',
		'edit_pages' =&gt; 'edit page template',
		'page_article_hed' =&gt; 'Article output',
		'page_article_nav_hed' =&gt; 'Article navigation',
		'page_misc_hed' =&gt; 'Miscellaneous',
		'page_nav_hed' =&gt; 'Site navigation',
		'page_xml_hed' =&gt; 'XML feeds',
		'you_are_editing_div' =&gt; 'You are editing div',
		'you_are_editing_page' =&gt; 'You are editing page template',
	),'plugin' =&gt; array(
		'bad_plugin_code' =&gt; 'Badly formed or empty plugin code',
		'broken_plugin' =&gt; 'broken',
		'edit_plugins' =&gt; 'edit plugins',
		'install' =&gt; 'Install',
		'install_plugin' =&gt; 'Install plugin',
		'old_plugin' =&gt; 'Old-style (text file) plugin installer',
		'plugin' =&gt; 'Plugin',
		'plugins' =&gt; 'Plugins',
		'plugin_help' =&gt; 'Plugin help',
		'previewing_plugin' =&gt; 'Previewing plugin:',
	),'prefs' =&gt; array(
		'active_language' =&gt; 'Currently active language',
		'admin_side_plugins' =&gt; 'Use admin side plugins?',
		'advanced_preferences' =&gt; 'Advanced preferences',
		'allow_article_php_scripting' =&gt; 'Allow PHP on articles?',
		'allow_form_override' =&gt; 'Allow form override?',
		'allow_page_php_scripting' =&gt; 'Allow PHP on pages?',
		'all_hits' =&gt; 'All hits',
		'archive_dateformat' =&gt; 'Archive date format',
		'archive_date_case' =&gt; 'Archive date case',
		'archive_dir' =&gt; 'Archive directory',
		'articles_use_excerpts' =&gt; 'Articles use excerpts?',
		'attach_titles_to_permalinks' =&gt; 'Attach titles to permalinks?',
		'category_subcategory' =&gt; '/category/subcategory',
		'check_for_txp_updates' =&gt; 'Check Textpattern Updates',
		'clean' =&gt; '/clean/',
		'comments_are_ol' =&gt; 'Present comments as a numbered list?',
		'comments_auto_append' =&gt; 'Automatically append comments to articles?',
		'comments_dateformat' =&gt; 'Comments date format',
		'comments_default_invite' =&gt; 'Default invite',
		'comments_disabled_after' =&gt; 'Disabled after',
		'comments_disallow_images' =&gt; 'Disallow user images',
		'comments_mode' =&gt; 'Comments mode',
		'comments_moderate' =&gt; 'Moderate comments',
		'comments_on_default' =&gt; 'On by default?',
		'comments_require_email' =&gt; 'Comments require user email?',
		'comments_require_name' =&gt; 'Comments require user name?',
		'comments_sendmail' =&gt; 'Mail comments to author',
		'comment_means_site_updated' =&gt; 'New comment means site updated?',
		'comment_nofollow' =&gt; 'Use nofollow on comments?',
		'convert_linebreaks' =&gt; 'Convert linebreaks',
		'custom_10_set' =&gt; 'Custom field 10 name',
		'custom_1_set' =&gt; 'Custom field 1 name',
		'custom_2_set' =&gt; 'Custom field 2 name',
		'custom_3_set' =&gt; 'Custom field 3 name',
		'custom_4_set' =&gt; 'Custom field 4 name',
		'custom_5_set' =&gt; 'Custom field 5 name',
		'custom_6_set' =&gt; 'Custom field 6 name',
		'custom_7_set' =&gt; 'Custom field 7 name',
		'custom_8_set' =&gt; 'Custom field 8 name',
		'custom_9_set' =&gt; 'Custom field 9 name',
		'czech' =&gt; 'Čeština',
		'danish' =&gt; 'Dansk',
		'dutch' =&gt; 'Nederlands',
		'edit_preferences' =&gt; 'Edit preferences',
		'edit_raw_css_by_default' =&gt; 'Edit raw CSS by default?',
		'english_gb' =&gt; 'English (GB)',
		'english_us' =&gt; 'English (US)',
		'expire_logs_after' =&gt; 'Expire logs after',
		'file_base_path' =&gt; 'File Upload Path',
		'finnish' =&gt; 'Finnish',
		'french' =&gt; 'Français',
		'from_file' =&gt; 'Install from file',
		'from_server' =&gt; 'Install from remote server',
		'gmtoffset' =&gt; 'Time Zone',
		'id_title' =&gt; '/id/title',
		'img_dir' =&gt; 'Image directory',
		'include_email_atom' =&gt; 'Include email in atom feeds?',
		'install_langfile' =&gt; 'To install new languages from file you can download them from {url} and place them inside your ./textpattern/lang/ directory.',
		'install_language' =&gt; 'Install language',
		'is_dst' =&gt; 'Daylight Savings',
		'language' =&gt; 'Language',
		'leave_text_untouched' =&gt; 'Leave text untouched',
		'locale' =&gt; 'Locale',
		'logging' =&gt; 'Logging',
		'logs_expire' =&gt; 'Expire logs after',
		'manage_languages' =&gt; 'Manage languages',
		'max_url_len' =&gt; 'Max URL length',
		'mentions' =&gt; 'Mentions',
		'messy' =&gt; '?=messy',
		'never_display_email' =&gt; 'Never display email?',
		'new_textpattern_version_available' =&gt; 'There is a completely new Textpattern version available. Do you want to try it?',
		'norwegian' =&gt; 'Norwegian',
		'no_popup' =&gt; 'current window',
		'override_emailcharset' =&gt; 'Use ISO-8859-1 for e-mails? (default is utf-8)',
		'page_mode' =&gt; 'Page mode',
		'path_from_root' =&gt; 'Subdirectory (if any)',
		'path_to_site_missing' =&gt; '$path_to_site is not set (update index.php)',
		'permalink_title_format' =&gt; 'Permalink title-like-this (default is TitleLikeThis)',
		'permlink_mode' =&gt; 'Permanent link mode',
		'ping_textpattern_com' =&gt; 'Ping textpattern.com?',
		'ping_weblogsdotcom' =&gt; 'Update Ping-o-matic',
		'polish' =&gt; 'Polish',
		'portuguese' =&gt; 'Portuguese',
		'preferences_saved' =&gt; 'Preferences saved',
		'prefs' =&gt; 'Prefs',
		'problem_connecting_rpc_server' =&gt; 'There is a problem trying to connect to the RPC server. Please, try again later.',
		'production_debug' =&gt; 'Debugging',
		'production_live' =&gt; 'Live',
		'production_status' =&gt; 'Production Status',
		'production_test' =&gt; 'Testing',
		'record_mentions' =&gt; 'Record mentions',
		'referrers_only' =&gt; 'Referrers only',
		'rpc_connect_error' =&gt; 'Can\'t connect to remote server to check for updated language files. Please try again later.',
		'rss_how_many' =&gt; 'How many articles on RSS?',
		'russian' =&gt; 'Russian',
		'scots' =&gt; 'Scots',
		'section_id_title' =&gt; '/section/id/title',
		'section_title' =&gt; '/section/title',
		'send_lastmod' =&gt; 'Send Last-Modified header',
		'show_article_category_count' =&gt; 'Show article count on Categories?',
		'show_comment_count_in_feed' =&gt; 'Show comment count in feeds?',
		'site_prefs' =&gt; 'Site Preferences',
		'spam_blacklists' =&gt; 'Spam blacklists (comma separated)',
		'spanish' =&gt; 'Español',
		'swedish' =&gt; 'Swedish',
		'syndicate_body_or_excerpt' =&gt; 'Syndicate excerpt? (default is body)',
		'tagalog' =&gt; 'Tagalog',
		'tempdir' =&gt; 'Temp folder',
		'textile_links' =&gt; 'Textile links description by default?',
		'thai' =&gt; 'Thai',
		'timeoffset' =&gt; 'Time offset (hours)',
		'title_only' =&gt; '/title',
		'updated_branch_version_available' =&gt; 'There is an updated version of this Textpattern branch available',
		'update_languages' =&gt; 'Update languages',
		'urls_to_ping' =&gt; 'URLs to ping (comma separated)',
		'url_mode' =&gt; 'URL mode',
		'use_comments' =&gt; 'Accept comments',
		'use_dns' =&gt; 'Use DNS?',
		'use_mail_on_feeds_id' =&gt; 'Use mail on feeds id?',
		'use_plugins' =&gt; 'Use plugins?',
		'year_month_day_title' =&gt; '/year/month/day/title',
	),'public' =&gt; array(
		'404_not_found' =&gt; '404 - The requested page was not found.',
		'active' =&gt; 'Active',
		'add' =&gt; 'Add',
		'admin' =&gt; 'Admin',
		'ago' =&gt; 'ago',
		'all' =&gt; 'All',
		'already_exists' =&gt; 'already exists',
		'alt_text' =&gt; 'Alt text',
		'article' =&gt; 'article',
		'articles' =&gt; 'Articles',
		'articles_found' =&gt; 'articles found',
		'article_found' =&gt; 'article found',
		'ascending' =&gt; 'Ascending',
		'author' =&gt; 'Author',
		'authors' =&gt; 'Authors',
		'a_few_seconds' =&gt; 'a few seconds',
		'blockquote' =&gt; 'Blockquote',
		'bulleted_list' =&gt; 'Bulleted list',
		'ca-es' =&gt; 'Català',
		'caption' =&gt; 'Caption',
		'categories' =&gt; 'Categories',
		'categorize' =&gt; 'Categorise',
		'category' =&gt; 'Category',
		'category1' =&gt; 'Cat. 1',
		'category2' =&gt; 'Cat. 2',
		'change' =&gt; 'change',
		'check_html' =&gt; 'Check HTML',
		'citation' =&gt; 'citation',
		'comment' =&gt; 'comment',
		'comments' =&gt; 'Comments',
		'comments_closed' =&gt; 'commenting closed for this article',
		'comments_on' =&gt; 'comments on',
		'comments_permlink' =&gt; 'Permanent link',
		'comment_comment' =&gt; 'Comment',
		'comment_email' =&gt; 'Email',
		'comment_email_required' =&gt; 'Please enter a valid email address',
		'comment_invitation' =&gt; 'Invitation',
		'comment_moderated' =&gt; 'Your comment is pending moderation. It will appear after it has been approved.',
		'comment_name' =&gt; 'Name',
		'comment_name_required' =&gt; 'Please enter your name',
		'comment_posted' =&gt; 'Thank you for adding your comment.',
		'comment_received' =&gt; '[{site}] comment received: {title}',
		'comment_recorded' =&gt; 'A comment on your post "{title}" was recorded.',
		'comment_required' =&gt; 'You must enter a comment',
		'comment_web' =&gt; 'Web',
		'contact' =&gt; 'Contact',
		'copy' =&gt; 'Copy',
		'create' =&gt; 'Create',
		'created' =&gt; 'created',
		'create_new' =&gt; 'Create new',
		'cs-cz' =&gt; 'Čeština',
		'css' =&gt; 'css',
		'custom' =&gt; 'custom',
		'da-dk' =&gt; 'Dansk',
		'date' =&gt; 'Date',
		'dateformat' =&gt; 'Date format',
		'date_case' =&gt; 'Date case',
		'day' =&gt; 'day',
		'days' =&gt; 'days',
		'de-de' =&gt; 'Deutsch',
		'default' =&gt; 'Default',
		'delete' =&gt; 'Delete',
		'deleted' =&gt; 'deleted',
		'deleted_text' =&gt; 'deleted text',
		'descending' =&gt; 'Descending',
		'description' =&gt; 'Description',
		'edit' =&gt; 'edit',
		'el-gr' =&gt; 'Ελληνικά',
		'email' =&gt; 'email',
		'email_address' =&gt; 'Email address',
		'emphasis' =&gt; 'emphasis',
		'en-gb' =&gt; 'English (GB)',
		'en-us' =&gt; 'English (US)',
		'english' =&gt; 'English',
		'es-es' =&gt; 'Español',
		'et-ee' =&gt; 'Eesti',
		'excerpt' =&gt; 'Excerpt',
		'experts_only' =&gt; 'experts only',
		'extensions' =&gt; 'Extensions',
		'fi-fi' =&gt; 'Suomi',
		'forget' =&gt; 'Forget',
		'form' =&gt; 'Form',
		'forms' =&gt; 'Forms',
		'fr-fr' =&gt; 'Français',
		'go' =&gt; 'go',
		'go_to' =&gt; 'Go to',
		'header' =&gt; 'header',
		'host' =&gt; 'Host',
		'hour' =&gt; 'hour',
		'hours' =&gt; 'hours',
		'hr' =&gt; 'horizontal rule',
		'HTML' =&gt; 'HTML',
		'hyperlink' =&gt; 'hyperlink',
		'image' =&gt; 'image',
		'imageurl' =&gt; 'imageurl',
		'inserted_text' =&gt; 'inserted text',
		'is-is' =&gt; 'Íslenska(Icelandic)',
		'it-it' =&gt; 'Italiano',
		'ja-jp' =&gt; '日本語',
		'keywords' =&gt; 'Keywords',
		'label' =&gt; 'Label',
		'last_modification' =&gt; 'Last Modification',
		'linebreak' =&gt; 'line break',
		'list' =&gt; 'List',
		'list_articles' =&gt; 'List Articles',
		'list_categories' =&gt; 'list categories',
		'list_discussions' =&gt; 'list comments',
		'list_links' =&gt; 'list links',
		'live' =&gt; 'Live',
		'lowercase' =&gt; 'lowercase',
		'lv-lv' =&gt; 'Latviešu',
		'manual' =&gt; 'Manual',
		'message' =&gt; 'Message',
		'message_deleted' =&gt; 'Message deleted',
		'message_preview' =&gt; 'Message Preview',
		'message_saved' =&gt; 'Message saved',
		'minute' =&gt; 'minute',
		'minutes' =&gt; 'minutes',
		'modified_by' =&gt; 'Last modified by',
		'month' =&gt; 'Month',
		'more' =&gt; 'More',
		'name' =&gt; 'name',
		'never' =&gt; 'never',
		'next' =&gt; 'next',
		'nl-nl' =&gt; 'Nederlands',
		'no' =&gt; 'no',
		'no-no' =&gt; 'Norsk',
		'none' =&gt; 'None',
		'nopopup' =&gt; 'nopopup',
		'numeric_list' =&gt; 'Numeric list',
		'off' =&gt; 'off',
		'older' =&gt; 'older',
		'on' =&gt; 'on',
		'only_articles_can_be_previewed' =&gt; 'NB: only article forms can be previewed.',
		'page' =&gt; 'Page',
		'pages' =&gt; 'Pages',
		'paragraph' =&gt; 'paragraph',
		'permanent_link' =&gt; 'Permanent link to this article',
		'permlink' =&gt; 'Permanent link',
		'pl-pl' =&gt; 'Polski',
		'plugin_load_error' =&gt; 'A problem occured while loading the plugin:',
		'plugin_load_error_above' =&gt; 'The above errors were caused by the plugin:',
		'popup' =&gt; 'popup',
		'post' =&gt; 'Post',
		'posted' =&gt; 'Posted',
		'posted_by' =&gt; 'Posted by',
		'prev' =&gt; 'prev',
		'preview' =&gt; 'preview',
		'pt-pt' =&gt; 'Português',
		'publish' =&gt; 'Publish',
		'published_at' =&gt; 'Published at',
		'recently' =&gt; 'Recently',
		'recent_articles' =&gt; 'Recent Articles',
		'recent_posts' =&gt; 'Recent Posts',
		'remember' =&gt; 'Remember',
		'revert' =&gt; 'Revert',
		'ru-ru' =&gt; 'Русский',
		'save' =&gt; 'Save',
		'saved' =&gt; 'saved',
		'save_button' =&gt; 'Save',
		'search' =&gt; 'Search',
		'search_results' =&gt; 'Search results',
		'select' =&gt; 'select',
		'selected' =&gt; 'selected',
		'site' =&gt; 'Site',
		'sitename' =&gt; 'Site name',
		'siteurl' =&gt; 'Site URL',
		'site_slogan' =&gt; 'Site tagline',
		'sk-sk' =&gt; 'Slovenčina',
		'status' =&gt; 'Status',
		'strong' =&gt; 'strong',
		'submit' =&gt; 'Submit',
		'subscript' =&gt; 'subscript',
		'superscript' =&gt; 'superscript',
		'sv-se' =&gt; 'Svenska',
		'syndicate' =&gt; 'Syndicate',
		'textile_help' =&gt; 'Textile Help',
		'text_conversion' =&gt; 'Text conversion',
		'text_handling' =&gt; 'Text handling',
		'th-th' =&gt; 'ไทย',
		'time' =&gt; 'Time',
		'title' =&gt; 'Title',
		'tooltip' =&gt; 'Link tooltip',
		'type' =&gt; 'Type',
		'undefined' =&gt; 'Undefined',
		'unknown_section' =&gt; 'Unknown section',
		'untitled' =&gt; 'Untitled',
		'updated' =&gt; 'updated',
		'upload' =&gt; 'Upload',
		'uploaded' =&gt; 'uploaded',
		'url' =&gt; 'URL',
		'value' =&gt; 'Value',
		'version' =&gt; 'Version',
		'view' =&gt; 'View',
		'website' =&gt; 'website',
		'week' =&gt; 'week',
		'weeks' =&gt; 'weeks',
		'yes' =&gt; 'yes',
		'your_branch_is_updated' =&gt; 'You have the most updated version of this Textpattern branch',
		'your_ip_is_blacklisted_by' =&gt; 'Your IP address has been blacklisted by',
		'you_have_been_banned' =&gt; 'You have been banned from commenting.',
		'yyyy-mm' =&gt; 'yyyy-mm',
		'zh-cn' =&gt; '中文(简体)',
		'zh-tw' =&gt; '中文(繁體)',
	),'section' =&gt; array(
		'delete_section_confirmation' =&gt; 'confirm section deletion',
		'edit_sections' =&gt; 'edit sections',
		'include_in_search' =&gt; 'Include in site search',
		'on_front_page' =&gt; 'On front page',
		'section' =&gt; 'Section',
		'sections' =&gt; 'Sections',
		'section_head' =&gt; 'Site Sections',
		'section_longtitle' =&gt; 'Section title',
		'section_name' =&gt; 'Section name',
		'section_name_already_exists' =&gt; 'Section name already exists',
		'selected_by_default' =&gt; 'Selected by default',
		'style' =&gt; 'Style',
		'uses_page' =&gt; 'Uses page',
		'uses_style' =&gt; 'Uses style',
	),'setup' =&gt; array(
		'about_to_create' =&gt; 'You are about to create and populate database tables.',
		'already_installed' =&gt; 'Looks like Textpattern is already installed. If you want to make a clean install, please remove &lt;code&gt;config.php&lt;/code&gt; from your &lt;code&gt;/textpattern/&lt;/code&gt; directory and try again.',
		'before_you_proceed' =&gt; 'Before you proceed',
		'checking_database' =&gt; 'Checking database connection...',
		'choose_password' =&gt; 'Choose a password',
		'confirm_site_path' =&gt; 'Please confirm the following path',
		'create_config' =&gt; 'create a file called &lt;code&gt;config.php&lt;/code&gt; in the &lt;code&gt;/textpattern/&lt;/code&gt; directory and paste the following inside:',
		'db_cant_connect' =&gt; 'Can\'t connect to database',
		'db_connected' =&gt; 'Connected',
		'db_doesnt_exist' =&gt; 'Database {dbname} doesn\'t exist',
		'db_must_exist' =&gt; 'Note that the database you specify must exist; Textpattern won’t create it for you.',
		'did_it' =&gt; 'I did it',
		'errors_during_install' =&gt; 'There were {num} errors during the installation. You can ask for help in the Textpattern forums.',
		'full_path_to_txp' =&gt; 'Full server path to Textpattern',
		'mysql_database' =&gt; 'MySQL database',
		'mysql_login' =&gt; 'MySQL login',
		'mysql_password' =&gt; 'MySQL password',
		'mysql_server' =&gt; 'MySQL server',
		'my_site' =&gt; 'My Site',
		'my_slogan' =&gt; 'My pithy slogan',
		'need_details' =&gt; 'Inevitably, we need a few details',
		'please_enter_url' =&gt; 'Please enter the web-reachable address of your site',
		'prefix_bad_characters' =&gt; 'The table prefix {dbprefix} contains characters that are not allowed.&lt;br /&gt; The first character must match one of &lt;b&gt;a-zA-Z_&lt;/b&gt; and all following characters must match one of &lt;b&gt;a-zA-Z0-9_&lt;/b&gt;',
		'prefix_warning' =&gt; '(Use ONLY if you require multiple installations in one database)',
		'setup_comment_invite' =&gt; 'Comment',
		'setup_login' =&gt; 'Choose a login name (basic characters and spaces only please)',
		'site_path' =&gt; 'Site path',
		'site_url' =&gt; 'Site URL',
		'table_prefix' =&gt; 'Table prefix',
		'thanks' =&gt; 'Thank you.',
		'thanks_for_interest' =&gt; 'Thank you for your interest in Textpattern.',
		'that_went_well' =&gt; 'That went well. Database tables were created and populated.',
		'using_db' =&gt; 'Using {dbname}',
		'warn_mail_unavailable' =&gt; 'Your php installation is missing the mail() function. Therefore no emails can be sent from textpattern, which limits certain functionality.',
		'welcome_to_textpattern' =&gt; 'Welcome to Textpattern',
		'your_email' =&gt; 'Your email address',
		'your_full_name' =&gt; 'Your full name',
		'you_can_access' =&gt; 'You should be able to access the &lt;a href="index.php"&gt;main interface&lt;/a&gt; with the login and password you chose.',
	),'tag' =&gt; array(
		'article_divider' =&gt; 'Article divider',
		'breadcrumb_linked' =&gt; 'Link breadcrumbs?',
		'breadcrumb_separator' =&gt; 'Breadcrumbs separator',
		'break' =&gt; 'Break',
		'build' =&gt; 'Build Tag',
		'button_text' =&gt; 'Button text',
		'comments_form' =&gt; 'Form',
		'comment_form' =&gt; 'Comment form',
		'file_download_tags' =&gt; 'File downloads',
		'flavour' =&gt; 'Flavour',
		'has_excerpt' =&gt; 'Has excerpt',
		'input_size' =&gt; 'Input size',
		'labeltag' =&gt; 'HTML tag for label',
		'limit' =&gt; 'Limit',
		'link_to_this_author' =&gt; 'Link to a list of other articles by this author?',
		'link_to_this_category' =&gt; 'Link to a list of other articles in this category?',
		'link_to_this_section' =&gt; 'Link to a list of other articles in this section?',
		'listform' =&gt; 'List form',
		'newer' =&gt; 'newer',
		'next_page_link' =&gt; 'Next page link',
		'page_file_hed' =&gt; 'File downloads',
		'search_input_form' =&gt; 'Search input',
		'search_results_form' =&gt; 'Search results',
		'sort_by' =&gt; 'Sort by',
		'sort_direction' =&gt; 'Sort direction',
		'tag' =&gt; 'Tag',
		'tags' =&gt; 'Tags',
		'tag_article' =&gt; 'Articles (single or list)',
		'tag_article_custom' =&gt; 'Articles (custom list)',
		'tag_article_image' =&gt; 'Article image',
		'tag_author' =&gt; 'Author',
		'tag_body' =&gt; 'Body',
		'tag_body_excerpt' =&gt; 'Body excerpt',
		'tag_breadcrumb' =&gt; 'Breadcrumb',
		'tag_category1' =&gt; 'Category 1',
		'tag_category2' =&gt; 'Category 2',
		'tag_category_list' =&gt; 'Category list',
		'tag_comments_invite' =&gt; 'Comments invite',
		'tag_comment_email_input' =&gt; 'Email input',
		'tag_comment_message_input' =&gt; 'Message input',
		'tag_comment_name' =&gt; 'Comment name',
		'tag_comment_name_input' =&gt; 'Name input',
		'tag_comment_permlink' =&gt; 'Permanent link',
		'tag_comment_preview' =&gt; 'Preview button',
		'tag_comment_remember' =&gt; 'Remember details checkbox',
		'tag_comment_submit' =&gt; 'Submit button',
		'tag_comment_time' =&gt; 'Time',
		'tag_comment_web_input' =&gt; 'Web input',
		'tag_css' =&gt; 'CSS link (head)',
		'tag_email' =&gt; 'Email link (spam-proof)',
		'tag_excerpt' =&gt; 'Excerpt',
		'tag_feed_link' =&gt; 'Feed to articles',
		'tag_file_download' =&gt; 'File download form',
		'tag_file_download_category' =&gt; 'Category',
		'tag_file_download_created' =&gt; 'Created time',
		'tag_file_download_downloads' =&gt; 'Download count',
		'tag_file_download_id' =&gt; 'ID',
		'tag_file_download_link' =&gt; 'Link',
		'tag_file_download_list' =&gt; 'File list',
		'tag_file_download_modified' =&gt; 'Modified time',
		'tag_file_download_name' =&gt; 'Name',
		'tag_file_download_size' =&gt; 'Size',
		'tag_home' =&gt; 'Home',
		'tag_inline_' =&gt; 'Inline at end',
		'tag_lang' =&gt; 'Language',
		'tag_link' =&gt; 'Link only',
		'tag_linkdesctitle' =&gt; 'Link, title=Description',
		'tag_linklist' =&gt; 'List of links',
		'tag_link_description' =&gt; 'Description only',
		'tag_link_feed_link' =&gt; 'Feed to links',
		'tag_link_text' =&gt; 'Link text only',
		'tag_link_to_home' =&gt; 'Link to home page',
		'tag_link_to_next' =&gt; 'Link to next article',
		'tag_link_to_prev' =&gt; 'Link to previous article',
		'tag_message' =&gt; 'Message',
		'tag_name' =&gt; 'Commenter name',
		'tag_newer' =&gt; 'Link to newer articles',
		'tag_next_article' =&gt; 'Next article',
		'tag_next_title' =&gt; 'Next article title',
		'tag_older' =&gt; 'Link to older articles',
		'tag_output_form' =&gt; 'Output form',
		'tag_page_title' =&gt; 'Page title',
		'tag_paging_link' =&gt; 'Next page link',
		'tag_password_protect' =&gt; 'Password protection',
		'tag_permlink' =&gt; 'Permanent link',
		'tag_popup' =&gt; 'Popup list',
		'tag_posted' =&gt; 'Posted',
		'tag_prev_article' =&gt; 'Previous article',
		'tag_prev_title' =&gt; 'Previous article title',
		'tag_recent_articles' =&gt; 'Recent articles',
		'tag_recent_comments' =&gt; 'Recent comments',
		'tag_related_articles' =&gt; 'Related articles',
		'tag_search_input' =&gt; 'Search input form',
		'tag_search_result_date' =&gt; 'Result date',
		'tag_search_result_excerpt' =&gt; 'Result excerpt',
		'tag_search_result_title' =&gt; 'Result title',
		'tag_search_result_url' =&gt; 'Result URL',
		'tag_section' =&gt; 'Section',
		'tag_sitename' =&gt; 'Site name',
		'tag_site_slogan' =&gt; 'Site tagline',
		'tag_title' =&gt; 'Title',
		'tag__inline' =&gt; 'Inline at beginning',
		'text_or_tag' =&gt; '* text or tag here *',
		'title_separator' =&gt; 'Sitename: Individual Article separator',
		'useful_tags' =&gt; 'Useful tags',
		'wraptag' =&gt; 'Wraptag',
	)
);
$lastmod = gmdate('Y-m-d H:m:s',1130840747);
?&gt;</pre></body></html>