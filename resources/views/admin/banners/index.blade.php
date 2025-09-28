@extends('admin.layouts.base-admin')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-1">Baneri</h5>
                    <div class="d-flex gap-2">
                        <form method="get" class="d-flex gap-2">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="pretraži naslov">
                            @php $statusOpts = ['draft'=>'Nacrt','active'=>'Aktivan','archived'=>'Arhivirano']; @endphp
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Status</option>
                                @foreach($statusOpts as $stVal => $stLabel)
                                    <option value="{{ $stVal }}" @selected(request('status')===$stVal)>{{ $stLabel }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-secondary">Filtriraj</button>

                        </form>
                        <a href="{{ route('banners.index') }}" class="btn  btn-outline-secondary">Poništi</a>
                        <a href="{{ route('banners.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Novi baner</a>
                    </div>
                </div>

                @if(session('success')) <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div> @endif

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th>Pregled</th>
                                <th>Naslov</th>
                                <th>Status</th>
                                <th>Klikovi</th>
                                <th class="text-end" style="width:160px;">Radnje</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $statusLabels = ['draft'=>'Nacrt','active'=>'Aktivan','archived'=>'Arhivirano'];
                            @endphp
                            @forelse($banners as $b)
                                @php $t = $b->translation(); @endphp
                                <tr>
                                    <td>{{ $b->id }}</td>
                                    <td>
                                        @if($url = $b->getFirstMediaUrl('banner','thumb'))
                                            <img src="{{ $url }}" class="rounded border" width="80" height="40" style="object-fit:cover;">
                                        @endif
                                    </td>
                                    <td class="text-break">
                                        <div class="fw-semibold">{{ $t?->title ?? '—' }}</div>
                                        @if($t?->url)<div class="text-muted small">{{ $t->url }}</div>@endif
                                    </td>
                                    <td>
                                        @php
                                            $badge = $b->status === 'active' ? 'badge text-bg-success'
                                                : ($b->status === 'draft' ? 'badge text-bg-warning' : 'badge text-bg-info');
                                        @endphp
                                        <span class="{{ $badge }}">{{ $statusLabels[$b->status] ?? ucfirst($b->status) }}</span>
                                    </td>
                                    <td>{{ number_format($b->clicks ?? 0) }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('banners.show', $b) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Prikaži"><i class="ti ti-eye"></i></a>
                                            <a href="{{ route('banners.edit', $b) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Uredi"><i class="ti ti-edit"></i></a>
                                            <form action="{{ route('banners.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Izbrisati baner?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-circle" title="Obriši"><i class="ti ti-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">Nema banera.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(method_exists($banners, 'links'))
                    <div class="card-footer">{{ $banners->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
