@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Timely Record')
<img src="https://timely-record.s3.eu-central-1.amazonaws.com/public/timely_record_logo_hor2.png" alt="Timely Record" style="max-width: 244px">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
