<?php

test('returns a successful response', function () {
    $response = $this->get(route('es.home'));

    $response->assertOk();
});
