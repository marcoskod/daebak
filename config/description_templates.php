<?php

/**
 * Templates d'affichage des descriptions produit (partagés Daebak / Be.Bright).
 * Chaque template = une vue resources/views/description-templates/{clé}.blade.php + le CSS public/css/desc-templates.css.
 * Le contenu reste le même (texte structuré avec "## Titre", "- puces", "Clé: valeur", "> citation") :
 * seul le style change. Choix par produit dans l'admin (champ « Template de description »).
 */
return [
    'templates' => [
        'classic' => ['icon' => 'align-left', 'label' => ['en' => 'Classic', 'fr' => 'Classique', 'ko' => '클래식'], 'hint' => ['en' => 'Clean single column with checklists.', 'fr' => 'Colonne unique épurée avec listes à coches.', 'ko' => '깔끔한 단일 컬럼과 체크리스트.']],
        'editorial' => ['icon' => 'newspaper', 'label' => ['en' => 'Editorial', 'fr' => 'Éditorial', 'ko' => '에디토리얼'], 'hint' => ['en' => 'Magazine layout, drop cap and pull quote.', 'fr' => 'Mise en page magazine, lettrine et citation.', 'ko' => '매거진 레이아웃, 드롭캡과 인용문.']],
        'cards' => ['icon' => 'grid', 'label' => ['en' => 'Feature cards', 'fr' => 'Cartes', 'ko' => '카드형'], 'hint' => ['en' => 'Each section becomes a responsive card.', 'fr' => 'Chaque section devient une carte responsive.', 'ko' => '각 섹션이 반응형 카드로 표시됩니다.']],
        'tabs' => ['icon' => 'tabs', 'label' => ['en' => 'Tabs', 'fr' => 'Onglets', 'ko' => '탭형'], 'hint' => ['en' => 'Compact tabs, ideal for long descriptions.', 'fr' => 'Onglets compacts pour les longues descriptions.', 'ko' => '긴 설명에 적합한 컴팩트 탭.']],
        'accordion' => ['icon' => 'list', 'label' => ['en' => 'Accordion', 'fr' => 'Accordéon', 'ko' => '아코디언'], 'hint' => ['en' => 'Collapsible sections, great on mobile.', 'fr' => 'Sections repliables, idéal sur mobile.', 'ko' => '접이식 섹션, 모바일에 최적.']],
        'spec-sheet' => ['icon' => 'ruler', 'label' => ['en' => 'Spec sheet', 'fr' => 'Fiche technique', 'ko' => '스펙 시트'], 'hint' => ['en' => 'Technical table beside a short summary.', 'fr' => 'Tableau technique à côté d\'un résumé.', 'ko' => '요약 옆에 기술 사양 표.']],
        'timeline' => ['icon' => 'steps', 'label' => ['en' => 'Timeline', 'fr' => 'Étapes', 'ko' => '타임라인'], 'hint' => ['en' => 'Numbered steps along a vertical line.', 'fr' => 'Étapes numérotées sur une ligne verticale.', 'ko' => '세로선 위 번호 단계.']],
        'showcase' => ['icon' => 'sparkles', 'label' => ['en' => 'Showcase', 'fr' => 'Vitrine', 'ko' => '쇼케이스'], 'hint' => ['en' => 'Bento tiles with a bold hero statement.', 'fr' => 'Tuiles bento avec grande accroche.', 'ko' => '큰 헤드라인과 벤토 타일.']],
    ],
];
