<?php

namespace App\Entity;

class Orc extends Warrior
{
    public function normalAttack_action(Warrior $warrior)
    {
        $warrior->getDamage(5);
    }
}
