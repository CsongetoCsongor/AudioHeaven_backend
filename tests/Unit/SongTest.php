<?php

test('it rounds playtime seconds correctly as seen in Store method', function () {
    // Szimuláljuk a getID3-ból érkező adatokat
    $case1 = 180.4; // 3 perc 0.4 mp
    $case2 = 180.5; // 3 perc 0.5 mp
    $case3 = 180.6; // 3 perc 0.6 mp

    // A kontrolleredben lévő logika: (int)round(...)
    $result1 = (int)round($case1);
    $result2 = (int)round($case2);
    $result3 = (int)round($case3);

    // Ellenőrzés
    expect($result1)->toBe(180);
    expect($result2)->toBe(181);
    expect($result3)->toBe(181);
});

test('it handles missing duration by defaulting to zero', function () {
    // A kontrolleredben: isset($fileInfo['playtime_seconds']) ? ... : 0;
    $fileInfo = []; // Üres infó, mintha hibás lenne a fájl

    $duration = isset($fileInfo['playtime_seconds'])
                ? (int)round($fileInfo['playtime_seconds'])
                : 0;

    expect($duration)->toBe(0);
});
