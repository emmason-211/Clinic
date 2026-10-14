<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>ETEI Clinic Management System</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Arial, sans-serif; }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #0f4c3a 0%, #0a3a2c 50%, #0f4c3a 100%);
      color: #fff;
    }

    /* ============ HERO SECTION ============ */
    .hero {
      min-height: calc(100vh - 320px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
    }

    .landing {
      max-width: 900px;
      width: 100%;
      text-align: center;
    }

    .logo-circle {
      width: 90px;
      height: 90px;
      background: #fff;
      border-radius: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .logo-circle img {
      width: 65px;
      height: 65px;
      object-fit: contain;
    }

    h1 {
      font-size: 52px;
      font-weight: 700;
      margin-bottom: 15px;
      letter-spacing: -1px;
    }
    h1 .green  { color: #fff; }
    h1 .red    { color: #f87171; }

    .subtitle {
      font-size: 22px;
      color: #d1fae5;
      margin-bottom: 30px;
      font-weight: 400;
    }

    .badges {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 40px;
      flex-wrap: wrap;
    }
    .badge-item {
      background: rgba(255,255,255,0.12);
      border: 1.5px solid rgba(255,255,255,0.25);
      padding: 10px 20px;
      border-radius: 30px;
      font-size: 14px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .badge-item .check { color: #4ade80; font-weight: bold; }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 50px;
    }
    .btn-big {
      padding: 15px 40px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-login {
      background: #facc15;
      color: #0f4c3a;
    }
    .btn-login:hover {
      background: #eab308;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(250,204,21,0.4);
    }
    .btn-register {
      background: transparent;
      color: #fff;
      border: 2px solid rgba(255,255,255,0.5);
    }
    .btn-register:hover {
      background: rgba(255,255,255,0.15);
      transform: translateY(-2px);
    }

    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 15px;
      margin-top: 30px;
    }
    .feature {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      padding: 20px 15px;
      border-radius: 14px;
      font-size: 13px;
      color: #d1fae5;
      transition: all 0.2s;
    }
    .feature:hover {
      background: rgba(255,255,255,0.12);
      transform: translateY(-3px);
    }
    .feature .icon {
      font-size: 24px;
      display: block;
      margin-bottom: 8px;
    }

    /* ============ FOOTER ============ */
    footer {
      background: rgba(0, 0, 0, 0.25);
      border-top: 1px solid rgba(255,255,255,0.1);
      padding: 50px 20px 0;
      color: #d1fae5;
    }

    .footer-container {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1.2fr;
      gap: 40px;
      padding-bottom: 40px;
    }

    .footer-col h4 {
      color: #fff;
      font-size: 15px;
      margin-bottom: 18px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .footer-col p,
    .footer-col a {
      color: #a7d7c7;
      font-size: 14px;
      line-height: 1.9;
      text-decoration: none;
      display: block;
      transition: color 0.2s;
    }
    .footer-col a:hover {
      color: #facc15;
    }

    /* About column */
    .footer-about .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 15px;
    }
    .footer-about .brand img {
      width: 45px;
      height: 45px;
      background: #fff;
      border-radius: 10px;
      padding: 6px;
    }
    .footer-about .brand span {
      font-size: 18px;
      font-weight: 700;
      color: #fff;
    }
    .footer-about .brand span .red { color: #f87171; }

    /* Contact list */
    .contact-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 10px;
      font-size: 14px;
      color: #a7d7c7;
    }
    .contact-item .ico {
      color: #facc15;
      font-size: 16px;
      width: 20px;
    }

    /* Social icons */
    .socials {
      display: flex;
      gap: 12px;
      margin-top: 15px;
    }
    .socials a {
      width: 38px;
      height: 38px;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: #fff;
      text-decoration: none;
      transition: all 0.2s;
    }
    .socials a:hover {
      background: #facc15;
      color: #0f4c3a;
      transform: translateY(-3px);
      border-color: #facc15;
    }

    /* Bottom bar */
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.1);
      padding: 20px;
      text-align: center;
      font-size: 13px;
      color: #86efac;
    }
    .footer-bottom span {
      color: #facc15;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .footer-container {
        grid-template-columns: 1fr 1fr;
        gap: 30px;
      }
    }

    @media (max-width: 600px) {
      h1 { font-size: 34px; }
      .subtitle { font-size: 16px; }
      .btn-big { padding: 12px 28px; font-size: 14px; }
      .footer-container {
        grid-template-columns: 1fr;
        gap: 25px;
        text-align: center;
      }
      .footer-about .brand { justify-content: center; }
      .socials { justify-content: center; }
      .contact-item { justify-content: center; }
    }
  </style>
</head>
<body>

  <!-- ============ HERO ============ -->
  <div class="hero">
    <div class="landing">

      <div class="logo-circle">
        <img src="assets/logo.png" alt="FDRE TVTI">
      </div>

      <h1>
        <span class="green">ETEI</span>
        <span class="red">Clinic</span>
      </h1>
      <p class="subtitle">Complete Clinic Management System</p>

      <div class="badges">
        <div class="badge-item"><span class="check">✔</span> No Installation</div>
        <div class="badge-item"><span class="check">✔</span> Works Online</div>
        <div class="badge-item"><span class="check">✔</span> 100% Private Data</div>
      </div>

      <div class="buttons">
        <a href="login.php" class="btn-big btn-login">🔐 Login</a>
        <a href="register.php" class="btn-big btn-register">📝 Register</a>
      </div>

      <div class="features">
        <div class="feature"><span class="icon">👥</span> Patient Records</div>
        <div class="feature"><span class="icon">📅</span> Appointments</div>
        <div class="feature"><span class="icon">🩺</span> Consultations</div>
        <div class="feature"><span class="icon">💊</span> Medicine Inventory</div>
        <div class="feature"><span class="icon">📊</span> Reports</div>
      </div>

    </div>
  </div>

  <!-- ============ FOOTER ============ -->
  <footer>
    <div class="footer-container">

      <!-- Column 1: About -->
      <div class="footer-col footer-about">
        <div class="brand">
          <img src="assets/logo.png" alt="FDRE TVTI">
          <span>ETEI  <span class="red">Clinic</span></span>
        </div>
        <p>
          A web-based Clinic Management System for students and staff of Ethiopian Technical
          Excellence Institute. Manage patient records, appointments, 
          consultations, prescriptions, and medicine inventory — all in one place.
        </p>
      </div>

      <!-- Column 2: Quick Links -->
      <div class="footer-col">
        <h4>Quick Links</h4>
        <a href="index.php">🏠 Home</a>
        <a href="login.php">🔐 Login</a>
        <a href="register.php">📝 Register</a>
        <a href="login.php">📅 Book Appointment</a>
      </div>

      <!-- Column 3: System -->
      <div class="footer-col">
        <h4>System</h4>
        <a href="#">👥 Patients</a>
        <a href="#">📅 Appointments</a>
        <a href="#">🩺 Visits</a>
        <a href="#">💊 Medicines</a>
        <a href="#">📊 Reports</a>
      </div>

      <!-- Column 4: Contact -->
      <div class="footer-col">
        <h4>Contact Us</h4>
        <div class="contact-item">
          <span class="ico">📍</span>
          <span>ETEI Campus<br>Ethiopia</span>
        </div>
        <div class="contact-item">
          <span class="ico">📞</span>
          <span>+251 9223 345 678</span>
        </div>
        <div class="contact-item">
          <span class="ico">✉️</span>
          <span>clinic@etei.edu</span>
        </div>

        <div class="socials">
          <a href="#" title="Facebook">f</a>
          <a href="#" title="Twitter">𝕏</a>
          <a href="#" title="LinkedIn">in</a>
          <a href="#" title="Email">✉</a>
        </div>
      </div>

    </div>

    <!-- Bottom bar -->
    <div class="footer-bottom">
      © 2026 <span>ETEI Clinic Management System</span> · All Rights Reserved
      · Designed by <span>Emmanuel Jada Pelgrino</span>
    </div>
  </footer>

</body>
</html>