<?php

namespace App\Http\Controllers;

use App\Models\VendorPartner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorPartnerController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('clients.view'), 403);

        $search = trim($request->string('q')->toString());
        $status = $request->string('status')->toString();
        $category = $request->string('category')->toString();
        $sort = $request->string('sort')->toString() ?: 'name';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $allowedSorts = ['name', 'category', 'service_type', 'city', 'status', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $vendors = VendorPartner::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('service_type', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('vendor-partners.index', compact('vendors', 'search', 'status', 'category', 'sort', 'direction'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('clients.create'), 403);

        return view('vendor-partners.create', [
            'vendor' => new VendorPartner(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('clients.create'), 403);

        $data = $this->validated($request);
        $data['code'] = $data['code'] ?: 'VP-'.now()->format('Ym').'-'.str_pad((string) (VendorPartner::count() + 1), 4, '0', STR_PAD_LEFT);

        $vendor = VendorPartner::create($data);

        return redirect()->route('partners.vendors.show', $vendor)->with('success', 'Partner/vendor berhasil dibuat.');
    }

    public function show(Request $request, VendorPartner $vendor): View
    {
        abort_unless($request->user()->can('clients.view'), 403);

        return view('vendor-partners.show', compact('vendor'));
    }

    public function edit(Request $request, VendorPartner $vendor): View
    {
        abort_unless($request->user()->can('clients.update'), 403);

        return view('vendor-partners.edit', compact('vendor'));
    }

    public function update(Request $request, VendorPartner $vendor): RedirectResponse
    {
        abort_unless($request->user()->can('clients.update'), 403);

        $vendor->update($this->validated($request, $vendor));

        return redirect()->route('partners.vendors.show', $vendor)->with('success', 'Partner/vendor berhasil diperbarui.');
    }

    protected function validated(Request $request, ?VendorPartner $vendor = null): array
    {
        return $request->validate([
            'code' => ['nullable', 'string', 'max:50', Rule::unique('vendor_partners', 'code')->ignore($vendor?->id)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['vendor', 'partner', 'supplier'])],
            'service_type' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive', 'onboarding'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
