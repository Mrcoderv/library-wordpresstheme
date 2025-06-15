<?php
/**
 * The template for displaying the footer
 *
 * @package LibraryTheme
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3><?php bloginfo('name'); ?></h3>
                    <p><?php bloginfo('description'); ?></p>
                    
                    <?php if (has_nav_menu('footer')): ?>
                        <nav class="footer-navigation">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer',
                                'menu_class'     => 'footer-menu',
                                'container'      => false,
                                'depth'          => 1,
                            ));
                            ?>
                        </nav>
                    <?php endif; ?>
                </div>
                
                <div class="footer-stats">
                    <?php
                    // Display some library statistics
                    $total_books = wp_count_posts('book');
                    $total_libraries = wp_count_posts('library');
                    $total_users = count_users();
                    ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_books->publish; ?></span>
                        <span class="stat-label"><?php _e('Books Available', 'library-theme'); ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_libraries->publish; ?></span>
                        <span class="stat-label"><?php _e('Partner Libraries', 'library-theme'); ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_users['total_users']; ?></span>
                        <span class="stat-label"><?php _e('Registered Users', 'library-theme'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'library-theme'); ?></p>
                <p><?php _e('Powered by Live Library Management Theme', 'library-theme'); ?></p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

<style>
.footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-info h3 {
    margin-bottom: 1rem;
    color: white;
}

.footer-navigation {
    margin-top: 1rem;
}

.footer-menu {
    display: flex;
    list-style: none;
    gap: 1rem;
    flex-wrap: wrap;
}

.footer-menu a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-menu a:hover {
    color: white;
}

.footer-stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 5px;
}

.stat-item .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
}

.stat-item .stat-label {
    font-size: 0.8rem;
    color: #ccc;
}

.footer-bottom {
    border-top: 1px solid #555;
    padding-top: 1rem;
    text-align: center;
    color: #ccc;
}

.footer-bottom p {
    margin: 0.25rem 0;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .footer-menu {
        justify-content: center;
    }
    
    .footer-stats {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>

</body>
</html>