<?php

namespace App\Utils;

class Regex {
    const PASSWORD = "/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,100}$/";
}