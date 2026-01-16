<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions - Car Rental Agency</title>
    <link rel="stylesheet" href="css/pyetjeFAQ.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    <main class="faq-page">
        <section class="faq-hero">
            <div class="faq-hero-content">
                <span class="hero-pill">Support Center</span>
                <h1>Frequently Asked Questions</h1>
                <p>Tap a question to reveal the answer. Everything you need to know about cars, motorcycles, and payments.</p>
            </div>
            <div class="faq-hero-visual">
                <img class="hero-logo" src="/Makina/images/logo.png" alt="Turbo Rentals logo">
            </div>
        </section>

        <section class="faq-grid">
            <article class="faq-category">
                <header class="faq-category-header">
                    <div>
                        <h2>Rental Process</h2>
                        <p>Start, extend, and manage bookings quickly.</p>
                    </div>
                    <img src="/Makina/images/cars/cover-benz.jpg" alt="Car rental" class="faq-media">
                </header>
                <div class="faq-accordion">
                    <details>
                        <summary>How can I reserve a vehicle?</summary>
                        <p>Reserve online through our website or call us directly for quick support.</p>
                    </details>
                    <details>
                        <summary>What documents are required to rent?</summary>
                        <p>You need a valid driver’s license, an ID document, and a payment card.</p>
                    </details>
                    <details>
                        <summary>Can I extend my rental?</summary>
                        <p>Yes. Contact support before your return time and we will confirm availability.</p>
                    </details>
                </div>
            </article>

            <article class="faq-category">
                <header class="faq-category-header">
                    <div>
                        <h2>Pricing & Payments</h2>
                        <p>Clear pricing, deposits, and payment options.</p>
                    </div>
                    <img src="/Makina/images/logo.png" alt="Payments" class="faq-media">
                </header>
                <div class="faq-accordion">
                    <details>
                        <summary>What payment methods do you accept?</summary>
                        <p>Online card payments, Raiffaisen card at pickup, and cash at pickup.</p>
                    </details>
                    <details>
                        <summary>Is a security deposit required?</summary>
                        <p>Yes, a refundable deposit is held depending on the vehicle category.</p>
                    </details>
                    <details>
                        <summary>Are there any hidden fees?</summary>
                        <p>No. All fees are shown at checkout and confirmed before booking.</p>
                    </details>
                </div>
            </article>

            <div class="faq-divider">
                <img src="/Makina/images/cars/cover-bmw.jpg" alt="Premium car">
                <span>Cars & Motorcycles</span>
                <img src="/Makina/images/ducati.jpg" alt="Sport motorcycle">
            </div>

            <article class="faq-category">
                <header class="faq-category-header">
                    <div>
                        <h2>Pickup & Return</h2>
                        <p>Timings, locations, and late returns.</p>
                    </div>
                    <img src="/Makina/images/cars/cover-ferrari.jpg" alt="Pickup and return" class="faq-media">
                </header>
                <div class="faq-accordion">
                    <details>
                        <summary>Can I pick up in one city and return in another?</summary>
                        <p>Yes, with advance notice. Fees vary by distance.</p>
                    </details>
                    <details>
                        <summary>What are your pickup and drop-off hours?</summary>
                        <p>We operate 24/7. After-hours support is available.</p>
                    </details>
                    <details>
                        <summary>What happens if I return late?</summary>
                        <p>Late returns may incur hourly charges. Contact us if delays occur.</p>
                    </details>
                </div>
            </article>

            <article class="faq-category">
                <header class="faq-category-header">
                    <div>
                        <h2>Insurance & Safety</h2>
                        <p>Coverage options and roadside assistance.</p>
                    </div>
                    <img src="/Makina/images/albsig.png" alt="Insurance coverage" class="faq-media">
                </header>
                <div class="faq-accordion">
                    <details>
                        <summary>Is insurance included in the rental price?</summary>
                        <p>Yes, basic insurance is included. Full coverage is optional.</p>
                    </details>
                    <details>
                        <summary>What happens if the vehicle breaks down?</summary>
                        <p>We provide 24/7 roadside assistance and a replacement when needed.</p>
                    </details>
                    <details>
                        <summary>Are international trips covered?</summary>
                        <p>Cross-border travel requires prior approval and upgraded coverage.</p>
                    </details>
                </div>
            </article>

            <div class="faq-divider">
                <img src="/Makina/images/kawasaki.jpg" alt="Kawasaki motorcycle">
                <span>Two Wheels Ready</span>
                <img src="/Makina/images/suzuki.jpg" alt="Suzuki motorcycle">
            </div>

            <article class="faq-category">
                <header class="faq-category-header">
                    <div>
                        <h2>Motorcycles</h2>
                        <p>Two-wheel rentals and rider requirements.</p>
                    </div>
                    <img src="/Makina/images/honda.jpg" alt="Motorcycle rentals" class="faq-media">
                </header>
                <div class="faq-accordion">
                    <details>
                        <summary>Do you rent motorcycles year-round?</summary>
                        <p>Yes, weather permitting. Seasonal offers vary by model.</p>
                    </details>
                    <details>
                        <summary>What license do I need?</summary>
                        <p>A valid motorcycle license is required for all motorcycle rentals.</p>
                    </details>
                    <details>
                        <summary>Is protective gear provided?</summary>
                        <p>Helmets are included. Jackets and gloves are available on request.</p>
                    </details>
                </div>
            </article>
        </section>
    </main>
<script src="/Makina/registerLogin/sessionTimeout.js"></script>

</body>
</html>
