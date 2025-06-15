<?php
/**
 * Template part for displaying book cards
 *
 * @package LibraryTheme
 */

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
$author_names = array();
if ($authors && !is_wp_error($authors)) {
    foreach ($authors as $author) {
        $author_names[] = $author->name;
    }
}

// Get categories
$categories = get_the_terms($book_id, 'book_category');
$category_names = array();
if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        $category_names[] = $category->name;
    }
}

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

<div class="book-card" data-book-id="<?php echo $book_id; ?>">
    <?php if (has_post_thumbnail()): ?>
        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" class="book-cover">
    <?php else: ?>
        <div class="book-cover">
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(45deg, #f0f0f0, #e0e0e0); color: #999; font-size: 3rem;">
                📚
            </div>
        </div>
    <?php endif; ?>
    
    <div class="book-info">
        <h3 class="book-title">
            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                <?php the_title(); ?>
            </a>
        </h3>
        
        <?php if (!empty($author_names)): ?>
            <div class="book-author">
                <?php _e('by', 'library-theme'); ?> <?php echo implode(', ', $author_names); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($category_names)): ?>
            <div class="book-categories" style="font-size: 0.8rem; color: #888; margin-bottom: 0.5rem;">
                <?php echo implode(', ', $category_names); ?>
            </div>
        <?php endif; ?>
        
        <div class="book-description">
            <?php echo wp_trim_words(get_the_content(), 15); ?>
        </div>
        
        <?php if ($library): ?>
            <div class="book-library" style="font-size: 0.8rem; color: #666; margin-bottom: 0.5rem;">
                <strong><?php _e('Library:', 'library-theme'); ?></strong> <?php echo $library->post_title; ?>
            </div>
        <?php endif; ?>
        
        <div class="book-meta">
            <div class="book-details">
                <?php if ($pages): ?>
                    <span class="pages"><?php echo $pages; ?> <?php _e('pages', 'library-theme'); ?></span>
                <?php endif; ?>
                
                <?php if ($publication_date): ?>
                    <span class="pub-date"><?php echo date('Y', strtotime($publication_date)); ?></span>
                <?php endif; ?>
            </div>
            
            <span class="book-status <?php echo $status_class; ?>">
                <?php echo $status_text; ?>
            </span>
        </div>
        
        <div class="book-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                <?php _e('View Details', 'library-theme'); ?>
            </a>
            
            <?php if (is_user_logged_in() && $status === 'available'): ?>
                <?php if (library_user_can_access_book($book_id)): ?>
                    <?php if ($digital_file): ?>
                        <a href="<?php echo home_url('/book/' . get_post_field('post_name', $book_id) . '/read/'); ?>" 
                           class="btn btn-secondary btn-sm">
                            <?php _e('Read Online', 'library-theme'); ?>
                        </a>
                    <?php endif; ?>
                    <button class="btn btn-secondary btn-sm borrow-book" data-book-id="<?php echo $book_id; ?>">
                        <?php _e('Borrow', 'library-theme'); ?>
                    </button>
                <?php else: ?>
                    <span class="btn btn-secondary btn-sm" style="opacity: 0.6; cursor: not-allowed;">
                        <?php _e('Different Library', 'library-theme'); ?>
                    </span>
                <?php endif; ?>
            <?php elseif (!is_user_logged_in()): ?>
                <a href="<?php echo home_url('/login/'); ?>" class="btn btn-secondary btn-sm">
                    <?php _e('Login to Borrow', 'library-theme'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.book-actions .btn {
    flex: 1;
    min-width: 80px;
    text-align: center;
}

.book-card:hover .book-actions {
    opacity: 1;
}

.book-actions {
    opacity: 0.8;
    transition: opacity 0.3s;
}
</style>