<?php

test('tamu yang membuka root diarahkan ke login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
