<?php
/**
 * Admin footer include for Genuine Landscapers website
 * Contains the closing tags and common scripts for admin pages
 */
?>
            </div><!-- /.admin-container -->
        </main><!-- /.admin-content -->
    </div><!-- /.admin-wrapper -->
    
    <!-- Scripts -->
    <script src="../js/main.js"></script>
    <script src="js/admin.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.admin-wrapper').classList.toggle('sidebar-collapsed');
        });
        
        // Responsive sidebar behavior
        function handleResize() {
            if (window.innerWidth < 992) {
                document.querySelector('.admin-wrapper').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('.admin-wrapper').classList.remove('sidebar-collapsed');
            }
        }
        
        // Initial check and event listener
        handleResize();
        window.addEventListener('resize', handleResize);
        
        // Flash messages auto-hide
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(message) {
            setTimeout(function() {
                message.classList.add('fade-out');
                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            }, 5000);
        });
    </script>
</body>
</html>
