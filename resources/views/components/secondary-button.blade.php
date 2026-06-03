<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-[9px] border border-[#D7DCE3] bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#378ADD]/20 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
