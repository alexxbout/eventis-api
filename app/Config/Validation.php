<?php

namespace Config;

use App\Utils\Regex;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig {
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public $user_add_validation = [
        "lastname"  => "required|max_length[30]",
        "firstname" => "required|max_length[30]",
        "password"  => "required|regex_match[" . Regex::PASSWORD . "]",
        "idFoyer"   => "required|integer",
        "idRole"    => "required|integer"
    ];

    public $user_update_password_validation = [
        "id"          => "required|integer",
        "oldPassword" => "required|regex_match[" . Regex::PASSWORD . "]",
        "newPassword" => "required|regex_match[" . Regex::PASSWORD . "]"
    ];

    public $user_update_data_validation = [
        "lastname"  => "permit_empty|max_length[30]",
        "firstname" => "permit_empty|max_length[30]"
    ];

    public $user_update_login_logout_validation = [
        "id" => "required|integer"
    ];

    public $event_cancel_validation = [
        "id" => "required|integer",
        "reason" => "permit_empty|max_length[500]"
    ];

    public $event_uncancel_validation = [
        "id" => "required|integer"
    ];

    public $event_update_validation = [
        "id" => "required|integer"
    ];

    public $event_add_validation = [
        
    ];
}
