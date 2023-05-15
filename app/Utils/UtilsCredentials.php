<?php

namespace App\Utils;

use App\Models\UserModel;

class UtilsCredentials {
    /**
     * It generates a valid random login for a user
     * 
     * @param UserModel userModel the user model
     * @param string lastname
     * @param string firstname
     * 
     * @return string the login
     */
    public static function getValidRandomLogin(string $lastname, string $firstname): string {
        $lastname = str_replace(" ", "", $lastname);
        $firstname = str_replace(" ", "", $firstname);

        $login = strtolower($firstname[0] . $lastname);

        $i = 0;

        $userModel = new UserModel();

        while ($userModel->getByLogin($login) != null) {
            $login = strtolower($firstname[0] . $lastname . $i);
            $i++;
        }

        return $login;
    }
}
