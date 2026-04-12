<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class QrCodeService
{
    /**
     * Generate QR Code sebagai base64 PNG image
     * Menggunakan Endroid QR Code library
     * 
     * @param string $data - Data untuk di-encode ke QR code (biasanya ID pesanan)
     * @return string - Base64 data URI dari PNG image
     */
    public static function generateBase64($data)
    {
        try {
            // Create QR Code instance
            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
            );

            // Generate PNG image
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Get image data dan convert ke base64
            $imageData = $result->getDataUri();
            
            return $imageData;

        } catch (\Exception $e) {
            \Log::error('QR Code generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate QR Code dan simpan ke file
     * 
     * @param string $data - Data untuk di-encode
     * @param string $filename - Nama file tanpa extension
     * @param string $path - Path untuk menyimpan file
     * @return string - Path file yang disimpan
     */
    public static function generateFile($data, $filename, $path = 'public/qrcodes')
    {
        try {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            // Create QR Code instance
            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
            );

            // Generate PNG image
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Save to file
            $filepath = $path . '/' . $filename . '.png';
            file_put_contents($filepath, $result->getString());

            return $filepath;

        } catch (\Exception $e) {
            \Log::error('QR Code file generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate QR Code sebagai SVG
     * 
     * @param string $data - Data untuk di-encode
     * @return string - SVG markup atau base64 SVG data URI
     */
    public static function generateSvg($data)
    {
        try {
            // Create QR Code instance
            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
            );

            // Generate SVG image menggunakan SvgWriter
            // Note: SvgWriter mungkin perlu di-require terpisah
            $writer = new \Endroid\QrCode\Writer\SvgWriter();
            $result = $writer->write($qrCode);
            
            return $result->getString();

        } catch (\Exception $e) {
            \Log::warning('QR Code SVG generation fallback to PNG: ' . $e->getMessage());
            // Fallback ke PNG jika SVG gagal
            return self::generateBase64($data);
        }
    }
}
