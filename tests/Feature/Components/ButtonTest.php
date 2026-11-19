<?php

it('renders a link when an href is given', function () {
    $view = $this->blade('<x-button href="/contacto">Hablemos</x-button>');

    $view->assertSee('<a href="/contacto"', false)->assertDontSee('<button', false);
});

it('renders a button when no href is given', function () {
    $view = $this->blade('<x-button type="submit">Enviar</x-button>');

    $view->assertSee('<button type="submit"', false)->assertDontSee('<a ', false);
});
