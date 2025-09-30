<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Interlink Placement System - Footer Example</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #111827; /* Black */
            --primary-dark: #000000;
            --secondary: #6B7280; /* Grey */
            --accent: #3B82F6; /* Electric Blue */
            --accent-alt: #06B6D4; /* Cyan */
            --dark: #111827;
            --light: #FFFFFF;
            --gray: #6B7280;
            --light-gray: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--dark);
            background-color: var(--light);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main content area - just for demo */
        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Footer Styles */
        .footer {
            background-color: var(--primary);
            color: var(--light);
            padding: 4rem 0 2rem;
            margin-top: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .footer-description {
            color: var(--light-gray);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .social-icon:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .footer-heading {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--accent);
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .footer-link {
            color: var(--secondary);
            text-decoration: none;
            transition: color 0.2s ease;
            font-size: 0.95rem;
        }

        .footer-link:hover {
            color: var(--accent-alt);
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .contact-item {
            display: flex;
            gap: 0.75rem;
            color: var(--light-gray);
            font-size: 0.95rem;
        }

        .contact-icon {
            color: var(--accent);
            font-size: 1.125rem;
            width: 20px;
            text-align: center;
        }

        .newsletter-form {
            display: flex;
            margin-top: 1rem;
        }

        .newsletter-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.375rem 0 0 0.375rem;
            font-size: 0.95rem;
        }

        .newsletter-button {
            background-color: var(--accent);
            color: var(--light);
            border: none;
            border-radius: 0 0.375rem 0.375rem 0;
            padding: 0 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .newsletter-button:hover {
            background-color: var(--accent-alt);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.875rem;
            color: var(--light-gray);
        }

        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-bottom-link {
            color: var(--light-gray);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-bottom-link:hover {
            color: white;
        }

        .back-to-top {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--accent);
            color: var(--light);
            border-radius: 50%;
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
            opacity: 0.8;
        }

        .back-to-top:hover {
            opacity: 1;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .footer-bottom-links {
                flex-direction: column;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>
 

    <!-- Footer Start -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Column 1: About -->
                <div class="footer-column">
                    <a href="#" class="footer-logo">
                        <i class="fas fa-lightbulb"></i>
                        Interlink
                    </a>
                    <p class="footer-description">
                        Connecting talented students with great organizations. Our platform makes it easy to find opportunities, manage applications, and build your career.
                    </p>
                    <div class="social-icons">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 2: Freelancers -->
                <!-- <div class="footer-column">
                    <h3 class="footer-heading">For Students</h3>
                    <div class="footer-links">
                        <a href="/opportunities" class="footer-link">Find Opportunities</a>
                        <a href="/profile/edit" class="footer-link">Create Profile</a>
                        <a href="#" class="footer-link">Student Resources</a>
                        <a href="#" class="footer-link">Success Stories</a>
                        <a href="#" class="footer-link">Community</a>
                    </div>
                </div> -->

                <!-- Column 3: Clients -->
                <!-- <div class="footer-column">
                    <h3 class="footer-heading">For Organizations</h3>
                    <div class="footer-links">
                        <a href="#" class="footer-link">How to Recruit</a>
                        <a href="{{ route('opportunities.create') }}" class="footer-link">Post an Opportunity</a>
                        <a href="/students" class="footer-link">Browse Students</a>
                        <a href="#" class="footer-link">Organization Solutions</a>
                        <a href="#" class="footer-link">Organization Success Stories</a>
                    </div>
                </div> -->

                <!-- Column 4: Company -->
                <!-- <div class="footer-column">
                    <h3 class="footer-heading">Company</h3>
                    <div class="footer-links">
                        <a href="#" class="footer-link">About Us</a>
                        <a href="#" class="footer-link">Our Team</a>
                        <a href="#" class="footer-link">Careers</a>
                        <a href="#" class="footer-link">Press</a>
                        <a href="#" class="footer-link">Blog</a>
                        <a href="#" class="footer-link">Contact Us</a>
                    </div>
                </div> -->

                <!-- Column 5: Contact & Newsletter -->
                <!-- <div class="footer-column">
                    <h3 class="footer-heading">Stay Updated</h3>
                    <p class="footer-description">Subscribe to our newsletter for the latest updates, tips, and special offers.</p>
                    <form class="newsletter-form">
                        <input type="email" class="newsletter-input" placeholder="Your email address" required>
                        <button type="submit" class="newsletter-button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    <h3 class="footer-heading" style="margin-top: 2rem;">Contact Us</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <span>Mbagathi Way, Highrise Estate</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone-alt contact-icon"></i>
                            <span>+254 (714) 464-999</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope contact-icon"></i>
                            <span>support@interlink.com</span>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>© {{ date('Y') }} Interlink Placement System. All rights reserved.</p>
                <!-- <div class="footer-bottom-links">
                    <a href="#" class="footer-bottom-link">Terms of Service</a>
                    <a href="#" class="footer-bottom-link">Privacy Policy</a>
                    <a href="#" class="footer-bottom-link">Cookie Policy</a>
                    <a href="#" class="footer-bottom-link">Accessibility</a>
                </div> -->
            </div>
        </div>
    </footer>

    <!-- Back to top button -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>
</body>
</html>