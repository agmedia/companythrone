<h3>{{ __('front/cart.podaci') }}:</h3>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td style="width: 40%">{{ __('front/cart.name') }}:</td>
        <td style="width: 60%"><b>{{ $order->payment_fname . ' ' . $order->payment_lname }}</b></td>
    </tr>
    <tr>
        <td>{{ __('front/cart.adresa') }}:</td>
        <td><b>{{ $order->payment_address }}</b></td>
    </tr>
    <tr>
        <td>{{ __('front/cart.grad') }}:</td>
        <td><b>{{ $order->payment_zip . ' ' . $order->payment_city }}</b></td>
    </tr>
    <tr>
        <td>{{ __('front/cart.email') }}:</td>
        <td><b>{{ $order->payment_email }}</b></td>
    </tr>
    <tr>
        <td>{{ __('front/cart.telefon') }}:</td>
        <td><b>{{ ($order->payment_phone) ? $order->payment_phone : '' }}</b></td>
    </tr>
    @if( ! empty($order->company) || ! empty($order->oib))
        <tr><td></td><td></td></tr>
        <tr>
            <td>{{ __('front/cart.tvrtka') }}:</td>
            <td><b>{{ ($order->company) ? $order->company : '' }}</b></td>
        </tr>
        <tr>
            <td>{{ __('front/cart.oib') }}:</td>
            <td><b>{{ ($order->oib) ? $order->oib : '' }}</b></td>
        </tr>
    @endif
</table>
