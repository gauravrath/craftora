<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminSidebarTest extends TestCase
{
    public function test_master_menu_is_closed_on_other_pages_and_adminlte_script_is_loaded(): void
    {
        Route::get('/dashboard', fn () => view('admin.layouts.app'));

        $response = $this->get('/dashboard')->assertOk();
        $xpath = $this->xpath($response->getContent());

        $this->assertSame(0, $xpath->query('//*[@id="navigation"]/li[contains(concat(" ", normalize-space(@class), " "), " menu-open ")]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="navigation"]/li/a[@href="#" and @aria-expanded="false"]')->length);
        $this->assertSame(1, $xpath->query('//script[contains(@src, "/dist/js/adminlte.js")]')->length);
    }

    public function test_master_menu_is_open_on_child_and_nested_child_pages(): void
    {
        foreach (['category/create' => 'category', 'product/123/edit' => 'product', 'inventory' => 'inventory'] as $path => $activeItem) {
            $this->app->instance('request', Request::create('/'.$path));
            $xpath = $this->xpath(view('admin.layouts.app')->render());

            $this->assertSame(1, $xpath->query('//*[@id="navigation"]/li[contains(concat(" ", normalize-space(@class), " "), " menu-open ")]/a[@aria-expanded="true"]')->length);
            $this->assertSame(1, $xpath->query('//*[@id="navigation"]//ul[contains(@class, "nav-treeview")]//a[contains(@class, "active") and substring(@href, string-length(@href) - string-length("/'.$activeItem.'") + 1) = "/'.$activeItem.'"]')->length);
        }
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

        return new DOMXPath($document);
    }
}
