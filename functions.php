<?php
/**
 * Live Library Management Theme Functions
 * 
 * @package LibraryTheme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function library_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'library-theme'),
        'footer' => __('Footer Menu', 'library-theme'),
    ));
    
    // Set content width
    $GLOBALS['content_width'] = 1200;
}
add_action('after_setup_theme', 'library_theme_setup');

// Enqueue scripts and styles
function library_theme_scripts() {
    wp_enqueue_style('library-theme-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('library-theme-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('library-theme-script', 'library_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('library_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'library_theme_scripts');

// Register custom post types
function library_register_post_types() {
    // Books post type
    register_post_type('book', array(
        'labels' => array(
            'name' => __('Books', 'library-theme'),
            'singular_name' => __('Book', 'library-theme'),
            'add_new' => __('Add New Book', 'library-theme'),
            'add_new_item' => __('Add New Book', 'library-theme'),
            'edit_item' => __('Edit Book', 'library-theme'),
            'new_item' => __('New Book', 'library-theme'),
            'view_item' => __('View Book', 'library-theme'),
            'search_items' => __('Search Books', 'library-theme'),
            'not_found' => __('No books found', 'library-theme'),
            'not_found_in_trash' => __('No books found in trash', 'library-theme'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-book',
        'rewrite' => array('slug' => 'books'),
        'show_in_rest' => true,
    ));
    
    // Libraries post type
    register_post_type('library', array(
        'labels' => array(
            'name' => __('Libraries', 'library-theme'),
            'singular_name' => __('Library', 'library-theme'),
            'add_new' => __('Add New Library', 'library-theme'),
            'add_new_item' => __('Add New Library', 'library-theme'),
            'edit_item' => __('Edit Library', 'library-theme'),
            'new_item' => __('New Library', 'library-theme'),
            'view_item' => __('View Library', 'library-theme'),
            'search_items' => __('Search Libraries', 'library-theme'),
            'not_found' => __('No libraries found', 'library-theme'),
            'not_found_in_trash' => __('No libraries found in trash', 'library-theme'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-building',
        'rewrite' => array('slug' => 'libraries'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'library_register_post_types');

// Register taxonomies
function library_register_taxonomies() {
    // Book categories
    register_taxonomy('book_category', 'book', array(
        'labels' => array(
            'name' => __('Book Categories', 'library-theme'),
            'singular_name' => __('Book Category', 'library-theme'),
            'search_items' => __('Search Categories', 'library-theme'),
            'all_items' => __('All Categories', 'library-theme'),
            'edit_item' => __('Edit Category', 'library-theme'),
            'update_item' => __('Update Category', 'library-theme'),
            'add_new_item' => __('Add New Category', 'library-theme'),
            'new_item_name' => __('New Category Name', 'library-theme'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'book-category'),
    ));
    
    // Book authors
    register_taxonomy('book_author', 'book', array(
        'labels' => array(
            'name' => __('Authors', 'library-theme'),
            'singular_name' => __('Author', 'library-theme'),
            'search_items' => __('Search Authors', 'library-theme'),
            'all_items' => __('All Authors', 'library-theme'),
            'edit_item' => __('Edit Author', 'library-theme'),
            'update_item' => __('Update Author', 'library-theme'),
            'add_new_item' => __('Add New Author', 'library-theme'),
            'new_item_name' => __('New Author Name', 'library-theme'),
        ),
        'hierarchical' => false,
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'author'),
    ));
}
add_action('init', 'library_register_taxonomies');

// Add custom user roles
function library_add_user_roles() {
    // Library Admin role
    add_role('library_admin', __('Library Admin', 'library-theme'), array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'upload_files' => true,
        'edit_books' => true,
        'edit_others_books' => true,
        'publish_books' => true,
        'read_private_books' => true,
        'delete_books' => true,
        'delete_private_books' => true,
        'delete_published_books' => true,
        'delete_others_books' => true,
        'edit_private_books' => true,
        'edit_published_books' => true,
        'manage_book_terms' => true,
        'edit_book_terms' => true,
        'delete_book_terms' => true,
        'assign_book_terms' => true,
    ));
    
    // Librarian role
    add_role('librarian', __('Librarian', 'library-theme'), array(
        'read' => true,
        'edit_books' => true,
        'publish_books' => true,
        'read_private_books' => true,
        'edit_private_books' => true,
        'edit_published_books' => true,
        'assign_book_terms' => true,
    ));
    
    // Library Patron role
    add_role('library_patron', __('Library Patron', 'library-theme'), array(
        'read' => true,
        'read_books' => true,
    ));
}
add_action('init', 'library_add_user_roles');

// Add custom meta boxes
function library_add_meta_boxes() {
    add_meta_box(
        'book_details',
        __('Book Details', 'library-theme'),
        'library_book_details_callback',
        'book',
        'normal',
        'high'
    );
    
    add_meta_box(
        'library_details',
        __('Library Details', 'library-theme'),
        'library_details_callback',
        'library',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'library_add_meta_boxes');

// Book details meta box callback
function library_book_details_callback($post) {
    wp_nonce_field('library_book_details_nonce', 'library_book_details_nonce');
    
    $isbn = get_post_meta($post->ID, '_book_isbn', true);
    $publisher = get_post_meta($post->ID, '_book_publisher', true);
    $publication_date = get_post_meta($post->ID, '_book_publication_date', true);
    $pages = get_post_meta($post->ID, '_book_pages', true);
    $language = get_post_meta($post->ID, '_book_language', true);
    $status = get_post_meta($post->ID, '_book_status', true);
    $library_id = get_post_meta($post->ID, '_book_library', true);
    $digital_file = get_post_meta($post->ID, '_book_digital_file', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="book_isbn"><?php _e('ISBN', 'library-theme'); ?></label></th>
            <td><input type="text" id="book_isbn" name="book_isbn" value="<?php echo esc_attr($isbn); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="book_publisher"><?php _e('Publisher', 'library-theme'); ?></label></th>
            <td><input type="text" id="book_publisher" name="book_publisher" value="<?php echo esc_attr($publisher); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="book_publication_date"><?php _e('Publication Date', 'library-theme'); ?></label></th>
            <td><input type="date" id="book_publication_date" name="book_publication_date" value="<?php echo esc_attr($publication_date); ?>" /></td>
        </tr>
        <tr>
            <th><label for="book_pages"><?php _e('Pages', 'library-theme'); ?></label></th>
            <td><input type="number" id="book_pages" name="book_pages" value="<?php echo esc_attr($pages); ?>" /></td>
        </tr>
        <tr>
            <th><label for="book_language"><?php _e('Language', 'library-theme'); ?></label></th>
            <td><input type="text" id="book_language" name="book_language" value="<?php echo esc_attr($language); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="book_status"><?php _e('Status', 'library-theme'); ?></label></th>
            <td>
                <select id="book_status" name="book_status">
                    <option value="available" <?php selected($status, 'available'); ?>><?php _e('Available', 'library-theme'); ?></option>
                    <option value="borrowed" <?php selected($status, 'borrowed'); ?>><?php _e('Borrowed', 'library-theme'); ?></option>
                    <option value="maintenance" <?php selected($status, 'maintenance'); ?>><?php _e('Under Maintenance', 'library-theme'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="book_library"><?php _e('Library', 'library-theme'); ?></label></th>
            <td>
                <select id="book_library" name="book_library">
                    <option value=""><?php _e('Select Library', 'library-theme'); ?></option>
                    <?php
                    $libraries = get_posts(array('post_type' => 'library', 'numberposts' => -1));
                    foreach ($libraries as $library) {
                        echo '<option value="' . $library->ID . '" ' . selected($library_id, $library->ID, false) . '>' . $library->post_title . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="book_digital_file"><?php _e('Digital File URL', 'library-theme'); ?></label></th>
            <td><input type="url" id="book_digital_file" name="book_digital_file" value="<?php echo esc_attr($digital_file); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

// Library details meta box callback
function library_details_callback($post) {
    wp_nonce_field('library_library_details_nonce', 'library_library_details_nonce');
    
    $address = get_post_meta($post->ID, '_library_address', true);
    $phone = get_post_meta($post->ID, '_library_phone', true);
    $email = get_post_meta($post->ID, '_library_email', true);
    $website = get_post_meta($post->ID, '_library_website', true);
    $hours = get_post_meta($post->ID, '_library_hours', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="library_address"><?php _e('Address', 'library-theme'); ?></label></th>
            <td><textarea id="library_address" name="library_address" rows="3" class="large-text"><?php echo esc_textarea($address); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="library_phone"><?php _e('Phone', 'library-theme'); ?></label></th>
            <td><input type="tel" id="library_phone" name="library_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="library_email"><?php _e('Email', 'library-theme'); ?></label></th>
            <td><input type="email" id="library_email" name="library_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="library_website"><?php _e('Website', 'library-theme'); ?></label></th>
            <td><input type="url" id="library_website" name="library_website" value="<?php echo esc_attr($website); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="library_hours"><?php _e('Operating Hours', 'library-theme'); ?></label></th>
            <td><textarea id="library_hours" name="library_hours" rows="5" class="large-text"><?php echo esc_textarea($hours); ?></textarea></td>
        </tr>
    </table>
    <?php
}

// Save meta box data
function library_save_meta_boxes($post_id) {
    // Check if nonce is valid
    if (!isset($_POST['library_book_details_nonce']) && !isset($_POST['library_library_details_nonce'])) {
        return;
    }
    
    if (isset($_POST['library_book_details_nonce']) && !wp_verify_nonce($_POST['library_book_details_nonce'], 'library_book_details_nonce')) {
        return;
    }
    
    if (isset($_POST['library_library_details_nonce']) && !wp_verify_nonce($_POST['library_library_details_nonce'], 'library_library_details_nonce')) {
        return;
    }
    
    // Check if user has permission to edit
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save book meta
    if (get_post_type($post_id) === 'book') {
        $fields = array('isbn', 'publisher', 'publication_date', 'pages', 'language', 'status', 'library', 'digital_file');
        foreach ($fields as $field) {
            if (isset($_POST['book_' . $field])) {
                update_post_meta($post_id, '_book_' . $field, sanitize_text_field($_POST['book_' . $field]));
            }
        }
    }
    
    // Save library meta
    if (get_post_type($post_id) === 'library') {
        $fields = array('address', 'phone', 'email', 'website', 'hours');
        foreach ($fields as $field) {
            if (isset($_POST['library_' . $field])) {
                update_post_meta($post_id, '_library_' . $field, sanitize_textarea_field($_POST['library_' . $field]));
            }
        }
    }
}
add_action('save_post', 'library_save_meta_boxes');

// AJAX handlers for book search and filtering
function library_ajax_search_books() {
    check_ajax_referer('library_nonce', 'nonce');
    
    $search = sanitize_text_field($_POST['search']);
    $category = sanitize_text_field($_POST['category']);
    $author = sanitize_text_field($_POST['author']);
    $library = sanitize_text_field($_POST['library']);
    $status = sanitize_text_field($_POST['status']);
    
    $args = array(
        'post_type' => 'book',
        'posts_per_page' => 12,
        'post_status' => 'publish',
    );
    
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    $meta_query = array();
    if (!empty($library)) {
        $meta_query[] = array(
            'key' => '_book_library',
            'value' => $library,
            'compare' => '='
        );
    }
    
    if (!empty($status)) {
        $meta_query[] = array(
            'key' => '_book_status',
            'value' => $status,
            'compare' => '='
        );
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    
    $tax_query = array();
    if (!empty($category)) {
        $tax_query[] = array(
            'taxonomy' => 'book_category',
            'field' => 'slug',
            'terms' => $category
        );
    }
    
    if (!empty($author)) {
        $tax_query[] = array(
            'taxonomy' => 'book_author',
            'field' => 'slug',
            'terms' => $author
        );
    }
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    $books = new WP_Query($args);
    
    ob_start();
    if ($books->have_posts()) {
        while ($books->have_posts()) {
            $books->the_post();
            get_template_part('template-parts/book-card');
        }
    } else {
        echo '<p class="no-books-found">' . __('No books found matching your criteria.', 'library-theme') . '</p>';
    }
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_search_books', 'library_ajax_search_books');
add_action('wp_ajax_nopriv_search_books', 'library_ajax_search_books');

// User registration and login functions
function library_handle_registration() {
    if (isset($_POST['library_register'])) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $library_id = intval($_POST['library_id']);
        
        $errors = array();
        
        if (empty($username)) {
            $errors[] = __('Username is required.', 'library-theme');
        }
        
        if (empty($email) || !is_email($email)) {
            $errors[] = __('Valid email is required.', 'library-theme');
        }
        
        if (empty($password)) {
            $errors[] = __('Password is required.', 'library-theme');
        }
        
        if (username_exists($username)) {
            $errors[] = __('Username already exists.', 'library-theme');
        }
        
        if (email_exists($email)) {
            $errors[] = __('Email already exists.', 'library-theme');
        }
        
        if (empty($errors)) {
            $user_id = wp_create_user($username, $password, $email);
            
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('library_patron');
                
                if ($library_id) {
                    update_user_meta($user_id, 'library_id', $library_id);
                }
                
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                
                wp_redirect(home_url('/dashboard/'));
                exit;
            } else {
                $errors[] = $user_id->get_error_message();
            }
        }
        
        return $errors;
    }
    
    return false;
}

// Get user's library
function library_get_user_library($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $library_id = get_user_meta($user_id, 'library_id', true);
    
    if ($library_id) {
        return get_post($library_id);
    }
    
    return false;
}

// Check if user can access book
function library_user_can_access_book($book_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return false;
    }
    
    $user_library = library_get_user_library($user_id);
    $book_library_id = get_post_meta($book_id, '_book_library', true);
    
    if ($user_library && $user_library->ID == $book_library_id) {
        return true;
    }
    
    return false;
}

// Customize login redirect
function library_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('library_admin', $user->roles) || in_array('librarian', $user->roles)) {
            return admin_url();
        } else {
            return home_url('/dashboard/');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'library_login_redirect', 10, 3);

// Add custom query vars
function library_add_query_vars($vars) {
    $vars[] = 'library_action';
    return $vars;
}
add_filter('query_vars', 'library_add_query_vars');

// Handle custom rewrite rules
function library_rewrite_rules() {
    add_rewrite_rule('^dashboard/?$', 'index.php?library_action=dashboard', 'top');
    add_rewrite_rule('^login/?$', 'index.php?library_action=login', 'top');
    add_rewrite_rule('^register/?$', 'index.php?library_action=register', 'top');
    add_rewrite_rule('^book/([^/]+)/read/?$', 'index.php?library_action=read_book&book_slug=$matches[1]', 'top');
}
add_action('init', 'library_rewrite_rules');

// Template redirect for custom pages
function library_template_redirect() {
    $action = get_query_var('library_action');
    
    switch ($action) {
        case 'dashboard':
            include get_template_directory() . '/page-dashboard.php';
            exit;
        case 'login':
            include get_template_directory() . '/page-login.php';
            exit;
        case 'register':
            include get_template_directory() . '/page-register.php';
            exit;
        case 'read_book':
            include get_template_directory() . '/page-read-book.php';
            exit;
    }
}
add_action('template_redirect', 'library_template_redirect');

// AJAX handler for borrowing books
function library_ajax_borrow_book() {
    check_ajax_referer('library_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to borrow books.', 'library-theme')));
    }
    
    $book_id = intval($_POST['book_id']);
    $user_id = get_current_user_id();
    
    // Check if user can access this book
    if (!library_user_can_access_book($book_id, $user_id)) {
        wp_send_json_error(array('message' => __('You cannot borrow books from this library.', 'library-theme')));
    }
    
    // Check if book is available
    $status = get_post_meta($book_id, '_book_status', true);
    if ($status !== 'available') {
        wp_send_json_error(array('message' => __('This book is not available for borrowing.', 'library-theme')));
    }
    
    // Update book status
    update_post_meta($book_id, '_book_status', 'borrowed');
    update_post_meta($book_id, '_borrowed_by', $user_id);
    update_post_meta($book_id, '_borrowed_date', current_time('mysql'));
    
    // Set due date (14 days from now)
    $due_date = date('Y-m-d', strtotime('+14 days'));
    update_post_meta($book_id, '_due_date_' . $user_id, $due_date);
    
    wp_send_json_success(array('message' => __('Book borrowed successfully!', 'library-theme')));
}
add_action('wp_ajax_borrow_book', 'library_ajax_borrow_book');

// AJAX handler for returning books
function library_ajax_return_book() {
    check_ajax_referer('library_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to return books.', 'library-theme')));
    }
    
    $book_id = intval($_POST['book_id']);
    $user_id = get_current_user_id();
    
    // Check if user has borrowed this book
    $borrowed_by = get_post_meta($book_id, '_borrowed_by', true);
    if ($borrowed_by != $user_id) {
        wp_send_json_error(array('message' => __('You have not borrowed this book.', 'library-theme')));
    }
    
    // Update book status
    update_post_meta($book_id, '_book_status', 'available');
    delete_post_meta($book_id, '_borrowed_by');
    delete_post_meta($book_id, '_borrowed_date');
    delete_post_meta($book_id, '_due_date_' . $user_id);
    
    // Add to return history
    $return_history = get_post_meta($book_id, '_return_history', true);
    if (!is_array($return_history)) {
        $return_history = array();
    }
    
    $return_history[] = array(
        'user_id' => $user_id,
        'return_date' => current_time('mysql')
    );
    
    update_post_meta($book_id, '_return_history', $return_history);
    
    wp_send_json_success(array('message' => __('Book returned successfully!', 'library-theme')));
}
add_action('wp_ajax_return_book', 'library_ajax_return_book');

// AJAX handler for saving reading progress
function library_ajax_save_reading_progress() {
    check_ajax_referer('save_reading_progress', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error();
    }
    
    $book_id = intval($_POST['book_id']);
    $progress = floatval($_POST['progress']);
    $reading_time = intval($_POST['reading_time']);
    $user_id = get_current_user_id();
    
    // Save reading progress
    $progress_data = array(
        'progress' => $progress,
        'reading_time' => $reading_time,
        'last_updated' => current_time('mysql')
    );
    
    update_user_meta($user_id, 'reading_progress_' . $book_id, $progress_data);
    
    wp_send_json_success();
}
add_action('wp_ajax_save_reading_progress', 'library_ajax_save_reading_progress');

// AJAX handler for loading more books
function library_ajax_load_more_books() {
    check_ajax_referer('library_nonce', 'nonce');
    
    $page = intval($_POST['page']);
    
    $args = array(
        'post_type' => 'book',
        'posts_per_page' => 12,
        'paged' => $page,
        'post_status' => 'publish'
    );
    
    $books = new WP_Query($args);
    
    ob_start();
    if ($books->have_posts()) {
        while ($books->have_posts()) {
            $books->the_post();
            get_template_part('template-parts/book-card');
        }
    }
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    if (!empty($html)) {
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_load_more_books', 'library_ajax_load_more_books');
add_action('wp_ajax_nopriv_load_more_books', 'library_ajax_load_more_books');

// AJAX handler for search suggestions
function library_ajax_search_suggestions() {
    check_ajax_referer('library_nonce', 'nonce');
    
    $query = sanitize_text_field($_POST['query']);
    $suggestions = array();
    
    // Get book titles
    $books = get_posts(array(
        'post_type' => 'book',
        's' => $query,
        'posts_per_page' => 5,
        'post_status' => 'publish'
    ));
    
    foreach ($books as $book) {
        $suggestions[] = $book->post_title;
    }
    
    // Get author names
    $authors = get_terms(array(
        'taxonomy' => 'book_author',
        'name__like' => $query,
        'number' => 3,
        'hide_empty' => false
    ));
    
    foreach ($authors as $author) {
        $suggestions[] = $author->name;
    }
    
    wp_send_json_success(array('suggestions' => array_unique($suggestions)));
}
add_action('wp_ajax_search_suggestions', 'library_ajax_search_suggestions');
add_action('wp_ajax_nopriv_search_suggestions', 'library_ajax_search_suggestions');

// Create demo data on theme activation
function library_create_demo_data() {
    // Create demo libraries
    $demo_libraries = array(
        array(
            'title' => 'Central Public Library',
            'content' => 'The main public library serving the downtown area with a comprehensive collection of books, digital resources, and community programs.',
            'meta' => array(
                '_library_address' => "123 Main Street\nDowntown, City 12345",
                '_library_phone' => '(555) 123-4567',
                '_library_email' => 'info@centrallibrary.org',
                '_library_website' => 'https://centrallibrary.org',
                '_library_hours' => "Monday - Friday: 9:00 AM - 8:00 PM\nSaturday: 10:00 AM - 6:00 PM\nSunday: 12:00 PM - 5:00 PM"
            )
        ),
        array(
            'title' => 'University Library',
            'content' => 'Academic library serving students, faculty, and researchers with specialized collections and study spaces.',
            'meta' => array(
                '_library_address' => "456 University Ave\nCampus, City 12346",
                '_library_phone' => '(555) 234-5678',
                '_library_email' => 'library@university.edu',
                '_library_website' => 'https://library.university.edu',
                '_library_hours' => "Monday - Thursday: 7:00 AM - 11:00 PM\nFriday: 7:00 AM - 8:00 PM\nSaturday: 9:00 AM - 6:00 PM\nSunday: 10:00 AM - 11:00 PM"
            )
        ),
        array(
            'title' => 'Community Branch Library',
            'content' => 'Neighborhood library focusing on family-friendly programs and local community resources.',
            'meta' => array(
                '_library_address' => "789 Oak Street\nSuburban, City 12347",
                '_library_phone' => '(555) 345-6789',
                '_library_email' => 'community@library.org',
                '_library_website' => 'https://communitylibrary.org',
                '_library_hours' => "Monday - Wednesday: 10:00 AM - 7:00 PM\nThursday - Friday: 10:00 AM - 6:00 PM\nSaturday: 10:00 AM - 4:00 PM\nSunday: Closed"
            )
        )
    );
    
    $library_ids = array();
    foreach ($demo_libraries as $library_data) {
        $library_id = wp_insert_post(array(
            'post_title' => $library_data['title'],
            'post_content' => $library_data['content'],
            'post_type' => 'library',
            'post_status' => 'publish'
        ));
        
        if ($library_id) {
            foreach ($library_data['meta'] as $key => $value) {
                update_post_meta($library_id, $key, $value);
            }
            $library_ids[] = $library_id;
        }
    }
    
    // Create demo book categories
    $categories = array('Fiction', 'Non-Fiction', 'Science', 'History', 'Biography', 'Technology', 'Art', 'Philosophy');
    $category_ids = array();
    foreach ($categories as $category) {
        $term = wp_insert_term($category, 'book_category');
        if (!is_wp_error($term)) {
            $category_ids[] = $term['term_id'];
        }
    }
    
    // Create demo authors
    $authors = array('Jane Austen', 'Mark Twain', 'Stephen King', 'Agatha Christie', 'Isaac Asimov', 'Maya Angelou', 'George Orwell', 'J.K. Rowling');
    $author_ids = array();
    foreach ($authors as $author) {
        $term = wp_insert_term($author, 'book_author');
        if (!is_wp_error($term)) {
            $author_ids[] = $term['term_id'];
        }
    }
    
    // Create demo books
    $demo_books = array(
        array(
            'title' => 'Pride and Prejudice',
            'content' => 'A classic novel about love, marriage, and social class in 19th century England.',
            'author' => 'Jane Austen',
            'category' => 'Fiction',
            'meta' => array(
                '_book_isbn' => '978-0-14-143951-8',
                '_book_publisher' => 'Penguin Classics',
                '_book_publication_date' => '1813-01-28',
                '_book_pages' => '432',
                '_book_language' => 'English',
                '_book_status' => 'available'
            )
        ),
        array(
            'title' => 'The Adventures of Tom Sawyer',
            'content' => 'The story of a young boy growing up along the Mississippi River.',
            'author' => 'Mark Twain',
            'category' => 'Fiction',
            'meta' => array(
                '_book_isbn' => '978-0-486-40077-4',
                '_book_publisher' => 'Dover Publications',
                '_book_publication_date' => '1876-06-01',
                '_book_pages' => '224',
                '_book_language' => 'English',
                '_book_status' => 'available'
            )
        ),
        array(
            'title' => 'The Shining',
            'content' => 'A psychological horror novel about a family isolated in a haunted hotel.',
            'author' => 'Stephen King',
            'category' => 'Fiction',
            'meta' => array(
                '_book_isbn' => '978-0-307-74365-9',
                '_book_publisher' => 'Anchor Books',
                '_book_publication_date' => '1977-01-28',
                '_book_pages' => '688',
                '_book_language' => 'English',
                '_book_status' => 'available'
            )
        ),
        array(
            'title' => 'Murder on the Orient Express',
            'content' => 'A classic detective novel featuring Hercule Poirot.',
            'author' => 'Agatha Christie',
            'category' => 'Fiction',
            'meta' => array(
                '_book_isbn' => '978-0-06-207350-4',
                '_book_publisher' => 'William Morrow Paperbacks',
                '_book_publication_date' => '1934-01-01',
                '_book_pages' => '256',
                '_book_language' => 'English',
                '_book_status' => 'borrowed'
            )
        ),
        array(
            'title' => 'Foundation',
            'content' => 'The first novel in the Foundation series, a science fiction epic.',
            'author' => 'Isaac Asimov',
            'category' => 'Science',
            'meta' => array(
                '_book_isbn' => '978-0-553-29335-0',
                '_book_publisher' => 'Bantam Spectra',
                '_book_publication_date' => '1951-05-01',
                '_book_pages' => '244',
                '_book_language' => 'English',
                '_book_status' => 'available'
            )
        ),
        array(
            'title' => 'I Know Why the Caged Bird Sings',
            'content' => 'An autobiographical work by Maya Angelou about her early years.',
            'author' => 'Maya Angelou',
            'category' => 'Biography',
            'meta' => array(
                '_book_isbn' => '978-0-345-51440-8',
                '_book_publisher' => 'Ballantine Books',
                '_book_publication_date' => '1969-01-01',
                '_book_pages' => '289',
                '_book_language' => 'English',
                '_book_status' => 'available'
            )
        )
    );
    
    foreach ($demo_books as $book_data) {
        $book_id = wp_insert_post(array(
            'post_title' => $book_data['title'],
            'post_content' => $book_data['content'],
            'post_type' => 'book',
            'post_status' => 'publish'
        ));
        
        if ($book_id) {
            // Add meta data
            foreach ($book_data['meta'] as $key => $value) {
                update_post_meta($book_id, $key, $value);
            }
            
            // Assign to random library
            if (!empty($library_ids)) {
                $random_library = $library_ids[array_rand($library_ids)];
                update_post_meta($book_id, '_book_library', $random_library);
            }
            
            // Assign author
            $author_term = get_term_by('name', $book_data['author'], 'book_author');
            if ($author_term) {
                wp_set_post_terms($book_id, array($author_term->term_id), 'book_author');
            }
            
            // Assign category
            $category_term = get_term_by('name', $book_data['category'], 'book_category');
            if ($category_term) {
                wp_set_post_terms($book_id, array($category_term->term_id), 'book_category');
            }
        }
    }
    
    // Create demo users
    $demo_users = array(
        array(
            'username' => 'admin',
            'email' => 'admin@library.local',
            'password' => 'admin123',
            'role' => 'library_admin',
            'display_name' => 'Library Administrator'
        ),
        array(
            'username' => 'librarian',
            'email' => 'librarian@library.local',
            'password' => 'librarian123',
            'role' => 'librarian',
            'display_name' => 'Head Librarian'
        ),
        array(
            'username' => 'patron',
            'email' => 'patron@library.local',
            'password' => 'patron123',
            'role' => 'library_patron',
            'display_name' => 'Library Patron'
        )
    );
    
    foreach ($demo_users as $user_data) {
        if (!username_exists($user_data['username']) && !email_exists($user_data['email'])) {
            $user_id = wp_create_user($user_data['username'], $user_data['password'], $user_data['email']);
            
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role($user_data['role']);
                wp_update_user(array(
                    'ID' => $user_id,
                    'display_name' => $user_data['display_name']
                ));
                
                // Assign to random library for patrons
                if ($user_data['role'] === 'library_patron' && !empty($library_ids)) {
                    $random_library = $library_ids[array_rand($library_ids)];
                    update_user_meta($user_id, 'library_id', $random_library);
                }
            }
        }
    }
}

// Flush rewrite rules on theme activation
function library_flush_rewrite_rules() {
    library_rewrite_rules();
    flush_rewrite_rules();
    
    // Create demo data only once
    if (!get_option('library_demo_data_created')) {
        library_create_demo_data();
        update_option('library_demo_data_created', true);
    }
}
add_action('after_switch_theme', 'library_flush_rewrite_rules');