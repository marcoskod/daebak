<?php

/**
 * Informations communes par type de produit (fiches techniques, entretien) + vocabulaire des sections.
 * Utilisé par CatalogSeeder pour assembler des descriptions complètes dans chaque langue.
 */
return [
    'headings' => [
        'en' => ['highlights' => 'Highlights', 'story' => 'The story', 'specs' => 'Specifications', 'care' => 'Care & use', 'shipping' => 'Production & shipping'],
        'fr' => ['highlights' => 'Points forts', 'story' => 'L\'histoire', 'specs' => 'Fiche technique', 'care' => 'Entretien & usage', 'shipping' => 'Production & livraison'],
        'ko' => ['highlights' => '하이라이트', 'story' => '디자인 이야기', 'specs' => '제품 사양', 'care' => '관리 & 사용법', 'shipping' => '제작 & 배송'],
    ],

    'shipping' => [
        'en' => 'Made on demand by **:provider** in the region closest to you and printed only after you order — no overstock, less waste. Production takes **:min–:max business days**, then shipping time depends on your country (see the estimator on this page).',
        'fr' => 'Fabriqué à la demande par **:provider** dans la région la plus proche de chez vous et imprimé seulement après votre commande : zéro surstock, moins de gaspillage. Production en **:min à :max jours ouvrés**, puis livraison selon votre pays (voir l\'estimation sur cette page).',
        'ko' => '**:provider**가 주문 후 가장 가까운 지역에서 주문 제작하여 재고 낭비를 줄입니다. 제작에는 **영업일 기준 :min~:max일**이 걸리며, 이후 배송 기간은 국가에 따라 다릅니다(이 페이지의 배송 예상 참고).',
    ],

    'types' => [
        'tee' => [
            'en' => ['specs' => ['Material' => '100% ring-spun combed cotton', 'Weight' => '180 gsm, mid-weight', 'Fit' => 'Unisex, relaxed', 'Print' => 'Direct-to-garment (DTG), water-based inks', 'Sizes' => 'XS – 3XL'], 'care' => ['Machine wash cold, inside out', 'Tumble dry low or hang dry', 'Do not iron directly on the print']],
            'fr' => ['specs' => ['Matière' => '100 % coton peigné filé à l\'anneau', 'Grammage' => '180 g/m², moyen', 'Coupe' => 'Unisexe, décontractée', 'Impression' => 'Directe sur textile (DTG), encres à l\'eau', 'Tailles' => 'XS – 3XL'], 'care' => ['Lavage en machine à froid, à l\'envers', 'Séchage doux ou à l\'air libre', 'Ne pas repasser sur l\'impression']],
            'ko' => ['specs' => ['소재' => '100% 링스펀 코마사 면', '중량' => '180gsm, 미디엄 웨이트', '핏' => '유니섹스, 릴랙스드', '프린트' => 'DTG(직접 인쇄), 수성 잉크', '사이즈' => 'XS – 3XL'], 'care' => ['찬물, 뒤집어서 세탁기 세탁', '저온 건조 또는 자연 건조', '프린트 부위에 직접 다림질 금지']],
        ],
        'hoodie' => [
            'en' => ['specs' => ['Material' => '80% cotton, 20% recycled polyester fleece', 'Weight' => '300 gsm, heavyweight', 'Fit' => 'Unisex, relaxed with drop shoulder', 'Details' => 'Lined hood, kangaroo pocket, flat drawcords', 'Sizes' => 'S – 3XL'], 'care' => ['Wash cold with similar colors, inside out', 'Tumble dry low', 'Do not bleach or dry-clean']],
            'fr' => ['specs' => ['Matière' => '80 % coton, 20 % polyester recyclé (molleton)', 'Grammage' => '300 g/m², épais', 'Coupe' => 'Unisexe, ample, épaules tombantes', 'Détails' => 'Capuche doublée, poche kangourou, cordons plats', 'Tailles' => 'S – 3XL'], 'care' => ['Lavage à froid avec couleurs similaires, à l\'envers', 'Séchage doux', 'Pas de javel ni de nettoyage à sec']],
            'ko' => ['specs' => ['소재' => '면 80%, 재생 폴리에스터 20% (기모)', '중량' => '300gsm, 헤비웨이트', '핏' => '유니섹스, 드롭숄더 오버핏', '디테일' => '안감 후드, 캥거루 포켓, 플랫 드로코드', '사이즈' => 'S – 3XL'], 'care' => ['비슷한 색상끼리 뒤집어서 찬물 세탁', '저온 건조', '표백제·드라이클리닝 금지']],
        ],
        'cap' => [
            'en' => ['specs' => ['Material' => '100% cotton twill, unstructured', 'Closure' => 'Adjustable brass buckle strap', 'Decoration' => '3D-look embroidery (up to 6 thread colors)', 'Fit' => 'One size, 54–62 cm'], 'care' => ['Spot clean with a damp cloth', 'Air dry, reshape while damp', 'Do not machine wash']],
            'fr' => ['specs' => ['Matière' => 'Sergé 100 % coton, non structuré', 'Fermeture' => 'Sangle réglable à boucle laiton', 'Décoration' => 'Broderie effet 3D (jusqu\'à 6 couleurs de fil)', 'Taille' => 'Unique, 54–62 cm'], 'care' => ['Nettoyage localisé avec un chiffon humide', 'Séchage à l\'air, remettre en forme humide', 'Ne pas laver en machine']],
            'ko' => ['specs' => ['소재' => '면 100% 트윌, 언스트럭처드', '조절' => '황동 버클 스트랩', '장식' => '3D 느낌 자수(최대 6색 실)', '사이즈' => '프리, 54–62cm'], 'care' => ['젖은 천으로 부분 세척', '자연 건조, 젖은 상태에서 모양 잡기', '세탁기 사용 금지']],
        ],
        'mug' => [
            'en' => ['specs' => ['Material' => 'High-grade white ceramic, glossy', 'Capacity' => '11 oz (325 ml)', 'Print' => 'Sublimation, wraps the full body', 'Safety' => 'Dishwasher and microwave safe', 'Handle' => 'C-shaped, comfortable grip'], 'care' => ['Dishwasher safe (top rack recommended)', 'Microwave safe', 'Avoid abrasive sponges on the print']],
            'fr' => ['specs' => ['Matière' => 'Céramique blanche haute qualité, brillante', 'Contenance' => '11 oz (325 ml)', 'Impression' => 'Sublimation, sur tout le pourtour', 'Sécurité' => 'Compatible lave-vaisselle et micro-ondes', 'Anse' => 'En C, prise confortable'], 'care' => ['Lave-vaisselle OK (panier du haut conseillé)', 'Compatible micro-ondes', 'Éviter les éponges abrasives sur l\'impression']],
            'ko' => ['specs' => ['소재' => '고급 화이트 세라믹, 유광', '용량' => '11oz (325ml)', '프린트' => '승화전사, 전면 래핑', '안전' => '식기세척기·전자레인지 사용 가능', '손잡이' => 'C자형, 편안한 그립'], 'care' => ['식기세척기 사용 가능(상단 권장)', '전자레인지 사용 가능', '프린트 부위에 거친 수세미 사용 금지']],
        ],
        'tumbler' => [
            'en' => ['specs' => ['Material' => '18/8 stainless steel, powder-coated', 'Capacity' => '20 oz (590 ml)', 'Insulation' => 'Double-wall vacuum: 12 h hot, 24 h cold', 'Lid' => 'BPA-free, slide closure + reusable straw', 'Print' => 'UV-resistant sublimation wrap'], 'care' => ['Hand wash recommended', 'Not microwave safe', 'Do not freeze with liquid inside']],
            'fr' => ['specs' => ['Matière' => 'Acier inoxydable 18/8, revêtement poudre', 'Contenance' => '20 oz (590 ml)', 'Isolation' => 'Double paroi sous vide : 12 h chaud, 24 h froid', 'Couvercle' => 'Sans BPA, fermeture coulissante + paille réutilisable', 'Impression' => 'Sublimation résistante aux UV'], 'care' => ['Lavage à la main recommandé', 'Non compatible micro-ondes', 'Ne pas congeler avec du liquide']],
            'ko' => ['specs' => ['소재' => '18/8 스테인리스, 파우더 코팅', '용량' => '20oz (590ml)', '보온·보냉' => '이중 진공: 온음료 12시간, 냉음료 24시간', '뚜껑' => 'BPA-free, 슬라이드 + 재사용 빨대', '프린트' => '자외선에 강한 승화전사'], 'care' => ['손세척 권장', '전자레인지 사용 불가', '액체를 넣은 채 냉동 금지']],
        ],
        'cushion' => [
            'en' => ['specs' => ['Cover' => 'Spun polyester, soft suede-like touch', 'Size' => '18 × 18 in (45 × 45 cm)', 'Insert' => 'Hypoallergenic filling included', 'Closure' => 'Concealed zipper', 'Print' => 'Dye-sublimation, vivid on both sides'], 'care' => ['Remove insert, machine wash cover cold', 'Tumble dry low', 'Do not bleach']],
            'fr' => ['specs' => ['Housse' => 'Polyester filé, toucher doux façon daim', 'Dimensions' => '45 × 45 cm', 'Garnissage' => 'Rembourrage hypoallergénique inclus', 'Fermeture' => 'Zip invisible', 'Impression' => 'Sublimation, éclatante recto verso'], 'care' => ['Retirer le coussin, housse en machine à froid', 'Séchage doux', 'Ne pas utiliser de javel']],
            'ko' => ['specs' => ['커버' => '방적 폴리에스터, 스웨이드 느낌', '크기' => '45 × 45cm', '속통' => '저자극 충전재 포함', '여밈' => '히든 지퍼', '프린트' => '승화전사, 양면 선명'], 'care' => ['속통 분리 후 커버 찬물 세탁', '저온 건조', '표백제 사용 금지']],
        ],
        'poster' => [
            'en' => ['specs' => ['Paper' => 'Museum-quality matte, 200 gsm, acid-free', 'Sizes' => '12×16 in, 18×24 in, 24×36 in', 'Inks' => 'Pigment-based, fade-resistant (75+ years)', 'Frame' => 'Not included — ready for standard frames', 'Shipping' => 'Rolled in a protective tube'], 'care' => ['Keep away from direct sunlight', 'Frame behind glass or acrylic', 'Dust with a dry soft cloth']],
            'fr' => ['specs' => ['Papier' => 'Mat qualité musée, 200 g/m², sans acide', 'Formats' => '30×40 cm, 45×60 cm, 60×90 cm', 'Encres' => 'Pigmentaires, résistantes à la décoloration (75 ans+)', 'Cadre' => 'Non inclus — format standard', 'Expédition' => 'Roulé dans un tube de protection'], 'care' => ['Éviter la lumière directe du soleil', 'Encadrer sous verre ou acrylique', 'Dépoussiérer avec un chiffon sec']],
            'ko' => ['specs' => ['용지' => '뮤지엄급 무광 200gsm, 무산성', '사이즈' => '30×40cm, 45×60cm, 60×90cm', '잉크' => '안료 잉크, 75년 이상 변색 저항', '액자' => '미포함 — 표준 규격', '배송' => '보호 튜브에 말아서 발송'], 'care' => ['직사광선을 피해 보관', '유리 또는 아크릴 액자 권장', '마른 부드러운 천으로 먼지 제거']],
        ],
        'canvas' => [
            'en' => ['specs' => ['Material' => 'Cotton-poly canvas, 400 gsm, satin finish', 'Frame' => 'FSC-certified pine, 1.5 in deep', 'Sizes' => '12×16 in, 18×24 in, 24×36 in', 'Inks' => 'Latex, UV-resistant', 'Hanging' => 'Pre-installed hardware'], 'care' => ['Dust gently with a dry cloth', 'Avoid humid rooms and direct sun', 'Do not use cleaning sprays']],
            'fr' => ['specs' => ['Matière' => 'Toile coton-polyester 400 g/m², finition satinée', 'Châssis' => 'Pin certifié FSC, 3,8 cm d\'épaisseur', 'Formats' => '30×40 cm, 45×60 cm, 60×90 cm', 'Encres' => 'Latex, résistantes aux UV', 'Accroche' => 'Système pré-installé'], 'care' => ['Dépoussiérer avec un chiffon sec', 'Éviter pièces humides et soleil direct', 'Ne pas utiliser de produits nettoyants']],
            'ko' => ['specs' => ['소재' => '면·폴리 캔버스 400gsm, 새틴 마감', '프레임' => 'FSC 인증 소나무, 두께 3.8cm', '사이즈' => '30×40cm, 45×60cm, 60×90cm', '잉크' => '라텍스, 자외선 저항', '걸이' => '설치 완료 상태'], 'care' => ['마른 천으로 가볍게 먼지 제거', '습한 공간·직사광선 피하기', '세정 스프레이 사용 금지']],
        ],
        'tote' => [
            'en' => ['specs' => ['Material' => '100% organic cotton canvas, 280 gsm', 'Size' => '15 × 16 in, 10 in handles', 'Capacity' => 'Up to 10 kg (22 lb)', 'Print' => 'DTG, front side', 'Inside' => 'Reinforced stitching, flat bottom'], 'care' => ['Machine wash cold', 'Air dry flat', 'Iron inside out at low heat']],
            'fr' => ['specs' => ['Matière' => 'Toile 100 % coton bio, 280 g/m²', 'Dimensions' => '38 × 41 cm, anses de 25 cm', 'Capacité' => 'Jusqu\'à 10 kg', 'Impression' => 'DTG, face avant', 'Finition' => 'Coutures renforcées, fond plat'], 'care' => ['Lavage en machine à froid', 'Séchage à plat', 'Repasser à l\'envers à basse température']],
            'ko' => ['specs' => ['소재' => '100% 유기농 면 캔버스, 280gsm', '크기' => '38 × 41cm, 손잡이 25cm', '적재량' => '최대 10kg', '프린트' => 'DTG, 앞면', '마감' => '보강 박음질, 바닥 평면'], 'care' => ['찬물 세탁기 세탁', '펼쳐서 자연 건조', '뒤집어서 저온 다림질']],
        ],
        'phone' => [
            'en' => ['specs' => ['Material' => 'Impact-resistant polycarbonate shell + TPU lining', 'Finish' => 'Glossy, scratch-resistant', 'Compatibility' => 'iPhone 13–16 and Samsung Galaxy S23–S25 series', 'Protection' => 'Raised edges for screen and camera', 'Wireless charging' => 'Supported'], 'care' => ['Wipe with a soft damp cloth', 'Avoid alcohol-based cleaners', 'Remove before extreme heat']],
            'fr' => ['specs' => ['Matière' => 'Coque polycarbonate antichoc + doublure TPU', 'Finition' => 'Brillante, résistante aux rayures', 'Compatibilité' => 'iPhone 13–16 et Samsung Galaxy S23–S25', 'Protection' => 'Rebords surélevés écran et appareil photo', 'Charge sans fil' => 'Compatible'], 'care' => ['Essuyer avec un chiffon doux humide', 'Éviter les nettoyants à base d\'alcool', 'Retirer avant une chaleur extrême']],
            'ko' => ['specs' => ['소재' => '충격 방지 폴리카보네이트 + TPU 안감', '마감' => '유광, 스크래치 방지', '호환' => 'iPhone 13–16, Galaxy S23–S25 시리즈', '보호' => '화면·카메라 보호 턱', '무선충전' => '지원'], 'care' => ['부드러운 젖은 천으로 닦기', '알코올 세정제 사용 금지', '고온에 노출 전 분리']],
        ],
        'stickers' => [
            'en' => ['specs' => ['Material' => 'Premium vinyl, waterproof and UV-resistant', 'Sheet' => '6 kiss-cut stickers on a 5.5 × 8.5 in sheet', 'Finish' => 'Matte or glossy (matte default)', 'Adhesive' => 'Strong, repositionable for 24 h', 'Outdoor life' => 'Up to 3 years'], 'care' => ['Apply on clean, dry surfaces', 'Press firmly for 30 seconds', 'Avoid pressure washers']],
            'fr' => ['specs' => ['Matière' => 'Vinyle premium, étanche et résistant aux UV', 'Planche' => '6 stickers prédécoupés sur 14 × 21,6 cm', 'Finition' => 'Mate ou brillante (mate par défaut)', 'Adhésif' => 'Fort, repositionnable 24 h', 'Durée en extérieur' => 'Jusqu\'à 3 ans'], 'care' => ['Appliquer sur surface propre et sèche', 'Presser fermement 30 secondes', 'Éviter les nettoyeurs haute pression']],
            'ko' => ['specs' => ['소재' => '프리미엄 비닐, 방수·자외선 차단', '시트' => '14 × 21.6cm 시트에 키스컷 스티커 6장', '마감' => '무광 또는 유광(기본 무광)', '접착' => '강력, 24시간 재부착 가능', '야외 수명' => '최대 3년'], 'care' => ['깨끗하고 마른 표면에 부착', '30초간 꾹 눌러주기', '고압 세척기 사용 금지']],
        ],
        'notebook' => [
            'en' => ['specs' => ['Cover' => 'Hardcover, soft-touch laminate, printed full-bleed', 'Pages' => '128 pages, 100 gsm, dotted or ruled', 'Binding' => 'Spiral wire-o, lays flat', 'Size' => 'A5 (5.8 × 8.3 in)', 'Extras' => 'Elastic closure and ribbon'], 'care' => ['Keep away from moisture', 'Use gel or ballpoint pens for best results', 'Do not bend the spiral']],
            'fr' => ['specs' => ['Couverture' => 'Rigide, pelliculage soft-touch, impression pleine page', 'Pages' => '128 pages, 100 g/m², pointillé ou ligné', 'Reliure' => 'Spirale métallique, s\'ouvre à plat', 'Format' => 'A5 (14,8 × 21 cm)', 'Bonus' => 'Élastique de fermeture et marque-page'], 'care' => ['Tenir à l\'abri de l\'humidité', 'Stylos gel ou bille recommandés', 'Ne pas plier la spirale']],
            'ko' => ['specs' => ['표지' => '하드커버, 소프트터치 코팅, 전면 인쇄', '페이지' => '128쪽, 100gsm, 도트 또는 줄노트', '제본' => '스파이럴, 완전히 펼쳐짐', '크기' => 'A5 (14.8 × 21cm)', '구성' => '고무밴드와 책갈피 리본'], 'care' => ['습기를 피해 보관', '젤·볼펜 사용 권장', '스파이럴을 구부리지 마세요']],
        ],
        'deskmat' => [
            'en' => ['specs' => ['Material' => 'Micro-textured fabric top, natural rubber base', 'Size' => '31.5 × 15.7 in (80 × 40 cm), 3 mm thick', 'Edges' => 'Anti-fray stitched border', 'Print' => 'Sublimation, edge to edge', 'Grip' => 'Non-slip base, mouse-friendly surface'], 'care' => ['Spot clean with mild soap', 'Air dry flat', 'Do not machine wash']],
            'fr' => ['specs' => ['Matière' => 'Tissu micro-texturé, base en caoutchouc naturel', 'Dimensions' => '80 × 40 cm, 3 mm d\'épaisseur', 'Bords' => 'Contour cousu anti-effilochage', 'Impression' => 'Sublimation, bord à bord', 'Adhérence' => 'Base antidérapante, surface adaptée à la souris'], 'care' => ['Nettoyage localisé au savon doux', 'Séchage à plat', 'Ne pas laver en machine']],
            'ko' => ['specs' => ['소재' => '마이크로 텍스처 원단, 천연 고무 바닥', '크기' => '80 × 40cm, 두께 3mm', '가장자리' => '올풀림 방지 스티치', '프린트' => '승화전사, 끝까지 풀 프린트', '그립' => '미끄럼 방지 바닥, 마우스 최적 표면'], 'care' => ['순한 비누로 부분 세척', '펼쳐서 자연 건조', '세탁기 사용 금지']],
        ],
        'keychain' => [
            'en' => ['specs' => ['Material' => '3 mm clear acrylic, double-sided print', 'Size' => '2.5 in (6.5 cm)', 'Hardware' => 'Nickel-plated ball chain and ring', 'Finish' => 'Glossy, rounded edges', 'Weight' => 'Approx. 12 g'], 'care' => ['Wipe with a soft dry cloth', 'Avoid solvents', 'Do not leave in a hot car']],
            'fr' => ['specs' => ['Matière' => 'Acrylique transparent 3 mm, impression recto verso', 'Taille' => '6,5 cm', 'Accessoires' => 'Chaînette boule et anneau nickelés', 'Finition' => 'Brillante, bords arrondis', 'Poids' => 'Env. 12 g'], 'care' => ['Essuyer avec un chiffon doux et sec', 'Éviter les solvants', 'Ne pas laisser dans une voiture chaude']],
            'ko' => ['specs' => ['소재' => '3mm 투명 아크릴, 양면 인쇄', '크기' => '6.5cm', '부자재' => '니켈 도금 볼체인과 링', '마감' => '유광, 둥근 모서리', '무게' => '약 12g'], 'care' => ['부드러운 마른 천으로 닦기', '용제 사용 금지', '뜨거운 차 안에 방치 금지']],
        ],
    ],
];
