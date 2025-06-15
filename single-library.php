<?php
/**
 * Single Library Template
 *
 * @package LibraryTheme
 */

get_header();

while (have_posts()) :
    the_post();
    
    $library_id = get_the_ID();
    $address = get_post_meta($library_id, '_library_address', true);
    $phone = get_post_meta($library_id, '_library_phone', true);
    $email = get_post_meta($library_id, '_library_email', true);
    $website = get_post_meta($library_id, '_library_website', true);
    $hours = get_post_meta($library_id, '_library_hours', true);
?>

<main class="site-main">
    <div class="container">
        <article class="library-single">
            <div class="library-header" style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem; margin-bottom: 2rem;">
                <div class="library-main-info">
                    <h1 class="library-title" style="font-size: 2.5rem; margin-bottom: 1rem; color: #333;">
                        <?php the_title(); ?>
                    </h1>
                    
                    <div class="library-description" style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 2rem;">
                        <?php the_content(); ?>
                    </div>
                    
                    <div class="library-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        <?php
                        // Get library statistics
                        $total_books = get_posts(array(
                            'post_type' => 'book',
                            'meta_query' => array(
                                array(
                                    'key' => '_book_library',
                                    'value' => $library_id,
                                    'compare' => '='
                                )
                            ),
                            'numberposts' => -1
                        ));
                        
                        $available_books = get_posts(array(
                            'post_type' => 'book',
                            'meta_query' => array(
                                array(
                                    'key' => '_book_library',
                                    'value' => $library_id,
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
                        
                        $registered_users = get_users(array(
                            'meta_key' => 'library_id',
                            'meta_value' => $library_id,
                            'count_total' => true
                        ));
                        ?>
                        
                        <div class="stat-card">
                            <span class="stat-number"><?php echo count($total_books); ?></span>
                            <span class="stat-label"><?php _e('Total Books', 'library-theme'); ?></span>
                        </div>
                        
                        <div class="stat-card">
                            <span class="stat-number"><?php echo count($available_books); ?></span>
                            <span class="stat-label"><?php _e('Available Now', 'library-theme'); ?></span>
                        </div>
                        
                        <div class="stat-card">
                            <span class="stat-number"><?php echo is_object($registered_users) ? $registered_users->get_total() : count($registered_users); ?></span>
                            <span class="stat-label"><?php _e('Registered Users', 'library-theme'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="library-sidebar">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="library-image" style="margin-bottom: 2rem;">
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" 
                                 style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        </div>
                    <?php endif; ?>
                    
                    <div class="library-contact" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <h3 style="margin-bottom: 1rem; color: #667eea;"><?php _e('Contact Information', 'library-theme'); ?></h3>
                        
                        <?php if ($address): ?>
                            <div class="contact-item" style="margin-bottom: 1rem;">
                                <strong style="display: block; margin-bottom: 0.25rem;"><?php _e('Address:', 'library-theme'); ?></strong>
                                <span style="white-space: pre-line;"><?php echo esc_html($address); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($phone): ?>
                            <div class="contact-item" style="margin-bottom: 1rem;">
                                <strong style="display: block; margin-bottom: 0.25rem;"><?php _e('Phone:', 'library-theme'); ?></strong>
                                <a href="tel:<?php echo esc_attr($phone); ?>" style="color: #667eea; text-decoration: none;">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($email): ?>
                            <div class="contact-item" style="margin-bottom: 1rem;">
                                <strong style="display: block; margin-bottom: 0.25rem;"><?php _e('Email:', 'library-theme'); ?></strong>
                                <a href="mailto:<?php echo esc_attr($email); ?>" style="color: #667eea; text-decoration: none;">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($website): ?>
                            <div class="contact-item" style="margin-bottom: 1rem;">
                                <strong style="display: block; margin-bottom: 0.25rem;"><?php _e('Website:', 'library-theme'); ?></strong>
                                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener" style="color: #667eea; text-decoration: none;">
                                    <?php echo esc_html($website); ?> ↗
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($hours): ?>
                            <div class="contact-item">
                                <strong style="display: block; margin-bottom: 0.5rem;"><?php _e('Hours:', 'library-theme'); ?></strong>
                                <div style="white-space: pre-line; font-size: 0.9rem; line-height: 1.4;">
                                    <?php echo esc_html($hours); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!is_user_logged_in()): ?>
                            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
                                <a href="<?php echo home_url('/register/'); ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                                    <?php _e('Join This Library', 'library-theme'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Library Books -->
            <section class="library-books">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2><?php _e('Books Available at This Library', 'library-theme'); ?></h2>
                    
                    <div class="book-filters" style="display: flex; gap: 1rem;">
                        <select id="category-filter" class="form-control" style="width: auto;">
                            <option value=""><?php _e('All Categories', 'library-theme'); ?></option>
                            <?php
                            $categories = get_terms(array(
                                'taxonomy' => 'book_category',
                                'hide_empty' => true,
                            ));
                            foreach ($categories as $category) {
                                echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
                            }
                            ?>
                        </select>
                        
                        <select id="status-filter" class="form-control" style="width: auto;">
                            <option value=""><?php _e('All Status', 'library-theme'); ?></option>
                            <option value="available"><?php _e('Available', 'library-theme'); ?></option>
                            <option value="borrowed"><?php _e('Borrowed', 'library-theme'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div id="library-books-grid" class="books-grid">
                    <?php
                    $library_books = get_posts(array(
                        'post_type' => 'book',
                        'meta_query' => array(
                            array(
                                'key' => '_book_library',
                                'value' => $library_id,
                                'compare' => '='
                            )
                        ),
                        'posts_per_page' => 12,
                        'post_status' => 'publish'
                    ));
                    
                    if ($library_books) {
                        foreach ($library_books as $book) {
                            setup_postdata($book);
                            get_template_part('template-parts/book-card');
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<div class="no-books-found text-center" style="grid-column: 1 / -1;">';
                        echo '<h3>' . __('No books available', 'library-theme') . '</h3>';
                        echo '<p>' . __('This library has not added any books to the system yet.', 'library-theme') . '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <?php if (count($library_books) >= 12): ?>
                    <div class="text-center mt-3">
                        <button id="load-more-library-books" class="btn btn-secondary" data-library-id="<?php echo $library_id; ?>">
                            <?php _e('Load More Books', 'library-theme'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Library Categories -->
            <?php
            $library_categories = get_terms(array(
                'taxonomy' => 'book_category',
                'hide_empty' => true,
                'meta_query' => array(
                    array(
                        'key' => 'library_id',
                        'value' => $library_id,
                        'compare' => '='
                    )
                )
            ));
            
            if (!empty($library_categories)):
            ?>
                <section class="library-categories mt-3">
                    <h2 style="margin-bottom: 2rem;"><?php _e('Browse by Category', 'library-theme'); ?></h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <?php foreach ($library_categories as $category): ?>
                            <a href="<?php echo get_term_link($category); ?>" 
                               class="category-card" 
                               style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: inherit; transition: transform 0.3s;">
                                <h3 style="margin-bottom: 0.5rem; color: #667eea;"><?php echo $category->name; ?></h3>
                                <p style="color: #666; margin: 0; font-size: 0.9rem;">
                                    <?php printf(_n('%d book', '%d books', $category->count, 'library-theme'), $category->count); ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </article>
    </div>
</main>

<script>
jQuery(document).ready(function($) {
    let libraryBooksPage = 1;
    
    // Filter library books
    $('#category-filter, #status-filter').on('change', function() {
        filterLibraryBooks();
    });
    
    function filterLibraryBooks() {
        const category = $('#category-filter').val();
        const status = $('#status-filter').val();
        const libraryId = <?php echo $library_id; ?>;
        
        $.ajax({
            url: library_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_library_books',
                library_id: libraryId,
                category: category,
                status: status,
                nonce: library_ajax.nonce
            },
            beforeSend: function() {
                $('#library-books-grid').html('<div class="text-center"><div class="loading"></div><p><?php _e('Loading books...', 'library-theme'); ?></p></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#library-books-grid').html(response.data.html);
                    // Re-initialize book actions
                    initializeBookActions();
                }
            }
        });
    }
    
    // Load more library books
    $('#load-more-library-books').on('click', function() {
        const button = $(this);
        const libraryId = button.data('library-id');
        libraryBooksPage++;
        
        $.ajax({
            url: library_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_library_books',
                library_id: libraryId,
                page: libraryBooksPage,
                nonce: library_ajax.nonce
            },
            beforeSend: function() {
                button.prop('disabled', true).text('<?php _e('Loading...', 'library-theme'); ?>');
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $('#library-books-grid').append(response.data.html);
                    initializeBookActions();
                    button.prop('disabled', false).text('<?php _e('Load More Books', 'library-theme'); ?>');
                } else {
                    button.hide();
                }
            },
            error: function() {
                button.prop('disabled', false).text('<?php _e('Load More Books', 'library-theme'); ?>');
                libraryBooksPage--;
            }
        });
    });
    
    // Category card hover effects
    $('.category-card').hover(
        function() {
            $(this).css('transform', 'translateY(-5px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
});
</script>

<style>
@media (max-width: 768px) {
    .library-header {
        grid-template-columns: 1fr !important;
    }
    
    .library-sidebar {
        order: -1;
    }
    
    .library-stats {
        grid-template-columns: 1fr 1fr !important;
    }
    
    .book-filters {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .book-filters select {
        width: 100% !important;
    }
}

.category-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}
</style>

<?php
endwhile;

get_footer();
?>