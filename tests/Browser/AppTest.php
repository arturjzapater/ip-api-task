<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AppTest extends DuskTestCase
{
    public function testAppLoads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('#button')
                ->assertVisible('#button')
                ->assertSeeIn('#button', 'Click here');
        });
    }

    public function testButtonClick()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('#button')
                ->click('#button')
                ->assertVisible('#button > #loading')
                ->waitUntilMissingText('Click here')
                ->assertMissing('#button > #loading');
        });
    }
}
