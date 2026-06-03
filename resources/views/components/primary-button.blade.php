<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-[9px] border border-[#042C53] bg-[#042C53] px-4 py-2.5 text-sm font-semibold text-white transition duration-200 hover:border-[#053561] hover:bg-[#053561] focus:outline-none focus:ring-2 focus:ring-[#378ADD]/20']) }}>
    {{ $slot }}
</button>
