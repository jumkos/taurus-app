@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{asset('/img/logo.png')}}" class="logo" alt="Taurus Logo">
@else
<img src="{{asset('/img/logo.png')}}" class="logo" alt="Taurus Logo">
@endif
</a>
</td>
</tr>
