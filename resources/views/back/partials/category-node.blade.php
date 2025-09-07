<div class="ml-{{ isset($depth) ? $depth*4 : 0 }} py-2">
    <div class="flex items-center gap-2">
        <span class="font-medium">{{ $node->name }}</span>
        <span class="text-xs text-gray-500">(#{{ $node->id }})</span>
    </div>
    @if($node->children->isNotEmpty())
        <div class="mt-1 space-y-1">
            @foreach($node->children as $child)
                @include('back.partials.category-node', ['node' => $child, 'depth' => ($depth ?? 0)+1])
            @endforeach
        </div>
    @endif
</div>
