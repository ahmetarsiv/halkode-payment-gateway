<?php

namespace Webkul\Halkode\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository   $orderRepository,
        protected InvoiceRepository $invoiceRepository
    ) {
        //
    }

    /**
     * Redirects to the Halk Öde payment page.
     */
    public function redirect()
    {
        $cart = Cart::getCart();

        return view('halkode::pay-smart-3d', [
            'cart'       => $cart,
            'invoice_id' => $cart->id,
            'total'      => number_format($cart->grand_total, 2, '.', ''),
        ]);
    }

    /**
     * Redirects to the 3D.
     */
    public function callback(Request $request)
    {
        $cart = Cart::getCart();

        $invoiceId = rand();

        $products = 0;
        $halkodeItems = [];
        foreach ($cart->items as $product) {
            $halkodeItems[$products] = [
                'name'        => $product->name,
                'price'       => number_format($product->price, 2, '.', ''),
                'quantity'    => $product->quantity,
                'description' => $product->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS',
            ];
            $products++;
        }

        if ($cart->shipping_amount > 0) {
            $halkodeItems[] = [
                'name'        => $cart->shipping_method,
                'price'       => number_format($cart->shipping_amount, 2, '.', ''),
                'quantity'    => 1,
                'description' => 'SERVICE',
            ];
        }

        $payload = [
            "cc_holder_name"      => $request->cc_holder_name,
            "cc_no"               => $request->cc_no,
            "expiry_month"        => $request->expiry_month,
            "expiry_year"         => $request->expiry_year,
            "cvv"                 => $request->cvv,
            "currency_code"       => "TRY",
            "installments_number" => 1,
            "invoice_id"          => $invoiceId,
            "invoice_description" => "Grand total:" . $cart->grand_total,
            "total"               => number_format($cart->grand_total, 2, '.', ''),
            "items"               => json_encode($halkodeItems),
            "name"                => $cart['customer_first_name'],
            "surname"             => $cart['customer_last_name'],
            "merchant_key"        => env('HALKODE_MERCHANT_KEY'),
            "hash_key"            => $this->generateHash(number_format($cart->grand_total, 2, '.', ''), 1, 'TRY', env('HALKODE_MERCHANT_KEY'), $invoiceId, env('HALKODE_APP_SECRET')),
            "return_url"          => route('halkode.success'),
            "cancel_url"          => route('halkode.cancel'),
        ];

        $ch = curl_init(env('HALKODE_BASE_URL'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo json_encode(["error" => "API isteği başarısız. HTTP Kodu: $httpCode"]);
            exit;
        }

        echo $response;
    }

    /**
     * Place an order and redirect to the success page.
     */
    public function success()
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Redirect to the cart page with error message.
     */
    public function failure()
    {
        session()->flash('error', 'Halk Öde payment was either cancelled or the transaction failed.');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Prepares order's invoice data for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    protected function generateHash($total, $installment, $currency_code, $merchant_key, $invoice_id, $app_secret)
    {
        $data = $total . '|' . $installment . '|' . $currency_code . '|' . $merchant_key . '|' . $invoice_id;
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);
        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", 0, $iv);
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);
        return $msg_encrypted_bundle;
    }
}
