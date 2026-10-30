<?php

namespace App\Support\Art;

/**
 * Motifs vectoriels originaux d'inspiration coréenne (boîte 200×200).
 * Générés en SVG pour illustrer les produits « print on demand » : remplaçables par les vrais
 * visuels des designers (Printful / Printify…) sans toucher au code (champ image du produit).
 * $p = palette : ink (traits), paper (clair), plus couleurs hanbok.
 */
class Motifs
{
    public const HANBOK = ['red' => '#cd2e3a', 'blue' => '#0047a0', 'yellow' => '#f2b632', 'green' => '#2f7d5b', 'pink' => '#f08aa4', 'orange' => '#e8892b', 'black' => '#1c1b1a', 'white' => '#faf6ef'];

    public static function palette(bool $darkGarment): array
    {
        return self::HANBOK + ['ink' => $darkGarment ? '#f6efe4' : '#2a1d18', 'paper' => $darkGarment ? '#2a1d18' : '#faf6ef'];
    }

    public static function render(string $name, array $p): string
    {
        return method_exists(self::class, $name) ? self::$name($p) : self::hangul($p, '대박', 'DAEBAK');
    }

    /** Texte hangul + sous-titre latin. */
    public static function hangul(array $p, string $text = '사랑', string $sub = 'SARANG', ?string $accent = null): string
    {
        $a = $accent ?? $p['red'];
        $size = mb_strlen($text) <= 2 ? 92 : 64;

        return <<<SVG
<g font-family="'Noto Sans KR','Malgun Gothic','Apple SD Gothic Neo',sans-serif" text-anchor="middle">
  <text x="100" y="108" font-size="{$size}" font-weight="900" fill="{$p['ink']}" letter-spacing="-2">{$text}</text>
  <rect x="48" y="122" width="104" height="5" rx="2.5" fill="{$a}"/>
  <text x="100" y="152" font-size="15" font-weight="700" letter-spacing="7" fill="{$p['ink']}">{$sub}</text>
  <path d="M100 176c-12-9-20-15-20-23a10 10 0 0 1 20-3 10 10 0 0 1 20 3c0 8-8 14-20 23z" fill="{$a}" transform="translate(20,38) scale(.8)" opacity=".95"/>
</g>
SVG;
    }

    public static function taegeuk(array $p): string
    {
        $tri = '';
        foreach ([[45, 3, 'solid'], [135, 3, 'broken'], [225, 3, 'mix'], [315, 3, 'mix2']] as [$ang, , $kind]) {
            $bars = '';
            for ($i = 0; $i < 3; $i++) {
                $y = ($i - 1) * 9;
                $broken = match ($kind) { 'solid' => false, 'broken' => true, 'mix' => $i !== 1, default => $i === 1 };
                $bars .= $broken
                    ? "<rect x='-20' y='".($y - 2.5)."' width='15' height='5' rx='1' fill='{$p['ink']}'/><rect x='5' y='".($y - 2.5)."' width='15' height='5' rx='1' fill='{$p['ink']}'/>"
                    : "<rect x='-20' y='".($y - 2.5)."' width='40' height='5' rx='1' fill='{$p['ink']}'/>";
            }
            $tri .= "<g transform='rotate({$ang}) translate(0,-80) rotate(90)'>{$bars}</g>";
        }

        return <<<SVG
<g transform="translate(100,100)">
  {$tri}
  <circle r="46" fill="{$p['blue']}"/>
  <path d="M-46 0A46 46 0 0 1 46 0A23 23 0 0 1 0 0A23 23 0 0 0 -46 0Z" fill="{$p['red']}"/>
  <circle r="46" fill="none" stroke="{$p['ink']}" stroke-width="2"/>
</g>
SVG;
    }

    public static function dancheong(array $p): string
    {
        $petals = '';
        $cols = [$p['red'], $p['blue'], $p['yellow'], $p['green']];
        for ($i = 0; $i < 8; $i++) {
            $petals .= "<ellipse cx='0' cy='-52' rx='15' ry='30' fill='{$cols[$i % 4]}' stroke='{$p['ink']}' stroke-width='2' transform='rotate(".($i * 45).")'/>";
        }
        $dots = '';
        for ($i = 0; $i < 16; $i++) {
            $dots .= "<circle cx='0' cy='-90' r='3' fill='{$p['ink']}' transform='rotate(".($i * 22.5).")'/>";
        }

        return <<<SVG
<g transform="translate(100,100)">
  {$dots}{$petals}
  <circle r="34" fill="{$p['yellow']}" stroke="{$p['ink']}" stroke-width="2.5"/>
  <circle r="24" fill="{$p['red']}" stroke="{$p['ink']}" stroke-width="2"/>
  <circle r="14" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="2"/>
  <circle r="6" fill="{$p['blue']}"/>
</g>
SVG;
    }

    public static function crane(array $p): string
    {
        return <<<SVG
<g>
  <circle cx="100" cy="96" r="62" fill="{$p['red']}"/>
  <g fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round">
    <path d="M70 66C52 40 70 22 92 18c-8 18-4 34 4 52z" opacity=".92"/>
    <path d="M52 112C80 94 120 94 150 106C130 122 88 128 52 112z"/>
    <path d="M92 104C76 62 104 30 142 24c-14 22-16 48-12 78z"/>
    <path d="M52 112L28 130l32-10z"/>
    <path d="M150 106c16-8 20-26 12-38" fill="none" stroke-width="5"/>
    <circle cx="162" cy="66" r="5.5"/>
    <path d="M166 64l16-3" fill="none"/>
    <path d="M70 120L44 150M82 124L62 152" fill="none" stroke-width="2"/>
  </g>
  <path d="M20 168c20-10 36 8 56-2s34-6 52 0 40 10 60-2" fill="none" stroke="{$p['ink']}" stroke-width="3" stroke-linecap="round"/>
  <path d="M30 182c18-8 30 6 48-2s30-4 46 0" fill="none" stroke="{$p['ink']}" stroke-width="2" stroke-linecap="round" opacity=".6"/>
</g>
SVG;
    }

    public static function skyline(array $p): string
    {
        $b = '';
        $heights = [60, 90, 48, 112, 70, 98, 54, 82, 66];
        $x = 14;
        foreach ($heights as $i => $h) {
            $w = 18 + ($i % 3) * 4;
            $b .= "<rect x='{$x}' y='".(176 - $h)."' width='{$w}' height='{$h}' fill='{$p['ink']}' opacity='".(0.78 + ($i % 3) * .08)."'/>";
            for ($wy = 176 - $h + 8; $wy < 168; $wy += 14) {
                $b .= "<rect x='".($x + 4)."' y='{$wy}' width='3' height='5' fill='{$p['yellow']}' opacity='.9'/>";
                if ($w > 20) {
                    $b .= "<rect x='".($x + $w - 8)."' y='{$wy}' width='3' height='5' fill='{$p['yellow']}' opacity='.7'/>";
                }
            }
            $x += $w + 3;
        }
        $stars = '';
        foreach ([[30, 30], [58, 18], [150, 26], [172, 52], [24, 66], [128, 14]] as [$sx, $sy]) {
            $stars .= "<circle cx='{$sx}' cy='{$sy}' r='1.6' fill='{$p['yellow']}'/>";
        }

        return <<<SVG
<g>
  {$stars}
  <circle cx="148" cy="52" r="26" fill="{$p['yellow']}" opacity=".95"/>
  <circle cx="158" cy="46" r="22" fill="{$p['paper']}" opacity=".0"/>
  <g fill="{$p['ink']}">
    <rect x="97" y="40" width="3.4" height="70"/>
    <path d="M93 112h11l-2-26h-7z"/><ellipse cx="98.7" cy="80" rx="12" ry="5"/><ellipse cx="98.7" cy="66" rx="8" ry="4"/>
    <rect x="98" y="22" width="1.8" height="22"/>
  </g>
  {$b}
  <rect x="8" y="176" width="184" height="4" rx="2" fill="{$p['ink']}"/>
</g>
SVG;
    }

    public static function tiger(array $p): string
    {
        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round">
  <circle cx="52" cy="54" r="19" fill="{$p['orange']}" stroke="{$p['ink']}" stroke-width="3"/><circle cx="52" cy="56" r="10" fill="{$p['pink']}"/>
  <circle cx="148" cy="54" r="19" fill="{$p['orange']}" stroke="{$p['ink']}" stroke-width="3"/><circle cx="148" cy="56" r="10" fill="{$p['pink']}"/>
  <circle cx="100" cy="104" r="64" fill="{$p['orange']}" stroke="{$p['ink']}" stroke-width="3"/>
  <ellipse cx="100" cy="132" rx="38" ry="28" fill="{$p['white']}"/>
  <g stroke="{$p['ink']}" stroke-width="5" fill="none"><path d="M100 50v14M86 52l4 14M114 52l-4 14"/><path d="M92 58h16M90 64h20"/>
  <path d="M40 98l16 4M38 112l17 1M42 126l15-4M160 98l-16 4M162 112l-17 1M158 126l-15-4"/></g>
  <ellipse cx="76" cy="98" rx="11" ry="13" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="2.5"/><circle cx="78" cy="100" r="6.5" fill="{$p['ink']}"/><circle cx="80" cy="97" r="2" fill="#fff"/>
  <ellipse cx="124" cy="98" rx="11" ry="13" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="2.5"/><circle cx="122" cy="100" r="6.5" fill="{$p['ink']}"/><circle cx="124" cy="97" r="2" fill="#fff"/>
  <path d="M91 118h18l-9 10z" fill="{$p['pink']}" stroke="{$p['ink']}" stroke-width="2.5"/>
  <path d="M100 128v8M84 140q8 8 16-4 8 12 16 4" fill="none" stroke="{$p['ink']}" stroke-width="3"/>
  <circle cx="64" cy="124" r="6" fill="{$p['pink']}" opacity=".6"/><circle cx="136" cy="124" r="6" fill="{$p['pink']}" opacity=".6"/>
  <path d="M30 176c10-14 28-10 34-24" fill="none" stroke="{$p['ink']}" stroke-width="3"/>
  <g transform="translate(150,156)"><ellipse rx="14" ry="8" fill="{$p['ink']}"/><path d="M-4 -2l-14 -8 4 10z" fill="{$p['ink']}"/><ellipse cx="3" cy="3" rx="7" ry="3.5" fill="{$p['white']}"/><circle cx="8" cy="-3" r="1.4" fill="{$p['white']}"/></g>
</g>
SVG;
    }

    public static function moonjar(array $p): string
    {
        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round">
  <circle cx="100" cy="92" r="70" fill="{$p['yellow']}" opacity=".35"/>
  <path d="M82 40c0-8 36-8 36 0 0 14 38 28 38 70 0 34-26 48-56 48s-56-14-56-48c0-42 38-56 38-70z" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="3"/>
  <path d="M60 130c-5 18 6 22 18 24" fill="none" stroke="{$p['ink']}" stroke-width="2" opacity=".25"/>
  <path d="M64 142c20 6 40-2 60-18s24-26 28-40" fill="none" stroke="{$p['ink']}" stroke-width="3"/>
  <g fill="{$p['pink']}" stroke="{$p['ink']}" stroke-width="1.5">
    <circle cx="150" cy="86" r="7"/><circle cx="130" cy="112" r="6"/><circle cx="96" cy="136" r="6"/><circle cx="76" cy="146" r="5"/>
  </g>
  <g fill="{$p['yellow']}"><circle cx="150" cy="86" r="2"/><circle cx="130" cy="112" r="2"/><circle cx="96" cy="136" r="2"/></g>
  <rect x="40" y="170" width="120" height="6" rx="3" fill="{$p['ink']}"/>
</g>
SVG;
    }

    protected static function blossom(float $x, float $y, float $r, string $petal, string $center, string $stroke): string
    {
        $out = "<g transform='translate({$x},{$y})'>";
        for ($i = 0; $i < 5; $i++) {
            $a = $i * 72 - 90;
            $px = round(cos(deg2rad($a)) * $r * .78, 2);
            $py = round(sin(deg2rad($a)) * $r * .78, 2);
            $out .= "<circle cx='{$px}' cy='{$py}' r='".round($r * .62, 2)."' fill='{$petal}' stroke='{$stroke}' stroke-width='1.4'/>";
        }
        $out .= "<circle r='".round($r * .3, 2)."' fill='{$center}'/>";
        for ($i = 0; $i < 5; $i++) {
            $a = $i * 72 - 54;
            $out .= "<circle cx='".round(cos(deg2rad($a)) * $r * .5, 2)."' cy='".round(sin(deg2rad($a)) * $r * .5, 2)."' r='1.2' fill='{$stroke}'/>";
        }

        return $out.'</g>';
    }

    public static function plum(array $p): string
    {
        $f = self::blossom(54, 66, 17, $p['white'], $p['yellow'], $p['ink']).self::blossom(112, 48, 20, $p['pink'], $p['yellow'], $p['ink']).self::blossom(150, 104, 17, $p['white'], $p['yellow'], $p['ink']).self::blossom(84, 130, 15, $p['pink'], $p['yellow'], $p['ink']).self::blossom(34, 152, 12, $p['white'], $p['yellow'], $p['ink']);

        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round">
  <path d="M10 188C36 164 44 132 76 112c24-15 38-38 64-66" fill="none" stroke="{$p['ink']}" stroke-width="7"/>
  <path d="M76 112c24 6 46-2 80-12M44 156c-10-12-12-22-6-34" fill="none" stroke="{$p['ink']}" stroke-width="4"/>
  {$f}
  <circle cx="132" cy="150" r="4" fill="{$p['pink']}"/><circle cx="170" cy="60" r="4" fill="{$p['pink']}"/><circle cx="26" cy="96" r="3.5" fill="{$p['pink']}"/>
</g>
SVG;
    }

    public static function cherry(array $p): string
    {
        $petals = '';
        foreach ([[40, 40, 20], [150, 36, -30], [172, 128, 40], [30, 138, -10], [96, 170, 70], [112, 90, 20], [60, 94, -50], [168, 76, 10]] as $i => [$x, $y, $r]) {
            $s = $i % 2 ? 1 : 1.3;
            $petals .= "<path d='M0 0c-6-8-6-18 0-22 0 4 6 4 6 0 6 4 6 14 0 22z' fill='{$p['pink']}' stroke='{$p['ink']}' stroke-width='1.2' transform='translate({$x},{$y}) rotate({$r}) scale({$s})'/>";
        }
        $f = self::blossom(100, 100, 40, '#fbd2dc', $p['yellow'], $p['ink']).self::blossom(46, 62, 20, $p['pink'], $p['yellow'], $p['ink']).self::blossom(152, 64, 22, '#fbd2dc', $p['yellow'], $p['ink']);

        return "<g stroke-linecap='round' stroke-linejoin='round'><circle cx='100' cy='100' r='82' fill='{$p['pink']}' opacity='.22'/>{$f}{$petals}</g>";
    }

    public static function lattice(array $p): string
    {
        $lines = '';
        for ($i = 0; $i <= 6; $i++) {
            $v = 30 + $i * 23.3;
            $lines .= "<path d='M{$v} 30V170M30 {$v}H170' stroke='{$p['ink']}' stroke-width='2'/>";
        }
        $f = self::blossom(100, 100, 24, $p['red'], $p['yellow'], $p['ink']);

        return <<<SVG
<g>
  <rect x="20" y="20" width="160" height="160" rx="6" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="6"/>
  {$lines}
  <rect x="20" y="20" width="160" height="160" rx="6" fill="none" stroke="{$p['ink']}" stroke-width="6"/>
  <circle cx="100" cy="100" r="34" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="3"/>
  {$f}
  <g fill="{$p['red']}"><circle cx="40" cy="40" r="4"/><circle cx="160" cy="40" r="4"/><circle cx="40" cy="160" r="4"/><circle cx="160" cy="160" r="4"/></g>
</g>
SVG;
    }

    public static function irworobongdo(array $p): string
    {
        $peaks = '';
        foreach ([[100, 50, 50, '#2f7d5b'], [48, 78, 44, '#1f6b8a'], [152, 78, 44, '#1f6b8a'], [20, 104, 34, '#2f7d5b'], [182, 104, 34, '#2f7d5b']] as [$cx, $top, $w, $c]) {
            $peaks .= "<path d='M".($cx - $w)." 150L{$cx} {$top}L".($cx + $w)." 150z' fill='{$c}' stroke='{$p['ink']}' stroke-width='2' stroke-linejoin='round'/>";
        }
        $waves = '';
        for ($i = 0; $i < 4; $i++) {
            $y = 150 + $i * 11;
            $waves .= "<path d='M0 {$y}q12 -9 25 0t25 0 25 0 25 0 25 0 25 0 25 0 25 0' fill='none' stroke='#cfe3ee' stroke-width='2.2' opacity='".(0.9 - $i * .15)."'/>";
        }

        return <<<SVG
<g>
  <rect x="0" y="0" width="200" height="200" rx="8" fill="#16335c"/>
  <circle cx="54" cy="40" r="20" fill="{$p['red']}"/><circle cx="146" cy="40" r="17" fill="{$p['white']}"/>
  {$peaks}
  <rect x="0" y="150" width="200" height="50" fill="#1c4a78"/>{$waves}
  <g fill="#e9d9a8"><circle cx="30" cy="20" r="1.4"/><circle cx="92" cy="14" r="1.4"/><circle cx="178" cy="16" r="1.4"/></g>
</g>
SVG;
    }

    public static function bojagi(array $p): string
    {
        $cols = [$p['red'], $p['yellow'], $p['blue'], $p['green'], $p['pink'], $p['white'], $p['orange'], '#8b5fbf'];
        $cells = '';
        $y = 18;
        $heights = [34, 50, 42, 38];
        foreach ($heights as $ri => $h) {
            $x = 18;
            $widths = $ri % 2 ? [50, 36, 56, 22] : [28, 56, 34, 46];
            foreach ($widths as $ci => $w) {
                $c = $cols[($ri * 3 + $ci * 2 + $ri) % count($cols)];
                $cells .= "<rect x='{$x}' y='{$y}' width='{$w}' height='{$h}' fill='{$c}' stroke='{$p['ink']}' stroke-width='1.6'/><rect x='".($x + 3)."' y='".($y + 3)."' width='".($w - 6)."' height='".($h - 6)."' fill='none' stroke='{$p['ink']}' stroke-width='1' stroke-dasharray='3 3' opacity='.55'/>";
                $x += $w;
            }
            $y += $h;
        }

        return "<g>{$cells}<rect x='18' y='18' width='164' height='164' fill='none' stroke='{$p['ink']}' stroke-width='4'/></g>";
    }

    public static function haechi(array $p): string
    {
        $mane = '';
        for ($i = 0; $i < 14; $i++) {
            $mane .= "<path d='M-10 -62L0 -88L10 -62z' fill='".($i % 2 ? $p['orange'] : $p['yellow'])."' stroke='{$p['ink']}' stroke-width='2' transform='rotate(".($i * 25.7).")'/>";
        }

        return <<<SVG
<g transform="translate(100,102)" stroke-linecap="round" stroke-linejoin="round">
  {$mane}
  <circle r="58" fill="#3a9d9a" stroke="{$p['ink']}" stroke-width="3"/>
  <path d="M-6 -56L0 -80L6 -56z" fill="{$p['yellow']}" stroke="{$p['ink']}" stroke-width="2.5"/>
  <circle cx="-34" cy="-34" r="10" fill="#3a9d9a" stroke="{$p['ink']}" stroke-width="3"/><circle cx="34" cy="-34" r="10" fill="#3a9d9a" stroke="{$p['ink']}" stroke-width="3"/>
  <ellipse cx="-22" cy="-8" rx="13" ry="15" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="2.5"/><circle cx="-20" cy="-6" r="7.5" fill="{$p['ink']}"/><circle cx="-17" cy="-9" r="2.4" fill="#fff"/>
  <ellipse cx="22" cy="-8" rx="13" ry="15" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="2.5"/><circle cx="20" cy="-6" r="7.5" fill="{$p['ink']}"/><circle cx="23" cy="-9" r="2.4" fill="#fff"/>
  <path d="M-22 -28q-4-12 6-14M22 -28q4-12-6-14" fill="none" stroke="{$p['ink']}" stroke-width="3"/>
  <ellipse cx="0" cy="16" rx="11" ry="8" fill="{$p['ink']}"/>
  <path d="M-30 26q30 28 60 0" fill="{$p['red']}" stroke="{$p['ink']}" stroke-width="3"/><path d="M-12 36l5 8 5-8M2 36l5 8 5-8" fill="{$p['white']}" stroke="{$p['ink']}" stroke-width="1.8"/>
  <path d="M-48 12a8 8 0 1 1 8 8M48 12a8 8 0 1 0-8 8" fill="none" stroke="{$p['ink']}" stroke-width="2.4"/>
</g>
SVG;
    }

    /** 6 éléments « kimchi friends » séparés pour la planche de stickers (boîte 100×100). */
    public static function kimchiItems(array $p): array
    {
        $face = fn (float $y = 0) => "<circle cx='-9' cy='".($y)."' r='2.6' fill='{$p['black']}'/><circle cx='9' cy='".($y)."' r='2.6' fill='{$p['black']}'/><ellipse cx='-15' cy='".($y + 6)."' rx='4' ry='2.5' fill='{$p['pink']}' opacity='.7'/><ellipse cx='15' cy='".($y + 6)."' rx='4' ry='2.5' fill='{$p['pink']}' opacity='.7'/><path d='M-4 ".($y + 5)."q4 4 8 0' fill='none' stroke='{$p['black']}' stroke-width='2' stroke-linecap='round'/>";

        return [
            "<g transform='translate(50,52)'><path d='M-30 24C-38-8-24-40 0-42S38-8 30 24C20 38-20 38-30 24z' fill='#e8f2c9' stroke='{$p['black']}' stroke-width='3'/><path d='M-26 4C-10-6 12-6 26 4' fill='none' stroke='{$p['green']}' stroke-width='6' stroke-linecap='round' opacity='.5'/><path d='M-14-24c10 6 18 6 28 0' fill='none' stroke='{$p['red']}' stroke-width='3' stroke-linecap='round'/>".$face(6).'</g>',
            "<g transform='translate(50,52)'><path d='M-8-30c-10-14-20-12-22-4M0-32c0-16 4-20 8-18M8-30c10-14 20-12 22-4' fill='none' stroke='{$p['green']}' stroke-width='5' stroke-linecap='round'/><path d='M-26-22C-8-30 8-30 26-22 22 4 8 36 0 38-8 36-22 4-26-22z' fill='#fff' stroke='{$p['black']}' stroke-width='3'/>".$face(-4).'</g>',
            "<g transform='translate(50,54)'><path d='M0-36L34 30Q0 40-34 30z' fill='#fff' stroke='{$p['black']}' stroke-width='3' stroke-linejoin='round'/><path d='M-24 14H24l10 16Q0 40-34 30z' fill='{$p['black']}'/>".$face(2).'</g>',
            "<g transform='translate(50,52)'><path d='M0 30c-40-26-34-56-10-58 6 0 10 4 10 8 0-4 4-8 10-8 24 2 30 32-10 58z' fill='{$p['red']}' stroke='{$p['black']}' stroke-width='3' stroke-linejoin='round'/>".str_replace($p['black'], '#fff', $face(-6)).'</g>',
            "<g transform='translate(50,54)'><path d='M-8-34C20-38 36-14 24 14c-8 16-26 14-30 6' fill='{$p['red']}' stroke='{$p['black']}' stroke-width='3'/><path d='M-8-34c-6-6-2-12 4-12' fill='none' stroke='{$p['green']}' stroke-width='5' stroke-linecap='round'/><circle cx='-8' cy='-6' r='2.2' fill='#fff'/><circle cx='4' cy='-6' r='2.2' fill='#fff'/><path d='M-8 6q6 5 12 0' fill='none' stroke='#fff' stroke-width='2.2' stroke-linecap='round'/></g>",
            "<g transform='translate(50,56)'><path d='M-36-6h72c0 26-14 38-36 38S-36 20-36-6z' fill='#f1d8b2' stroke='{$p['black']}' stroke-width='3'/><ellipse cx='0' cy='-6' rx='36' ry='11' fill='#fff' stroke='{$p['black']}' stroke-width='3'/><circle cx='-14' cy='-8' r='6' fill='{$p['red']}'/><circle cx='4' cy='-10' r='5' fill='{$p['green']}'/><circle cx='16' cy='-6' r='5' fill='{$p['yellow']}'/><ellipse cx='0' cy='-14' rx='6' ry='4' fill='#fff' stroke='{$p['black']}' stroke-width='2'/><circle cx='0' cy='-14' r='2.5' fill='{$p['yellow']}'/>".$face(20).'</g>',
        ];
    }

    public static function kimchi(array $p): string
    {
        $items = self::kimchiItems($p);
        $out = "<g text-anchor='middle' font-family=\"'Noto Sans KR','Malgun Gothic',sans-serif\">";
        $pos = [[8, 16, .9], [96, 8, .9], [52, 62, 1.05]];
        foreach ([0, 1, 2] as $i) {
            [$x, $y, $s] = $pos[$i];
            $out .= "<g transform='translate({$x},{$y}) scale({$s})'>{$items[$i]}</g>";
        }
        $out .= "<text x='100' y='176' font-size='17' font-weight='900' letter-spacing='3' fill='{$p['ink']}'>KIMCHI FRIENDS</text><text x='100' y='192' font-size='10' font-weight='700' fill='{$p['red']}' letter-spacing='4'>김치 프렌즈</text></g>";

        return $out;
    }

    public static function neon(array $p): string
    {
        $pink = '#ff5fa8';
        $cyan = '#4fe7ff';

        return <<<SVG
<g font-family="'Noto Sans KR','Malgun Gothic','Apple SD Gothic Neo',sans-serif" text-anchor="middle">
  <defs><filter id="neon-glow" x="-30%" y="-30%" width="160%" height="160%"><feGaussianBlur stdDeviation="4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs>
  <rect x="6" y="14" width="188" height="172" rx="22" fill="#14102b"/>
  <g filter="url(#neon-glow)" fill="none" stroke-linecap="round">
    <rect x="18" y="26" width="164" height="148" rx="16" stroke="{$cyan}" stroke-width="4"/>
    <path d="M100 38v26M96 64h8M99 64l-2 14h6l-2-14M100 78v18" stroke="{$pink}" stroke-width="3"/>
    <text x="100" y="128" font-size="38" font-weight="800" fill="none" stroke="{$pink}" stroke-width="3" letter-spacing="3">SEOUL</text>
    <text x="100" y="158" font-size="22" font-weight="800" fill="none" stroke="{$cyan}" stroke-width="2.4" letter-spacing="6">서울</text>
  </g>
  <text x="100" y="128" font-size="38" font-weight="800" fill="#ffd7ea" letter-spacing="3" opacity=".9">SEOUL</text>
  <text x="100" y="158" font-size="22" font-weight="800" fill="#d4f8ff" letter-spacing="6" opacity=".9">서울</text>
</g>
SVG;
    }

    public static function tangerine(array $p): string
    {
        $seg = '';
        for ($i = 0; $i < 28; $i++) {
            $seg .= "<circle cx='".round(100 + cos($i * .9) * (18 + ($i % 5) * 9), 1)."' cy='".round(108 + sin($i * 1.7) * (14 + ($i % 4) * 9), 1)."' r='1.6' fill='#c96a12' opacity='.5'/>";
        }

        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round">
  <circle cx="100" cy="110" r="64" fill="{$p['orange']}" stroke="{$p['ink']}" stroke-width="3"/>
  <ellipse cx="78" cy="84" rx="18" ry="10" fill="#fff" opacity=".35" transform="rotate(-30 78 84)"/>
  {$seg}
  <path d="M100 50c0-14 6-22 14-26" fill="none" stroke="{$p['ink']}" stroke-width="4"/>
  <path d="M104 48c14-20 40-18 50-8-12 14-36 20-50 8z" fill="{$p['green']}" stroke="{$p['ink']}" stroke-width="3"/><path d="M110 46c12-6 24-8 36-4" fill="none" stroke="{$p['ink']}" stroke-width="1.6" opacity=".6"/>
  <circle cx="80" cy="112" r="5" fill="{$p['ink']}"/><circle cx="120" cy="112" r="5" fill="{$p['ink']}"/>
  <ellipse cx="68" cy="126" rx="8" ry="5" fill="{$p['pink']}" opacity=".8"/><ellipse cx="132" cy="126" rx="8" ry="5" fill="{$p['pink']}" opacity=".8"/>
  <path d="M90 124q10 12 20 0" fill="none" stroke="{$p['ink']}" stroke-width="3.5"/>
  <text x="100" y="196" font-size="15" font-weight="900" text-anchor="middle" letter-spacing="5" fill="{$p['ink']}" font-family="'Noto Sans KR','Malgun Gothic',sans-serif">감귤 · JEJU</text>
</g>
SVG;
    }

    public static function bananamilk(array $p): string
    {
        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round" font-family="'Noto Sans KR','Malgun Gothic',sans-serif" text-anchor="middle">
  <circle cx="100" cy="100" r="86" fill="{$p['yellow']}" opacity=".28"/>
  <path d="M130 22l4-14" stroke="{$p['ink']}" stroke-width="4"/>
  <path d="M60 56h80l-6 120H66z" fill="#fff3b0" stroke="{$p['ink']}" stroke-width="3.5"/>
  <path d="M56 44h88v14H56z" fill="{$p['yellow']}" stroke="{$p['ink']}" stroke-width="3.5"/>
  <path d="M68 100h64l-2 36H70z" fill="#fff" opacity=".55"/>
  <circle cx="86" cy="110" r="3.4" fill="{$p['ink']}"/><circle cx="114" cy="110" r="3.4" fill="{$p['ink']}"/><ellipse cx="78" cy="120" rx="5" ry="3" fill="{$p['pink']}"/><ellipse cx="122" cy="120" rx="5" ry="3" fill="{$p['pink']}"/>
  <path d="M94 118q6 7 12 0" fill="none" stroke="{$p['ink']}" stroke-width="3"/>
  <text x="100" y="84" font-size="18" font-weight="900" fill="{$p['ink']}">바나나</text>
  <text x="100" y="158" font-size="14" font-weight="900" fill="{$p['ink']}" letter-spacing="1">우유 MILK</text>
  <path d="M78 70c8-9 24-9 34 0" fill="none" stroke="{$p['ink']}" stroke-width="2.5"/>
</g>
SVG;
    }

    public static function dokkaebi(array $p): string
    {
        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round">
  <path d="M46 30l16 30M154 30l-16 30" stroke="{$p['ink']}" stroke-width="3" fill="{$p['yellow']}"/>
  <path d="M40 74c-16-14-8-40 8-44 2 12 10 22 22 26M160 74c16-14 8-40-8-44-2 12-10 22-22 26" fill="{$p['red']}" stroke="{$p['ink']}" stroke-width="3"/>
  <path d="M54 56c4-24 88-24 92 0 6 40-4 78-46 78S48 96 54 56z" fill="#5a7fd6" stroke="{$p['ink']}" stroke-width="3.5"/>
  <path d="M62 66c12-10 64-10 76 0" fill="none" stroke="{$p['ink']}" stroke-width="3"/>
  <path d="M70 80q10-10 24-2M130 80q-10-10-24-2" fill="none" stroke="{$p['ink']}" stroke-width="4"/>
  <circle cx="82" cy="92" r="10" fill="#fff" stroke="{$p['ink']}" stroke-width="2.6"/><circle cx="84" cy="93" r="5.5" fill="{$p['ink']}"/>
  <circle cx="118" cy="92" r="10" fill="#fff" stroke="{$p['ink']}" stroke-width="2.6"/><circle cx="116" cy="93" r="5.5" fill="{$p['ink']}"/>
  <path d="M96 102l4 8 4-8z" fill="{$p['ink']}"/>
  <path d="M72 116q28 24 56 0" fill="#2a1d18" stroke="{$p['ink']}" stroke-width="3"/><path d="M82 123l5 10 5-10M108 123l5 10 5-10" fill="#fff" stroke="{$p['ink']}" stroke-width="2"/>
  <g transform="rotate(32 150 150)"><rect x="140" y="104" width="20" height="84" rx="8" fill="#a9763d" stroke="{$p['ink']}" stroke-width="3"/><g fill="{$p['yellow']}" stroke="{$p['ink']}" stroke-width="1.6"><circle cx="150" cy="112" r="4"/><circle cx="145" cy="126" r="3.6"/><circle cx="155" cy="124" r="3.6"/><circle cx="150" cy="138" r="3.6"/></g></g>
  <g fill="{$p['yellow']}"><path d="M30 130l4 8 8 4-8 4-4 8-4-8-8-4 8-4z"/><circle cx="170" cy="168" r="4"/></g>
</g>
SVG;
    }

    public static function maedeup(array $p): string
    {
        $loops = '';
        foreach ([0, 60, 120] as $r) {
            $loops .= "<ellipse cx='0' cy='0' rx='46' ry='19' transform='rotate({$r})' fill='none' stroke='{$p['red']}' stroke-width='9'/><ellipse cx='0' cy='0' rx='46' ry='19' transform='rotate({$r})' fill='none' stroke='{$p['ink']}' stroke-width='1.2' opacity='.55'/>";
        }
        $tassel = '';
        for ($i = -4; $i <= 4; $i++) {
            $tassel .= "<path d='M".($i * 3)." 152q".($i * 2)." 24 ".($i * 3.4)." 46' fill='none' stroke='".($i % 2 ? $p['red'] : '#e45a63')."' stroke-width='3'/>";
        }

        return <<<SVG
<g stroke-linecap="round" stroke-linejoin="round" transform="translate(100,0)">
  <circle cx="0" cy="12" r="9" fill="none" stroke="{$p['yellow']}" stroke-width="5"/>
  <path d="M0 21v28" stroke="{$p['yellow']}" stroke-width="5"/>
  <g transform="translate(0,84)">{$loops}<circle r="12" fill="{$p['yellow']}" stroke="{$p['ink']}" stroke-width="2.4"/></g>
  <path d="M-8 120q8 14 16 0l4 24h-24z" fill="{$p['red']}" stroke="{$p['ink']}" stroke-width="2.4"/>
  {$tassel}
</g>
SVG;
    }

    public static function hanriver(array $p): string
    {
        $arches = '';
        for ($i = 0; $i < 6; $i++) {
            $x = 8 + $i * 31;
            $arches .= "<path d='M{$x} 120q15.5 -26 31 0' fill='none' stroke='{$p['ink']}' stroke-width='3'/><path d='M".($x + 15.5)." 98v22' stroke='{$p['ink']}' stroke-width='1.6'/>";
        }
        $water = '';
        for ($i = 0; $i < 6; $i++) {
            $y = 132 + $i * 11;
            $w = 150 - $i * 18;
            $water .= "<rect x='".(100 - $w / 2)."' y='{$y}' width='{$w}' height='4' rx='2' fill='".($i % 2 ? '#ffd27a' : '#ff9a5a')."' opacity='".(0.95 - $i * .1)."'/>";
        }

        return <<<SVG
<g stroke-linecap="round">
  <rect x="0" y="0" width="200" height="200" rx="8" fill="#ff8f6b"/>
  <rect x="0" y="0" width="200" height="86" fill="#f26b7a" opacity=".55"/><rect x="0" y="0" width="200" height="40" fill="#8a4a9e" opacity=".5"/>
  <circle cx="100" cy="116" r="38" fill="#ffe28a"/><rect x="0" y="116" width="200" height="84" fill="#26335f"/>
  <rect x="0" y="116" width="200" height="6" fill="#ffb07a" opacity=".6"/>
  {$water}
  <path d="M0 98H200" stroke="{$p['ink']}" stroke-width="4"/>{$arches}
  <path d="M0 124H200" stroke="{$p['ink']}" stroke-width="3"/>
  <g fill="#ffffff" opacity=".9"><circle cx="30" cy="26" r="1.4"/><circle cx="78" cy="14" r="1.2"/><circle cx="160" cy="22" r="1.4"/><circle cx="188" cy="48" r="1.2"/></g>
</g>
SVG;
    }

    public static function arcade(array $p): string
    {
        $px = '';
        $heart = ['0110110', '1111111', '1111111', '0111110', '0011100', '0001000'];
        foreach ($heart as $r => $row) {
            foreach (str_split($row) as $c => $v) {
                if ($v === '1') {
                    $px .= "<rect x='".(58 + $c * 12)."' y='".(96 + $r * 12)."' width='12' height='12' fill='".($r < 2 && $c < 3 ? '#ff8aa8' : '#ff3d6e')."'/>";
                }
            }
        }

        return <<<SVG
<g font-family="'Noto Sans KR','Malgun Gothic',monospace" text-anchor="middle" shape-rendering="crispEdges">
  <rect x="10" y="14" width="180" height="172" fill="#1a1440"/>
  <rect x="10" y="14" width="180" height="172" fill="none" stroke="#4fe7ff" stroke-width="5"/>
  <rect x="18" y="22" width="164" height="156" fill="none" stroke="#ff5fa8" stroke-width="2" stroke-dasharray="6 4"/>
  <text x="103" y="73" font-size="44" font-weight="900" fill="#ff5fa8">게임</text>
  <text x="100" y="70" font-size="44" font-weight="900" fill="#ffe55c">게임</text>
  {$px}
  <text x="100" y="176" font-size="11" font-weight="900" fill="#4fe7ff" letter-spacing="3">PRESS START</text>
  <g fill="#ffe55c"><rect x="26" y="34" width="6" height="6"/><rect x="168" y="34" width="6" height="6"/><rect x="26" y="150" width="6" height="6"/><rect x="168" y="150" width="6" height="6"/></g>
</g>
SVG;
    }

    public static function mugunghwa(array $p): string
    {
        $petals = '';
        foreach ([0, 72, 144, 216, 288] as $a) {
            $petals .= "<path d='M0 0C-30 -20 -34 -62 0 -78 34 -62 30 -20 0 0z' fill='#d86fb5' stroke='{$p['ink']}' stroke-width='2.5' transform='rotate({$a})'/><path d='M0 -6C-10 -22 -10 -40 0 -52' fill='none' stroke='#a8337f' stroke-width='2' transform='rotate({$a})' opacity='.7'/>";
        }

        return <<<SVG
<g transform="translate(100,100)" stroke-linecap="round" stroke-linejoin="round">
  {$petals}
  <circle r="18" fill="#a8337f" opacity=".85"/><circle r="9" fill="{$p['yellow']}"/>
  <path d="M0 0L6 -50" stroke="{$p['yellow']}" stroke-width="3"/><circle cx="6" cy="-52" r="4" fill="{$p['red']}"/>
</g>
SVG;
    }

    public static function nightpattern(array $p): string
    {
        return self::hangul($p, '대박', 'DAEBAK', $p['orange']);
    }
}
