<?php
/**
 * Registration Page
 *
 * @package LibraryTheme
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard/'));
    exit;
}

get_header();

$errors = library_handle_registration();
?>

<main class="site-main">
    <div class="container">
        <div class="register-page" style="max-width: 600px; margin: 2rem auto;">
            <div class="register-form-container" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h1 class="text-center mb-2"><?php _e('Join Our Library Network', 'library-theme'); ?></h1>
                <p class="text-center mb-3" style="color: #666;">
                    <?php _e('Create an account to access books from participating libraries.', 'library-theme'); ?>
                </p>

                <?php if ($errors && !empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo esc_html($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="username"><?php _e('Username', 'library-theme'); ?> <span style="color: red;">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>" 
                               placeholder="<?php _e('Choose a unique username', 'library-theme'); ?>" required>
                        <small style="color: #666; font-size: 0.8rem;">
                            <?php _e('Username must be unique and contain only letters, numbers, and underscores.', 'library-theme'); ?>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="email"><?php _e('Email Address', 'library-theme'); ?> <span style="color: red;">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" 
                               placeholder="<?php _e('your.email@example.com', 'library-theme'); ?>" required>
                        <small style="color: #666; font-size: 0.8rem;">
                            <?php _e('We will use this email for account notifications and password resets.', 'library-theme'); ?>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password"><?php _e('Password', 'library-theme'); ?> <span style="color: red;">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="<?php _e('Create a strong password', 'library-theme'); ?>" required>
                        <small style="color: #666; font-size: 0.8rem;">
                            <?php _e('Password should be at least 8 characters long.', 'library-theme'); ?>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password"><?php _e('Confirm Password', 'library-theme'); ?> <span style="color: red;">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                               placeholder="<?php _e('Re-enter your password', 'library-theme'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="library_id"><?php _e('Select Your Library', 'library-theme'); ?> <span style="color: red;">*</span></label>
                        <select id="library_id" name="library_id" class="form-control" required>
                            <option value=""><?php _e('Choose your local library', 'library-theme'); ?></option>
                            <?php
                            $libraries = get_posts(array(
                                'post_type' => 'library',
                                'numberposts' => -1,
                                'post_status' => 'publish',
                                'orderby' => 'title',
                                'order' => 'ASC'
                            ));
                            
                            foreach ($libraries as $library) {
                                $selected = (isset($_POST['library_id']) && $_POST['library_id'] == $library->ID) ? 'selected' : '';
                                echo '<option value="' . $library->ID . '" ' . $selected . '>' . esc_html($library->post_title) . '</option>';
                            }
                            ?>
                        </select>
                        <small style="color: #666; font-size: 0.8rem;">
                            <?php _e('You can only access books from your selected library.', 'library-theme'); ?>
                        </small>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <input type="checkbox" id="terms" name="terms" required style="margin-top: 0.25rem;">
                            <span style="font-size: 0.9rem;">
                                <?php _e('I agree to the', 'library-theme'); ?> 
                                <a href="#" style="color: #667eea; text-decoration: none;" onclick="showTerms(); return false;">
                                    <?php _e('Terms of Service', 'library-theme'); ?>
                                </a> 
                                <?php _e('and', 'library-theme'); ?> 
                                <a href="#" style="color: #667eea; text-decoration: none;" onclick="showPrivacy(); return false;">
                                    <?php _e('Privacy Policy', 'library-theme'); ?>
                                </a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" name="library_register" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        <?php _e('Create Account', 'library-theme'); ?>
                    </button>
                </form>

                <div class="register-footer text-center mt-3" style="padding-top: 1rem; border-top: 1px solid #e9ecef;">
                    <p><?php _e('Already have an account?', 'library-theme'); ?> 
                       <a href="<?php echo home_url('/login/'); ?>" style="color: #667eea; text-decoration: none;">
                           <?php _e('Login here', 'library-theme'); ?>
                       </a>
                    </p>
                    <p><a href="<?php echo home_url('/'); ?>" style="color: #666; text-decoration: none;">
                        ← <?php _e('Back to Home', 'library-theme'); ?>
                    </a></p>
                </div>
            </div>

            <!-- Registration Benefits -->
            <div class="registration-benefits" style="background: #f8f9fa; padding: 2rem; border-radius: 10px; margin-top: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: #667eea; text-align: center;">
                    <?php _e('Why Join Our Library Network?', 'library-theme'); ?>
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">📚</div>
                        <h4 style="margin-bottom: 0.5rem;"><?php _e('Access Thousands of Books', 'library-theme'); ?></h4>
                        <p style="color: #666; font-size: 0.9rem;">
                            <?php _e('Browse and borrow from extensive digital and physical collections.', 'library-theme'); ?>
                        </p>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">💻</div>
                        <h4 style="margin-bottom: 0.5rem;"><?php _e('Digital Reading', 'library-theme'); ?></h4>
                        <p style="color: #666; font-size: 0.9rem;">
                            <?php _e('Read books online with our built-in digital reader.', 'library-theme'); ?>
                        </p>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔍</div>
                        <h4 style="margin-bottom: 0.5rem;"><?php _e('Advanced Search', 'library-theme'); ?></h4>
                        <p style="color: #666; font-size: 0.9rem;">
                            <?php _e('Find books by title, author, category, or keyword quickly.', 'library-theme'); ?>
                        </p>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">📱</div>
                        <h4 style="margin-bottom: 0.5rem;"><?php _e('Mobile Friendly', 'library-theme'); ?></h4>
                        <p style="color: #666; font-size: 0.9rem;">
                            <?php _e('Access your library account from any device, anywhere.', 'library-theme'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Terms of Service Modal -->
<div id="terms-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 10px; max-width: 600px; max-height: 80vh; overflow-y: auto;">
        <h3><?php _e('Terms of Service', 'library-theme'); ?></h3>
        <div style="margin: 1rem 0; line-height: 1.6;">
            <p><?php _e('By creating an account, you agree to:', 'library-theme'); ?></p>
            <ul style="margin-left: 1rem;">
                <li><?php _e('Use the library services responsibly and in accordance with library policies.', 'library-theme'); ?></li>
                <li><?php _e('Return borrowed books on time and in good condition.', 'library-theme'); ?></li>
                <li><?php _e('Respect copyright laws and not distribute digital content illegally.', 'library-theme'); ?></li>
                <li><?php _e('Keep your account information secure and not share login credentials.', 'library-theme'); ?></li>
                <li><?php _e('Notify the library of any changes to your contact information.', 'library-theme'); ?></li>
            </ul>
        </div>
        <button onclick="closeModal('terms-modal')" class="btn btn-primary"><?php _e('Close', 'library-theme'); ?></button>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div id="privacy-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 10px; max-width: 600px; max-height: 80vh; overflow-y: auto;">
        <h3><?php _e('Privacy Policy', 'library-theme'); ?></h3>
        <div style="margin: 1rem 0; line-height: 1.6;">
            <p><?php _e('We collect and use your information to:', 'library-theme'); ?></p>
            <ul style="margin-left: 1rem;">
                <li><?php _e('Provide library services and manage your account.', 'library-theme'); ?></li>
                <li><?php _e('Send notifications about due dates and library updates.', 'library-theme'); ?></li>
                <li><?php _e('Improve our services and user experience.', 'library-theme'); ?></li>
                <li><?php _e('Comply with legal requirements and library policies.', 'library-theme'); ?></li>
            </ul>
            <p><?php _e('We do not sell or share your personal information with third parties without your consent.', 'library-theme'); ?></p>
        </div>
        <button onclick="closeModal('privacy-modal')" class="btn btn-primary"><?php _e('Close', 'library-theme'); ?></button>
    </div>
</div>

<script>
function showTerms() {
    document.getElementById('terms-modal').style.display = 'block';
}

function showPrivacy() {
    document.getElementById('privacy-modal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.style.position === 'fixed') {
        e.target.style.display = 'none';
    }
});

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('blur', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('<?php _e('Passwords do not match', 'library-theme'); ?>');
    } else {
        this.setCustomValidity('');
    }
});

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    let strengthText = '';
    let strengthColor = '';
    
    switch (strength) {
        case 0:
        case 1:
            strengthText = '<?php _e('Weak', 'library-theme'); ?>';
            strengthColor = '#dc3545';
            break;
        case 2:
        case 3:
            strengthText = '<?php _e('Medium', 'library-theme'); ?>';
            strengthColor = '#ffc107';
            break;
        case 4:
        case 5:
            strengthText = '<?php _e('Strong', 'library-theme'); ?>';
            strengthColor = '#28a745';
            break;
    }
    
    let indicator = document.getElementById('password-strength');
    if (!indicator) {
        indicator = document.createElement('small');
        indicator.id = 'password-strength';
        indicator.style.fontSize = '0.8rem';
        this.parentNode.appendChild(indicator);
    }
    
    if (password.length > 0) {
        indicator.textContent = '<?php _e('Password strength:', 'library-theme'); ?> ' + strengthText;
        indicator.style.color = strengthColor;
    } else {
        indicator.textContent = '';
    }
});
</script>

<?php get_footer(); ?>