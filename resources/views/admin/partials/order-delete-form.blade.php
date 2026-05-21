<form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="{{ !empty($class) && str_contains($class, 'w-100') ? 'd-block' : 'd-inline' }}"
      onsubmit="return confirm('Удалить заказ {{ $order->numberLabel() }}? Это действие нельзя отменить.');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger {{ $class ?? '' }}" title="Удалить заказ">
        <i class="fas fa-trash me-1"></i>@if(!empty($label)){{ $label }}@endif
    </button>
</form>
