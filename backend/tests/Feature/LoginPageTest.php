<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_page_displays_brand_and_login_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('class="login-brand"', false)
            ->assertSee('dist/assets/img/logo.png')
            ->assertSee('class="login-content"', false)
            ->assertSee('action="'.route('login').'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false);
    }

    public function test_login_page_displays_validation_errors_and_old_email(): void
    {
        $this->from('/')->post('/login', ['email' => 'example@example.com'])
            ->assertRedirect('/');

        $this->get('/')
            ->assertOk()
            ->assertSee('example@example.com')
            ->assertSee('The password field is required.');
    }
}
