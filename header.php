<?php
/**
 * The header for our theme
 *
 * @package LibraryTheme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="site-branding">
                    <?php if (has_custom_logo()): ?>
                        <div class="site-logo">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="site-title-group">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title" rel="home">
                            <?php bloginfo('name'); ?>
                        </a>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()):
                        ?>
                            <p class="site-description"><?php echo $description; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'fallback_cb'    => 'library_default_menu',
                    ));
                    ?>
                </nav>

                <div class="user-actions">
                    <?php if (is_user_logged_in()): ?>
                        <?php $current_user = wp_get_current_user(); ?>
                        <span class="user-welcome">
                            <?php printf(__('Welcome, %s', 'library-theme'), $current_user->display_name); ?>
                        </span>
                        <a href="<?php echo home_url('/dashboard/'); ?>" class="btn btn-secondary">
                            <?php _e('Dashboard', 'library-theme'); ?>
                        </a>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-secondary">
                            <?php _e('Logout', 'library-theme'); ?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo home_url('/login/'); ?>" class="btn btn-secondary">
                            <?php _e('Login', 'library-theme'); ?>
                        </a>
                        <a href="<?php echo home_url('/register/'); ?>" class="btn btn-primary">
                            <?php _e('Register', 'library-theme'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">

<?php
// Default menu fallback
function library_default_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . home_url('/') . '">' . __('Home', 'library-theme') . '</a></li>';
    echo '<li><a href="' . home_url('/books/') . '">' . __('Books', 'library-theme') . '</a></li>';
    echo '<li><a href="' . home_url('/libraries/') . '">' . __('Libraries', 'library-theme') . '</a></li>';
    if (is_user_logged_in()) {
        echo '<li><a href="' . home_url('/dashboard/') . '">' . __('Dashboard', 'library-theme') . '</a></li>';
    }
    echo '</ul>';
}
?>