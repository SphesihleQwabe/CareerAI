-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2026 at 10:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `careerai`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role` enum('user','assistant') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `role`, `message`, `created_at`) VALUES
(1, 5, 'user', 'hi', '2026-10-28 15:24:19'),
(2, 5, 'assistant', 'Hello. How can I assist you with your career progression toward becoming a PHP developer today?', '2026-10-28 15:24:21'),
(3, 5, 'user', 'what is water', '2026-10-28 15:24:33'),
(4, 5, 'assistant', 'In a professional context, if you are referring to the **Waterfall model**, it is a traditional project management methodology often used in software development.\n\nIt follows a linear, sequential approach where each phase of the project—Requirements, Analysis, Design, Coding, Testing, and Deployment—must be completed before the next one begins.\n\nIn the context of PHP development:\n* **Pros:** It provides clear structure and documentation, which can be useful for enterprise-level projects.\n* **Cons:** It lacks flexibility. Unlike Agile methodologies, it is difficult to go back and change requirements once a phase is completed.\n\nAre you currently working on a project using this methodology, or are you researching development lifecycles?', '2026-10-28 15:24:35'),
(5, 5, 'user', 'who are you', '2026-10-28 15:25:00'),
(6, 5, 'assistant', 'I am CareerAI, your professional career assistant. My purpose is to provide guidance, resources, and strategic advice to help you achieve your goal of becoming a specialized PHP developer.', '2026-10-28 15:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `cvs`
--

CREATE TABLE `cvs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cvs`
--

INSERT INTO `cvs` (`id`, `user_id`, `original_filename`, `stored_filename`, `file_size`, `file_type`, `uploaded_at`) VALUES
(11, 5, 'Sphesihle_Qwabe_CV.pdf', '260f50f02e22347fb1ff37ecfaba8b77.pdf', 96483, 'application/pdf', '2026-11-06 11:21:57');

-- --------------------------------------------------------

--
-- Table structure for table `cv_analysis`
--

CREATE TABLE `cv_analysis` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `cv_id` int(10) UNSIGNED NOT NULL,
  `ai_response` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv_analysis`
--

INSERT INTO `cv_analysis` (`id`, `user_id`, `cv_id`, `ai_response`, `created_at`) VALUES
(23, 5, 11, '<h3>CV Analysis Report: Sphesihle Smiso Qwabe</h3>\n\n<p><strong>Overall Score: 72/100</strong></p>\n<p><em>Note: This score reflects your strong technical foundation and practical experience, though the CV structure could be further optimized for modern Applicant Tracking Systems by highlighting specific impact metrics and modern frameworks.</em></p>\n\n<h3>Top 3 Strengths</h3>\n<ul>\n    <li><strong>Practical Project Experience:</strong> Your internship at NetApps Development clearly demonstrates your ability to build a full-stack e-commerce application, including real-world integrations like PayFast and OTP verification.</li>\n    <li><strong>Strong Foundational Knowledge:</strong> You have a clear grasp of the LAMP stack (Linux/Apache/MySQL/PHP), which is highly relevant for entry-level web development roles.</li>\n    <li><strong>Soft Skills & Leadership:</strong> Your experience in student mentoring and committee roles provides excellent evidence of leadership, communication, and interpersonal skills—traits that employers highly value in junior talent.</li>\n</ul>\n\n<h3>Top 3 Areas for Improvement</h3>\n<ul>\n    <li><strong>Quantifiable Impact:</strong> Instead of listing tasks (e.g., \"Assisted with troubleshooting\"), try to use metrics. For example: \"Resolved an average of 10+ technical tickets per week\" or \"Improved site load time by 15%.\"</li>\n    <li><strong>Expand the Tech Stack:</strong> While PHP/MySQL is great, the current industry trend favors modern frameworks. Consider mentioning familiarity with Laravel (for PHP) or React (for frontend).</li>\n    <li><strong>Professional Summary:</strong> The current summary is functional but standard. Aim for a \"hook\" in the first sentence that states your core expertise and what value you bring to a team immediately.</li>\n</ul>\n\n<h3>Missing Skills</h3>\n<ul>\n    <li><strong>Version Control (Advanced):</strong> You list Git/GitHub, but highlight specific workflows like \"Git branching strategies\" or \"Pull requests\" to show collaborative coding experience.</li>\n    <li><strong>API Development:</strong> Mentioning experience with RESTful APIs would be a significant advantage for software development roles.</li>\n    <li><strong>Testing Frameworks:</strong> Familiarity with Unit Testing (e.g., PHPUnit or Jest) would help you stand out as a more professional, quality-focused developer.</li>\n    <li><strong>Cloud Basics:</strong> Exposure to AWS, Azure, or basic deployment processes (cPanel, Heroku) would bridge the gap between \"coding\" and \"production.\"</li>\n</ul>\n\n<h3>Suggested Job Roles</h3>\n<ul>\n    <li><strong>Junior Web Developer (PHP/Full Stack)</strong></li>\n    <li><strong>IT Support Technician / Desktop Support</strong></li>\n    <li><strong>Junior Software Developer</strong></li>\n    <li><strong>Database Administrator Intern</strong></li>\n    <li><strong>Technical Assistant</strong></li>\n</ul>', '2026-11-06 11:22:35'),
(24, 5, 11, '<h3>CV Analysis Report: Sphesihle Smiso Qwabe</h3>\n\n<p><strong>Overall ATS Score: 74/100</strong></p>\n<p><em>Note: This score reflects your strong technical foundation and recent internship experience. To increase this score, focus on quantifying your impact and expanding your modern framework knowledge.</em></p>\n\n<h3>Top 3 Strengths</h3>\n<ul>\n    <li><strong>Practical Full-Stack Exposure:</strong> You have tangible experience building an e-commerce application, including complex features like payment gateway integration (PayFast) and OTP authentication. This is highly attractive to employers.</li>\n    <li><strong>Strong Soft Skills & Leadership:</strong> Your background as a mentor and House Committee member demonstrates excellent communication, teamwork, and leadership, which differentiates you from other junior developers.</li>\n    <li><strong>Relevant Education:</strong> A Diploma from MUT with a focus on Software Development provides a solid academic base that aligns perfectly with your practical experience.</li>\n</ul>\n\n<h3>Top 3 Areas for Improvement</h3>\n<ul>\n    <li><strong>Quantify Achievements:</strong> Instead of saying \"Assisted with troubleshooting,\" state \"Resolved 15+ hardware/software support tickets per week.\" Use metrics to prove your efficiency.</li>\n    <li><strong>Modernize the Tech Stack:</strong> While PHP/MySQL is great for legacy systems, modern development is shifting toward frameworks like Laravel (for PHP) or moving into JavaScript frameworks like React or Node.js.</li>\n    <li><strong>Formatting & Layout:</strong> Ensure your layout is clean and ATS-friendly by using standard headings and minimizing decorative symbols that can confuse parsing software.</li>\n</ul>\n\n<h3>Missing Skills</h3>\n<ul>\n    <li><strong>Modern Frameworks:</strong> Familiarity with a PHP framework like <strong>Laravel</strong> is essential for professional web development roles.</li>\n    <li><strong>API Development:</strong> Experience with RESTful APIs is a highly requested skill for backend roles.</li>\n    <li><strong>Cloud Basics:</strong> Exposure to cloud platforms (AWS, Azure, or Firebase) would be a massive advantage for a junior developer.</li>\n    <li><strong>Agile/Scrum:</strong> Mentioning familiarity with project management methodologies like Jira or Trello would highlight your workplace readiness.</li>\n</ul>\n\n<h3>Suggested Job Roles</h3>\n<ul>\n    <li><strong>Junior Web Developer (PHP/Full-Stack)</strong></li>\n    <li><strong>Junior Software Developer</strong></li>\n    <li><strong>IT Support Technician / Desktop Support</strong></li>\n    <li><strong>Junior Systems Analyst</strong></li>\n    <li><strong>Graduate Intern (Software Development/IT)</strong></li>\n</ul>', '2026-11-06 11:22:43'),
(25, 5, 11, '<h3>CV Analysis Report: Sphesihle Smiso Qwabe</h3>\n\n<p><strong>Overall Score: 74/100</strong></p>\n<p><em>Note: This score reflects your strong technical foundation and practical project experience. To reach a higher score, focus on quantifying your impact and diversifying your tech stack beyond legacy PHP/MySQL.</em></p>\n\n<h3>Top 3 Strengths</h3>\n<ul>\n    <li><strong>Practical Full-Stack Project:</strong> Your work at NetApps Development demonstrates a complete project lifecycle, from database design to payment integration (PayFast), which is highly attractive to recruiters.</li>\n    <li><strong>Leadership & Soft Skills:</strong> Your documented experience in student mentoring and committee roles demonstrates reliability, communication, and professional maturity beyond just technical tasks.</li>\n    <li><strong>Clear Technical Foundation:</strong> You have a cohesive set of core web development skills (PHP, SQL, JS) that align well with entry-level development roles.</li>\n</ul>\n\n<h3>Top 3 Areas for Improvement</h3>\n<ul>\n    <li><strong>Quantify Achievements:</strong> Your current experience is task-based. Use metrics where possible. Instead of \"Developed an e-commerce app,\" use \"Developed an e-commerce platform that successfully integrated PayFast API, reducing checkout friction.\"</li>\n    <li><strong>Standardize Formatting:</strong> Ensure your \"Personal Details\" are consolidated. Avoid repeating \"Durban\" twice and ensure your contact details are linked (e.g., clickable email and LinkedIn profile).</li>\n    <li><strong>Highlight Modern Frameworks:</strong> You are currently heavy on vanilla PHP. Mentioning familiarity with modern frameworks (like Laravel) or version control best practices (Git branching strategies) will make you more competitive.</li>\n</ul>\n\n<h3>Missing Skills</h3>\n<ul>\n    <li><strong>Modern Frameworks:</strong> Add experience or exposure to Laravel (for PHP) or React/Vue (for frontend).</li>\n    <li><strong>API Development:</strong> You have integrated payments, but explicitly list experience with RESTful API design.</li>\n    <li><strong>Cloud & DevOps Basics:</strong> Knowledge of AWS, Azure, or basic deployment (e.g., Docker, Heroku, or Netlify) would bridge the gap between \"student project\" and \"production-ready software.\"</li>\n    <li><strong>Agile/Scrum:</strong> Mention familiarity with JIRA, Trello, or Agile methodologies, as this is standard in professional development environments.</li>\n</ul>\n\n<h3>Suggested Job Roles</h3>\n<ul>\n    <li><strong>Junior Web Developer:</strong> Your PHP/MySQL stack is perfectly suited for small-to-medium enterprise (SME) digital agencies.</li>\n    <li><strong>IT Support Technician:</strong> Your hardware/software troubleshooting experience makes you a strong candidate for help-desk or Tier 1 support roles.</li>\n    <li><strong>Junior Software Developer (Graduate Program):</strong> Your balanced background in leadership and coding makes you an ideal candidate for corporate graduate development programs.</li>\n    <li><strong>Technical Assistant:</strong> Roles that require both coding knowledge and the ability to maintain internal office hardware/networks.</li>\n</ul>', '2026-11-06 11:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `interview_questions`
--

CREATE TABLE `interview_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `career_goal` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `user_answer` text NOT NULL DEFAULT '',
  `ai_feedback` text NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_questions`
--

INSERT INTO `interview_questions` (`id`, `user_id`, `career_goal`, `question`, `user_answer`, `ai_feedback`, `created_at`) VALUES
(6, 5, 'PHP developer', 'Tell me about a challenging project you worked on during your studies and how you overcame a specific technical obstacle.', 'g', '<div style=\'margin-bottom: 8px;\'><strong>Score: 0/100</strong></div><p style=\'margin-bottom: 12px;\'><p>The answer provided is completely inadequate. A single letter response fails to address the question, demonstrates a lack of professional communication, and provides zero insight into your technical background or problem-solving capabilities.</p></p><div style=\'background: white; padding: 12px; border: 1px solid #DCE7F2; border-radius: 6px;\'><strong>Ideal Answer:</strong><br><p>During my studies, I built an inventory management system using PHP and MySQL. A major obstacle was handling race conditions when multiple users updated stock levels simultaneously. I overcame this by implementing database transactions and using \'SELECT ... FOR UPDATE\' locking mechanisms to ensure data integrity during concurrent writes. This experience taught me the importance of atomic operations in web applications.</p></div>', '2026-10-29 15:25:18'),
(7, 5, 'PHP developer', 'How do you handle conflict or a difference of opinion when working within a team environment?', '', '', '2026-10-29 15:25:18'),
(8, 5, 'PHP developer', 'What is the difference between \'==\' and \'===\' in PHP, and why would you choose one over the other?', '', '', '2026-10-29 15:25:18'),
(9, 5, 'PHP developer', 'Can you explain the concept of Dependency Injection in PHP and why it is beneficial for code maintainability?', '', '', '2026-10-29 15:25:18'),
(10, 5, 'PHP developer', 'What is the purpose of a PSR (PHP Standard Recommendation), and can you name one you are familiar with?', '', '', '2026-10-29 15:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `location`, `description`, `requirements`, `created_at`) VALUES
(1, 'Junior PHP Developer', 'TechSolutions SA', 'Johannesburg', 'We are looking for an enthusiastic Junior PHP Developer to join our dynamic team. You will work on building and maintaining web applications.', 'PHP, MySQL, HTML, CSS, JavaScript. Understanding of MVC frameworks like Laravel is a plus.', '2026-10-21 14:04:36'),
(2, 'IT Support Technician', 'Global Helpdesk', 'Cape Town', 'Provide first-line technical support to end-users. Troubleshoot hardware and software issues, manage tickets, and ensure customer satisfaction.', 'Windows OS, Networking basics (TCP/IP, DNS), hardware troubleshooting, excellent communication skills.', '2026-10-21 14:04:36'),
(3, 'Data Analyst Intern', 'DataCorp', 'Durban', 'Exciting internship opportunity for a recent graduate to assist the data team with cleaning, analyzing, and visualizing data.', 'Excel, SQL, Python or R, strong analytical mindset, attention to detail.', '2026-10-21 14:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `job_matches`
--

CREATE TABLE `job_matches` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `match_score` int(10) UNSIGNED NOT NULL,
  `ai_feedback` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_matches`
--

INSERT INTO `job_matches` (`id`, `user_id`, `job_id`, `match_score`, `ai_feedback`, `created_at`) VALUES
(1, 5, 1, 50, '<p>The candidate shows good potential for a junior position with 1 year of software development experience and an explicit goal to specialize in PHP. However, their profile lacks specific demonstration of the required technical stack, notably PHP, MySQL, HTML, CSS, JavaScript, and familiarity with MVC frameworks like Laravel.</p>', '2026-10-21 14:32:04'),
(2, 5, 3, 20, '<p>While the candidate has a foundational background in software development as a recent graduate, their stated focus is strictly on PHP web development. They currently lack the core analytical competencies required for this role, specifically missing proficiency in <strong>Excel</strong>, <strong>SQL</strong>, and <strong>Python or R</strong>, and their career trajectory does not align with a data analytics path.</p>', '2026-10-21 14:45:40'),
(3, 5, 2, 25, '<p>The candidate has a background in software development aiming for PHP roles, which represents a significant mismatch with this IT Support position. While their general technical literacy is beneficial, they are missing core requirements such as hands-on hardware troubleshooting, Windows OS support, and foundational networking knowledge (TCP/IP, DNS).</p>', '2026-10-21 14:45:57');

-- --------------------------------------------------------

--
-- Table structure for table `learning_roadmaps`
--

CREATE TABLE `learning_roadmaps` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `career_goal` varchar(100) NOT NULL,
  `roadmap_content` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_roadmaps`
--

INSERT INTO `learning_roadmaps` (`id`, `user_id`, `career_goal`, `roadmap_content`, `created_at`) VALUES
(1, 5, 'PHP developer', '<h3>Stage 1: Core Fundamentals & Modern PHP</h3>\n<ul>\n    <li><strong>Language Basics:</strong> Master PHP syntax, data types, control structures, and functions.</li>\n    <li><strong>Modern Standards:</strong> Learn PSR standards and Composer for dependency management.</li>\n    <li><strong>Project:</strong> Build a CLI-based task manager to practice logic and data handling.</li>\n</ul>\n\n<h3>Stage 2: Database Integration & Web Foundations</h3>\n<ul>\n    <li><strong>Database Management:</strong> Learn MySQL/MariaDB, SQL queries, and PDO for secure database interactions.</li>\n    <li><strong>Web Concepts:</strong> Understand HTTP methods, sessions, cookies, and form handling.</li>\n    <li><strong>Project:</strong> Develop a CRUD-based web application (e.g., a simple Blog or Inventory System).</li>\n</ul>\n\n<h3>Stage 3: Professional Frameworks & Architecture</h3>\n<ul>\n    <li><strong>Framework Proficiency:</strong> Deep dive into Laravel (industry leader) or Symfony.</li>\n    <li><strong>Architecture:</strong> Study MVC (Model-View-Controller) patterns and RESTful API design.</li>\n    <li><strong>Project:</strong> Create a full-stack e-commerce dashboard with authentication and API integration.</li>\n</ul>\n\n<h3>Stage 4: Advanced Engineering & Deployment</h3>\n<ul>\n    <li><strong>Testing & Quality:</strong> Implement PHPUnit for unit testing and code debugging.</li>\n    <li><strong>DevOps Basics:</strong> Learn Git workflows, Docker containerization, and cloud deployment (AWS/DigitalOcean).</li>\n    <li><strong>Project:</strong> Deploy a high-performance web application featuring automated testing and CI/CD pipelines.</li>\n</ul>', '2026-10-26 15:31:58');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `current_status` enum('high_school','university','graduate','professional') NOT NULL DEFAULT 'university',
  `headline` varchar(100) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `location` varchar(100) NOT NULL DEFAULT '',
  `career_goal` varchar(100) NOT NULL DEFAULT '',
  `years_experience` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `matric_subjects` varchar(255) NOT NULL DEFAULT '',
  `matric_aps` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `current_status`, `headline`, `bio`, `location`, `career_goal`, `years_experience`, `matric_subjects`, `matric_aps`, `created_at`, `updated_at`) VALUES
(1, 5, 'graduate', 'Software developer', 'I want to specalise in PHP', 'Durban', 'PHP developer', 1, '', 0, '2026-10-21 14:19:06', '2026-10-21 14:19:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL DEFAULT '',
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `otp_code` varchar(6) NOT NULL DEFAULT '',
  `otp_expires_at` datetime DEFAULT NULL,
  `otp_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `reset_token` varchar(64) NOT NULL DEFAULT '',
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `role`, `is_active`, `email_verified`, `otp_code`, `otp_expires_at`, `otp_attempts`, `reset_token`, `reset_expires_at`, `created_at`, `updated_at`) VALUES
(5, 'Sphesihle', 'Qwabe', 'qwabesphesihle64@gmail.com', '0763668977', '$2y$10$GMlbWpkZAov8mytYVyE0LO4keDG.KaYn1QPBQ4ScS28jYg.E81xIu', 'user', 1, 1, '', NULL, 0, '', NULL, '2026-10-16 10:25:24', '2026-10-16 10:39:29'),
(6, 'System', 'Administrator', 'admin@careerai.local', '0821234567', '$2y$10$hJkNEIuGAHyKSNIuiuhHXeB1CpO8R1xR1yN8KOZ72XbWjK43bZ9py', 'admin', 1, 1, '', NULL, 0, '', NULL, '2026-10-29 15:43:11', '2026-11-06 11:35:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_chat_user` (`user_id`);

--
-- Indexes for table `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `cv_analysis`
--
ALTER TABLE `cv_analysis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `fk_analysis_cv` (`cv_id`);

--
-- Indexes for table `interview_questions`
--
ALTER TABLE `interview_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_interview_user` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_matches`
--
ALTER TABLE `job_matches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_job` (`user_id`,`job_id`),
  ADD KEY `fk_matches_job` (`job_id`);

--
-- Indexes for table `learning_roadmaps`
--
ALTER TABLE `learning_roadmaps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_id` (`user_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cvs`
--
ALTER TABLE `cvs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cv_analysis`
--
ALTER TABLE `cv_analysis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `interview_questions`
--
ALTER TABLE `interview_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_matches`
--
ALTER TABLE `job_matches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `learning_roadmaps`
--
ALTER TABLE `learning_roadmaps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `fk_chat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cvs`
--
ALTER TABLE `cvs`
  ADD CONSTRAINT `fk_cvs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cv_analysis`
--
ALTER TABLE `cv_analysis`
  ADD CONSTRAINT `fk_analysis_cv` FOREIGN KEY (`cv_id`) REFERENCES `cvs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_analysis_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_questions`
--
ALTER TABLE `interview_questions`
  ADD CONSTRAINT `fk_interview_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_matches`
--
ALTER TABLE `job_matches`
  ADD CONSTRAINT `fk_matches_job` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_matches_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_roadmaps`
--
ALTER TABLE `learning_roadmaps`
  ADD CONSTRAINT `fk_roadmap_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
