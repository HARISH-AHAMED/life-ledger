<?php
// Prepare your payload data
$data = [
    'service_id' => 'your_service_id',
    'template_id' => 'your_template_id',
    'user_id' => 'your_public_key', // EmailJS public key
    'template_params' => [
        'user_email' => 'user@example.com',
        'total_spent' => '₹3,200',
        'top_category' => 'Food & Beverages',
        'budget_remaining' => '₹1,800'
    ]
];

// Setup cURL
$ch = curl_init('https://api.emailjs.com/api/v1.0/email/send');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute and capture response
$response = curl_exec($ch);
curl_close($ch);

// Check response
if ($response) {
    echo "Weekly email sent successfully via EmailJS!";
} else {
    echo "Failed to send email via EmailJS.";
}
?>
