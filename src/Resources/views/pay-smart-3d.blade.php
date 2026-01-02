@push('meta')
    <meta name="description" content="@lang('halkode::app.halkode.info')"/>
    <meta name="keywords" content="@lang('halkode::app.halkode.info')"/>
@endPush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        @lang('halkode::app.resources.title')
    </x-slot>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-[500px] rounded-xl shadow-xl p-6 sm:p-8">


            <div class="flex items-center justify-between mb-8">
                <div>
                    <a href="{{ route('halkode.cancel') }}" class="font-medium text-blue-700 hover:underline">
                        Sepete geri dön
                    </a>
                </div>

                <div>
                    @if ($logo = core()->getCurrentChannel()->logo_url)
                        <img
                            src="{{ $logo }}"
                            alt="{{ config('app.name') }}"
                            style="height: 40px; width: 110px;"
                        />
                    @else
                        <img
                            src="{{ bagisto_asset('images/logo.svg', 'shop') }}"
                            alt="{{ config('app.name') }}"
                            width="131"
                            height="29"
                            style="width: 156px;height: 40px;"
                        />
                    @endif
                </div>
            </div>

            <x-shop::form
                method="POST"
                :action="route('halkode.callback')"
                class="space-y-4"
            >
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                <input type="hidden" name="total" value="{{ $total }}">
                <input type="hidden" name="installments_number" value="1">

                <div>
                    <x-shop::form.control-group.control
                        type="text"
                        class="block w-[420px] max-w-full rounded-xl border-2 border-[#e9decc] bg-[#F1EADF] px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                        name="cc_holder_name"
                        rules="required"
                        label="Ad Soyad"
                        placeholder="Kart Sahibi Ad Soyad"
                    />

                    <x-shop::form.control-group.error control-name="cc_holder_name" />
                </div>

                <div>
                    <x-shop::form.control-group.control
                        type="text"
                        class="block w-[420px] max-w-full rounded-xl border-2 border-[#e9decc] bg-[#F1EADF] px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                        name="cc_no"
                        rules="required"
                        label="Kart Numarası"
                        placeholder="Kart Numarası"
                        maxlength="16"
                    />

                    <x-shop::form.control-group.error control-name="cc_no" />
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="flex gap-2">
                        <div>
                            <x-shop::form.control-group.control
                                type="text"
                                class="block w-[420px] max-w-full rounded-xl border-2 border-[#e9decc] bg-[#F1EADF] px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="expiry_month"
                                rules="required"
                                label="Ay"
                                placeholder="AA"
                                maxlength="2"
                                inputmode="numeric"
                            />

                            <x-shop::form.control-group.error control-name="expiry_month" />
                        </div>

                        <div>
                            <x-shop::form.control-group.control
                                type="text"
                                class="block w-[420px] max-w-full rounded-xl border-2 border-[#e9decc] bg-[#F1EADF] px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="expiry_year"
                                rules="required"
                                label="Yıl"
                                placeholder="YY"
                                maxlength="2"
                                inputmode="numeric"
                            />

                            <x-shop::form.control-group.error control-name="expiry_year" />
                        </div>

                        <div>
                            <x-shop::form.control-group.control
                                type="text"
                                class="block w-[420px] max-w-full rounded-xl border-2 border-[#e9decc] bg-[#F1EADF] px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="cvv"
                                rules="required"
                                label="CVV"
                                placeholder="CVV"
                                maxlength="3"
                                inputmode="numeric"

                            />

                            <x-shop::form.control-group.error control-name="cvv" />
                        </div>
                    </div>
                </div>

                <x-shop::button
                    type="submit"
                    class="secondary-button w-full max-w-full max-md:py-3 max-sm:rounded-lg max-sm:py-1.5"
                    :title="'Öde' . ' ' . number_format($total, 2) . ' ' . 'TRY'"

                />
            </x-shop::form>

            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
                <span>256-bit SSL Güvenli Ödeme</span>
            </div>

        </div>
    </div>
</x-shop::layouts>
