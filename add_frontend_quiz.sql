-- Frontend Development Quiz Questions
-- Import this file to add frontend quiz questions to your database
-- Run in phpMyAdmin or MySQL command line

USE `projects`;

-- Add Frontend Development Quiz Questions
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

-- Verify the questions were added
SELECT COUNT(*) as 'Total Frontend Questions' FROM quiz_questions WHERE category = 'Frontend';
