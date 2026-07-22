-- Job Application Portal Database Export
-- Database: `projects`
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `admin_login`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_user_name` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `admin_login`
-- Default password is 'admin123' (hashed)
INSERT INTO `admin_login` (`admin_user_name`, `admin_password`) VALUES
('admin', 'admin123');

-- --------------------------------------------------------
-- Table structure for table `jobregistration`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobregistration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `refer` varchar(255) DEFAULT NULL,
  `planguage` varchar(255) NOT NULL,
  `cv_doc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `quiz_questions`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `question` text NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  `answer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `quiz_questions`
INSERT INTO `quiz_questions` (`category`, `question`, `option1`, `option2`, `option3`, `option4`, `answer`) VALUES
('PHP', 'What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Personal Hypertext Processor', 'PHP: Hypertext Preprocessor'),
('PHP', 'Which symbol is used to access properties of an object in PHP?', '.', '->', '::', '@', '->'),
('PHP', 'Which function is used to include a file and stop execution if not found?', 'include()', 'require()', 'load()', 'attach()', 'require()'),
('PHP', 'Which operator is used to concatenate strings in PHP?', '+', '.', '&&', '&', '.'),
('PHP', 'Which superglobal contains form data sent via GET?', '$_POST', '$_COOKIE', '$_GET', '$_SESSION', '$_GET'),
('PHP', 'Which function starts a session in PHP?', 'start_session()', 'session_begin()', 'session_start()', 'begin_session()', 'session_start()'),
('PHP', 'Which built-in function escapes HTML characters to prevent XSS?', 'htmlspecialchars()', 'strip_tags()', 'addslashes()', 'urlencode()', 'htmlspecialchars()'),
('Java', 'Which of the following is not a Java features?', 'Dynamic', 'Architecture Neutral', 'Use of pointers', 'Object-oriented', 'Use of pointers'),
('Java', 'What is the return type of the hashCode() method in the Object class?', 'Object', 'int', 'long', 'void', 'int'),
('Java', 'Which keyword is used for inheritance in Java?', 'implements', 'extends', 'inherits', 'instanceof', 'extends'),
('Java', 'What does JVM stand for?', 'Java Variable Machine', 'Java Vendor Machine', 'Java Virtual Machine', 'Java Verified Machine', 'Java Virtual Machine'),
('Java', 'Checked exceptions are subclasses of which class?', 'RuntimeException', 'Error', 'Exception', 'Throwable', 'Exception'),
('Java', 'What is the default value of a boolean variable in a Java class?', 'true', 'false', '0', 'null', 'false'),
('Python', 'Which data type is used to store multiple items in a single variable?', 'List', 'Integer', 'Float', 'Boolean', 'List'),
('Python', 'How do you create a variable with the numeric value 5?', 'x = 5', 'x = int(5)', 'Both are correct', 'None is correct', 'Both are correct');

INSERT INTO `quiz_questions` (`category`, `question`, `option1`, `option2`, `option3`, `option4`, `answer`) VALUES
('Python', 'Which keyword is used to define a function in Python?', 'func', 'def', 'function', 'lambda', 'def'),
('Python', 'Which collection type is immutable?', 'List', 'Tuple', 'Set', 'Dictionary', 'Tuple'),
('Python', 'Which command installs a package named requests?', 'pip install requests', 'pip get requests', 'python -m requests', 'pip add requests', 'pip install requests'),
('Python', 'Which library provides the DataFrame structure?', 'numpy', 'pandas', 'requests', 'matplotlib', 'pandas');

-- Frontend Development Questions
INSERT INTO `quiz_questions` (`category`, `question`, `option1`, `option2`, `option3`, `option4`, `answer`) VALUES
('Frontend', 'Which HTML tag is used to define an internal style sheet?', '<css>', '<script>', '<style>', '<link>', '<style>'),
('Frontend', 'Which CSS property is used to change the text color?', 'color', 'text-color', 'font-color', 'text-style', 'color'),
('Frontend', 'Which HTML attribute is used to define inline styles?', 'class', 'style', 'styles', 'font', 'style'),
('Frontend', 'What does CSS stand for?', 'Cascading Style Sheets', 'Creative Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets', 'Cascading Style Sheets'),
('Frontend', 'Which property is used to change the background color in CSS?', 'bgcolor', 'background-color', 'color', 'bg-color', 'background-color'),
('Frontend', 'How do you select an element with id "demo" in CSS?', '.demo', '#demo', '*demo', 'demo', '#demo'),
('Frontend', 'Which JavaScript method is used to select an element by ID?', 'querySelector()', 'getElementById()', 'getElement()', 'selectById()', 'getElementById()'),
('Frontend', 'What is the correct HTML for creating a hyperlink?', '<a href="url">Link</a>', '<link>url</link>', '<a url="link">Text</a>', '<hyperlink>url</hyperlink>', '<a href="url">Link</a>'),
('Frontend', 'Which CSS property controls the text size?', 'text-size', 'font-size', 'text-style', 'font-style', 'font-size'),
('Frontend', 'How do you declare a JavaScript variable?', 'variable name;', 'v name;', 'var name;', 'declare name;', 'var name;'),
('Frontend', 'Which event occurs when a user clicks on an HTML element?', 'onchange', 'onmouseover', 'onclick', 'onhover', 'onclick'),
('Frontend', 'What is the correct syntax for referring to an external JavaScript file?', '<script href="file.js">', '<script name="file.js">', '<script src="file.js">', '<javascript>file.js</javascript>', '<script src="file.js">'),
('Frontend', 'Which HTML tag is used to define a JavaScript?', '<javascript>', '<js>', '<script>', '<code>', '<script>'),
('Frontend', 'How do you create a function in JavaScript?', 'function myFunction()', 'function:myFunction()', 'create myFunction()', 'def myFunction()', 'function myFunction()'),
('Frontend', 'Which CSS property is used to make text bold?', 'text-weight', 'font-weight', 'text-style', 'font-bold', 'font-weight'),
('Frontend', 'What is the correct HTML for inserting an image?', '<img href="image.jpg">', '<image src="image.jpg">', '<img src="image.jpg">', '<picture>image.jpg</picture>', '<img src="image.jpg">'),
('Frontend', 'Which CSS display value is used to create a flexible container?', 'flex', 'flexbox', 'flexible', 'grid', 'flex'),
('Frontend', 'What does DOM stand for?', 'Document Object Model', 'Display Object Management', 'Digital Orientation Model', 'Document Oriented Model', 'Document Object Model'),
('Frontend', 'Which HTML5 element is used for playing video files?', '<movie>', '<media>', '<video>', '<film>', '<video>'),
('Frontend', 'Which CSS framework is developed by Twitter?', 'Foundation', 'Bootstrap', 'Materialize', 'Bulma', 'Bootstrap'),
('Frontend', 'What is the correct HTML for making a checkbox?', '<input type="check">', '<input type="checkbox">', '<checkbox>', '<check>', '<input type="checkbox">'),
('Frontend', 'Which symbol is used for comments in JavaScript?', '<!-- -->', '//', '/* */', 'Both // and /* */', 'Both // and /* */'),
('Frontend', 'What is the default position value in CSS?', 'relative', 'absolute', 'static', 'fixed', 'static'),
('Frontend', 'Which method is used to add new elements to an array in JavaScript?', 'push()', 'add()', 'append()', 'insert()', 'push()'),
('Frontend', 'What is the correct HTML for making a dropdown list?', '<list>', '<select>', '<dropdown>', '<input type="list">', '<select>'),
('Frontend', 'Which CSS property is used to add spacing between elements?', 'spacing', 'margin', 'padding', 'Both margin and padding', 'Both margin and padding'),
('Frontend', 'What is the latest version of HTML?', 'HTML4', 'HTML5', 'HTML6', 'XHTML', 'HTML5'),
('Frontend', 'Which JavaScript framework is developed by Facebook?', 'Angular', 'Vue.js', 'React', 'Svelte', 'React'),
('Frontend', 'What does the "box-sizing: border-box" CSS property do?', 'Adds border to box', 'Includes padding and border in element total width', 'Creates a box shadow', 'Sets box dimensions', 'Includes padding and border in element total width'),
('Frontend', 'Which HTML attribute specifies an alternate text for an image?', 'title', 'alt', 'src', 'longdesc', 'alt');


--js development questions

INSERT INTO `quiz_questions` (`category`, `question`, `option1`, `option2`, `option3`, `option4`, `answer`) VALUES
('javascript', 'Which company developed JavaScript?', 'Netscape', 'Microsoft', 'Sun Microsystems', 'IBM', 'Netscape'),
('javascript', 'Which symbol is used for comments in JavaScript?', '<!-- -->', '//', '/* */', 'Both // and /* */', 'Both // and /* */'),
('javascript', 'What is the correct syntax for referring to an external JavaScript file?', '<script href="file.js">', '<script name="file.js">', '<script src="file.js">', '<javascript>file.js</javascript>', '<script src="file.js">'),
('javascript', 'Which method is used to add new elements to an array in JavaScript?', 'push()', 'add()', 'append()', 'insert()', 'push()'),
('javascript', 'What does JSON stand for?', 'JavaScript Object Notation', 'Java Standard Object Notation', 'JavaScript Online Notation', 'Java Source Object Notation', 'JavaScript Object Notation'),
('javascript', 'Which JavaScript framework is developed by Facebook?', 'Angular', 'Vue.js', 'React', 'Svelte', 'React'),
('javascript', 'What is the correct way to write a JavaScript array?', 'var colors = "red", "green", "blue"', 'var colors = (1:"red", 2:"green", 3:"blue")', 'var colors = ["red", "green", "blue"]', 'var colors = 1 = ("red"), 2 = ("green"), 3 = ("blue")', 'var colors = ["red", "green", "blue"]'),
('javascript', 'Which built-in method combines the text of two strings and returns a new string?', 'append()', 'concat()', 'attach()', 'None of the above', 'concat()'),
('javascript', 'Which of the following is a correct way to create a Promise in JavaScript?', 'new Promise()', 'Promise.create()', 'create Promise()', 'new createPromise()', 'new Promise()'),
('javascript', 'What is the output of "typeof NaN" in JavaScript?', '"number"', '"NaN"', '"undefined"', '"object"', '"number"');

-- --------------------------------------------------------
-- Table structure for table `user_info`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cpassword` varchar(255) NOT NULL,
  `user_degree` varchar(255) NOT NULL,
  `user_skills` varchar(255) NOT NULL,
  `profile` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `user_quiz_status`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_quiz_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` enum('passed','failed') NOT NULL,
  `grooming_completed` tinyint(1) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `grooming_content`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grooming_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample grooming content per category
INSERT INTO `grooming_content` (`category`, `content`) VALUES
('PHP', '<h4>Core PHP Essentials</h4><ul><li><b>Superglobals:</b> Know $_GET, $_POST, $_SESSION and how to sanitize input.</li><li><b>Sessions & Cookies:</b> session_start(), secure cookie flags, and logout flows.</li><li><b>Security:</b> htmlspecialchars(), prepared statements with mysqli/PDO to prevent XSS/SQLi.</li><li><b>File Uploads:</b> Validate MIME types, size limits, and store outside webroot.</li><li><b>OOP Basics:</b> classes, objects, constructors, visibility, and autoloading.</li></ul>'),
('Java', '<h4>Java Fundamentals</h4><ul><li><b>OOP Pillars:</b> encapsulation, inheritance, polymorphism, abstraction; use of extends/implements.</li><li><b>Exceptions:</b> checked vs unchecked; try/catch/finally; custom exceptions.</li><li><b>Collections:</b> List, Set, Map differences and common implementations.</li><li><b>JVM/JRE/JDK:</b> know the roles and how bytecode is executed.</li><li><b>Basics:</b> static vs instance members; equals/hashCode contract.</li></ul>'),
('Python', '<h4>Python Essentials</h4><ul><li><b>Data Types:</b> list vs tuple (immutability), dict, set.</li><li><b>Functions:</b> def, *args, **kwargs, defaults; lambda basics.</li><li><b>Packages:</b> pip install usage; virtual environments.</li><li><b>Pandas/Numpy:</b> DataFrame basics, when to use numpy arrays.</li><li><b>Style:</b> PEP 8 naming, readable code, and docstrings.</li></ul>'),
('Frontend', '<h4>Frontend Development Basics</h4><ul><li><b>HTML5:</b> semantic tags (header, nav, section), forms, input types.</li><li><b>CSS3:</b> Flexbox/Grid for layouts; box model; responsive design with media queries.</li><li><b>JavaScript:</b> DOM manipulation, event handling, ES6 features (let/const, arrow functions).</li><li><b>Frameworks:</b> basics of Bootstrap for styling; React.js component structure.</li><li><b>Best Practices:</b> accessibility (ARIA), performance optimization (minification, lazy loading).</li></ul>'),
('UI/UX', '<h4>UI/UX Design Principles</h4><ul><li><b>User-Centered Design:</b> focus on user needs, personas, and usability testing.</li><li><b>Wireframing:</b> low-fidelity sketches to high-fidelity prototypes using tools like Figma.</li><li><b>Color Theory:</b> color psychology, contrast ratios for accessibility.</li><li><b>Typography:</b> font pairing, readability, hierarchy.</li><li><b>Interaction Design:</b> feedback, affordances, and intuitive navigation.</li></ul>');

-- --------------------------------------------------------
-- Table structure for table `grooming_videos`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grooming_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `video_url` varchar(500) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in seconds',
  `order_index` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample video playlists for each category
INSERT INTO `grooming_videos` (`category`, `title`, `description`, `video_url`, `duration`, `order_index`) VALUES
('PHP', 'PHP Basics - Variables & Data Types', 'Learn the fundamentals of PHP variables, data types, and operators', 'https://www.youtube.com/embed/OK_JCtrrv-c', 900, 1),
('PHP', 'PHP Functions & Arrays', 'Master PHP functions, arrays, and control structures', 'https://www.youtube.com/embed/1SnPKhCdlsU', 1200, 2),
('PHP', 'PHP MySQL Database Integration', 'Connect PHP with MySQL and perform CRUD operations', 'https://www.youtube.com/embed/9Kz3nZnLZTk', 1500, 3),
('Java', 'Java Basics - OOP Concepts', 'Introduction to Object-Oriented Programming in Java', 'https://www.youtube.com/embed/RnqC3L1q0gQ', 1800, 1),
('Java', 'Java Collections Framework', 'Understanding ArrayList, HashMap, and other collections', 'https://www.youtube.com/embed/Vnodgy0GhcM', 1200, 2),
('Java', 'Exception Handling in Java', 'Learn try-catch blocks and custom exceptions', 'https://www.youtube.com/embed/1XAfapkBQjk', 900, 3),
('Python', 'Python Fundamentals', 'Variables, data types, and basic syntax in Python', 'https://www.youtube.com/embed/_uQrJ0TkZlc', 1000, 1),
('Python', 'Python Functions & Modules', 'Creating functions, importing modules, and packages', 'https://www.youtube.com/embed/9Os0o3wzS_I', 1100, 2),
('Python', 'Python Data Structures', 'Lists, dictionaries, tuples, and sets explained', 'https://www.youtube.com/embed/W8KRzm-HUcc', 1300, 3);



INSERT INTO `grooming_videos` (`category`, `title`, `description`, `video_url`, `duration`, `order_index`) VALUES
('Frontend', 'HTML & CSS Basics', 'Learn the structure of web pages using HTML and style them with CSS', 'https://www.youtube.com/embed/mU6anWqZJcc', 1500, 1),
('Frontend', 'JavaScript Fundamentals', 'Introduction to JavaScript programming language', 'https://www.youtube.com/embed/W6NZfCO5SIk', 1800, 2),
('Frontend', 'Responsive Web Design', 'Techniques to make web pages look good on all devices', 'https://www.youtube.com/embed/srvUrASNj0s', 1200, 3),
('Frontend', 'CSS Flexbox & Grid', 'Layout techniques using Flexbox and CSS Grid', 'https://www.youtube.com/embed/JJSoEo8JSnc', 1400, 4),
('Frontend', 'JavaScript DOM Manipulation', 'Interacting with web page elements using the DOM', 'https://www.youtube.com/embed/0ik6X4DJKCc', 1600, 5),
('javascript', 'ES6 Features', 'Learn about new features introduced in ES6', 'https://www.youtube.com/embed/NCwa_xi0Uuc', 1300, 6),
('javascript', 'Introduction to React.js', 'Building user interfaces with React.js library', 'https://www.youtube.com/embed/Dorf8i6lCuk', 2000, 7),
('javascript', 'JavaScript Asynchronous Programming', 'Understanding callbacks, promises, and async/await', 'https://www.youtube.com/embed/_8gHHBlbziw', 1700, 8),
('UI/UX', 'UI/UX Design Principles', 'Fundamental principles of user interface and user experience design', 'https://www.youtube.com/embed/9B4f3b3j8Xw', 1100, 1),
('UI/UX', 'Wireframing & Prototyping', 'Creating wireframes and prototypes for web and mobile apps', 'https://www.youtube.com/embed/YrX6D5bK2bY', 1300, 2),
('UI/UX', 'Color Theory in Design', 'Understanding color theory and its application in design', 'https://www.youtube.com/embed/8Xg5bW8J6Kk', 900, 3);


-- --------------------------------------------------------
-- Table structure for table `user_video_progress`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_video_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `watched_duration` int(11) NOT NULL DEFAULT 0 COMMENT 'Watched duration in seconds',
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `last_watched` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_video_unique` (`user_id`, `video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
