<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    public function dataUri(string $payload, int $size = 240, int $margin = 8): string
    {
        try {
            $result = $this->buildWithWriter(new PngWriter(), $payload, $size, $margin);

            return $result->getDataUri();
        } catch (\Throwable) {
            $result = $this->buildWithWriter(new SvgWriter(), $payload, $size, $margin);
            $svg = method_exists($result, 'getString') ? $result->getString() : '';

            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
    }

    private function buildWithWriter(object $writer, string $payload, int $size, int $margin): object
    {
        return Builder::create()
            ->writer($writer)
            ->data($payload)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size($size)
            ->margin($margin)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();
    }
}
