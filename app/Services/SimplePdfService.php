<?php

namespace App\Services;

class SimplePdfService
{
    public function textToPdf(string $title, array $lines): string
    {
        $content = "BT /F1 12 Tf 50 790 Td (" . $this->escape($title) . ") Tj";
        $y = 770;

        foreach ($lines as $line) {
            $content .= sprintf(" 0 -16 Td (%s) Tj", $this->escape((string) $line));
            $y -= 16;

            if ($y < 40) {
                break;
            }
        }

        $content .= ' ET';

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj';
        $objects[] = '4 0 obj << /Length ' . strlen($content) . ' >> stream ' . $content . ' endstream endobj';
        $objects[] = '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xref = strlen($pdf);
        $pdf .= 'xref 0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }

        $pdf .= 'trailer << /Root 1 0 R /Size ' . (count($objects) + 1) . " >>\n";
        $pdf .= 'startxref ' . $xref . "\n%%EOF";

        return $pdf;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
