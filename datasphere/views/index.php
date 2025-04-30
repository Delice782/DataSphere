<?php
$pageTitle = "DataSphere - Customer Feedback Platform";
require_once '../includes/header.php';
?>

<div id="home" class="hero-section">
    <div class="hero-content">
        <h1>Transform Your <span>Customer Feedback</span> Into Valuable Insights</h1>
        <p>Empower your business with real-time feedback analysis. Collect, respond, and improve based on what your customers really want.</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="cta-buttons">
                <a href="signup.php" class="btn-primary">Get Started</a>
                <a href="login.php" class="btn-secondary">Sign In</a>
            </div>
        <?php else: ?>
            <div class="cta-buttons">
                <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="features" class="features-section">
    <div class="feature-container">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa fa-comments"></i>
            </div>
            <h3>Real-time Feedback</h3>
            <p>Instantly collect and process customer feedback to make data-driven decisions that improve your business.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa fa-chart-line"></i>
            </div>
            <h3>Interactive Dashboard</h3>
            <p>Monitor trends and ratings in real-time with our intuitive analytics dashboard that highlights key insights.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa fa-users"></i>
            </div>
            <h3>Customer-Focused</h3>
            <p>Build stronger relationships with your customers through personalized responses and meaningful engagement.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa fa-tags"></i>
            </div>
            <h3>Smart Analysis</h3>
            <p>Automatically identify common issues and improvement areas to streamline your service enhancement efforts.</p>
        </div>
    </div>
</div>

<div id="testimonials" class="testimonials-section">
    <h2>Trusted by Business Leaders</h2>
    
    <div class="testimonial-container">
        <div class="testimonial-card">
            <div class="testimonial-content">
                <p>"DataSphere has completely changed how we handle customer feedback. We're now able to respond faster and improve our services based on actual customer needs."</p>
            </div>
            <div class="testimonial-author">
                <div class="author-avatar">1</div>
                <div class="author-info">
                    <h4>Titi Thierry</h4>
                    <p>Founder, Girux Cosmetics Company</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="testimonial-content">
                <p>"The analytics dashboard gives us insights we never had before. We've increased customer satisfaction by 27% since implementing this platform!"</p>
            </div>
            <div class="testimonial-author">
                <div class="author-avatar">2</div>
                <div class="author-info">
                    <h4>Sina Gerarld</h4>
                    <p>CEO, Urwibutso Entreprise</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="testimonial-content">
                <p>"The automated analysis saves us countless hours of manual review. The insights we get have been crucial for our product development roadmap."</p>
            </div>
            <div class="testimonial-author">
                <div class="author-avatar">3</div>
                <div class="author-info">
                    <h4>Dosita Igirimpuhwe</h4>
                    <p>CTO, Quincaillerie Manishimwe</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="about" class="about-section">
    <h2>About DataSphere</h2>
    <p>DataSphere is a data-driven feedback management platform designed to help businesses collect, analyze, and act on customer feedback effectively. Our mission is to bridge the gap between businesses and their customers, turning insights into actionable improvements.</p>
</div>
<?php require_once '../includes/footer.php'; ?>