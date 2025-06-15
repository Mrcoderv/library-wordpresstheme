/**
 * Main JavaScript for Library Theme
 */

jQuery(document).ready(function($) {
    
    // Book search functionality
    $('#book-search-form').on('submit', function(e) {
        e.preventDefault();
        searchBooks();
    });
    
    // Real-time search as user types
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchBooks();
        }, 500);
    });
    
    // Filter change handlers
    $('#category, #author, #library, #status').on('change', function() {
        searchBooks();
    });
    
    function searchBooks() {
        const formData = {
            action: 'search_books',
            search: $('#search').val(),
            category: $('#category').val(),
            author: $('#author').val(),
            library: $('#library').val(),
            status: $('#status').val(),
            nonce: library_ajax.nonce
        };
        
        $('#loading').show();
        
        $.ajax({
            url: library_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#loading').hide();
                if (response.success) {
                    $('#books-grid').html(response.data.html);
                    initializeBookActions();
                } else {
                    console.error('Search failed:', response);
                }
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                console.error('AJAX error:', error);
            }
        });
    }
    
    // Initialize book actions (borrow, return, etc.)
    function initializeBookActions() {
        // Borrow book functionality
        $('.borrow-book').off('click').on('click', function() {
            const bookId = $(this).data('book-id');
            const button = $(this);
            
            $.ajax({
                url: library_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'borrow_book',
                    book_id: bookId,
                    nonce: library_ajax.nonce
                },
                beforeSend: function() {
                    button.prop('disabled', true).text('Borrowing...');
                },
                success: function(response) {
                    if (response.success) {
                        alert('Book borrowed successfully!');
                        // Update the book card status
                        const bookCard = button.closest('.book-card');
                        bookCard.find('.book-status').removeClass('status-available').addClass('status-borrowed').text('Borrowed');
                        button.remove();
                    } else {
                        alert(response.data.message || 'Error borrowing book.');
                        button.prop('disabled', false).text('Borrow');
                    }
                },
                error: function() {
                    alert('Error borrowing book.');
                    button.prop('disabled', false).text('Borrow');
                }
            });
        });
        
        // Return book functionality
        $('.return-book').off('click').on('click', function() {
            const bookId = $(this).data('book-id');
            const button = $(this);
            
            if (confirm('Are you sure you want to return this book?')) {
                $.ajax({
                    url: library_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'return_book',
                        book_id: bookId,
                        nonce: library_ajax.nonce
                    },
                    beforeSend: function() {
                        button.prop('disabled', true).text('Returning...');
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Book returned successfully!');
                            // Remove the book card or update status
                            const bookCard = button.closest('.book-card');
                            bookCard.fadeOut();
                        } else {
                            alert(response.data.message || 'Error returning book.');
                            button.prop('disabled', false).text('Return');
                        }
                    },
                    error: function() {
                        alert('Error returning book.');
                        button.prop('disabled', false).text('Return');
                    }
                });
            }
        });
    }
    
    // Initialize book actions on page load
    initializeBookActions();
    
    // Mobile menu toggle
    $('.mobile-menu-toggle').on('click', function() {
        $('.main-navigation').toggleClass('active');
    });
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // Book card hover effects
    $('.book-card').hover(
        function() {
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
    
    // Form validation helpers
    $('form').on('submit', function() {
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        
        // Disable submit button to prevent double submission
        submitButton.prop('disabled', true);
        
        // Re-enable after 3 seconds
        setTimeout(function() {
            submitButton.prop('disabled', false);
        }, 3000);
    });
    
    // Password strength indicator
    $('input[type="password"]').on('input', function() {
        const password = $(this).val();
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        let strengthClass = '';
        let strengthText = '';
        
        switch (strength) {
            case 0:
            case 1:
                strengthClass = 'weak';
                strengthText = 'Weak';
                break;
            case 2:
            case 3:
                strengthClass = 'medium';
                strengthText = 'Medium';
                break;
            case 4:
            case 5:
                strengthClass = 'strong';
                strengthText = 'Strong';
                break;
        }
        
        let indicator = $(this).siblings('.password-strength');
        if (indicator.length === 0) {
            indicator = $('<div class="password-strength"></div>');
            $(this).after(indicator);
        }
        
        if (password.length > 0) {
            indicator.removeClass('weak medium strong').addClass(strengthClass).text('Password strength: ' + strengthText);
        } else {
            indicator.text('');
        }
    });
    
    // Image lazy loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Infinite scroll for book grid (optional)
    let loading = false;
    let page = 1;
    
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1000) {
            if (!loading) {
                loadMoreBooks();
            }
        }
    });
    
    function loadMoreBooks() {
        loading = true;
        page++;
        
        $.ajax({
            url: library_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_books',
                page: page,
                nonce: library_ajax.nonce
            },
            success: function(response) {
                loading = false;
                if (response.success && response.data.html) {
                    $('#books-grid').append(response.data.html);
                    initializeBookActions();
                } else {
                    // No more books to load
                    $(window).off('scroll');
                }
            },
            error: function() {
                loading = false;
                page--; // Reset page number on error
            }
        });
    }
    
    // Book rating system (if implemented)
    $('.star-rating').on('click', '.star', function() {
        const rating = $(this).data('rating');
        const bookId = $(this).closest('.star-rating').data('book-id');
        
        $.ajax({
            url: library_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'rate_book',
                book_id: bookId,
                rating: rating,
                nonce: library_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update star display
                    const starContainer = $('.star-rating[data-book-id="' + bookId + '"]');
                    starContainer.find('.star').removeClass('active');
                    starContainer.find('.star').slice(0, rating).addClass('active');
                }
            }
        });
    });
    
    // Search suggestions (autocomplete)
    $('#search').on('input', function() {
        const query = $(this).val();
        
        if (query.length >= 3) {
            $.ajax({
                url: library_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'search_suggestions',
                    query: query,
                    nonce: library_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showSearchSuggestions(response.data.suggestions);
                    }
                }
            });
        } else {
            hideSearchSuggestions();
        }
    });
    
    function showSearchSuggestions(suggestions) {
        let suggestionsList = $('#search-suggestions');
        if (suggestionsList.length === 0) {
            suggestionsList = $('<ul id="search-suggestions" class="search-suggestions"></ul>');
            $('#search').after(suggestionsList);
        }
        
        suggestionsList.empty();
        suggestions.forEach(function(suggestion) {
            const li = $('<li></li>').text(suggestion).on('click', function() {
                $('#search').val(suggestion);
                hideSearchSuggestions();
                searchBooks();
            });
            suggestionsList.append(li);
        });
        
        suggestionsList.show();
    }
    
    function hideSearchSuggestions() {
        $('#search-suggestions').hide();
    }
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search, #search-suggestions').length) {
            hideSearchSuggestions();
        }
    });
    
    // Keyboard navigation for search suggestions
    $('#search').on('keydown', function(e) {
        const suggestions = $('#search-suggestions li');
        const current = suggestions.filter('.active');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (current.length === 0) {
                suggestions.first().addClass('active');
            } else {
                current.removeClass('active').next().addClass('active');
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (current.length === 0) {
                suggestions.last().addClass('active');
            } else {
                current.removeClass('active').prev().addClass('active');
            }
        } else if (e.key === 'Enter') {
            if (current.length > 0) {
                e.preventDefault();
                current.click();
            }
        } else if (e.key === 'Escape') {
            hideSearchSuggestions();
        }
    });
});

// CSS for additional features
const additionalCSS = `
.password-strength {
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.password-strength.weak {
    color: #dc3545;
}

.password-strength.medium {
    color: #ffc107;
}

.password-strength.strong {
    color: #28a745;
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 5px 5px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    list-style: none;
    padding: 0;
    margin: 0;
}

.search-suggestions li {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.search-suggestions li:hover,
.search-suggestions li.active {
    background-color: #f8f9fa;
}

.search-suggestions li:last-child {
    border-bottom: none;
}

.book-card.hovered {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

.star-rating {
    display: flex;
    gap: 0.25rem;
}

.star {
    cursor: pointer;
    color: #ddd;
    font-size: 1.2rem;
}

.star.active {
    color: #ffc107;
}

.star:hover {
    color: #ffb400;
}

@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
    }
    
    .form-group {
        min-width: auto;
    }
    
    .mobile-menu-toggle {
        display: block;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }
    
    .main-navigation {
        display: none;
        width: 100%;
        margin-top: 1rem;
    }
    
    .main-navigation.active {
        display: block;
    }
    
    .nav-menu {
        flex-direction: column;
        gap: 0;
    }
    
    .nav-menu a {
        display: block;
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
}
`;

// Inject additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);