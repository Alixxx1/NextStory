@foreach (['ok', 'err'] as $m)@if (session($m))<div class="lc-msg {{ $m }}">{{ session($m) }}</div>@endif @endforeach
