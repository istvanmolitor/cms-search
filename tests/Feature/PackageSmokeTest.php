<?php

namespace Molitor\CmsSearch\Tests\Feature;

use Molitor\CmsSearch\Providers\CmsSearchServiceProvider;
use Tests\TestCase;

class PackageSmokeTest extends TestCase
{
    public function test_service_provider_is_loaded(): void
    {
        $this->assertTrue(class_exists(CmsSearchServiceProvider::class));
        $this->assertTrue($this->app->providerIsLoaded(CmsSearchServiceProvider::class));
    }
}

