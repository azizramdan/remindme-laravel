@inject('carbon', 'Carbon\Carbon')

<x-mail::layout>
<x-slot:header>
<tr>
<td class="header">
<h1 style="text-align: center;">RemindMe</h1>
</td>
</tr>
</x-slot:header>

This is a friendly reminder about the upcoming event in your schedule.

**Event Name**: {{ $reminder->title }}  
**Date and Time**: {{ $carbon::parse($reminder->event_at)->format('F j, Y, g:i a') }}  
**Description**: {{ $reminder->description }}  

<x-slot:footer>
<tr>
<td class="header" />
</tr>
</x-slot:footer>
</x-mail::layout>
