-- Company/Job Provider System Database Schema
-- This extends the existing database with company functionality

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `companies`
-- Stores company/job provider information
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_email` varchar(255) NOT NULL UNIQUE,
  `company_phone` varchar(20) NOT NULL,
  `company_address` text NOT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `industry` varchar(100) NOT NULL,
  `company_size` enum('1-10', '11-50', '51-200', '201-500', '501-1000', '1000+') NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active', 'inactive', 'pending') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample company data
INSERT INTO `companies` (`company_name`, `company_email`, `company_phone`, `company_address`, `company_website`, `industry`, `company_size`, `description`, `password`) VALUES
('Tech Solutions Inc', 'hr@techsolutions.com', '1234567890', '123 Tech Street, Silicon Valley, CA', 'https://techsolutions.com', 'Information Technology', '51-200', 'Leading software development company specializing in web and mobile applications', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Digital Innovations', 'jobs@digitalinnovations.com', '9876543210', '456 Innovation Blvd, San Francisco, CA', 'https://digitalinnovations.com', 'Software Development', '11-50', 'Innovative startup focused on AI and machine learning solutions', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------
-- Table structure for table `company_jobs`
-- Stores job postings by companies
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `company_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `job_category` varchar(100) NOT NULL COMMENT 'e.g., Java Developer, Python Developer, Frontend Developer, PHP Developer',
  `job_description` text NOT NULL,
  `requirements` text NOT NULL,
  `responsibilities` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `employment_type` enum('Full-Time', 'Part-Time', 'Contract', 'Internship') NOT NULL,
  `salary_range` varchar(100) DEFAULT NULL,
  `experience_required` varchar(50) NOT NULL,
  `skills_required` text NOT NULL,
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `status` enum('active', 'closed', 'draft') DEFAULT 'active',
  `vacancy_count` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample job postings
INSERT INTO `company_jobs` (`company_id`, `job_title`, `job_category`, `job_description`, `requirements`, `responsibilities`, `location`, `employment_type`, `salary_range`, `experience_required`, `skills_required`, `deadline`, `vacancy_count`) VALUES
(1, 'Senior Java Developer', 'Java', 'We are looking for an experienced Java developer to join our team and work on enterprise applications.', '- Bachelor\'s degree in Computer Science\n- 5+ years of Java experience\n- Strong knowledge of Spring Framework\n- Experience with microservices', '- Design and develop Java applications\n- Write clean, maintainable code\n- Collaborate with cross-functional teams\n- Mentor junior developers', 'San Francisco, CA', 'Full-Time', '$100,000 - $130,000', '5+ years', 'Java, Spring Boot, Microservices, REST APIs, MySQL', '2026-03-31', 2),
(1, 'Python Backend Developer', 'Python', 'Join our team to build scalable backend systems using Python and Django.', '- 3+ years Python experience\n- Django/Flask expertise\n- RESTful API development\n- Database design skills', '- Develop backend APIs\n- Optimize database queries\n- Implement security best practices\n- Write unit tests', 'Remote', 'Full-Time', '$90,000 - $120,000', '3+ years', 'Python, Django, REST APIs, PostgreSQL, Docker', '2026-02-28', 3),
(2, 'Frontend Developer (React)', 'Frontend', 'Looking for a creative frontend developer passionate about building beautiful user interfaces.', '- 2+ years frontend development\n- Expert in React.js\n- HTML5, CSS3, JavaScript\n- Responsive design', '- Build responsive web applications\n- Create reusable components\n- Optimize performance\n- Collaborate with designers', 'New York, NY', 'Full-Time', '$80,000 - $110,000', '2+ years', 'React, JavaScript, HTML5, CSS3, Bootstrap, Redux', '2026-02-15', 2),
(2, 'PHP Full Stack Developer', 'PHP', 'We need a skilled PHP developer to maintain and enhance our web applications.', '- 3+ years PHP experience\n- Laravel framework knowledge\n- MySQL database skills\n- Version control (Git)', '- Develop web applications\n- API development and integration\n- Database optimization\n- Bug fixing and maintenance', 'Los Angeles, CA', 'Contract', '$70,000 - $95,000', '3+ years', 'PHP, Laravel, MySQL, JavaScript, jQuery, Git', '2026-03-15', 1);

-- --------------------------------------------------------
-- Table structure for table `company_job_questions`
-- Stores quiz questions specific to each job posting
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `company_job_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  FOREIGN KEY (`job_id`) REFERENCES `company_jobs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample questions for Java Developer position (job_id = 1)
INSERT INTO `company_job_questions` (`job_id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_answer`) VALUES
(1, 'What is the main benefit of using Spring Boot?', 'Faster development', 'Reduced configuration', 'Built-in server', 'All of the above', 'All of the above'),
(1, 'Which annotation is used to create a REST controller in Spring?', '@Controller', '@RestController', '@Service', '@Component', '@RestController'),
(1, 'What is a microservice?', 'A small application', 'An independent deployable service', 'A database service', 'A testing framework', 'An independent deployable service'),
(1, 'Which design pattern is commonly used in Spring?', 'Singleton', 'Factory', 'Dependency Injection', 'Observer', 'Dependency Injection'),
(1, 'What is JPA?', 'Java Persistence API', 'Java Programming API', 'Java Protocol API', 'Java Package API', 'Java Persistence API');

-- Sample questions for Python Backend Developer position (job_id = 2)
INSERT INTO `company_job_questions` (`job_id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_answer`) VALUES
(2, 'Which framework is used for building web applications in Python?', 'Flask', 'Django', 'FastAPI', 'All of the above', 'All of the above'),
(2, 'What is Django ORM used for?', 'Database operations', 'User authentication', 'Template rendering', 'Static file serving', 'Database operations'),
(2, 'What does REST stand for?', 'Representational State Transfer', 'Remote State Transfer', 'Real Estate Transfer', 'Rapid State Transfer', 'Representational State Transfer'),
(2, 'Which HTTP method is used to update a resource?', 'GET', 'POST', 'PUT', 'DELETE', 'PUT'),
(2, 'What is a virtual environment in Python?', 'A cloud service', 'An isolated Python environment', 'A testing tool', 'A database', 'An isolated Python environment');

-- Sample questions for Frontend Developer position (job_id = 3)
INSERT INTO `company_job_questions` (`job_id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_answer`) VALUES
(3, 'What is React?', 'A JavaScript library', 'A CSS framework', 'A database', 'A backend framework', 'A JavaScript library'),
(3, 'What is JSX?', 'JavaScript XML', 'Java Syntax Extension', 'JSON XML', 'JavaScript Extension', 'JavaScript XML'),
(3, 'Which hook is used for side effects in React?', 'useState', 'useEffect', 'useContext', 'useMemo', 'useEffect'),
(3, 'What is Redux used for?', 'State management', 'Routing', 'API calls', 'Styling', 'State management'),
(3, 'What is the virtual DOM?', 'A real DOM copy', 'An in-memory representation of real DOM', 'A database', 'A server', 'An in-memory representation of real DOM');

-- Sample questions for PHP Developer position (job_id = 4)
INSERT INTO `company_job_questions` (`job_id`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_answer`) VALUES
(4, 'What is Laravel?', 'A PHP framework', 'A JavaScript library', 'A database', 'A CSS framework', 'A PHP framework'),
(4, 'Which Laravel component handles routing?', 'Eloquent', 'Blade', 'Route', 'Artisan', 'Route'),
(4, 'What is Eloquent in Laravel?', 'An ORM', 'A template engine', 'A testing tool', 'A CLI tool', 'An ORM'),
(4, 'What command creates a new Laravel project?', 'laravel new', 'composer create-project', 'php artisan new', 'npm create laravel', 'composer create-project'),
(4, 'What is middleware in Laravel?', 'A database layer', 'A filter for HTTP requests', 'A template system', 'A cache system', 'A filter for HTTP requests');

-- --------------------------------------------------------
-- Table structure for table `job_applications`
-- Stores applications from users to company jobs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `quiz_score` int(11) DEFAULT NULL,
  `quiz_status` enum('not_taken', 'passed', 'failed') DEFAULT 'not_taken',
  `application_status` enum('pending', 'reviewed', 'shortlisted', 'rejected') DEFAULT 'pending',
  `cover_letter` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`),
  FOREIGN KEY (`job_id`) REFERENCES `company_jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user_info`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `job_quiz_attempts`
-- Stores detailed quiz attempt information
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_quiz_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `correct_answers` int(11) NOT NULL,
  `score_percentage` decimal(5,2) NOT NULL,
  `attempt_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_taken` int(11) DEFAULT NULL COMMENT 'Time in seconds',
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`),
  KEY `job_id` (`job_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`application_id`) REFERENCES `job_applications`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`job_id`) REFERENCES `company_jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user_info`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
