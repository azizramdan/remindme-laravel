@inject('carbon', 'Carbon\Carbon')
@inject('htmlString', 'Illuminate\Support\HtmlString')

<x-mail::layout>
<x-slot:header>
<tr>
<td class="header">
<h1 style="text-align: center;">RemindMe</h1>
</td>
</tr>
</x-slot:header>

This is a friendly reminder about the upcoming event in your schedule.

**Title**:  
{{ $reminder->title }}

**Date and Time**:  
{{ $carbon::parse($reminder->event_at)->format('F j, Y, g:i a T') }}  
{{ $carbon::parse($reminder->event_at)->setTimezone('+0700')->format('F j, Y, g:i a T') }}  

**Description**:  
{{ new $htmlString(nl2br($reminder->description)) }}  

<x-slot:footer>
<tr>
<td class="header" />
</tr>
</x-slot:footer>
</x-mail::layout>
