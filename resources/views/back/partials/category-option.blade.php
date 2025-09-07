<option value="{{ $node->id }}">{{ $prefix }}{{ $node->name }}</option>
@foreach($node->children as $child)
    @include('back.partials.category-option', ['node' => $child, 'prefix' => $prefix.'— '])
@endforeach
