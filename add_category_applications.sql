-- Create table for direct category-based applications
-- This allows job seekers who pass category quizzes to apply directly to any company in that category

CREATE TABLE IF NOT EXISTS category_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Rejected', 'Interview') DEFAULT 'Pending',
    company_notes TEXT DEFAULT NULL,
    user_message TEXT DEFAULT NULL,
    interview_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES user_info(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (user_id, company_id, category),
    
    INDEX idx_user_id (user_id),
    INDEX idx_company_id (company_id),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_application_date (application_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data to demonstrate the feature
-- Note: Make sure these user_ids and company_ids exist in your database
-- INSERT INTO category_applications (user_id, company_id, category, user_message, status) VALUES
-- (1, 1, 'PHP', 'I have 3 years of experience in PHP development', 'Pending'),
-- (1, 2, 'PHP', 'Excited to work with your team!', 'Interview');
