<?php

if (! interface_exists(\Pest\Arch\Contracts\ArchExpectation::class)) {
    return;
}

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->each->not->toBeUsed();
