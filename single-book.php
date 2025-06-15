<?php
/**
 * Single Book Template
 *
 * @package LibraryTheme
 */

get_header();

while (have_posts()) :
    the_post();
    
    $book_id = get_the_ID();
    $isbn = get_post_meta($book_id, '_book_isbn', true);
    $publisher = get_post_meta($book_id, '_book_publisher', true);
    $publication_date = get_post_meta($book_id, '_book_publication_date', true);
    $pages = get_post_meta($book_id, '_book_pages', true);
    $language = get_post_meta($book_id, '_book_language', true);
    $status = get_post_meta($book_id, '_book_status', true);
    $library_id = get_post_meta($book_id, '_book_library', true);
    $digital_file = get_post_meta($book_id, '_book_digital_file', true);
    
    // Get library info
    $library = $library_id ? get_post($library_id) : null;
    
    // Get authors
    $authors = get_the_terms($book_id, 'book_author');
    
    // Get categories
    $categories = get_the_terms($book_id, 'book_category');
    
    $status_class = $status ? 'status-' . $status : 'status-unknown';
    $status_text = '';
    switch ($status) {
        case 'available':
            $status_text = __('Available', 'library-theme');
            break;
        case 'borrowed':
            $status_text = __('Borrowed', 'library-theme');
            break;
        case 'maintenance':
            $status_text = __('Under Maintenance', 'library-theme');
            break;
        default:
            $status_text = __('Unknown', 'library-theme');
    }
?>

<main class="site-main">
    <div class="container">
        <article class="book-single">
            <div class="book-header" style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem; margin-bottom: 2rem;">
                <div class="book-cover-large">
                    <?php if (has_post_thumbnail()): ?>
                        <img src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title(); ?>" 
                             style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
                    <?php else: ?>
                        <div style="width: 100%; height: 400px; background: linear-gradient(45deg, #f0f0f0, #e0e0e0); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #999;">
                            📚
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="book-details">
                    <h1 class="book-title" style="font-size: 2.5rem; margin-bottom: 1rem; color: #333;">
                        <?php the_title(); ?>
                    </h1>
                    
                    <?php if ($authors && !is_wp_error($authors)): ?>
                        <div class="book-authors" style="font-size: 1.2rem; color: #666; margin-bottom: 1rem;">
                            <?php _e('by', 'library-theme'); ?> 
                            <?php
                            $author_links = array();
                            foreach ($authors as $author) {
                                $author_links[] = '<a href="' . get_term_link($author) . '" style="color: #667eea; text-decoration: none;">' . $author->name . '</a>';
                            }
                            echo implode(', ', $author_links);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($categories && !is_wp_error($categories)): ?>
                        <div class="book-categories" style="margin-bottom: 1rem;">
                            <?php
                            foreach ($categories as $category) {
                                echo '<span style="display: inline-block; background: #667eea; color: white; padding: 0.25rem 0.5rem; border-radius: 15px; font-size: 0.8rem; margin-right: 0.5rem;">' . $category->name . '</span>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="book-status-large" style="margin-bottom: 1.5rem;">
                        <span class="book-status <?php echo $status_class; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?php echo $status_text; ?>
                        </span>
                    </div>
                    
                    <div class="book-meta-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        <?php if ($isbn): ?>
                            <div class="meta-item">
                                <strong><?php _e('ISBN:', 'library-theme'); ?></strong><br>
                                <span><?php echo esc_html($isbn); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($publisher): ?>
                            <div class="meta-item">
                                <strong><?php _e('Publisher:', 'library-theme'); ?></strong><br>
                                <span><?php echo esc_html($publisher); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($publication_date): ?>
                            <div class="meta-item">
                                <strong><?php _e('Published:', 'library-theme'); ?></strong><br>
                                <span><?php echo date('F j, Y', strtotime($publication_date)); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($pages): ?>
                            <div class="meta-item">
                                <strong><?php _e('Pages:', 'library-theme'); ?></strong><br>
                                <span><?php echo esc_html($pages); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($language): ?>
                            <div class="meta-item">
                                <strong><?php _e('Language:', 'library-theme'); ?></strong><br>
                                <span><?php echo esc_html($language); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($library): ?>
                        <div class="book-library-info" style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                            <h3 style="margin-bottom: 0.5rem;"><?php _e('Available at:', 'library-theme'); ?></h3>
                            <a href="<?php echo get_permalink($library->ID); ?>" style="color: #667eea; text-decoration: none; font-weight: bold;">
                                <?php echo $library->post_title; ?>
                            </a>
                            <?php
                            $library_address = get_post_meta($library->ID, '_library_address', true);
                            if ($library_address) {
                                echo '<br><small style="color: #666;">' . esc_html($library_address) . '</small>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="book-actions" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <?php if (is_user_logged_in() && $status === 'available'): ?>
                            <?php if (library_user_can_access_book($book_id)): ?>
                                <?php if ($digital_file): ?>
                                    <a href="<?php echo home_url('/book/' . get_post_field('post_name', $book_id) . '/read/'); ?>" 
                                       class="btn btn-primary">
                                        📖 <?php _e('Read Online', 'library-theme'); ?>
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-secondary borrow-book" data-book-id="<?php echo $book_id; ?>">
                                    📚 <?php _e('Borrow Book', 'library-theme'); ?>
                                </button>
                            <?php else: ?>
                                <span class="btn btn-secondary" style="opacity: 0.6; cursor: not-allowed;">
                                    🚫 <?php _e('Different Library', 'library-theme'); ?>
                                </span>
                            <?php endif; ?>
                        <?php elseif (!is_user_logged_in()): ?>
                            <a href="<?php echo home_url('/login/'); ?>" class="btn btn-primary">
                                🔐 <?php _e('Login to Borrow', 'library-theme'); ?>
                            </a>
                        <?php elseif ($status === 'borrowed'): ?>
                            <span class="btn btn-secondary" style="opacity: 0.6; cursor: not-allowed;">
                                ⏰ <?php _e('Currently Borrowed', 'library-theme'); ?>
                            </span>
                        <?php endif; ?>
                        
                        <button class="btn btn-secondary" onclick="window.print()">
                            🖨️ <?php _e('Print Details', 'library-theme'); ?>
                        </button>
                        
                        <button class="btn btn-secondary" onclick="shareBook()">
                            📤 <?php _e('Share', 'library-theme'); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="book-content" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 1rem;"><?php _e('Description', 'library-theme'); ?></h2>
                <div class="book-description" style="line-height: 1.8; font-size: 1.1rem;">
                    <?php the_content(); ?>
                </div>
            </div>
            
            <!-- Related Books -->
            <?php
            $related_books = array();
            
            // Get books by same author
            if ($authors && !is_wp_error($authors)) {
                $author_ids = wp_list_pluck($authors, 'term_id');
                $related_books = get_posts(array(
                    'post_type' => 'book',
                    'posts_per_page' => 6,
                    'post__not_in' => array($book_id),
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'book_author',
                            'field' => 'term_id',
                            'terms' => $author_ids
                        )
                    )
                ));
            }
            
            // If no books by same author, get books from same category
            if (empty($related_books) && $categories && !is_wp_error($categories)) {
                $category_ids = wp_list_pluck($categories, 'term_id');
                $related_books = get_posts(array(
                    'post_type' => 'book',
                    'posts_per_page' => 6,
                    'post__not_in' => array($book_id),
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'book_category',
                            'field' => 'term_id',
                            'terms' => $category_ids
                        )
                    )
                ));
            }
            
            // If still no books, get random books from same library
            if (empty($related_books) && $library_id) {
                $related_books = get_posts(array(
                    'post_type' => 'book',
                    'posts_per_page' => 6,
                    'post__not_in' => array($book_id),
                    'meta_query' => array(
                        array(
                            'key' => '_book_library',
                            'value' => $library_id,
                            'compare' => '='
                        )
                    ),
                    'orderby' => 'rand'
                ));
            }
            
            if (!empty($related_books)):
            ?>
                <section class="related-books mt-3">
                    <h2 style="margin-bottom: 2rem;"><?php _e('You Might Also Like', 'library-theme'); ?></h2>
                    <div class="books-grid">
                        <?php
                        foreach ($related_books as $related_book) {
                            setup_postdata($related_book);
                            get_template_part('template-parts/book-card');
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </section>
            <?php endif; ?>
        </article>
    </div>
</main>

<script>
function shareBook() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo esc_js(get_the_title()); ?>',
            text: '<?php echo esc_js(wp_trim_words(get_the_content(), 20)); ?>',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const url = window.location.href;
        const title = '<?php echo esc_js(get_the_title()); ?>';
        
        // Copy to clipboard
        navigator.clipboard.writeText(url).then(function() {
            alert('<?php _e('Book URL copied to clipboard!', 'library-theme'); ?>');
        }).catch(function() {
            // Fallback: show share options
            const shareText = `Check out this book: ${title} - ${url}`;
            const shareWindow = window.open('', '_blank', 'width=600,height=400');
            shareWindow.document.write(`
                <h3>Share this book</h3>
                <p>Copy and share this link:</p>
                <input type="text" value="${url}" style="width: 100%; padding: 10px;" readonly onclick="this.select()">
                <br><br>
                <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}" target="_blank">Share on Twitter</a><br>
                <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" target="_blank">Share on Facebook</a><br>
                <a href="mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(shareText)}" target="_blank">Share via Email</a>
            `);
        });
    }
}

// Print styles
const printStyles = `
@media print {
    .site-header, .site-footer, .book-actions, .related-books {
        display: none !important;
    }
    
    .book-header {
        grid-template-columns: 200px 1fr !important;
    }
    
    .book-cover-large img {
        max-height: 300px !important;
    }
    
    body {
        font-size: 12pt !important;
        line-height: 1.4 !important;
    }
    
    .book-title {
        font-size: 18pt !important;
    }
    
    .book-content {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
`;

const style = document.createElement('style');
style.textContent = printStyles;
document.head.appendChild(style);
</script>

<style>
@media (max-width: 768px) {
    .book-header {
        grid-template-columns: 1fr !important;
        text-align: center;
    }
    
    .book-cover-large {
        max-width: 250px;
        margin: 0 auto;
    }
    
    .book-meta-grid {
        grid-template-columns: 1fr 1fr !important;
    }
    
    .book-actions {
        justify-content: center;
    }
}
</style>

<?php
endwhile;

get_footer();
?>