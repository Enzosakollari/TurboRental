<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Makina/theme.css">
    <style>
        #bigContainer, h1, h2, p {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Manrope", sans-serif;
        }

        #bigContainer {
            background: radial-gradient(1200px 800px at 10% 0%, #1a2440 0%, #0a0f1c 55%, #05070f 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            padding: 72px 0 96px;
        }

        .content {
            width: min(980px, 92vw);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 84px;
        }

        .content section {
            padding-top: 24px;
        }

        .intro {
            text-align: center;
        }

        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 14px;
        }

        .logo-wrap img {
            width: 128px;
            height: 128px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.05);
            object-fit: cover;
            box-shadow: var(--shadow-sm);
        }

        h1 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 12px;
        }

        h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        p {
            color: var(--muted);
            font-size: 1rem;
        }

        .partner-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            margin-top: 12px;
        }

        .partner-card {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 14px;
            text-align: center;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .partner-card img {
            width: 124px;
            height: 124px;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.35));
        }

        .steps-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            margin-top: 12px;
        }

        .step-card {
            background: rgba(16, 23, 38, 0.7);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .step-card strong {
            color: #fff;
            font-size: 1rem;
        }

        .step-card span {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .guarantee-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 12px;
        }

        .guarantee-card {
            background: rgba(16, 23, 38, 0.7);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .guarantee-card strong {
            color: #fff;
            font-size: 1rem;
        }

        .guarantee-card span {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .payment-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 12px;
        }

        .payment-card {
            background: rgba(16, 23, 38, 0.7);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }

        .payment-card strong {
            color: #fff;
            font-size: 1rem;
        }

        .payment-card span {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .payment-logo {
            width: 160px;
            height: 96px;
            display: block;
            margin: 4px auto 6px;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.35));
        }

        .insurance-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            margin-top: 12px;
        }

        .insurance-card {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .insurance-card img {
            width: 260px;
            height: 140px;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.35));
        }

        .fade-section {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-section.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .site-footer {
            background: linear-gradient(135deg, #4a0d14, #6b111a);
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    <div id="bigContainer">
        <div class="content">
            <section class="intro fade-section">
                <div class="logo-wrap">
                    <img src="/Makina/images/logo.png" alt="Turbo Rentals logo">
                </div>
                <h1>About Us</h1>
                <p>Turbo Rentals delivers premium car rentals with a focus on design, reliability, and effortless experiences. We bring a curated fleet, fast booking, and support that keeps every trip simple and stress-free.</p>
            </section>

            <section class="fade-section">
                <h2>Main Sponsors</h2>
                <p>As you scroll, meet the car brands that power our lineup.</p>
                <div class="partner-grid">
                    <div class="partner-card">
                        <img src="/Makina/images/mclaren.jpeg" alt="McLaren logo">
                        <strong>McLaren</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/mercedes-benz.jpeg" alt="Mercedes-Benz logo">
                        <strong>Mercedes-Benz</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/bmw.jpeg" alt="BMW logo">
                        <strong>BMW</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/audi.jpeg" alt="Audi logo">
                        <strong>Audi</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/porsche.jpeg" alt="Porsche logo">
                        <strong>Porsche</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/maserati.jpeg" alt="Maserati logo">
                        <strong>Maserati</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/cadillac.jpeg" alt="Cadillac logo">
                        <strong>Cadillac</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/astonmartin.jpeg" alt="Aston Martin logo">
                        <strong>Aston Martin</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/lamborghini.png" alt="Lamborghini logo">
                        <strong>Lamborghini</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/ferrari.jpeg" alt="Ferrari logo">
                        <strong>Ferrari</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/ducati.jpg" alt="Ducati logo">
                        <strong>Ducati</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/honda.jpg" alt="Honda logo">
                        <strong>Honda</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/kawasaki.jpg" alt="Kawasaki logo">
                        <strong>Kawasaki</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/suzuki.jpg" alt="Suzuki logo">
                        <strong>Suzuki</strong>
                    </div>
                    <div class="partner-card">
                        <img src="/Makina/images/yamaha.png" alt="Yamaha logo">
                        <strong>Yamaha</strong>
                    </div>
                </div>
            </section>

            <section class="fade-section">
                <h2>How It Works</h2>
                <p>A simple flow from booking to return, built for speed and clarity.</p>
                <div class="steps-grid">
                    <div class="step-card">
                        <strong>1. Choose a car</strong>
                        <span>Browse the fleet and pick the ride that fits your trip.</span>
                    </div>
                    <div class="step-card">
                        <strong>2. Book in minutes</strong>
                        <span>Select dates, confirm details, and reserve instantly.</span>
                    </div>
                    <div class="step-card">
                        <strong>3. Pick up & drive</strong>
                        <span>Collect your car on time and hit the road with confidence.</span>
                    </div>
                    <div class="step-card">
                        <strong>4. Return with ease</strong>
                        <span>Drop off quickly and get back to what matters.</span>
                    </div>
                </div>
            </section>

            <section class="fade-section">
                <h2>Payments</h2>
                <p>Flexible options with clear steps from checkout to confirmation.</p>
                <div class="payment-grid">
                    <div class="payment-card">
                        <img class="payment-logo" src="/Makina/images/stripe.png" alt="Stripe logo">
                        <strong>Online card</strong>
                        <span>Pay online at checkout using your card.</span>
                        <span>We place a refundable hold for the security deposit.</span>
                    </div>
                    <div class="payment-card">
                        <img class="payment-logo" src="/Makina/images/raiffaisen.png" alt="Raiffaisen logo">
                        <strong>Raiffaisen card</strong>
                        <span>Pay at the dealership using your Raiffaisen card.</span>
                        <span>We confirm the booking on the spot.</span>
                    </div>
                    <div class="payment-card">
                        <img class="payment-logo" src="/Makina/images/cash.png" alt="Cash payment">
                        <strong>Cash on pickup</strong>
                        <span>Pay in cash at the dealership during pickup.</span>
                        <span>Bring your ID and booking code for a quick handoff.</span>
                    </div>
                </div>
            </section>

            <section class="fade-section">
                <h2>Guarantees & Safety Partners</h2>
                <p>We stand behind every booking with clear promises and trusted safety coverage.</p>
                <div class="guarantee-grid">
                    <div class="guarantee-card">
                        <strong>24/7 Road Assistance</strong>
                        <span>Immediate help for breakdowns, flats, or unexpected issues.</span>
                    </div>
                    <div class="guarantee-card">
                        <strong>Verified & Sanitized Vehicles</strong>
                        <span>Every car is inspected, cleaned, and documented before pickup.</span>
                    </div>
                    <div class="guarantee-card">
                        <strong>Transparent Pricing</strong>
                        <span>No hidden fees. What you see at checkout is what you pay.</span>
                    </div>
                    <div class="guarantee-card">
                        <strong>Flexible Protection</strong>
                        <span>Optional coverage tiers designed for city trips or long journeys.</span>
                    </div>
                </div>
                <div class="insurance-grid">
                    <div class="insurance-card">
                        <img src="/Makina/images/albsig.png" alt="Albsig logo">
                    </div>
                    <div class="insurance-card">
                        <img src="/Makina/images/eurosig.png" alt="Eurosig logo">
                    </div>
                </div>
            </section>
        </div>
    </div>
    <footer class="site-footer text-light py-4 mt-5">
        <div class="container">
            <div class="row gy-3 align-items-center">
                <div class="col-md-4 text-center text-md-start">
                    <strong>Turbo Rentals</strong>
                </div>
                <div class="col-md-4 text-center">
                    <div>Tel: +355 69 000 0000</div>
                    <div>Email: turborentals@gmail.com</div>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div>&copy; 2026 Turbo Rentals. All rights reserved.</div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const fadeSections = document.querySelectorAll('.fade-section');
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        fadeSections.forEach(section => observer.observe(section));
    </script>
    <script src="/Makina/registerLogin/sessionTimeout.js"></script>
</body>
</html>
