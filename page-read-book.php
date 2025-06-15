<?php
/**
 * Book Reading Page
 *
 * @package LibraryTheme
 */

// Get book slug from URL
$book_slug = get_query_var('book_slug');
if (!$book_slug) {
    wp_redirect(home_url());
    exit;
}

// Get book by slug
$book = get_page_by_path($book_slug, OBJECT, 'book');
if (!$book) {
    wp_redirect(home_url());
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

// Check if user can access this book
if (!library_user_can_access_book($book->ID)) {
    wp_redirect(home_url('/dashboard/'));
    exit;
}

// Get digital file URL
$digital_file = get_post_meta($book->ID, '_book_digital_file', true);
if (!$digital_file) {
    wp_redirect(get_permalink($book->ID));
    exit;
}

// Track reading activity
$current_user_id = get_current_user_id();
$reading_history = get_user_meta($current_user_id, 'reading_history', true);
if (!is_array($reading_history)) {
    $reading_history = array();
}

// Add current book to reading history if not already there
$book_found = false;
foreach ($reading_history as &$entry) {
    if ($entry['book_id'] == $book->ID) {
        $entry['last_read'] = current_time('mysql');
        $entry['read_count']++;
        $book_found = true;
        break;
    }
}

if (!$book_found) {
    $reading_history[] = array(
        'book_id' => $book->ID,
        'first_read' => current_time('mysql'),
        'last_read' => current_time('mysql'),
        'read_count' => 1
    );
}

update_user_meta($current_user_id, 'reading_history', $reading_history);

// Get book details
$authors = get_the_terms($book->ID, 'book_author');
$author_names = array();
if ($authors && !is_wp_error($authors)) {
    foreach ($authors as $author) {
        $author_names[] = $author->name;
    }
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html($book->post_title); ?> - <?php _e('Reading', 'library-theme'); ?> | <?php bloginfo('name'); ?></title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #e0e0e0;
            overflow: hidden;
        }
        
        .reader-container {
            display: flex;
            height: 100vh;
        }
        
        .reader-sidebar {
            width: 300px;
            background: #2d2d2d;
            padding: 1rem;
            overflow-y: auto;
            border-right: 1px solid #444;
            transition: transform 0.3s ease;
        }
        
        .reader-sidebar.hidden {
            transform: translateX(-100%);
        }
        
        .reader-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .reader-header {
            background: #333;
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444;
        }
        
        .reader-content {
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        
        .reader-iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
        }
        
        .pdf-viewer {
            width: 100%;
            height: 100%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .book-info {
            margin-bottom: 2rem;
        }
        
        .book-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #fff;
        }
        
        .book-author {
            color: #ccc;
            margin-bottom: 1rem;
        }
        
        .reading-controls {
            margin-bottom: 2rem;
        }
        
        .control-group {
            margin-bottom: 1rem;
        }
        
        .control-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ccc;
            font-size: 0.9rem;
        }
        
        .control-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #555;
            border-radius: 3px;
            background: #444;
            color: #fff;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .reading-progress {
            margin-bottom: 1rem;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #444;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #667eea;
            transition: width 0.3s ease;
        }
        
        .reading-stats {
            background: #444;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .stat-item:last-child {
            margin-bottom: 0;
        }
        
        .bookmarks {
            margin-top: 2rem;
        }
        
        .bookmark-item {
            background: #444;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .bookmark-item:hover {
            background: #555;
        }
        
        .bookmark-title {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .bookmark-page {
            font-size: 0.8rem;
            color: #ccc;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: #ccc;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .toggle-sidebar:hover {
            color: #fff;
        }
        
        @media (max-width: 768px) {
            .reader-sidebar {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 1000;
                width: 280px;
            }
            
            .reader-sidebar.hidden {
                transform: translateX(-100%);
            }
        }
        
        .fullscreen-btn {
            background: none;
            border: none;
            color: #ccc;
            font-size: 1rem;
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .fullscreen-btn:hover {
            color: #fff;
        }
        
        .theme-toggle {
            background: none;
            border: none;
            color: #ccc;
            font-size: 1rem;
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .theme-toggle:hover {
            color: #fff;
        }
        
        .reader-container.light-theme {
            background: #fff;
            color: #333;
        }
        
        .reader-container.light-theme .reader-sidebar {
            background: #f8f9fa;
            border-right-color: #dee2e6;
        }
        
        .reader-container.light-theme .reader-header {
            background: #e9ecef;
            border-bottom-color: #dee2e6;
        }
        
        .reader-container.light-theme .control-input {
            background: #fff;
            border-color: #ced4da;
            color: #333;
        }
        
        .reader-container.light-theme .reading-stats,
        .reader-container.light-theme .bookmark-item {
            background: #e9ecef;
        }
        
        .reader-container.light-theme .progress-bar {
            background: #e9ecef;
        }
    </style>
</head>

<body>
    <div class="reader-container" id="readerContainer">
        <div class="reader-sidebar" id="readerSidebar">
            <div class="book-info">
                <h1 class="book-title"><?php echo esc_html($book->post_title); ?></h1>
                <?php if (!empty($author_names)): ?>
                    <div class="book-author"><?php _e('by', 'library-theme'); ?> <?php echo implode(', ', $author_names); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="reading-controls">
                <div class="control-group">
                    <label><?php _e('Font Size', 'library-theme'); ?></label>
                    <input type="range" id="fontSize" class="control-input" min="12" max="24" value="16">
                </div>
                
                <div class="control-group">
                    <label><?php _e('Line Height', 'library-theme'); ?></label>
                    <input type="range" id="lineHeight" class="control-input" min="1.2" max="2.0" step="0.1" value="1.6">
                </div>
                
                <div class="control-group">
                    <label><?php _e('Page Width', 'library-theme'); ?></label>
                    <select id="pageWidth" class="control-input">
                        <option value="100%"><?php _e('Full Width', 'library-theme'); ?></option>
                        <option value="800px" selected><?php _e('Reading Width', 'library-theme'); ?></option>
                        <option value="600px"><?php _e('Narrow', 'library-theme'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="reading-progress">
                <label><?php _e('Reading Progress', 'library-theme'); ?></label>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
                <div style="font-size: 0.8rem; color: #ccc; margin-top: 0.5rem;">
                    <span id="progressText">0%</span>
                </div>
            </div>
            
            <div class="reading-stats">
                <h3 style="margin-bottom: 1rem; font-size: 1rem;"><?php _e('Reading Stats', 'library-theme'); ?></h3>
                <div class="stat-item">
                    <span><?php _e('Time Reading:', 'library-theme'); ?></span>
                    <span id="readingTime">0:00</span>
                </div>
                <div class="stat-item">
                    <span><?php _e('Pages Read:', 'library-theme'); ?></span>
                    <span id="pagesRead">0</span>
                </div>
                <div class="stat-item">
                    <span><?php _e('Estimated Time Left:', 'library-theme'); ?></span>
                    <span id="timeLeft">--:--</span>
                </div>
            </div>
            
            <div class="bookmarks">
                <h3 style="margin-bottom: 1rem; font-size: 1rem;"><?php _e('Bookmarks', 'library-theme'); ?></h3>
                <button class="btn btn-primary btn-small" onclick="addBookmark()" style="width: 100%; margin-bottom: 1rem;">
                    <?php _e('Add Bookmark', 'library-theme'); ?>
                </button>
                <div id="bookmarksList">
                    <!-- Bookmarks will be loaded here -->
                </div>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #444;">
                <a href="<?php echo get_permalink($book->ID); ?>" class="btn btn-secondary" style="width: 100%; text-align: center; margin-bottom: 0.5rem;">
                    ← <?php _e('Back to Book Details', 'library-theme'); ?>
                </a>
                <a href="<?php echo home_url('/dashboard/'); ?>" class="btn btn-secondary" style="width: 100%; text-align: center;">
                    🏠 <?php _e('Dashboard', 'library-theme'); ?>
                </a>
            </div>
        </div>
        
        <div class="reader-main">
            <div class="reader-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
                    <span style="font-weight: bold;"><?php echo esc_html($book->post_title); ?></span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="theme-toggle" onclick="toggleTheme()" title="<?php _e('Toggle Theme', 'library-theme'); ?>">🌓</button>
                    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="<?php _e('Toggle Fullscreen', 'library-theme'); ?>">⛶</button>
                    <span style="font-size: 0.9rem; color: #ccc;"><?php echo get_current_user()->display_name; ?></span>
                </div>
            </div>
            
            <div class="reader-content">
                <?php
                $file_extension = pathinfo($digital_file, PATHINFO_EXTENSION);
                
                if (in_array(strtolower($file_extension), array('pdf'))):
                ?>
                    <div class="pdf-viewer">
                        <iframe src="<?php echo esc_url($digital_file); ?>#toolbar=1&navpanes=1&scrollbar=1" 
                                class="reader-iframe" 
                                id="readerFrame"
                                title="<?php echo esc_attr($book->post_title); ?>">
                        </iframe>
                    </div>
                <?php else: ?>
                    <iframe src="<?php echo esc_url($digital_file); ?>" 
                            class="reader-iframe" 
                            id="readerFrame"
                            title="<?php echo esc_attr($book->post_title); ?>">
                    </iframe>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let startTime = Date.now();
        let readingTime = 0;
        let currentProgress = 0;
        let bookmarks = JSON.parse(localStorage.getItem('bookmarks_<?php echo $book->ID; ?>') || '[]');
        
        // Initialize reading session
        document.addEventListener('DOMContentLoaded', function() {
            loadBookmarks();
            updateReadingTime();
            
            // Update reading time every minute
            setInterval(updateReadingTime, 60000);
            
            // Save reading progress periodically
            setInterval(saveReadingProgress, 30000);
            
            // Load saved reading position
            loadReadingPosition();
        });
        
        function toggleSidebar() {
            const sidebar = document.getElementById('readerSidebar');
            sidebar.classList.toggle('hidden');
        }
        
        function toggleTheme() {
            const container = document.getElementById('readerContainer');
            container.classList.toggle('light-theme');
            
            // Save theme preference
            localStorage.setItem('readerTheme', container.classList.contains('light-theme') ? 'light' : 'dark');
        }
        
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }
        
        function updateReadingTime() {
            readingTime = Math.floor((Date.now() - startTime) / 1000 / 60);
            const hours = Math.floor(readingTime / 60);
            const minutes = readingTime % 60;
            
            document.getElementById('readingTime').textContent = 
                hours > 0 ? `${hours}:${minutes.toString().padStart(2, '0')}` : `${minutes}:00`;
        }
        
        function updateProgress(progress) {
            currentProgress = progress;
            document.getElementById('progressFill').style.width = progress + '%';
            document.getElementById('progressText').textContent = Math.round(progress) + '%';
            
            // Update pages read (assuming 100% = total pages)
            const totalPages = <?php echo intval(get_post_meta($book->ID, '_book_pages', true)) ?: 100; ?>;
            const pagesRead = Math.round((progress / 100) * totalPages);
            document.getElementById('pagesRead').textContent = pagesRead;
            
            // Estimate time left
            if (progress > 5) {
                const timePerPercent = readingTime / progress;
                const timeLeft = Math.round(timePerPercent * (100 - progress));
                const leftHours = Math.floor(timeLeft / 60);
                const leftMinutes = timeLeft % 60;
                
                document.getElementById('timeLeft').textContent = 
                    leftHours > 0 ? `${leftHours}:${leftMinutes.toString().padStart(2, '0')}` : `${leftMinutes}:00`;
            }
        }
        
        function addBookmark() {
            const title = prompt('<?php _e('Bookmark title:', 'library-theme'); ?>');
            if (title) {
                const bookmark = {
                    id: Date.now(),
                    title: title,
                    progress: currentProgress,
                    timestamp: new Date().toLocaleString()
                };
                
                bookmarks.push(bookmark);
                saveBookmarks();
                loadBookmarks();
            }
        }
        
        function loadBookmarks() {
            const bookmarksList = document.getElementById('bookmarksList');
            bookmarksList.innerHTML = '';
            
            bookmarks.forEach(bookmark => {
                const bookmarkElement = document.createElement('div');
                bookmarkElement.className = 'bookmark-item';
                bookmarkElement.innerHTML = `
                    <div class="bookmark-title">${bookmark.title}</div>
                    <div class="bookmark-page">${Math.round(bookmark.progress)}% - ${bookmark.timestamp}</div>
                `;
                
                bookmarkElement.onclick = () => {
                    updateProgress(bookmark.progress);
                    // Here you would also navigate to the specific position in the document
                };
                
                bookmarksList.appendChild(bookmarkElement);
            });
        }
        
        function saveBookmarks() {
            localStorage.setItem('bookmarks_<?php echo $book->ID; ?>', JSON.stringify(bookmarks));
        }
        
        function saveReadingProgress() {
            const progressData = {
                progress: currentProgress,
                readingTime: readingTime,
                lastRead: Date.now()
            };
            
            localStorage.setItem('reading_progress_<?php echo $book->ID; ?>', JSON.stringify(progressData));
            
            // Also save to server
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'save_reading_progress',
                    book_id: <?php echo $book->ID; ?>,
                    progress: currentProgress,
                    reading_time: readingTime,
                    nonce: '<?php echo wp_create_nonce('save_reading_progress'); ?>'
                })
            });
        }
        
        function loadReadingPosition() {
            const saved = localStorage.getItem('reading_progress_<?php echo $book->ID; ?>');
            if (saved) {
                const data = JSON.parse(saved);
                updateProgress(data.progress);
                readingTime = data.readingTime || 0;
                updateReadingTime();
            }
            
            // Load theme preference
            const theme = localStorage.getItem('readerTheme');
            if (theme === 'light') {
                document.getElementById('readerContainer').classList.add('light-theme');
            }
        }
        
        // Handle reading controls
        document.getElementById('fontSize').addEventListener('input', function() {
            const iframe = document.getElementById('readerFrame');
            try {
                iframe.contentDocument.body.style.fontSize = this.value + 'px';
            } catch (e) {
                // Cross-origin restrictions
            }
        });
        
        document.getElementById('lineHeight').addEventListener('input', function() {
            const iframe = document.getElementById('readerFrame');
            try {
                iframe.contentDocument.body.style.lineHeight = this.value;
            } catch (e) {
                // Cross-origin restrictions
            }
        });
        
        document.getElementById('pageWidth').addEventListener('change', function() {
            const iframe = document.getElementById('readerFrame');
            try {
                const content = iframe.contentDocument.querySelector('body');
                if (content) {
                    content.style.maxWidth = this.value;
                    content.style.margin = '0 auto';
                    content.style.padding = '2rem';
                }
            } catch (e) {
                // Cross-origin restrictions
            }
        });
        
        // Simulate reading progress (in a real implementation, this would track actual scroll position)
        setInterval(() => {
            if (currentProgress < 100) {
                updateProgress(currentProgress + 0.1);
            }
        }, 10000);
        
        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                saveReadingProgress();
            } else {
                startTime = Date.now() - (readingTime * 60 * 1000);
            }
        });
        
        // Save progress before leaving
        window.addEventListener('beforeunload', function() {
            saveReadingProgress();
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'b':
                        e.preventDefault();
                        addBookmark();
                        break;
                    case 's':
                        e.preventDefault();
                        toggleSidebar();
                        break;
                    case 't':
                        e.preventDefault();
                        toggleTheme();
                        break;
                }
            }
            
            if (e.key === 'F11') {
                e.preventDefault();
                toggleFullscreen();
            }
        });
    </script>
</body>
</html>