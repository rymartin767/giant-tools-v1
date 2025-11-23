<?php

test('dashboard is accessible without authentication', function () {
    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});
