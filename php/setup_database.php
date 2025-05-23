<?php
/**
 * Database setup script for Genuine Landscapers website
 * Creates database, tables, and inserts initial data
 */

// Include configuration
require_once 'config.php';

// Function to create database and tables
function setup_database() {
    try {
        // Create PDO connection without database name first
        $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Switch to the database
        $pdo->exec("USE " . DB_NAME);
        
        // Create users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            role ENUM('admin', 'editor', 'viewer') NOT NULL DEFAULT 'viewer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL
        )");
        
        // Create tips table
        $pdo->exec("CREATE TABLE IF NOT EXISTS tips (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            content TEXT NOT NULL,
            icon VARCHAR(50) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            featured BOOLEAN DEFAULT FALSE,
            published BOOLEAN DEFAULT TRUE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        )");
        
        // Create offers table
        $pdo->exec("CREATE TABLE IF NOT EXISTS offers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            content TEXT NOT NULL,
            expiry_date DATE,
            button_text VARCHAR(50) NOT NULL,
            button_link VARCHAR(255) NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        )");
        
        // Create testimonials table
        $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            text TEXT NOT NULL,
            author VARCHAR(100) NOT NULL,
            location VARCHAR(100) NOT NULL,
            rating DECIMAL(2,1) NOT NULL,
            approved BOOLEAN DEFAULT FALSE,
            featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create services table
        $pdo->exec("CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT NOT NULL,
            icon VARCHAR(50) NOT NULL,
            featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create callback_requests table
        $pdo->exec("CREATE TABLE IF NOT EXISTS callback_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reference VARCHAR(50) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            address TEXT NOT NULL,
            service VARCHAR(100) NOT NULL,
            message TEXT,
            status ENUM('pending', 'contacted', 'scheduled', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create quote_requests table
        $pdo->exec("CREATE TABLE IF NOT EXISTS quote_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reference VARCHAR(50) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            address TEXT NOT NULL,
            property_type VARCHAR(50) NOT NULL,
            property_size VARCHAR(50) NOT NULL,
            services TEXT,
            message TEXT,
            status ENUM('pending', 'quoted', 'accepted', 'declined', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create contact_submissions table
        $pdo->exec("CREATE TABLE IF NOT EXISTS contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create newsletter_subscribers table
        $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            name VARCHAR(100),
            active BOOLEAN DEFAULT TRUE,
            confirmation_token VARCHAR(100) UNIQUE,
            confirmed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create settings table
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT NOT NULL,
            setting_group VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        return true;
    } catch (PDOException $e) {
        log_activity('error', 'Database setup failed: ' . $e->getMessage());
        return false;
    }
}

// Function to insert sample data
function insert_sample_data() {
    try {
        $db = get_db_connection();
        
        // Check if users table is empty
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        $user_count = $stmt->fetchColumn();
        
        if ($user_count == 0) {
            // Insert default admin user (password: admin123)
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, password, email, first_name, last_name, role) 
                                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', $password_hash, 'admin@genuinelandscapers.ca', 'Admin', 'User', 'admin']);
            
            // Insert sample tips data
            $tips = [
                ['Summer Lawn Care Tips', 'Keep your lawn healthy during hot summer months by watering deeply but infrequently, mowing at a higher setting, and avoiding fertilization during extreme heat. Early morning watering reduces evaporation and helps prevent fungal diseases.', 'fa-sun', 'summer-lawn-care', 1],
                ['When to Prune Your Hedges', 'Learn the best times of year to prune different types of hedges for optimal growth. Most deciduous hedges should be pruned in late winter while dormant, while spring-flowering shrubs should be pruned immediately after flowering.', 'fa-cut', 'hedge-pruning', 1],
                ['Eco-Friendly Weed Control', 'Try these natural alternatives to chemical herbicides: vinegar-based solutions for walkways, corn gluten meal as a pre-emergent, and manual removal with proper tools. Mulching also helps prevent weed growth while improving soil health.', 'fa-leaf', 'eco-weed-control', 0],
                ['Fall Cleanup Essentials', 'Prepare your landscape for winter by removing fallen leaves, cutting perennials back, aerating your lawn, and applying a winter fertilizer. Don\'t forget to clean and store your garden tools properly to extend their life.', 'fa-wind', 'fall-cleanup', 0]
            ];
            
            $stmt = $db->prepare("INSERT INTO tips (title, content, icon, slug, featured) VALUES (?, ?, ?, ?, ?)");
            foreach ($tips as $tip) {
                $stmt->execute($tip);
            }
            
            // Insert sample offers data
            $offers = [
                ['Spring Cleanup Special', 'Get 20% off your first seasonal cleanup when you book before May 31st. Our comprehensive spring cleanup includes leaf removal, bed preparation, and initial lawn care to get your property ready for the growing season.', '2025-05-31', 'Claim Offer', '#quote', 1],
                ['Referral Rewards', 'Refer a friend and receive $50 off your next service when they become a customer. There\'s no limit to how many friends you can refer, so spread the word about our quality service and save on your landscaping needs.', NULL, 'Learn More', 'promotions.php', 1],
                ['Senior Discount', 'We offer a 10% discount on all services for seniors aged 65 and over. Our team specializes in making property maintenance hassle-free for seniors with reliable, consistent service you can count on.', NULL, 'View Services', 'services/senior.php', 1],
                ['Multi-Service Package', 'Book any three services together and save 15% on your total. Combine lawn maintenance, hedge trimming, garden care, or seasonal cleanup for maximum savings and a perfectly maintained property.', '2025-06-30', 'Request Quote', '#quote', 1]
            ];
            
            $stmt = $db->prepare("INSERT INTO offers (title, content, expiry_date, button_text, button_link, active) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($offers as $offer) {
                $stmt->execute($offer);
            }
            
            // Insert sample testimonials data
            $testimonials = [
                ['Amazing service! The team at Genuine Landscapers is professional, punctual, and does exceptional work. My lawn has never looked better. Highly recommended!', 'John D.', 'Windsor', 5.0, 1, 1],
                ['They keep my yard spotless all year round! As a senior, I appreciate their reliability and attention to detail. The team is always courteous and goes above and beyond.', 'Sarah K.', 'LaSalle', 5.0, 1, 1],
                ['Genuine Landscapers transformed our commercial property. Their attention to detail and consistent maintenance has improved our business\'s curb appeal significantly.', 'Michael R.', 'Tecumseh', 4.5, 1, 1],
                ['I\'ve been using their services for over 2 years now and couldn\'t be happier. The seasonal cleanup packages are especially valuable and save me so much time.', 'Jennifer L.', 'Windsor', 5.0, 1, 0]
            ];
            
            $stmt = $db->prepare("INSERT INTO testimonials (text, author, location, rating, approved, featured) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($testimonials as $testimonial) {
                $stmt->execute($testimonial);
            }
            
            // Insert sample services data
            $services = [
                ['Lawn Maintenance', 'lawn-maintenance', 'Regular mowing, edging, and maintenance to keep your lawn looking its best year-round.', 'fa-leaf', 1],
                ['Hedge Trimming', 'hedge-trimming', 'Professional trimming and shaping of hedges and shrubs to enhance your property\'s appearance.', 'fa-tree', 1],
                ['Sprinkler Systems', 'sprinkler-systems', 'Installation and maintenance of efficient irrigation systems to keep your landscape hydrated.', 'fa-tint', 1],
                ['Seasonal Cleanup', 'seasonal-cleanup', 'Spring and fall cleanup services to prepare your property for the changing seasons.', 'fa-snowflake', 1],
                ['Garden Care', 'garden-care', 'Expert weeding, mulching, and plant care to ensure your garden thrives in every season.', 'fa-seedling', 0],
                ['Lawn Aeration', 'lawn-aeration', 'Core aeration to reduce soil compaction and promote healthier root growth.', 'fa-air-freshener', 0]
            ];
            
            $stmt = $db->prepare("INSERT INTO services (name, slug, description, icon, featured) VALUES (?, ?, ?, ?, ?)");
            foreach ($services as $service) {
                $stmt->execute($service);
            }
            
            // Insert sample settings data
            $settings = [
                ['site_name', 'Genuine Landscapers', 'general'],
                ['site_description', 'Professional landscaping services in Windsor and surrounding areas', 'general'],
                ['contact_email', 'info@genuinelandscapers.ca', 'contact'],
                ['contact_phone', '519-300-6434', 'contact'],
                ['contact_address', '1609 Moy Ave, Windsor, ON N8X 4C8', 'contact'],
                ['business_hours', 'Monday - Friday: 8:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: Closed', 'contact'],
                ['facebook_url', 'https://www.facebook.com/genuinelandscapers', 'social'],
                ['instagram_url', 'https://www.instagram.com/genuinelandscapers', 'social'],
                ['linkedin_url', 'https://www.linkedin.com/company/genuine-landscapers', 'social']
            ];
            
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?)");
            foreach ($settings as $setting) {
                $stmt->execute($setting);
            }
            
            return true;
        } else {
            // Data already exists
            return false;
        }
    } catch (PDOException $e) {
        log_activity('error', 'Sample data insertion failed: ' . $e->getMessage());
        return false;
    }
}

// Run the setup
$setup_result = setup_database();
$data_result = insert_sample_data();

// Output results
if ($setup_result) {
    echo "Database setup completed successfully.<br>";
    
    if ($data_result) {
        echo "Sample data inserted successfully.<br>";
    } else {
        echo "Sample data was not inserted (may already exist).<br>";
    }
} else {
    echo "Database setup failed. Check logs for details.<br>";
}

echo "<p><a href='../index.php'>Return to homepage</a></p>";
