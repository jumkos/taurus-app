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
    <p>Hello, {{ $mailData->nameTo }}</p>
    <p>I just create a referral for you form Taurus application.</p>
    <p>Referral Details:</p>
    <ul>
        <li><strong>Customer Name:</strong> {{ $mailData->customerName }}</li>
        <li><strong>Customer Phone:</strong> {{ $mailData->customerPhone }}</li>
    </ul>
</body>
</html>
