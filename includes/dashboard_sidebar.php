<?php
/**
 * CareerAI - Dashboard Sidebar
 * Reusable navigation for all logged-in user pages.
 */
?>
<aside class="dash-sidebar">
    <nav class="dash-menu" aria-label="Dashboard navigation">
        <a href="<?php echo url('pages/dashboard.php'); ?>" 
           class="<?php echo $active_dash === 'dashboard' ? 'active' : ''; ?>">
            Dashboard
        </a>
        <a href="<?php echo url('pages/profile.php'); ?>" 
           class="<?php echo $active_dash === 'profile' ? 'active' : ''; ?>">
            My Profile
        </a>
                <a href="<?php echo url('pages/cv.php'); ?>" 
           class="<?php echo $active_dash === 'cv' ? 'active' : ''; ?>">
            My CV
        </a>
               <a href="<?php echo url('pages/cv_analysis.php'); ?>" 
           class="<?php echo $active_dash === 'analysis' ? 'active' : ''; ?>">
            AI CV Analysis
        </a>
                <a href="<?php echo url('pages/job_matching.php'); ?>" 
           class="<?php echo $active_dash === 'jobs' ? 'active' : ''; ?>">
            Job Matching
        </a>
                <a href="<?php echo url('pages/interview_prep.php'); ?>" 
           class="<?php echo $active_dash === 'interview' ? 'active' : ''; ?>">
            Interview Preparation
        </a>
                <a href="<?php echo url('pages/learning_roadmap.php'); ?>" 
           class="<?php echo $active_dash === 'roadmap' ? 'active' : ''; ?>">
            Learning Roadmap
        </a>
                <a href="<?php echo url('pages/chat_assistant.php'); ?>" 
           class="<?php echo $active_dash === 'chat' ? 'active' : ''; ?>">
            AI Career Assistant
        </a>
    </nav>
</aside>