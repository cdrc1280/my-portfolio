<?php

declare(strict_types=1);

namespace App\Domains\Seo\Actions;

use App\Domains\Seo\ValueObjects\ShareCard;
use GdImage;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Draws a 1200×630 Open Graph image in the site's dark theme (dot grid, emerald label, Geist type)
 * and keeps it on the local disk, keyed by its content, so each card is rendered once.
 */
final class RenderShareCardAction
{
    public const WIDTH = 1200;

    public const HEIGHT = 630;

    /** Bump to re-render every card after a design change. */
    private const VERSION = 1;

    private const PADDING = 80;

    /**
     * @return string Absolute path to the PNG.
     */
    public function execute(ShareCard $card): string
    {
        $disk = Storage::disk('local');
        $path = 'og/'.hash('sha256', self::VERSION.'|'.serialize($card)).'.png';

        if (! $disk->exists($path)) {
            $disk->put($path, $this->render($card));
        }

        return $disk->path($path);
    }

    private function render(ShareCard $card): string
    {
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        if ($image === false) {
            throw new RuntimeException('Could not create the share card image.');
        }

        imageantialias($image, true);

        $background = $this->color($image, 0x09, 0x09, 0x0B);
        $dot = $this->color($image, 0x27, 0x27, 0x2A);
        $emerald = $this->color($image, 0x10, 0xB9, 0x81);
        $white = $this->color($image, 0xFA, 0xFA, 0xFA);
        $muted = $this->color($image, 0xA1, 0xA1, 0xAA);
        $rule = $this->color($image, 0x3F, 0x3F, 0x46);

        imagefilledrectangle($image, 0, 0, self::WIDTH, self::HEIGHT, $background);

        for ($x = 12; $x < self::WIDTH; $x += 24) {
            for ($y = 12; $y < self::HEIGHT; $y += 24) {
                imagefilledellipse($image, $x, $y, 3, 3, $dot);
            }
        }

        imagefilledrectangle($image, 0, 0, 8, self::HEIGHT, $emerald);

        $sans = resource_path('fonts/og/Geist-SemiBold.ttf');
        $mono = resource_path('fonts/og/GeistMono-Regular.ttf');
        $width = self::WIDTH - self::PADDING * 2;
        $y = self::PADDING + 24;

        imagettftext($image, 22, 0, self::PADDING, $y, $emerald, $mono, '// '.mb_strtolower($card->label));
        $y += 40;

        foreach ($this->wrap($card->title, $sans, 60, $width, 3) as $line) {
            $y += 76;
            imagettftext($image, 60, 0, self::PADDING, $y, $white, $sans, $line);
        }

        if ($card->subtitle !== null && $card->subtitle !== '') {
            $y += 24;

            foreach ($this->wrap($card->subtitle, $sans, 26, $width, 2) as $line) {
                $y += 42;
                imagettftext($image, 26, 0, self::PADDING, $y, $muted, $sans, $line);
            }
        }

        $footer = self::HEIGHT - self::PADDING;
        imageline($image, self::PADDING, $footer - 56, self::WIDTH - self::PADDING, $footer - 56, $rule);
        imagettftext($image, 26, 0, self::PADDING, $footer, $white, $sans, $card->name);

        if ($card->domain !== null) {
            $box = imagettfbbox(20, 0, $mono, $card->domain);
            $textWidth = $box === false ? 0 : $box[2] - $box[0];
            imagettftext($image, 20, 0, self::WIDTH - self::PADDING - $textWidth, $footer, $muted, $mono, $card->domain);
        }

        ob_start();
        imagepng($image, null, 6);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    /**
     * Greedy word wrap by measured width; the last allowed line ends in an ellipsis if text remains.
     *
     * @return list<string>
     */
    private function wrap(string $text, string $font, int $size, int $maxWidth, int $maxLines): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : "{$current} {$word}";

            if ($current !== '' && $this->measure($candidate, $font, $size) > $maxWidth) {
                $lines[] = $current;
                $current = $word;

                if (count($lines) === $maxLines) {
                    $last = (string) array_pop($lines);
                    $lines[] = $this->ellipsize($last, $font, $size, $maxWidth);

                    return $lines;
                }
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function ellipsize(string $line, string $font, int $size, int $maxWidth): string
    {
        while ($line !== '' && $this->measure("{$line}…", $font, $size) > $maxWidth) {
            $line = (string) preg_replace('/\s*\S+$/u', '', $line);
        }

        return "{$line}…";
    }

    private function measure(string $text, string $font, int $size): int
    {
        $box = imagettfbbox($size, 0, $font, $text);

        return $box === false ? 0 : $box[2] - $box[0];
    }

    /**
     * @param  int<0, 255>  $red
     * @param  int<0, 255>  $green
     * @param  int<0, 255>  $blue
     */
    private function color(GdImage $image, int $red, int $green, int $blue): int
    {
        $color = imagecolorallocate($image, $red, $green, $blue);

        return $color === false ? 0 : $color;
    }
}
