<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Turbo Rentals</title>
    <link rel="stylesheet" href="css/kontakto.css">
</head>
<body>
    <?php include 'navigation.php'; ?>

    <main class="contact-page">
        <section class="contact-hero">
            <span class="contact-pill">Contact</span>
            <h1>Get in touch</h1>
            <p>We are here to help. Send us a message and we will reply as soon as possible.</p>
        </section>

        <section class="contact-cards">
            <article class="contact-card">
                <div class="contact-icon">AD</div>
                <h3>Address</h3>
                <p>Polytechnic University of Tirana</p>
                <p>Dervish Hima Street, Tirana</p>
            </article>
            <article class="contact-card">
                <div class="contact-icon">TEL</div>
                <h3>Call us</h3>
                <p>+355 4 123 4567</p>
                <p>+355 69 987 6543</p>
            </article>
            <article class="contact-card">
                <div class="contact-icon">EM</div>
                <h3>Email</h3>
                <p>info@turborentals.com</p>
                <p>support@turborentals.com</p>
            </article>
            <article class="contact-card">
                <div class="contact-icon">OR</div>
                <h3>Hours</h3>
                <p>Mon - Fri: 09:00 - 20:00</p>
                <p>Sat - Sun: 10:00 - 18:00</p>
            </article>
        </section>

        <section class="contact-grid">
            <div class="contact-form card">
                <h2>Write to us</h2>
                <form>
                    <div class="form-row">
                        <label>
                            Full name
                            <input type="text" name="full_name" placeholder="John Doe" required>
                        </label>
                        <label>
                            Email
                            <input type="email" name="email" placeholder="john@example.com" required>
                        </label>
                    </div>
                    <div class="form-row">
                        <label>
                            Phone number
                            <input type="tel" name="phone" placeholder="+355 6X XXX XXXX">
                        </label>
                        <label>
                            Subject
                            <input type="text" name="subject" placeholder="How can we help?">
                        </label>
                    </div>
                    <label>
                        Message
                        <textarea name="message" rows="5" placeholder="Tell us more about your request..."></textarea>
                    </label>
                    <button type="submit">Send message</button>
                </form>
            </div>

            <div class="contact-side">
                <div class="map-card card">
                    <h2>Our location</h2>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2996.6367188914915!2d19.818975175874936!3d41.31676597130894!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x135030e2870cc581%3A0x6c539e3ffce9fa68!2sPolytechnic%20University%20of%20Tirana!5e0!3m2!1sen!2s!4v1737539491572!5m2!1sen!2s" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="faq-card card">
                    <h2>Frequently asked questions</h2>
                    <ul>
                        <li>What documents are required to rent a car?</li>
                        <li>Can I cancel my reservation for free?</li>
                        <li>What is included in the price?</li>
                        <li>Do you offer a car with a driver?</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <script src="/Makina/registerLogin/sessionTimeout.js"></script>
</body>
</html>
