<?php

namespace App\Interfaces;

interface AuthInterface
{
    public function register(array $data);
    public function login(array $login);

    public function logout();
}
