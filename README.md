# CareerAI - Your Future, Powered by AI

CareerAI is a comprehensive, AI-powered career development platform designed to help students, graduates, and professionals navigate their career journeys. Built with a focus on security, accessibility, and real-world AI integration, it provides tools to analyze CVs, match with live jobs, prepare for interviews, and generate personalized learning roadmaps.

## 🚀 Key Features

*   **Secure Authentication:** User registration with OTP email verification, secure password hashing, and password reset functionality.
*   **AI CV Analysis:** Upload a PDF CV to receive instant ATS scoring, strengths, areas for improvement, and missing skills.
*   **Live Job Matching:** Integrates with live job APIs to find real roles, calculate AI compatibility scores, and provide direct application links.
*   **AI Interview Prep:** Generates realistic behavioral and technical interview questions and provides AI feedback on your typed answers.
*   **Learning Roadmap:** Creates a step-by-step, personalized learning path to bridge the gap between current skills and career goals.
*   **AI Career Assistant:** A real-time chat interface powered by Google Gemini for instant career advice.
*   **Admin Dashboard:** A secure, role-based admin panel to view system statistics, manage users, and monitor platform activity.
*   **Accessibility & Mobile-First:** Fully responsive design with WCAG AA compliance, including screen-reader support, keyboard navigation, and proper focus states.

## 🛠️ Technology Stack

*   **Backend:** PHP 8.x (using MySQLi prepared statements)
*   **Database:** MySQL
*   **Frontend:** HTML5, CSS3, Vanilla JavaScript
*   **AI Integration:** Google Gemini API (`gemini-3.1-flash-lite`)
*   **Email Services:** PHPMailer with Gmail SMTP
*   **PDF Parsing:** Smalot PDFParser
*   **Security:** Environment variables (`.env`), Session management, XSS protection, Secure file uploads.

## ⚙️ Installation & Setup (Local Development)

To run this project locally on your machine, follow these steps:

1.  **Clone the repository** into your XAMPP `htdocs` folder.
2.  **Install Dependencies:** Open your terminal, navigate to the project folder, and run:
    ```bash
    composer install