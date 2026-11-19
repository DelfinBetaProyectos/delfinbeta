<?php

it('counts only completed years since the career start', function (string $today, string $expectedYears) {
    config(['site.career_started_at' => '2008-01-01']);
    $this->travelTo($today);

    $this->blade('<x-years-of-experience />')->assertSeeText($expectedYears);
})->with([
    'before the anniversary' => ['2026-12-31', '18'],
    'on the anniversary' => ['2027-01-01', '19'],
]);
