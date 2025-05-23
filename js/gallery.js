<?php
/**
 * JavaScript for gallery functionality
 * Handles gallery filtering, modal display, and navigation
 */
?>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Gallery filtering
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    if (filterButtons.length > 0 && galleryItems.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get filter value
                const filterValue = this.getAttribute('data-filter');
                
                // Filter gallery items
                galleryItems.forEach(item => {
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Gallery modal functionality
    const modal = document.querySelector('.gallery-modal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDescription = document.getElementById('modal-description');
    const modalClose = document.querySelector('.modal-close');
    const modalPrev = document.querySelector('.modal-prev');
    const modalNext = document.querySelector('.modal-next');
    const zoomButtons = document.querySelectorAll('.gallery-zoom-btn');
    
    if (modal && zoomButtons.length > 0) {
        let currentIndex = 0;
        const galleryData = [];
        
        // Collect gallery data
        zoomButtons.forEach((button, index) => {
            galleryData.push({
                image: button.getAttribute('data-image'),
                title: button.getAttribute('data-title'),
                description: button.getAttribute('data-description')
            });
            
            // Open modal on button click
            button.addEventListener('click', function() {
                currentIndex = index;
                openModal(currentIndex);
            });
        });
        
        // Open modal function
        function openModal(index) {
            modalImage.src = galleryData[index].image;
            modalTitle.textContent = galleryData[index].title;
            modalDescription.textContent = galleryData[index].description;
            modal.style.display = 'flex';
            
            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        }
        
        // Close modal function
        function closeModal() {
            modal.style.display = 'none';
            
            // Re-enable body scrolling
            document.body.style.overflow = 'auto';
        }
        
        // Close modal on close button click
        if (modalClose) {
            modalClose.addEventListener('click', closeModal);
        }
        
        // Close modal on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        // Previous button
        if (modalPrev) {
            modalPrev.addEventListener('click', function() {
                currentIndex--;
                if (currentIndex < 0) {
                    currentIndex = galleryData.length - 1;
                }
                openModal(currentIndex);
            });
        }
        
        // Next button
        if (modalNext) {
            modalNext.addEventListener('click', function() {
                currentIndex++;
                if (currentIndex >= galleryData.length) {
                    currentIndex = 0;
                }
                openModal(currentIndex);
            });
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (modal.style.display === 'flex') {
                if (e.key === 'Escape') {
                    closeModal();
                } else if (e.key === 'ArrowLeft') {
                    currentIndex--;
                    if (currentIndex < 0) {
                        currentIndex = galleryData.length - 1;
                    }
                    openModal(currentIndex);
                } else if (e.key === 'ArrowRight') {
                    currentIndex++;
                    if (currentIndex >= galleryData.length) {
                        currentIndex = 0;
                    }
                    openModal(currentIndex);
                }
            }
        });
    }
    
    // FAQ accordion functionality
    const faqItems = document.querySelectorAll('.faq-item h3');
    
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            item.addEventListener('click', function() {
                this.parentElement.classList.toggle('active');
                
                // Close other items
                faqItems.forEach(otherItem => {
                    if (otherItem !== this) {
                        otherItem.parentElement.classList.remove('active');
                    }
                });
            });
        });
    }
});
