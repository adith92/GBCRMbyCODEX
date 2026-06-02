@props(['fallback'])

<a href="{{ url()->previous() !== url()->current() ? url()->previous() : $fallback }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
    Back
</a>
