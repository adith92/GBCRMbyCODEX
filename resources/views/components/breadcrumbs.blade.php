@props(['items' => []])

@if (! empty($items))
    <nav aria-label="Breadcrumb" class="overflow-x-auto">
        <ol class="flex min-w-max items-center gap-1.5 text-[11px] text-slate-500">
        @foreach ($items as $item)
            @if (! $loop->first)
                <li class="px-1 text-slate-300">/</li>
            @endif

            <li class="shrink-0">
                @if (! empty($item['url']) && ! $loop->last)
                    <a href="{{ $item['url'] }}" class="rounded-full px-2.5 py-1 transition hover:bg-white hover:text-[#185FA5]">{{ $item['label'] }}</a>
                @else
                    <span class="rounded-full border border-[#E5E7EB] bg-white px-2.5 py-1 font-semibold text-[#042C53]">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
        </ol>
    </nav>
@endif
