@php
    $t    = $node->translation();
    $img  = \Schema::hasTable('media') ? $node->getFirstMediaUrl('image', 'thumb') : null;
    $pt   = $node->parent?->translation(); // we leave parent blank if none
    $pad  = max(0, $level) * 20;           // indent children
@endphp

<tr>
    <td>{{ $node->id }}</td>
    <td>
        <div class="d-flex align-items-center gap-2" style="padding-left: {{ $pad }}px;">
            @if($img)
                <img src="{{ $img }}" alt="" class="rounded" style="width:28px;height:28px;object-fit:cover;">
            @else
                <span class="avatar bg-light border rounded" style="width:28px;height:28px;">
          <i class="ph-duotone ph-image text-muted"></i>
        </span>
            @endif
            <div>
                <div class="fw-semibold">{{ $t?->title ?? '—' }}</div>
                @if($t?->slug)
                    <div class="text-muted small">{{ $t->slug }}</div>
                @endif
            </div>
        </div>
    </td>
    <td><span class="badge bg-secondary text-uppercase">{{ $node->group }}</span></td>
    <td>{{ $pt?->title ?? '' }}</td> {{-- blank if no parent --}}
    <td>{{ $node->position }}</td>
    <td>
        @if($node->is_active)
            <span class="badge bg-success">{{ __('back/common.status.active') }}</span>
        @else
            <span class="badge bg-outline-secondary">{{ __('back/common.status.hidden') }}</span>
        @endif
    </td>
    <td class="text-muted">{{ $node->updated_at?->format('Y-m-d H:i') }}</td>
    <td class="text-end">
        <div class="d-inline-flex gap-2">
            <a href="{{ route('catalog.categories.edit', [$node, 'group'=>$group]) }}"
               class="btn btn-sm btn-outline-primary rounded-circle" title="{{ __('back/common.actions.edit') }}">
                <i class="ti ti-edit"></i>
            </a>
            <form action="{{ route('catalog.categories.destroy', $node) }}" method="POST"
                  onsubmit="return confirm('{{ __('back/categories.confirm_delete') }}')" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger rounded-circle" title="{{ __('back/common.actions.delete') }}">
                    <i class="ti ti-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>

@if($node->children && $node->children->isNotEmpty())
    @foreach($node->children as $child)
        @include('back.catalog.categories.partials.row', ['node' => $child, 'level' => $level + 1, 'group' => $group])
    @endforeach
@endif
