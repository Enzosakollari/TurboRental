<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Home Page</title>
        <link rel="stylesheet" href="css/index.css">
    </head>
    <body>
        <?php include 'navigation.php'; ?>

        <main class="home">
            <section class="hero">
                <div class="hero-content">
                    <span class="hero-badge">Premium Vehicle Rentals</span>
                    <h1>Drive Your <span>Dream Machine</span></h1>
                    <p>
                        New and reliable vehicles, suitable for every kind of trip,
                        with fast and professional service.
                    </p>
                    <div class="hero-actions">
                        <a class="btn-primary" href="/Makina/makinat/makinat.php">Explore Vehicles</a>
                        <a class="btn-ghost" href="/Makina/navigation/kontakto.php">View Pricing</a>
                    </div>
                </div>
            </section>

            <section class="stats-bar">
                <div class="stat">
                    <strong>500+</strong>
                    <span>Premium Vehicles</span>
                </div>
                <div class="stat">
                    <strong>50K+</strong>
                    <span>Happy Clients</span>
                </div>
                <div class="stat">
                    <strong>24/7</strong>
                    <span>Support</span>
                </div>
                <div class="stat">
                    <strong>15+</strong>
                    <span>Years Experience</span>
                </div>
            </section>

            <section class="featured">
                <div class="section-title">
                    <h2>Featured Vehicles</h2>
                    <p>Explore our premium collection of cars and SUVs curated for every journey.</p>
                </div>
                <div class="card-grid">
                    <article class="vehicle-card">
                        <div class="card-media">
                            <img src="/Makina/images/cars/cover-benz.jpg" alt="Premium Sedan">
                            <span class="chip">Sport Sedan</span>
                        </div>
                        <div class="card-body">
                            <h3>Premium Sedan</h3>
                            <div class="card-meta">
                                <span>Gasoline</span>
                                <span>Automatic</span>
                                <span>2.0L</span>
                            </div>
                            <div class="card-footer">
                                <div class="price">$49<span>/day</span></div>
                                <button class="btn-primary">Book Now</button>
                            </div>
                        </div>
                    </article>
                    <article class="vehicle-card">
                        <div class="card-media">
                            <img src="/Makina/images/cars/cover-bmw.jpg" alt="Luxury SUV">
                            <span class="chip">Luxury SUV</span>
                        </div>
                        <div class="card-body">
                            <h3>Luxury SUV</h3>
                            <div class="card-meta">
                                <span>Diesel</span>
                                <span>Automatic</span>
                                <span>AWD</span>
                            </div>
                            <div class="card-footer">
                                <div class="price">$89<span>/day</span></div>
                                <button class="btn-primary">Book Now</button>
                            </div>
                        </div>
                    </article>
                    <article class="vehicle-card">
                        <div class="card-media">
                            <img src="/Makina/images/cars/cover-ferrari.jpg" alt="City Hatchback">
                            <span class="chip">City Comfort</span>
                        </div>
                        <div class="card-body">
                            <h3>City Hatchback</h3>
                            <div class="card-meta">
                                <span>Hybrid</span>
                                <span>Manual</span>
                                <span>1.6L</span>
                            </div>
                            <div class="card-footer">
                                <div class="price">$39<span>/day</span></div>
                                <button class="btn-primary">Book Now</button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="why">
                <div class="section-title">
                    <h2>Why Choose Turbo Rentals</h2>
                    <p>Premium service that exceeds expectations.</p>
                </div>
                <div class="why-grid">
                    <div class="why-card">
                        <h4>Full Insurance</h4>
                        <p>Comprehensive coverage for total peace of mind.</p>
                    </div>
                    <div class="why-card">
                        <h4>24/7 Support</h4>
                        <p>Round-the-clock assistance whenever you need it.</p>
                    </div>
                    <div class="why-card">
                        <h4>Premium Fleet</h4>
                        <p>Hand-picked vehicles from trusted brands.</p>
                    </div>
                    <div class="why-card">
                        <h4>Expert Team</h4>
                        <p>Dedicated specialists ready to help you drive off fast.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div>
                <h3>Turbo Rentals</h3>
                <p>Experience luxury and performance with our premium collection.</p>
            </div>
            <div class="footer-links">
                <a href="/Makina/navigation/kontakto.php">Contact</a>
                <a href="/Makina/navigation/rrethNesh.php">About</a>
                <a href="/Makina/navigation/pyetjeFAQ.php">FAQ</a>
            </div>
        </footer>
        <script src="/Makina/registerLogin/sessionTimeout.js"></script>
</body>
</html>
