<?php

namespace App\Http\Requests;

use App\GoodsPackage;
use App\Packages\Utils\BitcoinUtils;
use App\Shop;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ShopStatsAccountingAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->shop()->enabled;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Shop $shop */
        $shop = Auth::user()->shop();

        return [
            'good' => 'required|in:' . $shop->availableGoods()->pluck('id')->implode(','),
            'amount' => 'required|numeric|min:0.001',
            'measure' => 'required|in:' . implode(',', [
                GoodsPackage::MEASURE_GRAM,
                GoodsPackage::MEASURE_PIECE,
                GoodsPackage::MEASURE_ML,
            ]),
            'price' => 'required|numeric|min:' . ($this->get('currency') == BitcoinUtils::CURRENCY_BTC ? 0.01 : 10),
            'currency' => 'required|in:' . implode(',', [
                BitcoinUtils::CURRENCY_RUB,
                BitcoinUtils::CURRENCY_BTC,
                BitcoinUtils::CURRENCY_USD,
            ]),
        ];
    }
}
