<!DOCTYPE html>
<html>
<head>
    <title>Referral Notification</title>
    <style>
        /* Add CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        strong {
            font-weight: bold;
        }

        p {
            color: #555;
        }
    </style>
</head>
<body>
    <p>Dear {{ $mailData->division }} Team,</p>
    <p>I just create a referral opportunity to your division in {{ $mailData->city }}, {{ $mailData->region }}. This is a prime chance to boost our customer base and drive growth in our local market.</p>
    <p><strong>Referral Details:</strong></p>
    <ul>
        <li>Customer Name: {{ $mailData->customerName }}</li>
        <li>Customer Phone: {{ $mailData->customerPhone }}</li>
    </ul>
    <p><strong>How to Get Involved:</strong></p>
    <ul>
        <li>1. Log in to your Taurus account</li>
        <li>2. Navigate to the "Referral Dashboard" section</li>
        <li>3. Locate the referral opportunity on behalf of {{ $mailData->customerName }} created by {{ $mailData->nameFrom }}.</li>
        <li>4. Take the first step by clicking "Process"</li>
    </ul>
    <p>Don't let this opportunity slip away. New customers are the lifeblood of our division's success, and this referral is a direct path to achieving that growth.</p>
    <p>Thank you for your dedication to our division's success. Let's work together to make the most of this opportunity and expand our customer base in {{ $mailData->city }}, {{ $mailData->region }}.</p>
    <br></br>
    <p>Best regards,</p>
    <p>{{ $mailData->nameFrom }}</p>
</body>
</html>
