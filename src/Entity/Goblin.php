<?php

namespace App\Entity;


class Goblin extends Warrior
{
    public function normalAttack_action(Warrior $warrior)
    {
        $warrior->getDamage(5);
    }

    public function massiveAttack_action(Warrior $warrior)
    {
        $warrior->getDamage(10);
        $this->getDamage(3);
    }
}
