// Main JavaScript for Genuine Landscapers website
// Handles animations, navigation, and interactive elements

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            const isExpanded = mobileMenu.classList.contains('active');
            mobileMenuToggle.setAttribute('aria-expanded', isExpanded);
            const spans = this.querySelectorAll('span');
            spans.forEach(span => span.classList.toggle('active'));
        });
    }
    
    // Back to top button
    const backToTopButton = document.querySelector('.back-to-top');
    
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // Scroll animations with Intersection Observer
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animateElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Stop observing once visible
                }
            });
        }, { threshold: 0.1 });
        
        animateElements.forEach(element => observer.observe(element));
    }
    
    // Testimonial carousel
    const reviewSlides = document.querySelectorAll('.review-slide');
    const prevReviewBtn = document.querySelector('.prev-review');
    const nextReviewBtn = document.querySelector('.next-review');
    const dots = document.querySelectorAll('.dot');
    
    if (reviewSlides.length > 0 && prevReviewBtn && nextReviewBtn && dots.length > 0) {
        let currentSlide = 0;
        
        // Show initial slide
        showSlide(currentSlide);
        
        // Previous button
        prevReviewBtn.addEventListener('click', function() {
            currentSlide = (currentSlide - 1 + reviewSlides.length) % reviewSlides.length;
            showSlide(currentSlide);
        });
        
        // Next button
        nextReviewBtn.addEventListener('click', function() {
            currentSlide = (currentSlide + 1) % reviewSlides.length;
            showSlide(currentSlide);
        });
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
        
        // Auto advance slides every 5 seconds
        setInterval(function() {
            currentSlide = (currentSlide + 1) % reviewSlides.length;
            showSlide(currentSlide);
        }, 5000);
        
        function showSlide(index) {
            reviewSlides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            reviewSlides[index].classList.add('active');
            dots[index].classList.add('active');
        }
    }
    
    // Form submission handling with AJAX
    const forms = document.querySelectorAll('form[data-ajax="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const messageContainer = document.getElementById(this.dataset.message);
            
            // Disable submit button during submission
            if (submitButton) {
                submitButton.disabled = true;
            }
            
            // Show loading message
            if (messageContainer) {
                messageContainer.innerHTML = '<div class="info-message">Processing your request...</div>';
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not OK');
                }
                return response.json();
            })
            .then(data => {
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                }
                
                if (messageContainer) {
                    if (data.success) {
                        messageContainer.innerHTML = '<div class="success-message">' + data.message + '</div>';
                        form.reset();
                    } else {
                        let errorHtml = '<div class="error-message">' + (data.message || 'An error occurred') + '</div>';
                        if (data.errors) {
                            for (const field in data.errors) {
                                errorHtml += '<div class="error-message">' + data.errors[field] + '</div>';
                            }
                        }
                        messageContainer.innerHTML = errorHtml;
                    }
                }
            })
            .catch(error => {
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                }
                
                if (messageContainer) {
                    messageContainer.innerHTML = '<div class="error-message">An error occurred. Please try again later.</div>';
                }
                console.error('Form submission error:', error);
            });
        });
    });
});