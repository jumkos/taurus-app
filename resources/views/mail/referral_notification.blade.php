<x-mail::message>
Dear {{ $mailData->division }} Team,

A referral opportunity just created to your division in {{ $mailData->city }}, {{ $mailData->region }}. This is a prime chance to boost our customer base and drive growth in our local market.

**Referral Details:**
- Product Categories: {{ $mailData->product }}
- Customer Name: {{ $mailData->customerName }}
- Referral Nominal: IDR {{ $mailData->nominal }}

**How to Get Involved:**
1. Log in to your Taurus account
2. Navigate to the "Referral Dashboard" section
3. Locate the referral opportunity on behalf of {{ $mailData->customerName }} created by {{ $mailData->nameFrom }}.
4. Take the first step by clicking "Process"

Don't let this opportunity slip away. New customers are the lifeblood of our division's success, and this referral is a direct path to achieving that growth.

Thank you for your dedication to our division's success. Let's work together to make the most of this opportunity and expand our customer base in {{ $mailData->city }}, {{ $mailData->region }}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
