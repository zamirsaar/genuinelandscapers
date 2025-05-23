<?php
/**
 * Main JavaScript for Genuine Landscapers website
 * Handles animations, navigation, and interactive elements
 */
?>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            
            // Toggle hamburger animation
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
    
    // Scroll animations
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animateElements.length > 0) {
        // Initial check for elements in viewport
        checkAnimateElements();
        
        // Check on scroll
        window.addEventListener('scroll', checkAnimateElements);
        
        function checkAnimateElements() {
            animateElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }
    }
    
    // Testimonial carousel
    const reviewSlides = document.querySelectorAll('.review-slide');
    const prevReviewBtn = document.querySelector('.prev-review');
    const nextReviewBtn = document.querySelector('.next-review');
    const dots = document.querySelectorAll('.dot');
    
    if (reviewSlides.length > 0 && prevReviewBtn && nextReviewBtn) {
        let currentSlide = 0;
        
        // Show initial slide
        showSlide(currentSlide);
        
        // Previous button
        prevReviewBtn.addEventListener('click', function() {
            currentSlide--;
            if (currentSlide < 0) {
                currentSlide = reviewSlides.length - 1;
            }
            showSlide(currentSlide);
        });
        
        // Next button
        nextReviewBtn.addEventListener('click', function() {
            currentSlide++;
            if (currentSlide >= reviewSlides.length) {
                currentSlide = 0;
            }
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
            currentSlide++;
            if (currentSlide >= reviewSlides.length) {
                currentSlide = 0;
            }
            showSlide(currentSlide);
        }, 5000);
        
        function showSlide(index) {
            // Hide all slides
            reviewSlides.forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Remove active class from all dots
            dots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            // Show current slide and activate current dot
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
            .then(response => response.json())
            .then(data => {
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                }
                
                if (messageContainer) {
                    if (data.success) {
                        messageContainer.innerHTML = '<div class="success-message">' + data.message + '</div>';
                        this.reset();
                    } else {
                        let errorHtml = '<div class="error-message">' + data.message + '</div>';
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
            });
        });
    });
});
