@props(['items' => []])

@if (! empty($items))
    <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
        @foreach ($items as $item)
            @if (! $loop->first)
                <span>/</span>
            @endif

            @if (! empty($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-slate-700">{{ $item['label'] }}</a>
            @else
                <span class="text-slate-700">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
