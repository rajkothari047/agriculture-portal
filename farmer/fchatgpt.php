<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AgriNovax Chatbot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      width: 100%;
      overflow: hidden; /* Prevents scrolling on html/body */
    }

    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      height: 100vh;
      overflow: hidden;
    }

    /* Make navbar stay at top */
    .navbar-custom {
      flex-shrink: 0;
    }

    /* Chatbot section takes remaining height */
    .chatbot-section {
      flex: 1;
      width: 100%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-height: 0; /* Important for flex children to respect overflow */
    }

    /* Chatbot container fills available space */
    .chatbot-container {
      flex: 1;
      width: 100%;
      overflow: hidden;
      background: #f5f5f5;
      min-height: 0; /* Important for flex children */
    }

    /* Iframe takes full container */
    .chatbot-container iframe {
      width: 100%;
      height: 100%;
      border: none;
      display: block;
    }

    /* ========== RESPONSIVE STYLES ========== */
    
    /* Mobile Devices (max-width: 767px) */
    @media (max-width: 767px) {
      body {
        height: 100vh;
        overflow: hidden;
        position: fixed;
        width: 100%;
      }
      
      .chatbot-container {
        flex: 1;
        width: 100%;
      }
      
      .chatbot-container iframe {
        width: 100%;
        height: 100%;
      }
    }
    
    /* Small Mobile Devices (max-width: 480px) */
    @media (max-width: 480px) {
      body {
        font-size: 14px;
      }
      
      .chatbot-container iframe {
        -webkit-overflow-scrolling: touch;
      }
    }
    
    /* Landscape Mode on Mobile */
    @media (max-width: 767px) and (orientation: landscape) {
      body {
        height: 100vh;
        overflow: hidden;
      }
      
      .chatbot-section {
        flex: 1;
      }
    }
    
    /* Tablet Devices (768px - 1024px) */
    @media (min-width: 768px) and (max-width: 1024px) {
      body {
        height: 100vh;
        overflow: hidden;
      }
      
      .chatbot-section {
        flex: 1;
      }
    }
    
    /* Desktop (min-width: 1025px) */
    @media (min-width: 1025px) {
      body {
        height: 100vh;
        overflow: hidden;
      }
      
      .chatbot-section {
        flex: 1;
      }
    }
    
    /* Fix for some browsers that have issues with vh on mobile */
    @supports (-webkit-touch-callout: none) {
      body {
        height: -webkit-fill-available;
      }
      
      html {
        height: -webkit-fill-available;
      }
    }
  </style>
</head>

<body>

<?php include ('fnav.php'); ?>

<div class="chatbot-section">
  <div class="chatbot-container">
    <iframe 
      src="https://cdn.botpress.cloud/webchat/v3.6/shareable.html?configUrl=https://files.bpcontent.cloud/2025/11/24/08/20251124080903-9QAT0A91.json"
      title="AgriNovax AI Assistant"
      allow="microphone; camera">
    </iframe>
  </div>
</div>

<!-- Optional: Small script to handle any iframe resize issues -->
<script>
  (function() {
    // Fix for iOS Safari viewport height issues
    function setViewportHeight() {
      let vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
      
      let body = document.body;
      if (body) {
        body.style.height = `${window.innerHeight}px`;
      }
    }
    
    window.addEventListener('resize', setViewportHeight);
    window.addEventListener('orientationchange', setViewportHeight);
    setViewportHeight();
  })();
</script>

</body>
</html>