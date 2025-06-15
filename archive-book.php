<?php
/**
 * Archive template for books
 *
 * @package LibraryTheme
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <header class="archive-header text-center mb-3">
            <h1 class="archive-title"><?php _e('Book Collection', 'library-theme'); ?></h1>
            <p class="archive-description">
                <?php _e('Browse our complete collection of books available across all partner libraries.', 'library-theme'); ?>
            </p>
        </header>

        <!-- Search and Filters -->
        <section class="search-filters">
            <form id="book-search-form" class="search-form">
                <div class="form-group">
                    <label for="search"><?php _e('Search Books', 'library-theme'); ?></label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="<?php _e('Enter book title, author, or keyword...', 'library-theme'); ?>"
                           value="<?php echo get_search_query(); ?>">
                </div>
                
                <div class="form-group">
                    <label for="category"><?php _e('Category', 'library-theme'); ?></label>
                    <select id="category" name="category" class="form-control">
                        <option value=""><?php _e('All Categories', 'library-theme'); ?></option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'book_category',
                            'hide_empty' => false,
                        ));
                        foreach ($categories as $category) {
                            echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="author"><?php _e('Author', 'library-theme'); ?></label>
                    <select id="author" name="author" class="form-control">
                        <option value=""><?php _e('All Authors', 'library-theme'); ?></option>
                        <?php
                        $authors = get_terms(array(
                            'taxonomy' => 'book_author',
                            'hide_empty' => false,
                        ));
                        foreach ($authors as $author) {
                            echo '<option value="' . $author->slug . '">' . $author->name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="library"><?php _e('Library', 'library-theme'); ?></label>
                    <select id="library" name="library" class="form-control">
                        <option value=""><?php _e('All Libraries', 'library-theme'); ?></option>
                        <?php
                        $libraries = get_posts(array(
                            'post_type' => 'library',
                            'numberposts' => -1,
                            'post_status' => 'publish'
                        ));
                        foreach ($libraries as $library) {
                            echo '<option value="' . $library->ID . '">' . $library->post_title . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status"><?php _e('Status', 'library-theme'); ?></label>
                    <select id="status" name="status" class="form-control">
                        <option value=""><?php _e('All Status', 'library-theme'); ?></option>
                        <option value="available"><?php _e('Available', 'library-theme'); ?></option>
                        <option value="borrowed"><?php _e('Borrowed', 'library-theme'); ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?php _e('Search', 'library-theme'); ?></button>
                </div>
            </form>
        </section>

        <!-- Results Summary -->
        <div class="results-summary mb-2">
            <?php
            global $wp_query;
            $total_books = $wp_query->found_posts;
            printf(
                _n(
                    'Showing %d book',
                    'Showing %d books',
                    $total_books,
                    'library-theme'
                ),
                $total_books
            );
            ?>
        </div>

        <!-- Books Grid -->
        <section class="books-section">
            <div id="books-grid" class="books-grid">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/book-card'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="no-books-found text-center" style="grid-column: 1 / -1;">
                        <h3><?php _e('No books found', 'library-theme'); ?></h3>
                        <p><?php _e('Try adjusting your search criteria or browse all books.', 'library-theme'); ?></p>
                        <a href="<?php echo get_post_type_archive_link('book'); ?>" class="btn btn-primary">
                            <?php _e('View All Books', 'library-theme'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div id="loading" class="text-center mt-3" style="display: none;">
                <div class="loading"></div>
                <p><?php _e('Loading books...', 'library-theme'); ?></p>
            </div>
        </section>

        <!-- Pagination -->
        <?php
        the_posts_pagination(array(
            'prev_text' => __('← Previous', 'library-theme'),
            'next_text' => __('Next →', 'library-theme'),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'library-theme') . ' </span>',
        ));
        ?>
    </div>
</main>

<?php get_footer(); ?>