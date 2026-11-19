<?php

test('returns a successful response', function () {
    $response = $this->get(route('site.home', ['locale' => 'es']));

    $response->assertOk();
});
