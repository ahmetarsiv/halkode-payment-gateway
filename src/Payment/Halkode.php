<?php

namespace Webkul\Halkode\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Halkode extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'halkode';

    /**
     * Get redirect url.
     */
    public function getRedirectUrl(): string
    {
        return route('halkode.redirect');
    }

    /**
     * Returns payment method image.
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/money-transfer.png', 'shop');
    }
}
