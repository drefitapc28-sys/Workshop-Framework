<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    public static function generateBase64(string $value): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode(
            $value,
            $generator::TYPE_CODE_128,
            2,  // lebar batang
            40  // tinggi barcode
        );
        return 'data:image/png;base64,' . base64_encode($barcode);
    }
}