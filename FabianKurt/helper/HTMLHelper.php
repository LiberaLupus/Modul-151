<?php


namespace helper;

class HTMLHelper extends BaseHelper
{
    public function formatPrice($price, $discount, $id = ''){
        return ($discount > 0) ? '<span id="oldprice'.$id.'" class="oldPrice">'.number_format((float)(round($price * 2, 1) / 2), 2, '.', '').'$</span><span id="price'.$id.'" class="newPrice">'.number_format((float)(round($price*(1 - $discount) * 2, 1) / 2), 2, '.', '').'$</span>' : '<span id="price'.$id.'" class="price">'.number_format((float)(round($price * 2, 1) / 2), 2, '.', '').'$</span>';
    }
}