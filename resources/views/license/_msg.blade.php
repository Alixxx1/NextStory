@foreach (['ok' => 'circle-check', 'err' => 'triangle-exclamation'] as $m => $ic)@if (session($m))<div class="lc-msg {{ $m }}"><i class="fa-solid fa-{{ $ic }}"></i><span>{{ session($m) }}</span></div>@endif @endforeach
@if (isset($errors) && $errors->any())<div class="lc-msg err"><i class="fa-solid fa-triangle-exclamation"></i><span>{{ $errors->first() }}</span></div>@endif
