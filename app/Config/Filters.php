<?php

namespace Config;

use App\Filters\AuthFilter;
use App\Filters\EducatorFilter;
use App\Filters\UserFilter;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;

class Filters extends BaseConfig {
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        "csrf"          => CSRF::class,
        "auth"          => AuthFilter::class
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        "before" => [],
        "after" => []
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * "post" => ["foo", "bar"]
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don’t expect could bypass the filter.
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * "isLoggedIn" => ["before" => ["account/*", "profiles/*"]]
     */
    public array $filters = [
        "auth" => [
            "before" => [
                "api/v1/*"
            ]
        ]
    ];
}
