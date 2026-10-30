<?php

return [
    'required' => ':attribute 항목은 필수입니다.',
    'email' => ':attribute 형식이 올바르지 않습니다.',
    'accepted' => ':attribute에 동의해 주세요.',
    'max' => ['string' => ':attribute은(는) :max자를 넘을 수 없습니다.'],
    'min' => ['string' => ':attribute은(는) 최소 :min자 이상이어야 합니다.'],
    'confirmed' => ':attribute 확인이 일치하지 않습니다.',
    'unique' => '이미 사용 중인 :attribute입니다.',
    'in' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'url' => ':attribute 형식이 올바르지 않습니다.',
    'attributes' => [
        'name' => '이름', 'email' => '이메일', 'password' => '비밀번호', 'customer_name' => '이름', 'customer_email' => '이메일',
        'shipping_address' => '주소', 'shipping_city' => '도시', 'shipping_country' => '국가', 'message' => '내용', 'accept_terms' => '약관',
    ],
];
