<?php
namespace App;

class GlobalVariable
{
    public function getRole() {
       return [
        'admin' => 'ADMIN',
        'seller' => 'SELLER',
        'user' => 'USER',
        ];
    }
}
