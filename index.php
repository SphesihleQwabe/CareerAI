<?php
/**
 * CareerAI - Landing Page
 */

$page_title = 'CareerAI - Your Future, Powered by AI';
$active_nav = 'home';

require_once __DIR__ . '/includes/header.php';
?>

    <!-- ============================= -->
    <!-- HERO SECTION                  -->
    <!-- ============================= -->
    <section class="hero">
        <div class="hero-container">

            <div class="hero-content">
                <p class="hero-eyebrow">YOUR FUTURE, POWERED BY AI</p>
                <h1 class="hero-title">
                    Build Your Career <br>
                    <span class="hero-title-accent">with CareerAI</span>
                </h1>
                <p class="hero-description">
                    Get personalized career guidance, analyze your CV, discover suitable
                    job opportunities, and prepare for interviews using intelligent
                    AI-powered tools.
                </p>
                <div class="hero-buttons">
                    <a href="<?php echo url('pages/register.php'); ?>" class="btn btn-primary btn-large">Get Started</a>
                    <a href="#features" class="btn btn-outline btn-large">Learn More</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="dashboard-preview">

                    <aside class="preview-sidebar">
                        <div class="preview-brand">
                            <span class="logo-mark small">CA</span>
                            <span class="logo-text small">CareerAI</span>
                        </div>
                        <ul class="preview-menu">
                            <li class="active">Dashboard</li>
                            <li>My Profile</li>
                            <li>My CV</li>
                            <li>AI CV Analysis</li>
                            <li>Job Matching</li>
                            <li>Interview Preparation</li>
                            <li>Learning Roadmap</li>
                            <li>AI Career Assistant</li>
                            <li>Career Progress</li>
                            <li>Settings</li>
                        </ul>
                    </aside>

                    <div class="preview-main">

                        <div class="preview-header">
                            <div>
                                <h3>Welcome back, Alex</h3>
                                <p>Your career journey is progressing well. Keep going!</p>
                            </div>
                        </div>

                        <div class="preview-stats">
                            <div class="preview-stat-card">
                                <p class="stat-label">CV Score</p>
                                <p class="stat-value">82%</p>
                            </div>
                            <div class="preview-stat-card">
                                <p class="stat-label">Skills Match</p>
                                <p class="stat-value">76%</p>
                            </div>
                            <div class="preview-stat-card">
                                <p class="stat-label">Career Readiness</p>
                                <p class="stat-value">78%</p>
                            </div>
                        </div>

                        <div class="preview-lower">

                            <div class="preview-panel">
                                <h4>Career Progress</h4>
                                <div class="chart-placeholder">
                                    <div class="bar" style="height: 40%;"></div>
                                    <div class="bar" style="height: 55%;"></div>
                                    <div class="bar" style="height: 45%;"></div>
                                    <div class="bar" style="height: 70%;"></div>
                                    <div class="bar" style="height: 85%;"></div>
                                    <div class="bar" style="height: 60%;"></div>
                                </div>
                            </div>

                            <div class="preview-panel">
                                <h4>Your Skills</h4>
                                <div class="skill-row">
                                    <span>PHP</span>
                                    <div class="skill-bar"><div class="skill-fill" style="width: 80%;"></div></div>
                                    <span>80%</span>
                                </div>
                                <div class="skill-row">
                                    <span>JavaScript</span>
                                    <div class="skill-bar"><div class="skill-fill" style="width: 70%;"></div></div>
                                    <span>70%</span>
                                </div>
                                <div class="skill-row">
                                    <span>MySQL</span>
                                    <div class="skill-bar"><div class="skill-fill" style="width: 65%;"></div></div>
                                    <span>65%</span>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="floating-badge badge-growth">
                    <p class="badge-title">Career Growth</p>
                    <p class="badge-sub">Track your progress</p>
                </div>
                <div class="floating-badge badge-match">
                    <p class="badge-title">Job Match</p>
                    <p class="badge-sub">92% compatible</p>
                </div>

            </div>

        </div>
    </section>

    <!-- ============================= -->
    <!-- FEATURES SECTION              -->
    <!-- ============================= -->
    <section class="features" id="features">
        <div class="section-container">

            <div class="section-header">
                <p class="section-eyebrow">POWERFUL CAREER TOOLS</p>
                <h2 class="section-title">Everything You Need to Build Your Career</h2>
                <p class="section-description">
                    CareerAI combines intelligent technology with practical career tools
                    to help you become job-ready.
                </p>
            </div>

            <div class="features-grid">

                <!-- AI CV Analysis -->
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">AI CV Analysis</h3>
                    <p class="feature-text">
                        Analyze your CV and receive intelligent feedback on your strengths,
                        weaknesses, and improvements.
                    </p>
                    <button class="feature-link" data-feature="cv">Learn more &rarr;</button>
                </article>

                <!-- Job Matching -->
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Job Matching</h3>
                    <p class="feature-text">
                        Discover job opportunities that match your skills, experience,
                        and career goals.
                    </p>
                    <button class="feature-link" data-feature="job">Learn more &rarr;</button>
                </article>

                <!-- Interview Preparation -->
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Interview Preparation</h3>
                    <p class="feature-text">
                        Practice realistic interview questions and receive AI-powered
                        feedback on your answers.
                    </p>
                    <button class="feature-link" data-feature="interview">Learn more &rarr;</button>
                </article>

                <!-- Learning Roadmap -->
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Learning Roadmap</h3>
                    <p class="feature-text">
                        Build a personalized learning path based on your career goals
                        and identified skill gaps.
                    </p>
                    <button class="feature-link" data-feature="roadmap">Learn more &rarr;</button>
                </article>

            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- HOW IT WORKS SECTION          -->
    <!-- ============================= -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-container">
            <div class="section-header">
                <p class="section-eyebrow">HOW IT WORKS</p>
                <h2 class="section-title">Your Career Journey Starts Here</h2>
                <p class="section-description">
                    A simple process designed to help you understand your career
                    direction and take the right next step.
                </p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <h3 class="step-title">Create Your Profile</h3>
                    <p class="step-text">Tell CareerAI about your education, skills, experience, interests, and career goals.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">02</div>
                    <h3 class="step-title">Get AI Insights</h3>
                    <p class="step-text">Our AI analyzes your information and identifies opportunities for your career development.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">03</div>
                    <h3 class="step-title">Build Your Future</h3>
                    <p class="step-text">Follow your recommendations, improve your skills, prepare for interviews, and discover opportunities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- CREATOR SECTION               -->
    <!-- ============================= -->
    <section class="creator-section" id="creator">
        <div class="section-container">
            <div class="creator-card">
                <!-- Your image is saved as creator.jpg in assets/images/ -->
                <img src="<?php echo url('assets/images/creator.jpg'); ?>" alt="Sphesihle Qwabe" class="creator-img">
                
                <div class="creator-info">
                    <p class="section-eyebrow">MEET THE CREATOR</p>
                    <h2 class="creator-name">Sphesihle Qwabe</h2>
                    <p class="creator-title">Full Stack Developer & AI Integrator</p>
                    
                    <p class="creator-bio">
                        I am the developer behind CareerAI. I designed and built this platform from the ground up to help students, graduates, and professionals navigate their career journeys using the power of artificial intelligence.
                    </p>
                    
                    <div class="creator-contact">
                        <a href="mailto:qwabesphesihle64@gmail.com" class="creator-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            qwabesphesihle64@gmail.com
                        </a>
                        <a href="tel:0763668977" class="creator-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            076 366 8977
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- MODAL POPUP (Hidden by default) -->
    <!-- ============================= -->
    <div id="feature-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2 id="modal-title" class="modal-title"></h2>
            <div id="modal-body" class="modal-body"></div>
            <a href="<?php echo url('pages/register.php'); ?>" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Get Started Free</a>
        </div>
    </div>

    <!-- ============================= -->
    <!-- MODAL JAVASCRIPT & CSS        -->
    <!-- ============================= -->
    <style>
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(11, 37, 69, 0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .modal-content { background: white; padding: 32px; border-radius: 16px; max-width: 500px; width: 100%; position: relative; box-shadow: 0 24px 60px rgba(11, 37, 69, 0.2); animation: modalFadeIn 0.2s ease-out; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .modal-close { position: absolute; top: 16px; right: 16px; background: transparent; border: none; font-size: 24px; color: var(--secondary-text); cursor: pointer; }
        .modal-close:hover { color: var(--dark-navy); }
        .modal-title { font-size: 22px; color: var(--dark-navy); margin-bottom: 12px; }
        .modal-body { font-size: 15px; color: var(--secondary-text); line-height: 1.6; }
        .feature-link { background: transparent; border: none; cursor: pointer; padding: 0; font-family: inherit; font-size: inherit; }
        
        /* Creator Section Styles */
        .creator-section { padding: 96px 0; background-color: var(--white); border-top: 1px solid var(--border-color); }
        .creator-card { display: flex; align-items: center; gap: 40px; background: var(--very-light-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px; max-width: 900px; margin: 0 auto; box-shadow: var(--shadow-md); }
        
        /* THE FIX: object-position pushes the crop to the top so your head shows */
        .creator-img { width: 180px; height: 180px; border-radius: 50%; object-fit: cover; object-position: center 15%; border: 4px solid var(--primary-blue); box-shadow: var(--shadow-lg); flex-shrink: 0; }
        
        .creator-info { flex: 1; }
        .creator-name { font-size: 28px; color: var(--dark-navy); margin-bottom: 4px; }
        .creator-title { font-size: 16px; font-weight: 600; color: var(--primary-blue); margin-bottom: 16px; }
        .creator-bio { font-size: 15px; color: var(--secondary-text); line-height: 1.7; margin-bottom: 24px; }
        .creator-contact { display: flex; flex-wrap: wrap; gap: 16px; }
        .creator-link { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: var(--dark-navy); background: var(--white); padding: 10px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); transition: all 0.2s ease; }
        .creator-link:hover { border-color: var(--primary-blue); color: var(--primary-blue); box-shadow: var(--shadow-sm); transform: translateY(-1px); }
        
        @media (max-width: 768px) {
            .creator-card { flex-direction: column; text-align: center; padding: 32px 20px; gap: 24px; }
            .creator-contact { justify-content: center; }
        }
    </style>

    <script>
        const featureData = {
            cv: { title: "AI CV Analysis", text: "Upload your CV (PDF or DOCX) and our AI will extract your skills, check your ATS compatibility score, identify missing information, and provide actionable feedback to help you pass automated HR filters." },
            job: { title: "Job Matching", text: "CareerAI compares your profile against thousands of real-world job requirements. We calculate a match percentage based on your skills, education, and experience, and tell you exactly what is missing to get the job." },
            interview: { title: "Interview Preparation", text: "Select your target job role, and the AI will generate realistic interview questions based on your actual CV. Type your answers and receive instant scoring, feedback on your weaknesses, and a perfect example answer." },
            roadmap: { title: "Learning Roadmap", text: "Stop guessing what to learn next. Our AI looks at your current skills vs. your target career, identifies the skill gaps, and builds a step-by-step learning roadmap with recommended stages to become job-ready." }
        };

        document.querySelectorAll('.feature-link').forEach(btn => {
            btn.addEventListener('click', function() {
                const featureKey = this.getAttribute('data-feature');
                const data = featureData[featureKey];
                
                if (data) {
                    document.getElementById('modal-title').textContent = data.title;
                    document.getElementById('modal-body').textContent = data.text;
                    document.getElementById('feature-modal').style.display = 'flex';
                }
            });
        });

        function closeModal() {
            document.getElementById('feature-modal').style.display = 'none';
        }

        document.getElementById('feature-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>