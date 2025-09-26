@extends('admin.layouts.base-admin')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-1">Banners</h5>
                    <div class="d-flex gap-2">
                        <form method="get" class="d-flex gap-2">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="search title">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Status</option>
                                @foreach(['draft','active','archived'] as $st)
                                    <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-secondary">Filter</button>
                            <a href="{{ route('banners.index') }}" class="btn btn-sm btn-light">Reset</a>
                        </form>
                        <a href="{{ route('banners.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> New</a>
                    </div>
                </div>

                @if(session('success')) <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div> @endif

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Clicks</th>
                                <th class="text-end" style="width:160px;">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
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
                  <span class="badge @class([
                    'bg-outline-secondary' => $b->status==='draft',
                    'bg-success' => $b->status==='active',
                    'bg-secondary' => $b->status==='archived',
                  ])">{{ ucfirst($b->status) }}</span>
                                    </td>
                                    <td>{{ number_format($b->clicks ?? 0) }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('banners.show', $b) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Show"><i class="ti ti-eye"></i></a>
                                            <a href="{{ route('banners.edit', $b) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit"><i class="ti ti-edit"></i></a>
                                            <form action="{{ route('banners.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete banner?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-circle" title="Delete"><i class="ti ti-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No banners.</td></tr>
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
