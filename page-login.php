<?php
/**
 * Login Page
 *
 * @package LibraryTheme
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard/'));
    exit;
}

get_header();

$errors = array();
$success_message = '';

// Handle login form submission
if (isset($_POST['library_login'])) {
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($username)) {
        $errors[] = __('Username is required.', 'library-theme');
    }
    
    if (empty($password)) {
        $errors[] = __('Password is required.', 'library-theme');
    }
    
    if (empty($errors)) {
        $creds = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember
        );
        
        $user = wp_signon($creds, false);
        
        if (is_wp_error($user)) {
            $errors[] = $user->get_error_message();
        } else {
            wp_redirect(home_url('/dashboard/'));
            exit;
        }
    }
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    $user_login = sanitize_text_field($_POST['user_login']);
    
    if (empty($user_login)) {
        $errors[] = __('Username or email is required.', 'library-theme');
    } else {
        $user_data = get_user_by('login', $user_login);
        if (!$user_data) {
            $user_data = get_user_by('email', $user_login);
        }
        
        if ($user_data) {
            $reset_key = get_password_reset_key($user_data);
            if (!is_wp_error($reset_key)) {
                $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user_data->user_login), 'login');
                
                $message = __('Someone has requested a password reset for the following account:', 'library-theme') . "\r\n\r\n";
                $message .= sprintf(__('Username: %s', 'library-theme'), $user_data->user_login) . "\r\n\r\n";
                $message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'library-theme') . "\r\n\r\n";
                $message .= __('To reset your password, visit the following address:', 'library-theme') . "\r\n\r\n";
                $message .= $reset_url . "\r\n";
                
                $title = sprintf(__('[%s] Password Reset', 'library-theme'), get_bloginfo('name'));
                
                if (wp_mail($user_data->user_email, $title, $message)) {
                    $success_message = __('Password reset email sent. Please check your email.', 'library-theme');
                } else {
                    $errors[] = __('Failed to send password reset email.', 'library-theme');
                }
            } else {
                $errors[] = __('Failed to generate password reset key.', 'library-theme');
            }
        } else {
            $errors[] = __('No user found with that username or email.', 'library-theme');
        }
    }
}
?>

<main class="site-main">
    <div class="container">
        <div class="login-page" style="max-width: 500px; margin: 2rem auto;">
            <div class="login-form-container" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h1 class="text-center mb-2"><?php _e('Login to Your Account', 'library-theme'); ?></h1>
                <p class="text-center mb-3" style="color: #666;">
                    <?php _e('Access your library account and manage your books.', 'library-theme'); ?>
                </p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo esc_html($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <p><?php echo esc_html($success_message); ?></p>
                    </div>
                <?php endif; ?>

                <div class="login-tabs" style="margin-bottom: 2rem;">
                    <button class="tab-button active" data-tab="login" style="padding: 0.5rem 1rem; border: none; background: #667eea; color: white; border-radius: 5px 5px 0 0; cursor: pointer;">
                        <?php _e('Login', 'library-theme'); ?>
                    </button>
                    <button class="tab-button" data-tab="reset" style="padding: 0.5rem 1rem; border: none; background: #f8f9fa; color: #333; border-radius: 5px 5px 0 0; cursor: pointer; margin-left: 5px;">
                        <?php _e('Reset Password', 'library-theme'); ?>
                    </button>
                </div>

                <!-- Login Form -->
                <div id="login-tab" class="tab-content active">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="username"><?php _e('Username or Email', 'library-theme'); ?></label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password"><?php _e('Password', 'library-theme'); ?></label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" id="remember" name="remember" value="1">
                            <label for="remember" style="margin: 0;"><?php _e('Remember me', 'library-theme'); ?></label>
                        </div>

                        <button type="submit" name="library_login" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            <?php _e('Login', 'library-theme'); ?>
                        </button>
                    </form>
                </div>

                <!-- Password Reset Form -->
                <div id="reset-tab" class="tab-content" style="display: none;">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="user_login"><?php _e('Username or Email', 'library-theme'); ?></label>
                            <input type="text" id="user_login" name="user_login" class="form-control" 
                                   placeholder="<?php _e('Enter your username or email address', 'library-theme'); ?>" required>
                        </div>

                        <button type="submit" name="reset_password" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            <?php _e('Send Reset Email', 'library-theme'); ?>
                        </button>
                    </form>
                </div>

                <div class="login-footer text-center mt-3" style="padding-top: 1rem; border-top: 1px solid #e9ecef;">
                    <p><?php _e("Don't have an account?", 'library-theme'); ?> 
                       <a href="<?php echo home_url('/register/'); ?>" style="color: #667eea; text-decoration: none;">
                           <?php _e('Register here', 'library-theme'); ?>
                       </a>
                    </p>
                    <p><a href="<?php echo home_url('/'); ?>" style="color: #666; text-decoration: none;">
                        ← <?php _e('Back to Home', 'library-theme'); ?>
                    </a></p>
                </div>
            </div>

            <!-- Demo Accounts Info -->
            <div class="demo-info" style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-top: 2rem; border-left: 4px solid #667eea;">
                <h3 style="margin-bottom: 1rem; color: #667eea;"><?php _e('Demo Accounts', 'library-theme'); ?></h3>
                <p style="margin-bottom: 1rem; color: #666;">
                    <?php _e('For testing purposes, you can use these demo accounts:', 'library-theme'); ?>
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="background: white; padding: 1rem; border-radius: 5px;">
                        <strong><?php _e('Library Admin', 'library-theme'); ?></strong><br>
                        <small style="color: #666;">
                            Username: admin<br>
                            Password: admin123
                        </small>
                    </div>
                    
                    <div style="background: white; padding: 1rem; border-radius: 5px;">
                        <strong><?php _e('Librarian', 'library-theme'); ?></strong><br>
                        <small style="color: #666;">
                            Username: librarian<br>
                            Password: librarian123
                        </small>
                    </div>
                    
                    <div style="background: white; padding: 1rem; border-radius: 5px;">
                        <strong><?php _e('Library Patron', 'library-theme'); ?></strong><br>
                        <small style="color: #666;">
                            Username: patron<br>
                            Password: patron123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.background = '#f8f9fa';
                btn.style.color = '#333';
            });
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            this.style.background = '#667eea';
            this.style.color = 'white';
            
            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.style.display = 'block';
            }
        });
    });
});
</script>

<?php get_footer(); ?>