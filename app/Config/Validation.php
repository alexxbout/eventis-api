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

    public $event_update_validation = [
        "id"          => "required|integer",
        "dateDebut"   => "permit_empty|valid_date[Y-m-d H: i: s]",
        "dateFin"     => "permit_empty|valid_date[Y-m-d H: i: s]",
        "title"       => "permit_empty|max_length[20]",
        "description" => "permit_empty|max_length[1000]",
        "pic"         => "permit_empty|max_length[50]"
    ];

    public $event_cancel_validation = [
        "id"     => "required|integer",
        "reason" => "required|max_length[50]"
    ];

    public $event_add_validation = [
        "zip"         => "required|max_length[5]",
        "dateDebut"   => "required|valid_date[Y-m-d H: i: s]",
        "dateFin"     => "required|valid_date[Y-m-d H: i: s]",
        "title"       => "required|max_length[20]",
        "description" => "required|max_length[1000]"
    ];

    public $event_addImage_validation = [
        "image"     => [
            "label" => "Image",
            "rules" => [
                "uploaded[image]",
                "is_image[image]",
                "ext_in[image,jpg,jpeg,png]"
            ]
        ]
    ];
}
