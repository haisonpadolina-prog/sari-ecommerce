<?php

namespace App\Support\Pdf;

/** Dependency-free PDF export. Controller supplies authorized, filtered records. */
final class LogisticsReportPdf
{
    private const WIDTH = 841.89;
    private const HEIGHT = 595.28;
    private const LEFT = 40.0;
    private const RIGHT = 801.89;
    private array $pages = [];
    private array $commands = [];
    private float $y = 0;

    public static function render(string $branch, string $period, string $generated, array $sections): string
    {
        $writer = new self;
        $writer->newPage($branch, $period, $generated);
        foreach ($sections as $section) {
            $writer->table($section, $branch, $period, $generated);
        }
        $writer->pages[] = $writer->commands;
        $streams = [];
        $total = count($writer->pages);
        foreach ($writer->pages as $index => $commands) {
            self::line($commands, 40, 553, 802, 553, '#CBD2DB', .6);
            self::text($commands, 40, 564, 'SARI LOGISTICS  |  Internal report  |  Amounts in PHP', 7, false, '#687385');
            self::text($commands, 730, 564, 'Page '.($index + 1).' / '.$total, 7, true, '#273444');
            $streams[] = implode("\n", $commands)."\n";
        }
        return self::assemble($streams);
    }

    private function newPage(string $branch, string $period, string $generated): void
    {
        if ($this->commands !== []) $this->pages[] = $this->commands;
        $this->commands = [];
        self::text($this->commands, 40, 26, 'SARI', 23, true, '#182433');
        self::text($this->commands, 119, 33, 'LOGISTICS  /  DELIVERY & EARNINGS REPORT', 11, true, '#273444');
        self::line($this->commands, 40, 60, 802, 60, '#273444', 1.2);
        $branchLines = self::wrap('Operating branch: '.$branch, 750, 8);
        $this->y = 71;
        foreach ($branchLines as $line) {
            self::text($this->commands, 40, $this->y, $line, 8, false, '#526176');
            $this->y += 11;
        }
        self::text($this->commands, 40, $this->y, 'Period: '.$period, 8, true, '#273444');
        $this->y += 13;
        self::text($this->commands, 40, $this->y, 'Generated: '.$generated.' (Asia/Manila)  |  Full filtered dataset', 7.5, false, '#687385');
        $this->y += 25;
    }

    private function table(array $section, string $branch, string $period, string $generated): void
    {
        $labels = $section['columns'];
        $widths = $section['widths'];
        $heading = function (bool $continued = false) use ($section, $labels, $widths): void {
            self::text($this->commands, 40, $this->y, $section['title'].($continued ? ' (continued)' : ''), 11, true, '#273444');
            $this->y += 20;
            self::rect($this->commands, 40, $this->y, 761.89, 25, '#EFF2F6');
            $x = 40;
            foreach ($labels as $i => $label) {
                self::text($this->commands, $x + 7, $this->y + 8, $label, 7, true, '#445166');
                $x += $widths[$i];
            }
            $this->y += 25;
        };
        if ($this->y + 80 > 535) $this->newPage($branch, $period, $generated);
        $heading();
        $rows = $section['rows'];
        if ($rows === []) $rows = [array_merge(['No records in this reporting period.'], array_fill(0, count($labels) - 1, ''))];
        foreach ($rows as $row) {
            $cells = [];
            foreach ($labels as $i => $_) $cells[] = self::wrap((string) ($row[$i] ?? ''), $widths[$i] - 14, 8);
            $height = max(26, max(array_map('count', $cells)) * 11 + 14);
            // Split exceptionally long cells across pages without discarding text.
            $offset = 0;
            $lineCount = max(array_map('count', $cells));
            do {
                if ($this->y + min($height, 37) > 535) {
                    $this->newPage($branch, $period, $generated);
                    $heading(true);
                }
                $take = max(1, (int) floor((535 - $this->y - 14) / 11));
                $take = min($take, $lineCount - $offset);
                $rowHeight = max(26, $take * 11 + 14);
                if (! empty($section['bars']) && $offset === 0) {
                    $percent = max(0, min(100, (float) ($row[2] ?? 0)));
                    self::rect($this->commands, 607, $this->y + 4, 188 * $percent / 100, $rowHeight - 8, '#F0DDB6');
                }
                $x = 40;
                foreach ($cells as $i => $lines) {
                    foreach (array_slice($lines, $offset, $take) as $j => $line) {
                        self::text($this->commands, $x + 7, $this->y + 7 + $j * 11, $line, 8, $i === 0, '#273444');
                    }
                    $x += $widths[$i];
                }
                $this->y += $rowHeight;
                self::line($this->commands, 40, $this->y, 802, $this->y, '#DDE2E8', .5);
                $offset += $take;
            } while ($offset < $lineCount);
        }
        $this->y += 22;
    }

    private static function wrap(string $value, float $width, float $size): array
    {
        $value = str_replace('₱', 'PHP ', $value);
        $encoded = function_exists('iconv') ? @iconv('UTF-8', 'Windows-1252//TRANSLIT', $value) : false;
        $encoded = $encoded === false ? (preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '') : $encoded;
        $encoded = preg_replace('/[\x00-\x1F\x7F]+/', ' ', $encoded) ?? '';
        $limit = max(1, (int) floor($width / ($size * .62)));
        return explode("\n", wordwrap(trim($encoded), $limit, "\n", true));
    }

    private static function text(array &$cmd, float $x, float $yFromTop, string $text, float $size, bool $bold, string $color): void
    {
        [$r, $g, $b] = self::rgb($color);
        $text = str_replace(["\\", '(', ')'], ["\\\\", '\\(', '\\)'], $text);
        $cmd[] = sprintf('BT /F%d %.2F Tf %.3F %.3F %.3F rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET',
            $bold ? 2 : 1, $size, $r, $g, $b, $x, self::HEIGHT - $yFromTop - $size, $text);
    }

    private static function rect(array &$cmd, float $x, float $yTop, float $w, float $h, string $color): void
    {
        [$r, $g, $b] = self::rgb($color);
        $cmd[] = sprintf('%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f', $r, $g, $b, $x, self::HEIGHT - $yTop - $h, $w, $h);
    }

    private static function line(array &$cmd, float $x1, float $y1, float $x2, float $y2, string $color, float $width): void
    {
        [$r, $g, $b] = self::rgb($color);
        $cmd[] = sprintf('%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S', $r, $g, $b, $width, $x1, self::HEIGHT - $y1, $x2, self::HEIGHT - $y2);
    }

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
