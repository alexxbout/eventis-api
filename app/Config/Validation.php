<?php

namespace Config;

use App\Utils\Regex;
use App\Utils\UtilsRegistrationCode;
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
        CreditCardRules::class
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
    public $addImage_validation = [
        "image"     => [
            "label" => "Image",
            "rules" => [
                "uploaded[image]",
                "is_image[image]",
                "ext_in[image,jpg,jpeg,png]"
            ]
        ]
    ];

    // --------------------------------------------------------------------
    // USER
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

    // --------------------------------------------------------------------
    // EVENT
    // --------------------------------------------------------------------
    public $event_update_validation = [
        "id"          => "required|integer",
        "dateDebut"   => "permit_empty|valid_date[Y-m-d H:i:s]",
        "dateFin"     => "permit_empty|valid_date[Y-m-d H:i:s]",
        "title"       => "permit_empty|max_length[20]",
        "description" => "permit_empty|max_length[1000]",
        "pic"         => "permit_empty|max_length[50]"
    ];

    public $event_cancel_validation = [
        "id"     => "required|integer",
        "reason" => "required|max_length[50]"
    ];

    public $event_uncancel_validation = [
        "id" => "required|integer"
    ];

    public $event_add_validation = [
        "zip"   => "required|max_length[5]",
        "title" => "required|max_length[20]",
        "start" => "required|valid_date[Y-m-d]",
        "idCategorie" => "required|integer"
    ];

    // --------------------------------------------------------------------
    // CODE
    // --------------------------------------------------------------------
    public $code_add_validation = [
        "idFoyer" => "permit_empty|integer",
        "idRole"  => "permit_empty|integer",
        "expire"  => "required|valid_date[Y-m-d H:i:s]"
    ];

    // --------------------------------------------------------------------
    // REGISTRATION
    // --------------------------------------------------------------------
    public $registration_add_validation = [
        "code"      => "required|exact_length[" . UtilsRegistrationCode::CODE_LENGTH . "]",
        "lastname"  => "required|max_length[30]",
        "firstname" => "required|max_length[30]",
        "password"  => "required|regex_match[" . Regex::PASSWORD . "]"
    ];

    // --------------------------------------------------------------------
    // FOYER
    // --------------------------------------------------------------------
    public $foyer_add_validation = [
        "siret"   => "required|exact_length[14]|alpha_numeric",
        "city"    => "required|max_length[10]",
        "zip"     => "required|max_length[5]",
        "address" => "required|max_length[50]"
    ];

    // --------------------------------------------------------------------
    // MESSAGE
    // --------------------------------------------------------------------
    public $conversation_add_validation = [
        "idSender"      => "required|integer",
        "idReceiver"    => "required|integer",
        "content"       => "required",
        "sentAt"        => "required|valid_date[Y-m-d H:i:s]"
    ];
}