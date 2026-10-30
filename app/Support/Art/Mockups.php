<?php

namespace App\Support\Art;

/**
 * Maquettes produit vectorielles (toile 800×800) : t-shirt, sweat, mug, gourde, casquette, coussin,
 * affiche, toile, tote bag, coque, stickers, carnet, tapis de bureau, porte-clés.
 * Le motif (SVG 200×200) est posé dans la zone d'impression de chaque produit.
 */
class Mockups
{
    public static function shade(string $hex, float $amt): string
    {
        $hex = ltrim($hex, '#');
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        $f = fn ($c) => max(0, min(255, (int) round($amt >= 0 ? $c + (255 - $c) * $amt : $c * (1 + $amt))));

        return sprintf('#%02x%02x%02x', $f($r), $f($g), $f($b));
    }

    public static function isDark(string $hex): bool
    {
        $hex = ltrim($hex, '#');

        return (0.299 * hexdec(substr($hex, 0, 2)) + 0.587 * hexdec(substr($hex, 2, 2)) + 0.114 * hexdec(substr($hex, 4, 2))) < 110;
    }

    public static function defs(string $id): string
    {
        return <<<SVG
<defs>
    <filter id="{$id}-blur" x="-20%" y="-200%" width="140%" height="500%"><feGaussianBlur stdDeviation="12"/></filter>
    <linearGradient id="{$id}-sh" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="#000" stop-opacity=".22"/><stop offset=".28" stop-color="#fff" stop-opacity=".12"/><stop offset=".62" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".26"/></linearGradient>
    <linearGradient id="{$id}-sv" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".16"/><stop offset=".5" stop-color="#fff" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".16"/></linearGradient>
  </defs>
SVG;
    }

    /** Fond studio + ombre au sol puis le produit. */
    public static function scene(string $inner, string $bg = '#f1e9df', string $id = 'm', bool $floor = true): string
    {
        $b2 = self::shade($bg, -.08);
        $shadow = $floor ? '<ellipse cx="400" cy="722" rx="250" ry="22" fill="#000" opacity=".16" filter="url(#'.$id.'-blur)"/>' : '';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" role="img">
  <defs>
    <radialGradient id="{$id}-bg" cx="50%" cy="38%" r="75%"><stop offset="0" stop-color="{$bg}"/><stop offset="1" stop-color="{$b2}"/></radialGradient>
    <filter id="{$id}-blur" x="-20%" y="-200%" width="140%" height="500%"><feGaussianBlur stdDeviation="12"/></filter>
    <linearGradient id="{$id}-sh" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="#000" stop-opacity=".22"/><stop offset=".28" stop-color="#fff" stop-opacity=".12"/><stop offset=".62" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".26"/></linearGradient>
    <linearGradient id="{$id}-sv" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".16"/><stop offset=".5" stop-color="#fff" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".16"/></linearGradient>
  </defs>
  <rect width="800" height="800" fill="url(#{$id}-bg)"/>
  <circle cx="650" cy="150" r="170" fill="#fff" opacity=".22"/><circle cx="120" cy="690" r="120" fill="#fff" opacity=".16"/>
  {$shadow}
  {$inner}
</svg>
SVG;
    }

    public static function motif(string $svg, float $x, float $y, float $scale, ?float $rot = null): string
    {
        $r = $rot ? " rotate({$rot} 100 100)" : '';

        return "<g transform='translate({$x},{$y}) scale({$scale}){$r}'>{$svg}</g>";
    }

    // ---- Vêtements --------------------------------------------------------------------------

    public static function tee(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.18);
        $path = 'M268 150L345 118Q400 186 455 118L532 150L648 252L580 334L536 296V646Q400 668 264 646V296L220 334L152 252Z';

        return <<<SVG
<g>
  <path d="{$path}" fill="{$c}"/>
  <path d="{$path}" fill="url(#{$id}-sh)"/><path d="{$path}" fill="url(#{$id}-sv)"/>
  <path d="M345 118Q400 186 455 118Q400 160 345 118z" fill="{$dark}"/>
  <path d="M350 126Q400 182 450 126" fill="none" stroke="{$dark}" stroke-width="9" stroke-linecap="round"/>
  <path d="M268 150L220 334M532 150L580 334" stroke="{$dark}" stroke-width="2" opacity=".55" fill="none"/>
  <path d="M264 628Q400 650 536 628" fill="none" stroke="{$dark}" stroke-width="2" opacity=".6"/>
  <path d="M300 450c20 40 10 120 4 170M510 410c-18 50-6 150 4 214" stroke="#000" stroke-opacity=".06" stroke-width="14" fill="none"/>
  {$motif}
</g>
SVG;
    }

    public static function hoodie(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.22);
        $body = 'M300 190C330 176 352 168 366 168H434C448 168 470 176 500 190L568 222L648 524L588 540L548 372V654Q400 682 252 654V372L212 540L152 524L232 222Z';

        return <<<SVG
<g>
  <path d="M322 170C316 78 484 78 478 170L478 210L322 210Z" fill="{$dark}"/>
  <path d="{$body}" fill="{$c}"/>
  <path d="{$body}" fill="url(#{$id}-sh)"/><path d="{$body}" fill="url(#{$id}-sv)"/>
  <path d="M336 168C344 232 456 232 464 168C446 150 354 150 336 168z" fill="{$dark}"/>
  <path d="M318 176C338 262 462 262 482 176" fill="none" stroke="{$dark}" stroke-width="10" stroke-linecap="round"/>
  <path d="M372 230L368 316M428 230L432 316" stroke="#f1ebe0" stroke-width="5" stroke-linecap="round"/>
  <circle cx="368" cy="320" r="5" fill="#f1ebe0"/><circle cx="432" cy="320" r="5" fill="#f1ebe0"/>
  <path d="M290 534H510L540 640Q400 664 260 640Z" fill="{$c}" stroke="{$dark}" stroke-width="3"/><path d="M290 534H510L540 640Q400 664 260 640Z" fill="url(#{$id}-sv)"/>
  <path d="M252 636Q400 664 548 636V656Q400 684 252 656Z" fill="{$dark}" opacity=".5"/>
  <path d="M152 524L212 540M588 540L648 524" stroke="{$dark}" stroke-width="12"/>
  <path d="M232 222L212 540M568 222L588 540" stroke="{$dark}" stroke-width="2" opacity=".5" fill="none"/>
  {$motif}
</g>
SVG;
    }

    public static function cap(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.2);

        return <<<SVG
<g>
  <path d="M222 448C214 214 586 214 578 448z" fill="{$c}"/><path d="M222 448C214 214 586 214 578 448z" fill="url(#{$id}-sh)"/>
  <path d="M400 232V448M330 252C310 310 304 380 308 448M470 252C490 310 496 380 492 448" stroke="{$dark}" stroke-width="2" fill="none" opacity=".6"/>
  <circle cx="400" cy="232" r="11" fill="{$dark}"/>
  <path d="M200 452Q400 410 600 452Q580 556 400 574Q220 556 200 452Z" fill="{$dark}"/>
  <path d="M200 452Q400 410 600 452" fill="none" stroke="#fff" stroke-opacity=".25" stroke-width="3"/>
  <path d="M216 466Q400 430 584 466" fill="none" stroke="#000" stroke-opacity=".18" stroke-width="2" stroke-dasharray="4 5"/>
  {$motif}
</g>
SVG;
    }

    // ---- Maison / objets ---------------------------------------------------------------------

    public static function mug(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.2);

        return <<<SVG
<g>
  <path d="M520 330C640 316 650 484 520 500" fill="none" stroke="{$dark}" stroke-width="40" stroke-linecap="round"/>
  <path d="M520 330C640 316 650 484 520 500" fill="none" stroke="{$c}" stroke-width="28" stroke-linecap="round"/>
  <path d="M262 240H540V540Q540 604 400 604Q262 604 262 540Z" fill="{$c}"/>
  <path d="M262 240H540V540Q540 604 400 604Q262 604 262 540Z" fill="url(#{$id}-sh)"/>
  <ellipse cx="401" cy="240" rx="139" ry="28" fill="{$dark}"/><ellipse cx="401" cy="244" rx="124" ry="21" fill="#3a2418"/><ellipse cx="401" cy="241" rx="124" ry="19" fill="#5a3a28"/>
  <path d="M300 270V540" stroke="#fff" stroke-opacity=".35" stroke-width="10" stroke-linecap="round"/>
  {$motif}
</g>
SVG;
    }

    public static function tumbler(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.2);

        return <<<SVG
<g>
  <path d="M476 120L500 56" stroke="#2a2220" stroke-width="14" stroke-linecap="round"/>
  <path d="M296 196H504L482 660Q480 690 450 690H350Q320 690 318 660Z" fill="{$c}"/><path d="M296 196H504L482 660Q480 690 450 690H350Q320 690 318 660Z" fill="url(#{$id}-sh)"/>
  <rect x="284" y="150" width="232" height="58" rx="22" fill="#2a2220"/><rect x="296" y="160" width="208" height="10" rx="5" fill="#fff" opacity=".18"/>
  <path d="M340 230L352 650" stroke="#fff" stroke-opacity=".35" stroke-width="10" stroke-linecap="round"/>
  <rect x="330" y="684" width="140" height="14" rx="7" fill="{$dark}"/>
  {$motif}
</g>
SVG;
    }

    public static function cushion(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.2);
        $p = 'M170 200Q400 140 630 200Q668 400 630 600Q400 660 170 600Q132 400 170 200Z';

        return <<<SVG
<g>
  <path d="{$p}" fill="{$c}"/><path d="{$p}" fill="url(#{$id}-sh)"/><path d="{$p}" fill="url(#{$id}-sv)"/>
  <path d="{$p}" fill="none" stroke="{$dark}" stroke-width="7" stroke-linejoin="round"/>
  <path d="M184 232Q400 176 616 232" fill="none" stroke="#fff" stroke-opacity=".25" stroke-width="5"/>
  <g fill="{$dark}" opacity=".5"><circle cx="400" cy="402" r="6"/></g>
  {$motif}
</g>
SVG;
    }

    public static function poster(string $c, string $motif, string $id): string
    {
        // $c = couleur du cadre
        return <<<SVG
<g>
  <rect x="190" y="110" width="420" height="580" fill="#000" opacity=".12" transform="translate(14,18)" filter="url(#{$id}-blur)"/>
  <rect x="190" y="110" width="420" height="580" fill="{$c}"/>
  <rect x="190" y="110" width="420" height="580" fill="url(#{$id}-sh)"/>
  <rect x="214" y="134" width="372" height="532" fill="#fbf8f2"/>
  <rect x="214" y="134" width="372" height="532" fill="none" stroke="#000" stroke-opacity=".12" stroke-width="2"/>
  <svg x="236" y="156" width="328" height="388" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice"><g>{$motif}</g></svg>
  <rect x="300" y="580" width="200" height="6" rx="3" fill="#2a1d18" opacity=".8"/><rect x="340" y="602" width="120" height="4" rx="2" fill="#2a1d18" opacity=".35"/>
</g>
SVG;
    }

    public static function canvas(string $c, string $motif, string $id): string
    {
        return <<<SVG
<g>
  <rect x="120" y="190" width="560" height="420" fill="#000" opacity=".12" transform="translate(14,18)" filter="url(#{$id}-blur)"/>
  <rect x="120" y="190" width="560" height="420" fill="{$c}"/><rect x="120" y="190" width="560" height="420" fill="url(#{$id}-sh)"/>
  <svg x="138" y="208" width="524" height="384" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice"><g>{$motif}</g></svg>
  <path d="M138 208H662V592H138Z" fill="none" stroke="#000" stroke-opacity=".2" stroke-width="2"/>
</g>
SVG;
    }

    public static function tote(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.2);

        return <<<SVG
<g>
  <path d="M310 250C300 80 500 80 490 250" fill="none" stroke="{$dark}" stroke-width="22" stroke-linecap="round"/>
  <path d="M310 250C300 80 500 80 490 250" fill="none" stroke="{$c}" stroke-width="14" stroke-linecap="round"/>
  <path d="M200 240H600L626 676H174Z" fill="{$c}"/><path d="M200 240H600L626 676H174Z" fill="url(#{$id}-sh)"/><path d="M200 240H600L626 676H174Z" fill="url(#{$id}-sv)"/>
  <path d="M200 240H600" stroke="{$dark}" stroke-width="6"/><path d="M184 640H616" stroke="{$dark}" stroke-width="2" stroke-dasharray="6 6" opacity=".7"/>
  <path d="M230 256V630M570 256V630" stroke="{$dark}" stroke-width="2" stroke-dasharray="5 6" opacity=".5"/>
  {$motif}
</g>
SVG;
    }

    public static function phonecase(string $c, string $motif, string $id): string
    {
        return <<<SVG
<g>
  <defs><clipPath id="{$id}-cp"><rect x="250" y="70" width="300" height="640" rx="48"/></clipPath></defs>
  <rect x="250" y="70" width="300" height="640" rx="48" fill="#000" opacity=".15" transform="translate(12,16)" filter="url(#{$id}-blur)"/>
  <rect x="250" y="70" width="300" height="640" rx="48" fill="{$c}"/>
  <g clip-path="url(#{$id}-cp)">
    <svg x="250" y="70" width="300" height="640" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice"><g>{$motif}</g></svg>
    <path d="M250 70h140L250 390z" fill="#fff" opacity=".12"/>
  </g>
  <rect x="250" y="70" width="300" height="640" rx="48" fill="none" stroke="#111" stroke-width="10"/>
  <rect x="276" y="96" width="128" height="128" rx="34" fill="#16161a"/>
  <circle cx="318" cy="140" r="26" fill="#2c2c34" stroke="#555" stroke-width="3"/><circle cx="318" cy="140" r="12" fill="#0a0a10"/>
  <circle cx="370" cy="180" r="26" fill="#2c2c34" stroke="#555" stroke-width="3"/><circle cx="370" cy="180" r="12" fill="#0a0a10"/>
  <circle cx="370" cy="118" r="8" fill="#f2c94c" opacity=".85"/>
</g>
SVG;
    }

    public static function notebook(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.25);
        $rings = '';
        for ($i = 0; $i < 9; $i++) {
            $y = 170 + $i * 52;
            $rings .= "<rect x='214' y='{$y}' width='46' height='12' rx='6' fill='#cfcfd2' stroke='#8d8d92' stroke-width='1.5'/>";
        }

        return <<<SVG
<g transform="rotate(-4 400 400)">
  <rect x="222" y="108" width="400" height="590" rx="16" fill="#000" opacity=".15" transform="translate(10,14)" filter="url(#{$id}-blur)"/>
  <rect x="236" y="104" width="388" height="592" rx="14" fill="{$dark}"/>
  <rect x="226" y="96" width="388" height="592" rx="14" fill="{$c}"/><rect x="226" y="96" width="388" height="592" rx="14" fill="url(#{$id}-sh)"/>
  <rect x="292" y="96" width="6" height="592" fill="#000" opacity=".12"/>
  {$rings}
  <g transform="translate(330,170)">{$motif}</g>
  <rect x="332" y="560" width="200" height="6" rx="3" fill="#fff" opacity=".7"/><rect x="372" y="580" width="120" height="4" rx="2" fill="#fff" opacity=".4"/>
</g>
SVG;
    }

    public static function deskmat(string $c, string $motif, string $id): string
    {
        $dark = self::shade($c, -.2);

        return <<<SVG
<g>
  <rect x="70" y="210" width="680" height="380" rx="34" fill="#000" opacity=".14" transform="translate(12,18)" filter="url(#{$id}-blur)"/>
  <rect x="70" y="210" width="660" height="380" rx="34" fill="{$c}"/>
  <svg x="84" y="224" width="632" height="352" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice" overflow="hidden"><g>{$motif}</g></svg>
  <rect x="70" y="210" width="660" height="380" rx="34" fill="url(#{$id}-sv)"/>
  <rect x="78" y="218" width="644" height="364" rx="28" fill="none" stroke="#fff" stroke-opacity=".6" stroke-width="3" stroke-dasharray="9 7"/>
</g>
SVG;
    }

    public static function keychain(string $c, string $motif, string $id): string
    {
        return <<<SVG
<g>
  <circle cx="400" cy="106" r="40" fill="none" stroke="#b8b8bd" stroke-width="12"/><circle cx="400" cy="106" r="40" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="3"/>
  <path d="M400 146v46" stroke="#b8b8bd" stroke-width="10"/>
  <rect x="204" y="192" width="392" height="392" rx="86" fill="#000" opacity=".16" transform="translate(10,16)" filter="url(#{$id}-blur)"/>
  <rect x="204" y="192" width="392" height="392" rx="86" fill="#fff" fill-opacity=".92"/>
  <rect x="204" y="192" width="392" height="392" rx="86" fill="none" stroke="#dcdcdf" stroke-width="4"/>
  <circle cx="400" cy="220" r="12" fill="#c9c9cf"/>
  <g transform="translate(232,236) scale(1.7)">{$motif}</g>
  <path d="M230 260Q260 220 330 214" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round" opacity=".8"/>
</g>
SVG;
    }

    public static function stickers(string $c, array $items, string $id): string
    {
        $cells = '';
        $pos = [[150, 130], [450, 120], [160, 400], [440, 400], [150, 640 - 70], [450, 630 - 70]];
        // trois colonnes x deux rangées plus compactes
        $pos = [[96, 120], [296, 150], [496, 120], [96, 380], [296, 410], [496, 380]];
        foreach ($items as $i => $it) {
            [$x, $y] = $pos[$i % 6];
            $rot = [-6, 5, -3, 4, -5, 3][$i % 6];
            $cells .= "<g transform='translate({$x},{$y}) rotate({$rot} 100 100)'><g transform='scale(2)'><path d='M50 4C80 4 98 24 98 52S78 100 50 100 2 80 2 52 20 4 50 4z' fill='#fff' stroke='#e4e0da' stroke-width='1.5'/><g transform='translate(0,-2)'>{$it}</g></g></g>";
        }

        return <<<SVG
<g>
  <rect x="50" y="90" width="700" height="580" rx="22" fill="#000" opacity=".12" transform="translate(10,14)" filter="url(#{$id}-blur)"/>
  <rect x="50" y="90" width="700" height="580" rx="22" fill="#fffdf9" stroke="#e9e2d6" stroke-width="3"/>
  {$cells}
  <rect x="90" y="620" width="620" height="6" rx="3" fill="#d9d0c2"/>
</g>
SVG;
    }

    /** SVG complet pour un type de produit. */
    public static function build(string $type, string $color, string $motif, string $bg, string $id, array $stickerItems = []): string
    {
        $inner = match ($type) {
            'tee' => self::tee($color, self::motif($motif, 276, 200, 1.24), $id),
            'hoodie' => self::hoodie($color, self::motif($motif, 296, 236, 1.04), $id),
            'cap' => self::cap($color, self::motif($motif, 336, 270, 0.64), $id),
            'mug' => self::mug($color, self::motif($motif, 292, 258, 1.2), $id),
            'tumbler' => self::tumbler($color, self::motif($motif, 322, 258, 0.78), $id),
            'cushion' => self::cushion($color, self::motif($motif, 234, 236, 1.65), $id),
            'poster' => self::poster($color, $motif, $id),
            'canvas' => self::canvas($color, $motif, $id),
            'tote' => self::tote($color, self::motif($motif, 292, 306, 1.08), $id),
            'phone' => self::phonecase($color, $motif, $id),
            'notebook' => self::notebook($color, $motif, $id),
            'deskmat' => self::deskmat($color, $motif, $id),
            'keychain' => self::keychain($color, $motif, $id),
            'stickers' => self::stickers($color, $stickerItems, $id),
            default => self::tee($color, self::motif($motif, 300, 232, 1.0), $id),
        };
        $floor = ! in_array($type, ['poster', 'canvas', 'phone', 'stickers'], true);

        return self::scene($inner, $bg, $id, $floor);
    }
}
