<?php

namespace App\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;

class CspPolicy extends Basic
{
    public function configure()
    {
        parent::configure();

        $this
            ->addDirective(Directive::STYLE, [
                'self',
                'https://cdn.datatables.net',
                'https://cdn.jsdelivr.net',
                'https://cdnjs.cloudflare.com',
                'https://fonts.googleapis.com',
                'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'
            ])
            ->addDirective(Directive::SCRIPT, [
                'self',
                'https://cdn.jsdelivr.net',
                'https://cdnjs.cloudflare.com',
                'https://cdn.datatables.net',
                'https://code.jquery.com',
                'https://www.google.com',
                'https://www.gstatic.com',
                'unsafe-inline',
                'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'

            ])
            ->addDirective(Directive::CONNECT, [
                'self',
                'https://cdn.jsdelivr.net',
                'https://code.jquery.com',
                'https://cdn.datatables.net',
                'https://cdnjs.cloudflare.com',
                'https://www.google.com',
                'https://www.gstatic.com',
                'unsafe-inline',
                'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'


            ])
            ->addDirective(Directive::FONT, [
                'self',
                'https://fonts.gstatic.com',
            ]);
    }
}
