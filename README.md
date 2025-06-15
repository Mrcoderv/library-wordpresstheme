# Live Library Management WordPress Theme

A comprehensive WordPress theme for managing live book libraries with multi-tenant support. This theme allows different libraries to manage their own user base and book collections while providing a unified platform for users to discover and access books.

## Features

### 🏛️ Multi-Library Support
- Each library can manage its own collection independently
- Users register with a specific library
- Library-specific user access controls
- Separate library profiles with contact information and hours

### 📚 Book Management
- Custom post type for books with detailed metadata
- Book categories and author taxonomies
- Book status tracking (available, borrowed, maintenance)
- ISBN, publisher, publication date, and other book details
- Digital file support for online reading

### 👥 User Management
- Custom user roles: Library Admin, Librarian, Library Patron
- User registration with library selection
- User dashboard with borrowed books and reading history
- Reading progress tracking

### 🔍 Advanced Search & Discovery
- Real-time search with AJAX
- Filter by category, author, library, and status
- Search suggestions and autocomplete
- Responsive book grid layout

### 📖 Digital Reading Experience
- Built-in book reader for digital files
- Reading progress tracking
- Bookmarks and reading statistics
- Customizable reading interface (font size, theme, etc.)
- Fullscreen reading mode

### 📱 Responsive Design
- Mobile-first responsive design
- Touch-friendly interface
- Optimized for all screen sizes
- Progressive Web App features

## Installation

### Requirements
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

### Installation Steps

1. **Download the theme**
   ```bash
   # Clone or download the theme files
   git clone [repository-url] library-theme
   ```

2. **Upload to WordPress**
   - Upload the `library-theme` folder to `/wp-content/themes/`
   - Or zip the folder and upload via WordPress admin

3. **Activate the theme**
   - Go to Appearance > Themes in WordPress admin
   - Find "Live Library Management" and click Activate

4. **Initial Setup**
   - The theme will automatically create demo data on first activation
   - Demo users, libraries, and books will be created for testing

## Demo Accounts

The theme creates demo accounts for testing:

| Role | Username | Password | Description |
|------|----------|----------|-------------|
| Library Admin | `admin` | `admin123` | Full administrative access |
| Librarian | `librarian` | `librarian123` | Book management access |
| Library Patron | `patron` | `patron123` | Regular user access |

## Configuration

### 1. Library Setup
1. Go to Libraries > Add New in WordPress admin
2. Fill in library details:
   - Name and description
   - Address and contact information
   - Operating hours
   - Upload a library image

### 2. Book Management
1. Go to Books > Add New
2. Enter book details:
   - Title and description
   - Select author(s) and categories
   - Add metadata (ISBN, publisher, pages, etc.)
   - Assign to a library
   - Set availability status
   - Upload book cover image
   - Add digital file URL for online reading

### 3. User Registration
- Users can register at `/register/`
- They must select a library during registration
- Users can only access books from their assigned library

### 4. Customization
- Go to Appearance > Customize
- Configure site identity, colors, and layout
- Set up navigation menus
- Configure widgets and other theme options

## Custom Post Types

### Books (`book`)
- **Fields**: Title, Description, Cover Image
- **Taxonomies**: Book Category, Book Author
- **Meta Fields**: ISBN, Publisher, Publication Date, Pages, Language, Status, Library, Digital File URL

### Libraries (`library`)
- **Fields**: Name, Description, Featured Image
- **Meta Fields**: Address, Phone, Email, Website, Operating Hours

## User Roles

### Library Admin (`library_admin`)
- Full access to book and library management
- User management capabilities
- System administration

### Librarian (`librarian`)
- Book management within assigned library
- User assistance and support
- Limited administrative access

### Library Patron (`library_patron`)
- Browse and search books
- Borrow and return books
- Access digital reading features
- Manage personal account

## Custom Pages

The theme includes several custom pages:

- `/dashboard/` - User dashboard
- `/login/` - User login
- `/register/` - User registration
- `/book/{slug}/read/` - Digital book reader

## AJAX Functionality

The theme uses AJAX for:
- Real-time book search and filtering
- Book borrowing and returning
- Loading more books (infinite scroll)
- Search suggestions
- Reading progress saving

## Hooks and Filters

### Actions
- `library_after_book_borrow` - Triggered after a book is borrowed
- `library_after_book_return` - Triggered after a book is returned
- `library_user_registered` - Triggered after user registration

### Filters
- `library_book_search_args` - Modify book search query arguments
- `library_user_can_access_book` - Control book access permissions
- `library_reading_progress_data` - Modify reading progress data

## Customization

### Styling
The theme uses CSS custom properties for easy customization:

```css
:root {
  --primary-color: #667eea;
  --secondary-color: #764ba2;
  --text-color: #333;
  --background-color: #f8f9fa;
}
```

### Templates
Override theme templates by copying them to your child theme:
- `single-book.php` - Individual book pages
- `archive-book.php` - Book listing pages
- `single-library.php` - Library pages
- `page-dashboard.php` - User dashboard

### Functions
Add custom functionality using WordPress hooks:

```php
// Example: Custom book borrowing logic
add_action('library_after_book_borrow', 'custom_book_borrow_notification');
function custom_book_borrow_notification($book_id, $user_id) {
    // Send email notification
    // Log borrowing activity
    // Update statistics
}
```

## Troubleshooting

### Common Issues

1. **Rewrite Rules Not Working**
   - Go to Settings > Permalinks and click "Save Changes"
   - This will flush the rewrite rules

2. **AJAX Not Working**
   - Check that jQuery is loaded
   - Verify AJAX URL and nonce values
   - Check browser console for JavaScript errors

3. **Books Not Displaying**
   - Verify books are assigned to libraries
   - Check user library assignment
   - Ensure book status is set correctly

4. **Reading Page Not Loading**
   - Verify digital file URL is accessible
   - Check user permissions for the book
   - Ensure book is borrowed by the user

### Debug Mode
Enable WordPress debug mode to troubleshoot issues:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## Performance Optimization

### Recommended Plugins
- **Caching**: WP Rocket, W3 Total Cache
- **Image Optimization**: Smush, ShortPixel
- **Database**: WP-Optimize
- **CDN**: Cloudflare, MaxCDN

### Best Practices
- Optimize images before uploading
- Use a caching plugin
- Minimize plugins
- Regular database cleanup
- Monitor site performance

## Security

### Recommendations
- Keep WordPress and plugins updated
- Use strong passwords
- Implement two-factor authentication
- Regular backups
- Security plugins (Wordfence, Sucuri)

### Theme Security Features
- Nonce verification for AJAX requests
- Input sanitization and validation
- Capability checks for user actions
- Secure file handling

## Support

### Documentation
- Theme documentation: [Link to docs]
- WordPress Codex: https://codex.wordpress.org/
- Developer resources: https://developer.wordpress.org/

### Community
- Support forum: [Link to forum]
- GitHub issues: [Link to issues]
- Discord/Slack: [Link to chat]

## Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

### Development Setup
```bash
# Clone the repository
git clone [repository-url]

# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

## License

This theme is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Multi-library support
- Book management system
- User roles and permissions
- Digital reading features
- Responsive design
- AJAX functionality

## Credits

- **Developer**: OpenHands AI
- **Icons**: Font Awesome
- **Fonts**: Google Fonts
- **Framework**: WordPress

---

For more information and support, please visit our [website](https://example.com) or contact us at [support@example.com](mailto:support@example.com).#   l i b r a r y - w o r d p r e s s t h e m e  
 