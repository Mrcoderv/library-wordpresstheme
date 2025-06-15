<?php
/**
 * User Dashboard Page
 *
 * @package LibraryTheme
 */

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$user_library = library_get_user_library();
?>

<main class="site-main">
    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <h1><?php printf(__('Welcome, %s', 'library-theme'), $current_user->display_name); ?></h1>
                <p><?php _e('Manage your library account and access your books.', 'library-theme'); ?></p>
            </div>

            <?php if ($user_library): ?>
                <div class="user-library-info mb-3">
                    <h2><?php _e('Your Library', 'library-theme'); ?></h2>
                    <div class="library-card" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <h3><?php echo $user_library->post_title; ?></h3>
                        <div class="library-details">
                            <?php
                            $address = get_post_meta($user_library->ID, '_library_address', true);
                            $phone = get_post_meta($user_library->ID, '_library_phone', true);
                            $email = get_post_meta($user_library->ID, '_library_email', true);
                            $hours = get_post_meta($user_library->ID, '_library_hours', true);
                            ?>
                            
                            <?php if ($address): ?>
                                <p><strong><?php _e('Address:', 'library-theme'); ?></strong> <?php echo nl2br(esc_html($address)); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($phone): ?>
                                <p><strong><?php _e('Phone:', 'library-theme'); ?></strong> <?php echo esc_html($phone); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($email): ?>
                                <p><strong><?php _e('Email:', 'library-theme'); ?></strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
                            <?php endif; ?>
                            
                            <?php if ($hours): ?>
                                <p><strong><?php _e('Hours:', 'library-theme'); ?></strong></p>
                                <div style="white-space: pre-line; margin-left: 1rem;"><?php echo esc_html($hours); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <?php
                // Get user's borrowed books
                $borrowed_books = get_posts(array(
                    'post_type' => 'book',
                    'meta_query' => array(
                        array(
                            'key' => '_borrowed_by',
                            'value' => $current_user->ID,
                            'compare' => '='
                        )
                    ),
                    'numberposts' => -1
                ));

                // Get available books in user's library
                $available_books = 0;
                if ($user_library) {
                    $available_books_query = get_posts(array(
                        'post_type' => 'book',
                        'meta_query' => array(
                            array(
                                'key' => '_book_library',
                                'value' => $user_library->ID,
                                'compare' => '='
                            ),
                            array(
                                'key' => '_book_status',
                                'value' => 'available',
                                'compare' => '='
                            )
                        ),
                        'numberposts' => -1
                    ));
                    $available_books = count($available_books_query);
                }

                // Get reading history count
                $reading_history = get_user_meta($current_user->ID, 'reading_history', true);
                $history_count = is_array($reading_history) ? count($reading_history) : 0;
                ?>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo count($borrowed_books); ?></span>
                    <span class="stat-label"><?php _e('Currently Borrowed', 'library-theme'); ?></span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo $available_books; ?></span>
                    <span class="stat-label"><?php _e('Available Books', 'library-theme'); ?></span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo $history_count; ?></span>
                    <span class="stat-label"><?php _e('Books Read', 'library-theme'); ?></span>
                </div>
            </div>

            <!-- Currently Borrowed Books -->
            <?php if (!empty($borrowed_books)): ?>
                <section class="borrowed-books mb-3">
                    <h2><?php _e('Currently Borrowed Books', 'library-theme'); ?></h2>
                    <div class="books-grid">
                        <?php foreach ($borrowed_books as $book): ?>
                            <?php
                            setup_postdata($book);
                            $due_date = get_post_meta($book->ID, '_due_date_' . $current_user->ID, true);
                            ?>
                            <div class="book-card">
                                <?php if (has_post_thumbnail($book->ID)): ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($book->ID, 'medium'); ?>" alt="<?php echo $book->post_title; ?>" class="book-cover">
                                <?php else: ?>
                                    <div class="book-cover"></div>
                                <?php endif; ?>
                                
                                <div class="book-info">
                                    <h3 class="book-title"><?php echo $book->post_title; ?></h3>
                                    
                                    <?php if ($due_date): ?>
                                        <div class="due-date" style="color: #dc3545; font-weight: bold; margin-bottom: 0.5rem;">
                                            <?php _e('Due:', 'library-theme'); ?> <?php echo date('M j, Y', strtotime($due_date)); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="book-actions">
                                        <a href="<?php echo get_permalink($book->ID); ?>" class="btn btn-primary btn-sm">
                                            <?php _e('View Details', 'library-theme'); ?>
                                        </a>
                                        
                                        <?php
                                        $digital_file = get_post_meta($book->ID, '_book_digital_file', true);
                                        if ($digital_file):
                                        ?>
                                            <a href="<?php echo home_url('/book/' . $book->post_name . '/read/'); ?>" class="btn btn-secondary btn-sm">
                                                <?php _e('Read Online', 'library-theme'); ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-secondary btn-sm return-book" data-book-id="<?php echo $book->ID; ?>">
                                            <?php _e('Return', 'library-theme'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Recommended Books -->
            <?php if ($user_library): ?>
                <section class="recommended-books">
                    <h2><?php _e('Recommended for You', 'library-theme'); ?></h2>
                    <div class="books-grid">
                        <?php
                        $recommended_books = get_posts(array(
                            'post_type' => 'book',
                            'meta_query' => array(
                                array(
                                    'key' => '_book_library',
                                    'value' => $user_library->ID,
                                    'compare' => '='
                                ),
                                array(
                                    'key' => '_book_status',
                                    'value' => 'available',
                                    'compare' => '='
                                )
                            ),
                            'posts_per_page' => 6,
                            'orderby' => 'rand'
                        ));

                        if ($recommended_books) {
                            foreach ($recommended_books as $book) {
                                setup_postdata($book);
                                get_template_part('template-parts/book-card');
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . __('No books available at the moment.', 'library-theme') . '</p>';
                        }
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Account Settings -->
            <section class="account-settings mt-3">
                <h2><?php _e('Account Settings', 'library-theme'); ?></h2>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <form id="update-profile-form" method="post">
                        <?php wp_nonce_field('update_profile', 'update_profile_nonce'); ?>
                        
                        <div class="form-group">
                            <label for="display_name"><?php _e('Display Name', 'library-theme'); ?></label>
                            <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_email"><?php _e('Email', 'library-theme'); ?></label>
                            <input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password"><?php _e('New Password (leave blank to keep current)', 'library-theme'); ?></label>
                            <input type="password" id="new_password" name="new_password" class="form-control">
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <?php _e('Update Profile', 'library-theme'); ?>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</main>

<?php
// Handle profile update
if (isset($_POST['update_profile']) && wp_verify_nonce($_POST['update_profile_nonce'], 'update_profile')) {
    $user_data = array(
        'ID' => $current_user->ID,
        'display_name' => sanitize_text_field($_POST['display_name']),
        'user_email' => sanitize_email($_POST['user_email'])
    );
    
    if (!empty($_POST['new_password'])) {
        $user_data['user_pass'] = $_POST['new_password'];
    }
    
    $result = wp_update_user($user_data);
    
    if (!is_wp_error($result)) {
        echo '<div class="alert alert-success">' . __('Profile updated successfully!', 'library-theme') . '</div>';
    } else {
        echo '<div class="alert alert-error">' . $result->get_error_message() . '</div>';
    }
}
?>

<script>
jQuery(document).ready(function($) {
    // Handle book return
    $('.return-book').on('click', function() {
        var bookId = $(this).data('book-id');
        var button = $(this);
        
        if (confirm('<?php _e('Are you sure you want to return this book?', 'library-theme'); ?>')) {
            $.ajax({
                url: library_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'return_book',
                    book_id: bookId,
                    nonce: library_ajax.nonce
                },
                beforeSend: function() {
                    button.prop('disabled', true).text('<?php _e('Returning...', 'library-theme'); ?>');
                },
                success: function(response) {
                    if (response.success) {
                        button.closest('.book-card').fadeOut();
                        location.reload();
                    } else {
                        alert(response.data.message || '<?php _e('Error returning book.', 'library-theme'); ?>');
                        button.prop('disabled', false).text('<?php _e('Return', 'library-theme'); ?>');
                    }
                },
                error: function() {
                    alert('<?php _e('Error returning book.', 'library-theme'); ?>');
                    button.prop('disabled', false).text('<?php _e('Return', 'library-theme'); ?>');
                }
            });
        }
    });
    
    // Handle book borrowing
    $('.borrow-book').on('click', function() {
        var bookId = $(this).data('book-id');
        var button = $(this);
        
        $.ajax({
            url: library_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'borrow_book',
                book_id: bookId,
                nonce: library_ajax.nonce
            },
            beforeSend: function() {
                button.prop('disabled', true).text('<?php _e('Borrowing...', 'library-theme'); ?>');
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('Book borrowed successfully!', 'library-theme'); ?>');
                    location.reload();
                } else {
                    alert(response.data.message || '<?php _e('Error borrowing book.', 'library-theme'); ?>');
                    button.prop('disabled', false).text('<?php _e('Borrow', 'library-theme'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('Error borrowing book.', 'library-theme'); ?>');
                button.prop('disabled', false).text('<?php _e('Borrow', 'library-theme'); ?>');
            }
        });
    });
});
</script>

<?php get_footer(); ?>