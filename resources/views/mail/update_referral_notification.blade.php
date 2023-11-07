<x-mail::message>
Dear {{ $mailData->nameTo }},

We hope this message finds you well. We are pleased to inform you that there has been an update regarding your referral, {{ $mailData->refCode }}. The referral status has progressed from '{{ $mailData->prevStatus }}' to '{{ $mailData->newStatus }}'.

**Referral Details:**
- Referral Code: {{ $mailData->refCode }}
- Customer Name: {{ $mailData->customerName }}
- Referral Status: {{ $mailData->newStatus }}
- Date of Update: {{ $mailData->updateDate }}

**Implications:**

This status change holds significant implications for the referral opportunity. Depending on the specific status change, it could indicate positive progress, challenges, or changes in the referral's journey. Please review the details of the new status to understand the current situation.

We value your ongoing involvement in this referral and appreciate your dedication to the process.</p>

Your contributions to this referral opportunity are invaluable, and we are committed to keeping you informed of all developments, whether positive or negative, as they occur.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
