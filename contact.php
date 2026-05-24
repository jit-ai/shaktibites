<?php
$page_title = 'Contact';
include 'includes/header.php';
?>

<!-- ===== CONTACT HERO ===== -->
<section class="contact-hero">
  <div class="container">
    <h1 class="contact-hero-title">Get In Touch</h1>
    <p class="contact-hero-sub">Have questions about our protein laddoos? We'd love to hear from you!</p>
  </div>
</section>

<!-- ===== CONTACT SECTION ===== -->
<section class="contact-section">
  <div class="container">
    <div class="contact-grid">

      <!-- Contact Info Card -->
      <div class="contact-info-card">
        <h3 class="contact-info-title">Contact Information</h3>
        <p class="contact-info-text">We're here to help! Reach out through any of these channels.</p>

        <div class="contact-detail-item">
          <div class="contact-icon-wrapper">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div class="contact-detail-content">
            <h5>Office Address</h5>
            <p>Shakti Bites Headquarters<br>123 Wellness Street, Health District<br>Mumbai, Maharashtra 400001<br>India</p>
          </div>
        </div>

        <div class="contact-detail-item">
          <div class="contact-icon-wrapper">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <div class="contact-detail-content">
            <h5>Phone Number</h5>
            <p>+91 98765 43210<br>Mon - Fri: 9:00 AM - 6:00 PM</p>
          </div>
        </div>

        <div class="contact-detail-item">
          <div class="contact-icon-wrapper">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <div class="contact-detail-content">
            <h5>Email Address</h5>
            <p>hello@shaktibites.com<br>support@shaktibites.com</p>
          </div>
        </div>

        <div class="contact-detail-item">
          <div class="contact-icon-wrapper">
            <i class="bi bi-clock-fill"></i>
          </div>
          <div class="contact-detail-content">
            <h5>Business Hours</h5>
            <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
          </div>
        </div>
      </div>

      <!-- Contact Form Card -->
      <div class="contact-form-card">
        <h3 class="contact-form-title">Send Us a Message</h3>
        <p class="contact-form-text">Fill out the form and our team will get back to you within 24 hours.</p>

        <form action="process_contact.php" method="post" class="contact-form">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
            </div>
            <div class="form-group">
              <label for="subject">Subject *</label>
              <input type="text" id="subject" name="subject" placeholder="What is this regarding?" required>
            </div>
          </div>

          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="5" placeholder="Tell us how we can help you..." required></textarea>
          </div>

          <button type="submit" class="btn btn-contact-send">Send Message</button>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- ===== MAP SECTION ===== -->
<section class="map-section">
  <div class="container">
    <div class="map-wrapper">
      <div class="map-placeholder">
        <i class="bi bi-geo-alt-fill map-marker"></i>
        <h4>Find Our Office</h4>
        <p>Shakti Bites Headquarters, Mumbai</p>
        <a href="https://maps.google.com" target="_blank" class="btn btn-map-direct">Get Directions</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>