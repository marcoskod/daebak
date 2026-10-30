<?php

namespace App\Support\Art;

/** Génère les visuels du site (produits, catégories, bannières, avatars, logo) en SVG dans public/images. */
class ArtBuilder
{
    public const BG = ['#f1e9df', '#e8efe6', '#f6e6e0', '#e6ecf3', '#f3ecd9', '#ece4f0'];

    public function __construct(protected string $publicPath, protected array $catalog)
    {
    }

    protected function write(string $relative, string $svg): string
    {
        $path = $this->publicPath.'/'.$relative;
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $svg);

        return $relative;
    }

    protected function motifSvg(string $spec, bool $dark): string
    {
        $p = Motifs::palette($dark);
        [$name, $args] = array_pad(explode(':', $spec, 2), 2, null);
        if ($name === 'hangul' && $args) {
            [$text, $sub] = array_pad(explode('|', $args, 2), 2, '');

            return Motifs::hangul($p, $text, $sub);
        }

        return Motifs::render($name, $p);
    }

    /** @return array{image: string, images: array, colors: array} */
    public function product(array $spec, int $index): array
    {
        $bg = self::BG[$index % count(self::BG)];
        $slug = $spec['slug'];
        $type = $spec['type'];
        $colors = [];
        $images = [];
        $first = null;

        foreach ($spec['colors'] as $ci => $key) {
            [$hex] = $this->catalog['colors'][$key];
            $dark = Mockups::isDark($hex);
            // pour affiches/toiles/coques, le coloris est le cadre/fond : le motif garde la palette claire
            $motifDark = in_array($type, ['poster', 'canvas', 'phone', 'keychain', 'stickers'], true) ? false : $dark;
            $motif = $this->motifSvg($spec['motif'], $motifDark);
            $items = $type === 'stickers' ? Motifs::kimchiItems(Motifs::palette(false)) : [];
            $svg = Mockups::build($type, $hex, $motif, $bg, 'p'.$index.$ci, $items);
            $rel = $this->write("images/products/{$slug}-{$key}.svg", $svg);
            $colors[] = ['key' => $key, 'hex' => $hex, 'image' => $rel];
            $images[] = $rel;
            $first ??= $rel;
        }

        // visuel « design à plat » + gros plan
        $baseHex = $this->catalog['colors'][$spec['colors'][0]][0];
        $artMotif = $this->motifSvg($spec['motif'], false);
        $art = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800"><defs><radialGradient id="a'.$index.'" cx="50%" cy="40%" r="70%"><stop offset="0" stop-color="#fffdf9"/><stop offset="1" stop-color="'.Mockups::shade($bg, -.04).'"/></radialGradient></defs><rect width="800" height="800" fill="url(#a'.$index.')"/><circle cx="400" cy="400" r="300" fill="'.Mockups::shade($bg, -.1).'" opacity=".55"/><g transform="translate(100,100) scale(3)">'.$artMotif.'</g></svg>';
        $images[] = $this->write("images/products/{$slug}-art.svg", $art);

        $dark = Mockups::isDark($baseHex) && ! in_array($type, ['poster', 'canvas', 'phone', 'keychain', 'stickers'], true);
        $fill = in_array($type, ['poster', 'canvas', 'phone', 'keychain', 'stickers'], true) ? $bg : $baseHex;
        $detail = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800"><defs><pattern id="w'.$index.'" width="6" height="6" patternUnits="userSpaceOnUse"><path d="M0 0L6 6M6 0L0 6" stroke="#000" stroke-opacity=".05" stroke-width="1"/></pattern><radialGradient id="v'.$index.'" cx="50%" cy="50%" r="75%"><stop offset=".6" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".28"/></radialGradient></defs><rect width="800" height="800" fill="'.$fill.'"/><rect width="800" height="800" fill="url(#w'.$index.')"/><g transform="translate(60,60) scale(3.7)">'.$this->motifSvg($spec['motif'], $dark).'</g><rect width="800" height="800" fill="url(#v'.$index.')"/></svg>';
        $images[] = $this->write("images/products/{$slug}-detail.svg", $detail);

        return ['image' => $first, 'images' => $images, 'colors' => $colors];
    }

    public function category(array $cat): ?string
    {
        if (empty($cat['art'])) {
            return null;
        }
        [$type, $hex, $motif, $bg] = $cat['art'];
        $items = $type === 'stickers' ? Motifs::kimchiItems(Motifs::palette(false)) : [];
        $svg = Mockups::build($type, $hex, $this->motifSvg($motif, Mockups::isDark($hex)), $bg, 'c'.$cat['slug'], $items);

        return $this->write("images/categories/{$cat['slug']}.svg", $svg);
    }

    /** Collage hero de l'accueil : trois produits qui se chevauchent sur un blob. */
    public function homeCollage(): string
    {
        $defs = Mockups::defs('h');
        $tee = Mockups::tee('#f4efe9', Mockups::motif($this->motifSvg('tiger', false), 276, 200, 1.24), 'h');
        $hoodie = Mockups::hoodie('#254232', Mockups::motif($this->motifSvg('crane', true), 296, 236, 1.04), 'h');
        $mug = Mockups::mug('#f4efe9', Mockups::motif($this->motifSvg('plum', false), 292, 258, 1.2), 'h');
        $tote = Mockups::tote('#f3ead8', Mockups::motif($this->motifSvg('bojagi', false), 292, 306, 1.08), 'h');
        $sticker = Mockups::stickers('#fff', Motifs::kimchiItems(Motifs::palette(false)), 'h');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 640" role="img">
  {$defs}
  <path d="M120 320C100 160 260 40 430 56S800 130 800 320 700 590 450 600 140 480 120 320z" fill="#fed7ca" opacity=".75"/>
  <circle cx="760" cy="110" r="64" fill="#c8553d" opacity=".9"/><circle cx="130" cy="520" r="42" fill="#173723" opacity=".85"/>
  <g transform="translate(40,120) scale(.5) rotate(-6 400 400)">{$mug}</g>
  <g transform="translate(470,70) scale(.62) rotate(5 400 400)">{$hoodie}</g>
  <g transform="translate(190,60) scale(.66) rotate(-3 400 400)">{$tee}</g>
  <g transform="translate(560,300) scale(.38) rotate(8 400 400)">{$tote}</g>
  <g transform="translate(20,320) scale(.34) rotate(-8 400 400)">{$sticker}</g>
</svg>
SVG;

        return $this->write('images/brand/hero-collage.svg', $svg);
    }

    // ---- Bannières de pages (1600×300) ------------------------------------------------------

    public function hero(string $key, string $kind): string
    {
        $c1 = '#442a22';
        $c2 = '#c8553d';
        $bgA = '#f7e6dc';
        $bgB = '#efd9cc';
        $pat = '';
        switch ($kind) {
            case 'dancheong':
                for ($i = 0; $i < 12; $i++) {
                    $x = $i * 140;
                    $pat .= "<g transform='translate({$x},150)' opacity='.18'><circle r='46' fill='none' stroke='{$c1}' stroke-width='3'/><circle r='30' fill='{$c2}'/><circle r='14' fill='{$c1}'/>".implode('', array_map(fn ($a) => "<ellipse cx='0' cy='-62' rx='9' ry='18' fill='{$c1}' transform='rotate({$a})'/>", range(0, 315, 45))).'</g>';
                }
                break;
            case 'lattice':
                $pat = "<g stroke='{$c1}' stroke-width='3' opacity='.14'>";
                for ($i = 0; $i < 60; $i++) {
                    $pat .= "<path d='M".($i * 40)." 0V300'/>";
                }
                for ($j = 0; $j < 8; $j++) {
                    $pat .= "<path d='M0 ".($j * 40)."H1600'/>";
                }
                $pat .= '</g>';
                break;
            case 'waves':
                for ($r = 0; $r < 7; $r++) {
                    $y = 40 + $r * 40;
                    $pat .= "<path d='M0 {$y}".str_repeat('q25 -26 50 0t50 0 ', 16)."' fill='none' stroke='{$c1}' stroke-width='3' opacity='.16'/>";
                }
                break;
            case 'bojagi':
                $cols = ['#cd2e3a', '#f2b632', '#0047a0', '#2f7d5b', '#f08aa4', '#e8892b'];
                $x = 0;
                $i = 0;
                while ($x < 1600) {
                    $w = 70 + (($i * 37) % 90);
                    $y = 0;
                    $j = 0;
                    while ($y < 300) {
                        $h = 50 + (($i * 29 + $j * 41) % 70);
                        $pat .= "<rect x='{$x}' y='{$y}' width='{$w}' height='{$h}' fill='".$cols[($i * 3 + $j) % 6]."' opacity='.13' stroke='{$c1}' stroke-opacity='.12' stroke-width='2'/>";
                        $y += $h;
                        $j++;
                    }
                    $x += $w;
                    $i++;
                }
                break;
            case 'moon':
                $pat = "<circle cx='1320' cy='130' r='96' fill='{$c2}' opacity='.18'/>";
                for ($i = 0; $i < 9; $i++) {
                    $x = $i * 190 + 30;
                    $pat .= "<path d='M{$x} 240q30 -34 60 -10t60 -10 50 14' fill='none' stroke='{$c1}' stroke-width='4' opacity='.15' stroke-linecap='round'/><path d='M".($x + 40)." 90q24 -26 48 -8t48 -8' fill='none' stroke='{$c1}' stroke-width='3' opacity='.12' stroke-linecap='round'/>";
                }
                break;
            case 'plum':
                $pat = "<path d='M0 280C200 220 300 200 460 120S800 60 1000 40' fill='none' stroke='{$c1}' stroke-width='8' opacity='.18' stroke-linecap='round'/><path d='M1000 40C1200 20 1400 60 1600 20' fill='none' stroke='{$c1}' stroke-width='6' opacity='.14'/>";
                foreach ([[160, 240], [320, 190], [470, 120], [620, 110], [800, 70], [980, 44], [1180, 40], [1400, 50]] as [$x, $y]) {
                    $pat .= "<g transform='translate({$x},{$y})' opacity='.35'>".implode('', array_map(fn ($a) => "<circle cx='".round(cos(deg2rad($a)) * 11, 1)."' cy='".round(sin(deg2rad($a)) * 11, 1)."' r='9' fill='#f08aa4'/>", [-90, -18, 54, 126, 198]))."<circle r='4' fill='#f2b632'/></g>";
                }
                break;
            case 'peaks':
                $pat = "<circle cx='260' cy='90' r='40' fill='{$c2}' opacity='.22'/>";
                foreach ([[200, 300, 280], [520, 300, 340], [900, 300, 300], [1240, 300, 360], [1520, 300, 240]] as [$x, $y, $w]) {
                    $pat .= "<path d='M".($x - $w / 2)." 300L{$x} ".(300 - $w * .52)."L".($x + $w / 2)." 300z' fill='{$c1}' opacity='.12'/>";
                }
                break;
            default: // dots (taegeuk-like)
                for ($i = 0; $i < 18; $i++) {
                    for ($j = 0; $j < 4; $j++) {
                        $pat .= "<circle cx='".($i * 100 + ($j % 2) * 50 + 30)."' cy='".($j * 80 + 30)."' r='7' fill='{$c1}' opacity='.12'/>";
                    }
                }
        }

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 300' preserveAspectRatio='xMidYMid slice'><defs><linearGradient id='g' x1='0' x2='1'><stop offset='0' stop-color='{$bgA}'/><stop offset='1' stop-color='{$bgB}'/></linearGradient></defs><rect width='1600' height='300' fill='url(#g)'/>{$pat}</svg>";

        return $this->write("images/heroes/{$key}.svg", $svg);
    }

    /** Couverture d'article (1200×630) : motif sur fond pastel avec formes décoratives. */
    public function blogCover(string $slug, string $motifSpec, string $bg): string
    {
        $motif = $this->motifSvg($motifSpec, false);
        $dark = Mockups::shade($bg, -.1);
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" preserveAspectRatio="xMidYMid slice"><defs><radialGradient id="g" cx="68%" cy="45%" r="70%"><stop offset="0" stop-color="#fffdf9"/><stop offset="1" stop-color="'.$bg.'"/></radialGradient></defs><rect width="1200" height="630" fill="url(#g)"/><circle cx="820" cy="315" r="270" fill="'.$dark.'" opacity=".55"/><circle cx="160" cy="560" r="120" fill="#fed7ca" opacity=".6"/><circle cx="1090" cy="90" r="70" fill="#c8553d" opacity=".25"/><g transform="translate(590,25) scale(3)">'.$motif.'</g><g fill="none" stroke="#442a22" stroke-opacity=".12" stroke-width="2"><path d="M60 80h260M60 100h180M60 120h220"/></g></svg>';

        return $this->write("images/blog/{$slug}.svg", $svg);
    }

    public function creator(int $n, string $skin, string $hair, string $shirt, string $style): string
    {
        $hairShape = match ($style) {
            'bob' => "<path d='M70 108C62 56 138 56 130 108 128 130 134 140 140 150 120 150 80 150 60 150 66 140 72 130 70 108z' fill='{$hair}'/>",
            'long' => "<path d='M64 112C56 50 144 50 136 112 140 150 146 176 148 190H52C54 176 60 150 64 112z' fill='{$hair}'/>",
            'bun' => "<circle cx='100' cy='46' r='16' fill='{$hair}'/><path d='M70 100C66 58 134 58 130 100z' fill='{$hair}'/>",
            default => "<path d='M70 96C70 58 130 58 130 96 120 82 80 82 70 96z' fill='{$hair}'/>",
        };
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'><defs><clipPath id='c'><circle cx='100' cy='100' r='100'/></clipPath></defs><g clip-path='url(#c)'><rect width='200' height='200' fill='".self::BG[$n % 6]."'/><circle cx='150' cy='40' r='30' fill='#fed7ca' opacity='.6'/><path d='M30 210C34 160 70 146 100 146S166 160 170 210z' fill='{$shirt}'/><rect x='88' y='122' width='24' height='30' fill='{$skin}'/>{$hairShape}<ellipse cx='100' cy='104' rx='28' ry='32' fill='{$skin}'/>".($style === 'short' ? "<path d='M72 96C72 66 128 66 128 96 118 84 82 84 72 96z' fill='{$hair}'/>" : '')."<circle cx='89' cy='106' r='3' fill='#2a1d18'/><circle cx='111' cy='106' r='3' fill='#2a1d18'/><path d='M92 120q8 7 16 0' fill='none' stroke='#2a1d18' stroke-width='2.4' stroke-linecap='round'/><ellipse cx='82' cy='116' rx='5' ry='3' fill='#f08aa4' opacity='.5'/><ellipse cx='118' cy='116' rx='5' ry='3' fill='#f08aa4' opacity='.5'/></g></svg>";

        return $this->write("images/creators/creator-{$n}.svg", $svg);
    }

    public function brand(): void
    {
        $mark = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" role="img" aria-label="Daebak">
  <rect width="96" height="96" rx="26" fill="#442a22"/>
  <path d="M30 22h19c17 0 29 11 29 26S66 74 49 74H30z" fill="none" stroke="#faf9f7" stroke-width="9" stroke-linejoin="round"/>
  <path d="M30 22v52" stroke="#faf9f7" stroke-width="9" stroke-linecap="round"/>
  <circle cx="62" cy="48" r="7" fill="#c8553d"/>
  <path d="M71 18l2.2 5.8L79 26l-5.8 2.2L71 34l-2.2-5.8L63 26l5.8-2.2z" fill="#fed7ca"/>
</svg>
SVG;
        $this->write('images/brand/mark.svg', $mark);
        $this->write('images/brand/logo.svg', $mark);
        $this->write('images/placeholder.svg', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><rect width="400" height="400" fill="#f4f3f1"/><g transform="translate(152,152) scale(1.0)" opacity=".55"><rect width="96" height="96" rx="26" fill="#442a22"/><path d="M30 22h19c17 0 29 11 29 26S66 74 49 74H30z" fill="none" stroke="#faf9f7" stroke-width="9" stroke-linejoin="round"/><path d="M30 22v52" stroke="#faf9f7" stroke-width="9" stroke-linecap="round"/></g></svg>');
    }
}
