<?php
// test_email.php - Test if PHPMailer is properly installed
echo "<h2>Testing PHPMailer Installation</h2>";

// Check if src folder exists
if(file_exists(__DIR__ . '/../src/PHPMailer.php')) {
    echo "✅ PHPMailer.php found<br>";
} else {
    echo "❌ PHPMailer.php NOT found. Check your path.<br>";
}

// Try to include PHPMailer
try {
    require __DIR__ . '/../src/PHPMailer.php';
    require __DIR__ . '/../src/SMTP.php';
    require __DIR__ . '/../src/Exception.php';
    echo "✅ PHPMailer files loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Failed to load: " . $e->getMessage() . "<br>";
}

// Check if email_config.php exists
if(file_exists('email_config.php')) {
    echo "✅ email_config.php found<br>";
} else {
    echo "❌ email_config.php NOT found<br>";
}

echo "<br><hr><br>";
echo "<strong>Next step:</strong> Update email_config.php with your Gmail credentials";
?>  