<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LP Gas Distribution</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      .hero {
        background: url("assets/banner.jpg") center/cover;
        color: white;
        text-align: center;
        padding: 100px 20px;
        backdrop-filter: blur(8px);
      }
      .service-card {
        transition: transform 0.3s;
      }
      .service-card:hover {
        transform: scale(1.05);
      }
      #services .card {
        height: 500px;
      }
      #services img {
        width: auto;
        height: 400px;
      }
      .footer {
        background-color: #222;
        color: #fff;
      }
      .footer p {
        margin: 0;
      }
      .contact-section {
        background-color: #f8f9fa;
      }
      .contact-section .form-control {
        margin-bottom: 10px;
      }
      .contact-section .btn {
        margin-top: 20px;
      }
    </style>
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="#">GasByGas</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="#services">Services</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#contact">Contact</a>
            </li>
          </ul>
          <li class="nav-item">
            <a href="login.php" class="btn btn-primary">Login</a>
          </li>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <h1>Reliable LP Gas Distribution</h1>
        <p>Safe & efficient gas delivery for homes and businesses.</p>
        <a href="index.php" class="btn btn-primary">Request a Gas</a>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
      <div class="container">
        <h2 class="text-center mb-4">Our Services</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="card service-card">
              <img src="assets/1.png" class="card-img-top" alt="Gas Delivery" />
              <div class="card-body">
                <h5 class="card-title">Gas Delivery</h5>
                <p class="card-text">
                  Fast and secure LP gas delivery to your location. We ensure timely deliveries for your home or business.
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card service-card">
              <img
                src="assets/2.jpg"
                class="card-img-top"
                alt="Safety Checks"
              />
              <div class="card-body">
                <h5 class="card-title">Safety Inspections</h5>
                <p class="card-text">
                  Ensuring safety standards for your gas installations. We perform thorough checks for your safety.
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card service-card">
              <img
                src="assets/3.png"
                class="card-img-top"
                alt="Customer Support"
              />
              <div class="card-body">
                <h5 class="card-title">24/7 Support</h5>
                <p class="card-text">
                  Reliable customer service for all your gas-related needs. Our team is always ready to assist you.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
      <div class="container">
        <h2 class="text-center mb-4">Contact Us</h2>
        <div class="row">
          <div class="col-md-6 mx-auto">
            <form>
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea class="form-control" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-white text-center py-3">
      <p>&copy; 2025 GasByGas. All Rights Reserved. <br> Providing quality gas services for your home and business.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
