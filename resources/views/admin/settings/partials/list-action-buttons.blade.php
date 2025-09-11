@php
    $item = $item ?? collect(['id' => 0]);
    $editHandler   = $editHandler   ?? 'openModal';
    $deleteHandler = $deleteHandler ?? 'deleteItem';
@endphp

<button class="btn btn-sm btn-outline-primary rounded-circle"
        onclick='{{ $editHandler }}(@json($item));'
        title="{{ __('back/common.actions.edit') }}">
    <i class="ti ti-edit"></i>
</button>

<button class="btn btn-sm btn-outline-danger rounded-circle"
        onclick="{{ $deleteHandler }}({{ (int)($item->id ?? 0) }});"
        title="{{ __('back/common.actions.delete') }}">
    <i class="ti ti-trash"></i>
</button>