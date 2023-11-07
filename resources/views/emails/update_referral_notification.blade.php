<!DOCTYPE html>
<html>
<head>
    <title>Update Referral Notification</title>
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
    <p>Dear {{ $mailData->nameTo }},</p>
    <p>We hope this message finds you well. We are pleased to inform you that there has been an update regarding your referral, {{ $mailData->refCode }}. The referral status has progressed from '{{ $mailData->prevStatus }}' to '{{ $mailData->newStatus }}'.</p>
    <p><strong>Referral Details:</strong></p>
    <ul>
        <li>Referral Code: {{ $mailData->refCode }}</li>
        <li>Customer Name: {{ $mailData->customerName }}</li>
        <li>Referral Status: {{ $mailData->newStatus }}</li>
        <li>Date of Update: {{ $mailData->updateDate }}</li>
    </ul>
    <p><strong>Implications:</strong></p>
    <p>This status change holds significant implications for the referral opportunity. Depending on the specific status change, it could indicate positive progress, challenges, or changes in the referral's journey. Please review the details of the new status to understand the current situation.</p>
    <p>We value your ongoing involvement in this referral and appreciate your dedication to the process.</p>
    <p>Your contributions to this referral opportunity are invaluable, and we are committed to keeping you informed of all developments, whether positive or negative, as they occur.</p>
    <br></br>
    <p>Best regards,</p>
    <p>{{ $mailData->nameFrom }}</p>
</body>
</html>
