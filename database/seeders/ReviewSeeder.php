<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

/** Avis de démonstration (marqués is_sample=1 : à supprimer avant la mise en production). */
class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);
        $authors = [
            ['Jiwoo K.', 'KR', 'ko'], ['Emily R.', 'US', 'en'], ['Camille D.', 'FR', 'fr'], ['Minseo P.', 'KR', 'ko'], ['Jake T.', 'US', 'en'],
            ['Chloé M.', 'FR', 'fr'], ['Sophie W.', 'GB', 'en'], ['Hayoung L.', 'KR', 'ko'], ['Liam O.', 'CA', 'en'], ['Léa B.', 'FR', 'fr'], ['Olivia S.', 'AU', 'en'], ['Seojun C.', 'KR', 'ko'],
        ];
        $bodies = [
            'en' => [[5, 'Even better in person', 'The print is crisp and the quality feels premium. Arrived faster than expected and the packaging was lovely.'], [5, 'Got so many compliments', 'Wore it to a café in Brooklyn and three people asked where it was from. Colors are exactly like the photos.'], [4, 'Great design, runs slightly large', 'Really love the artwork. Size down if you are between sizes. Fabric is soft and print has survived several washes.'], [5, 'Perfect gift', 'Bought it for my sister who studies Korean. She cried a little. Shipping was tracked the whole way.'], [4, 'Nice quality for print on demand', 'I was skeptical about made-to-order, but the finish is clean. Would order again.']],
            'fr' => [[5, 'Encore mieux en vrai', 'L\'impression est nette et la qualité semble premium. Reçu plus vite que prévu, emballage soigné.'], [5, 'Beaucoup de compliments', 'Porté dans un café à Paris, on m\'a demandé où je l\'avais trouvé. Les couleurs sont fidèles aux photos.'], [4, 'Super design, taille un peu grand', 'J\'adore l\'illustration. Prenez la taille en dessous si vous hésitez. Le tissu est doux et l\'impression résiste aux lavages.'], [5, 'Cadeau parfait', 'Offert à ma sœur qui apprend le coréen. Elle était ravie. Suivi de colis du début à la fin.'], [4, 'Belle qualité pour de l\'impression à la demande', 'J\'étais sceptique sur le sur-mesure, mais la finition est propre. Je recommanderai.']],
            'ko' => [[5, '실물이 더 예뻐요', '프린트가 선명하고 퀄리티가 고급스러워요. 생각보다 빨리 도착했고 포장도 정성스러웠어요.'], [5, '칭찬을 많이 받았어요', '카페에서 입었더니 어디서 샀냐고 물어보는 분이 많았어요. 색감도 사진 그대로예요.'], [4, '디자인 최고, 사이즈는 살짝 큰 편', '일러스트가 정말 마음에 들어요. 고민되면 한 사이즈 작게 주문하세요. 원단도 부드럽고 세탁 후에도 프린트가 멀쩡해요.'], [5, '선물용으로 딱이에요', '한국어 공부하는 친구에게 선물했는데 정말 좋아했어요. 배송 추적도 잘 되었어요.'], [4, '주문 제작치고 만족스러워요', '주문 제작이라 걱정했는데 마감이 깔끔해요. 재구매 의사 있어요.']],
        ];

        foreach (Product::all() as $product) {
            ProductReview::where('product_id', $product->id)->delete();
            $count = 5 + mt_rand(0, 3);
            for ($i = 0; $i < $count; $i++) {
                [$name, $country, $loc] = $authors[mt_rand(0, count($authors) - 1)];
                [$rating, $title, $body] = $bodies[$loc][mt_rand(0, count($bodies[$loc]) - 1)];
                ProductReview::create([
                    'product_id' => $product->id, 'author' => $name, 'country' => $country, 'rating' => $rating,
                    'title' => $title, 'body' => $body, 'locale' => $loc, 'verified' => true, 'is_sample' => true,
                    'created_at' => now()->subDays(mt_rand(2, 160)),
                ]);
            }
        }
    }
}
