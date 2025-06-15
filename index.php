<?php
/**
 * The main template file
 *
 * @package LibraryTheme
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <?php if (is_home() || is_front_page()): ?>
            <!-- Hero Section -->
            <section class="hero-section text-center mb-3">
                <h1><?php bloginfo('name'); ?></h1>
                <p class="lead"><?php bloginfo('description'); ?></p>
                <?php if (!is_user_logged_in()): ?>
                    <div class="hero-actions mt-2">
                        <a href="<?php echo home_url('/register/'); ?>" class="btn btn-primary"><?php _e('Join Library', 'library-theme'); ?></a>
                        <a href="<?php echo home_url('/login/'); ?>" class="btn btn-secondary"><?php _e('Login', 'library-theme'); ?></a>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Search and Filters -->
            <section class="search-filters">
                <form id="book-search-form" class="search-form">
                    <div class="form-group">
                        <label for="search"><?php _e('Search Books', 'library-theme'); ?></label>
                        <input type="text" id="search" name="search" class="form-control" placeholder="<?php _e('Enter book title, author, or keyword...', 'library-theme'); ?>">
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

            <!-- Books Grid -->
            <section class="books-section">
                <div id="books-grid" class="books-grid">
                    <?php
                    $books_query = new WP_Query(array(
                        'post_type' => 'book',
                        'posts_per_page' => 12,
                        'post_status' => 'publish'
                    ));
                    
                    if ($books_query->have_posts()) {
                        while ($books_query->have_posts()) {
                            $books_query->the_post();
                            get_template_part('template-parts/book-card');
                        }
                    } else {
                        echo '<p class="no-books-found text-center">' . __('No books found. Please check back later.', 'library-theme') . '</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
                
                <div id="loading" class="text-center mt-3" style="display: none;">
                    <div class="loading"></div>
                    <p><?php _e('Loading books...', 'library-theme'); ?></p>
                </div>
            </section>

            <!-- Featured Libraries -->
            <section class="libraries-section mt-3">
                <h2 class="text-center mb-2"><?php _e('Our Partner Libraries', 'library-theme'); ?></h2>
                <div class="books-grid">
                    <?php
                    $libraries_query = new WP_Query(array(
                        'post_type' => 'library',
                        'posts_per_page' => 6,
                        'post_status' => 'publish'
                    ));
                    
                    if ($libraries_query->have_posts()) {
                        while ($libraries_query->have_posts()) {
                            $libraries_query->the_post();
                            ?>
                            <div class="book-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" class="book-cover">
                                <?php else: ?>
                                    <div class="book-cover"></div>
                                <?php endif; ?>
                                
                                <div class="book-info">
                                    <h3 class="book-title"><?php the_title(); ?></h3>
                                    <div class="book-description">
                                        <?php echo wp_trim_words(get_the_content(), 20); ?>
                                    </div>
                                    
                                    <div class="book-meta">
                                        <?php
                                        $address = get_post_meta(get_the_ID(), '_library_address', true);
                                        if ($address) {
                                            echo '<span>' . esc_html($address) . '</span>';
                                        }
                                        ?>
                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm"><?php _e('View Details', 'library-theme'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </section>

        <?php else: ?>
            <!-- Default WordPress loop for other pages -->
            <?php if (have_posts()): ?>
                <div class="posts-container">
                    <?php while (have_posts()): the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-article'); ?>>
                            <header class="entry-header">
                                <h1 class="entry-title"><?php the_title(); ?></h1>
                                <?php if (get_post_type() === 'post'): ?>
                                    <div class="entry-meta">
                                        <span class="posted-on"><?php echo get_the_date(); ?></span>
                                        <span class="byline"><?php _e('by', 'library-theme'); ?> <?php the_author(); ?></span>
                                    </div>
                                <?php endif; ?>
                            </header>

                            <div class="entry-content">
                                <?php
                                if (is_singular()) {
                                    the_content();
                                } else {
                                    the_excerpt();
                                }
                                ?>
                            </div>

                            <?php if (!is_singular()): ?>
                                <footer class="entry-footer">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php _e('Read More', 'library-theme'); ?></a>
                                </footer>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; ?>

                    <?php
                    // Pagination
                    the_posts_pagination(array(
                        'prev_text' => __('Previous', 'library-theme'),
                        'next_text' => __('Next', 'library-theme'),
                    ));
                    ?>
                </div>
            <?php else: ?>
                <div class="no-content">
                    <h2><?php _e('Nothing Found', 'library-theme'); ?></h2>
                    <p><?php _e('It looks like nothing was found at this location. Maybe try a search?', 'library-theme'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>