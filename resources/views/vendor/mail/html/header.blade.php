@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
{{ $slot }}
</a>
</td>
</tr>
<!-- The top section of every HTML email (logo area) -->
