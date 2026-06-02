@props(['items' => []])

@if (! empty($items))
    <nav aria-label="Breadcrumb" class="overflow-x-auto">
        <ol class="flex min-w-max items-center gap-2 text-xs text-slate-500">
        @foreach ($items as $item)
            @if (! $loop->first)
                <li class="text-slate-300">/</li>
            @endif

            <li class="shrink-0">
                @if (! empty($item['url']) && ! $loop->last)
                    <a href="{{ $item['url'] }}" class="rounded-full px-2 py-1 transition hover:bg-slate-100 hover:text-slate-700">{{ $item['label'] }}</a>
                @else
                    <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
        </ol>
    </nav>
@endif
