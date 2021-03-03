<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;


abstract class Warrior
{
    
    public $name;
    
    public $health;
    
    public $plague;


    public function __construct(string $name, int $health)
    {
        $this->name = $name;
        $this->health = $health;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getPlague(): ?bool
    {
        return $this->plague;
    }

    public function setPlague(?bool $plague): self
    {
        $this->plague = $plague;

        return $this;
    }

    public function getDamage(int $degats)
    {
        $this->health -= $degats;
        return $this;
    }

    public function plague()
    {
        if ($this->getPlague()) {
            $this->getDamage(3);
            return $this->getName() . "est empoisonné et a subi 3 points de dégats\n";
        }
        return false;
    }

    public function maybeSuccumbs($playersAlive)
    {
        if ($this->getHealth() <= 0) {
            $offset = array_search($this, $playersAlive);
            array_splice($playersAlive, $offset, 1);
        }
        return $playersAlive;
    }

    public function getRandomMethod(): string
    {
        //----------- actions possibles de l'entité
        $action_methods = preg_grep('/_action/', get_class_methods($this));
        //---------------------- methode aléatoire
        return $action_methods[rand(0, count($action_methods) - 1)];
    }

    public function fight($randMethod, Warrior $victim)
    {
        if ($randMethod == 'heal_action') { // Witch self healing
            $this->$randMethod($this);
        } else {
            $this->$randMethod($victim);
        }
    }

    public function searchRandomTarget($playersAlive): Warrior
    {
        $targets = array_slice($playersAlive, 0); // copie du tableau
        $selfOffset =  array_search($this, $targets); // recherche le propre index du joueur
        //---------------------- tableau intermédiare pour retirer le jour de la liste des cibles
        array_splice($targets, $selfOffset, 1);
        if (count($targets) > 1) {
            $random_target = $targets[rand(0, count($targets) - 1)];
        } else {
            $random_target = $targets[0];
        }
        return $random_target;
    }
}
