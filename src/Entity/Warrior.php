<?php

namespace App\Entity;

use App\Repository\WarriorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WarriorRepository::class)
 */
abstract class Warrior
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\Column(type="integer")
     */
    public $health;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
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
            return $this->getName() . ' est empoisonné et a subi 3 points de dégats <br>';
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
        // $targets = array_slice($playersAlive, 0); // copie du tableau
        $selfOffset =  array_search($this, $playersAlive); // recherche le propre index du joueur
        //---------------------- tableau intermédiare pour retirer le jour de la liste des cibles
        array_splice($playersAlive, $selfOffset, 1);
        $random_target =  count($playersAlive) > 0 ? $playersAlive[rand(0, count($playersAlive) - 1)] : $playersAlive[0];
        return $random_target;
    }
}
