<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wellbeing & EAP Services</title>
    <link rel="stylesheet" href="{{ asset('assets/css/home-page.css') }}">
</head>

<body>

    <section class="hero">
        <div class="container">
            <h1>Supporting Mental Health & Wellbeing</h1>
            <p>Comprehensive Employee Assistance & Psychological Services</p>
            <button id="contactBtn">Contact Us</button>
        </div>
    </section>

    <section class="services">
        <div class="service-card">
            <h3>Employee Assistance Program</h3>
            <p>Confidential support for your team’s mental health and wellbeing.</p>
        </div>
        <div class="service-card">
            <h3>Manager Assistance</h3>
            <p>Guidance for leaders supporting their staff in challenging situations.</p>
        </div>
        <div class="service-card">
            <h3>Critical Incident Support</h3>
            <p>Immediate response to traumatic workplace events.</p>
        </div>
    </section>

    <section class="about">
        <div class="container">
            <h2>Why Choose Us?</h2>
            <p>We provide evidence-based psychological services delivered by a highly qualified and experienced team. Our programs are tailored to suit your organisation’s needs. We value confidentiality, care, and commitment to mental health and wellbeing in the workplace.</p>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <h2>What Our Clients Say</h2>
            <div class="testimonial-card">
                <p>"Incredible support services — highly professional and caring team."</p>
                <strong>- John D.</strong>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Ready to Support Your Team?</h2>
            <button onclick="contactUs()">Get in Touch</button>
        </div>
    </section>

    <footer>
        <nav>
            <a href="/privacy-policy" target="_blank">Privacy Policy</a>
            <a href="/terms-of-use" target="_blank">Terms of Use</a>
        </nav>
        <p>&copy; 2025 Wellbeing Services. All rights reserved.</p>
    </footer>

    <script nonce="{{ csp_nonce() }}" src="{{ asset('assets/js/home-page.js') }}"></script>


</body>

</html>