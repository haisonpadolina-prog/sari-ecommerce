<?php

namespace App\Support\Pdf;


/**
 * Small, dependency-free PDF writer for the SARI admin monitoring directory.
 * Outputs a real, multi-page A4 landscape PDF (not HTML/Print and not CSV).
 * Data must already be authorized and filtered by the controller.
 */
final class MonitoringRosterPdf
{
    private const WIDTH = 841.89;  // A4 landscape in PDF points.
    private const HEIGHT = 595.28;
    private const LEFT = 42.0;
    private const TABLE_TOP = 157.0;
    private const ROW_TOP = 184.0;
    private const ROW_HEIGHT = 27.0;
    private const PAGE_SIZE = 13;

    /** @var list<float> */
    private const COL_WIDTHS = [175.0, 176.0, 132.0, 92.0, 88.0, 94.0];

    /**
     * @param list<list<string>> $records Filtered, authorized rows from the controller.
     */
    public static function render(array $records, string $section, string $status, string $search, string $generatedAt): string
    {
        $headings = [
            'sellers' => ['STORE', 'EMAIL', 'LOCATION', 'PRODUCTS', 'ORDERS', 'STATUS'],
            'logistics' => ['PROVIDER', 'EMAIL', 'AREA', 'HUBS', 'DELIVERIES', 'STATUS'],
            'buyers' => ['BUYER', 'EMAIL', 'AREA', 'ORDERS', 'DELIVERED', 'STATUS'],
            'sla' => ['ORDER', 'STAGE', 'SELLER', 'LOGISTICS', 'ELAPSED', 'SLA STATE'],
        ];
        if (!isset($headings[$section])) {
            throw new \InvalidArgumentException('Unsupported monitoring export section.');
        }

        $rows = [];
        foreach ($records as $record) {
            if (count($record) !== 6) {
                throw new \InvalidArgumentException('Each monitoring PDF row must contain six fields.');
            }
            $rows[] = array_map(static fn ($value): array => [(string) $value, ''], array_values($record));
        }
        $chunks = array_chunk($rows, self::PAGE_SIZE);
        if ($chunks === []) $chunks = [[]];
        $pdfPages = [];
        foreach ($chunks as $index => $chunk) {
            $pdfPages[] = self::drawPage($chunk, $headings[$section], $section, $status, $search, $index + 1, count($chunks), count($rows), $generatedAt);
        }
        return self::assemble($pdfPages);
    }

    /** @param list<list<array{string,string}>> $rows */
    private static function drawPage(array $rows, array $labels, string $section, string $status, string $search, int $page, int $pages, int $total, string $generatedAt): string
    {
        $commands = [];
        self::text($commands, 42, 29, 'SARI', 20, true, '#C88912');
        self::text($commands, 100, 36, 'ADMINISTRATION', 8.3, true, '#344054');
        self::text($commands, 42, 51, 'PLATFORM MONITORING  /  ' . strtoupper($section), 13.2, true, '#26313D');
        self::text($commands, 42, 69, 'SARI administration  |  Filtered operations review  |  Riders managed by Logistics', 8.4, false, '#667085');
        self::rect($commands, 42, 80, 757, 2.3, '#C88912');

        self::text($commands, 42, 97, 'Exported records', 7.6, false, '#667085');
        self::text($commands, 42, 110, (string) $total, 13, true, '#253343');
        self::text($commands, 260, 97, 'Generated (PHT)', 7.6, false, '#667085');
        self::text($commands, 260, 110, $generatedAt, 10.1, true, '#253343');
        self::text($commands, 593, 97, 'Document', 7.6, false, '#667085');
        self::text($commands, 593, 110, 'SARI-MONITORING-' . strtoupper($section), 8, true, '#253343');
        self::line($commands, 42, 129, 799, 129, '#E5E7EB', .8);

        self::text($commands, 42, 144, 'Status: ' . ($section === 'sla' ? 'SLA stages' : strtoupper($status)) . '  |  Search: ' . ($search !== '' ? self::clamp($search, 420, 9) : 'All records'), 9.5, true, '#26313D');
        self::rect($commands, self::LEFT, self::TABLE_TOP, 757, 27, '#344054');
        $x = self::LEFT;
        foreach ($labels as $i => $label) {
            self::text($commands, $x + 9, 164, $label, 7.2, true, '#FFFFFF');
            $x += self::COL_WIDTHS[$i];
        }

        if ($rows === []) {
            self::text($commands, 42 + 14, 209, 'No monitoring records matched the selected filters.', 11, false, '#667085');
        }
        foreach ($rows as $index => $fields) {
            $top = self::ROW_TOP + $index * self::ROW_HEIGHT;
            if ($index % 2 === 1) {
                self::rect($commands, self::LEFT, $top, 757, self::ROW_HEIGHT, '#F8FAFC');
            }
            self::line($commands, self::LEFT, $top + self::ROW_HEIGHT, 799, $top + self::ROW_HEIGHT, '#E5E7EB', .65);
            $x = self::LEFT;
            foreach ($fields as $col => $lines) {
                $width = self::COL_WIDTHS[$col];
                self::text($commands, $x + 9, $top + 6, self::clamp($lines[0], $width - 17, $col === 0 ? 8.0 : 7.8), 7.8, $col === 0, '#293440');
                if ($lines[1] !== '') {
                    self::text($commands, $x + 9, $top + 18, self::clamp($lines[1], $width - 17, 7), 7, false, '#84909D');
                }
                $x += $width;
            }
        }

        self::line($commands, 42, 552, 799, 552, '#E5E7EB', .7);
        self::text($commands, 42, 560, 'SARI  |  Internal platform monitoring export  |  Handle as confidential information', 7.0, false, '#667085');
        self::text($commands, 749, 560, 'Page ' . $page . ' of ' . $pages, 7.5, true, '#344054');

        return implode("\n", $commands) . "\n";
    }

    private static function clamp(string $text, float $width, float $fontSize): string
    {
        $text = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $text) ?? '');
        $maxLength = max(4, (int) floor($width / ($fontSize * 0.53)));
        // Count using encoded display bytes, but return the original Unicode
        // string so encoding and PDF escaping happen exactly once in text().
        if (strlen(self::encode($text)) <= $maxLength) {
            return $text;
        }
        $characters = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $short = '';
        foreach ($characters as $character) {
            if (strlen(self::encode($short . $character . '...')) > $maxLength) {
                break;
            }
            $short .= $character;
        }

        return $short . '...';
    }

    private static function encode(string $value): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $converted = function_exists('iconv') ? @iconv('UTF-8', 'Windows-1252//TRANSLIT', $value) : false;
        if ($converted === false) {
            $converted = preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '';
        }

        return str_replace(["\\", '(', ')'], ["\\\\", '\\(', '\\)'], $converted);
    }

    private static function text(array &$cmd, float $x, float $yFromTop, string $text, float $size, bool $bold, string $color): void
    {
        [$r, $g, $b] = self::rgb($color);
        $text = self::encode($text);
        $cmd[] = sprintf('BT /F%d %.2F Tf %.3F %.3F %.3F rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET',
            $bold ? 2 : 1, $size, $r, $g, $b, $x, self::HEIGHT - $yFromTop - $size, $text);
    }

    private static function rect(array &$cmd, float $x, float $yTop, float $w, float $h, string $color): void
    {
        [$r, $g, $b] = self::rgb($color);
        $cmd[] = sprintf('%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f',
            $r, $g, $b, $x, self::HEIGHT - $yTop - $h, $w, $h);
    }

    private static function line(array &$cmd, float $x1, float $y1, float $x2, float $y2, string $color, float $width): void
    {
        [$r, $g, $b] = self::rgb($color);
        $cmd[] = sprintf('%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S',
            $r, $g, $b, $width, $x1, self::HEIGHT - $y1, $x2, self::HEIGHT - $y2);
    }

    /** @return array{float,float,float} */
    private static function rgb(string $hex): array
    {
        return [hexdec(substr($hex, 1, 2)) / 255, hexdec(substr($hex, 3, 2)) / 255, hexdec(substr($hex, 5, 2)) / 255];
    }

    /** @param list<string> $streams */
    private static function assemble(array $streams): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
        ];
        $kids = [];
        foreach ($streams as $i => $stream) {
            $pageId = 5 + $i * 2;
            $contentId = $pageId + 1;
            $kids[] = $pageId . ' 0 R';
            $objects[$pageId] = sprintf('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>', self::WIDTH, self::HEIGHT, $contentId);
            $objects[$contentId] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        }
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($streams) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $body . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref' . "\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($objects as $id => $body) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$id]) . "\n";
        }
        $pdf .= 'trailer' . "\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";

        return $pdf;
    }
}
