<?php

namespace App\Controller;


use App\Entity\Goblin;
use App\Entity\Orc;
use App\Entity\Witch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class BattleController extends AbstractController
{
    private $session;
    private $playersAlive;
    private $summary = "";
    private $winner = false;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="battle")
     */
    public function index(): Response
    {
        if ($this->session->get('players_alive')) {
            session_unset();
        }
        return $this->render('battle.html.twig');
    }

    /**
     * @Route("/status", name="status")
     */
    public function getPlayersStatus()
    {
        return $this->playersAlive;
    }

    /**
     * @Route("/next-turn", name="next-turn")
     */
    public function nextTurn()
    {
        if (!$this->session->get('players_alive')) {
            $goblin = new Goblin('Goblin', 100);
            $witch = new Witch('Witch', 50);
            $orc = new Orc('Orc', 100);
            $playersAlive = [$goblin, $witch, $orc];
        } else {
            $playersAlive = $this->session->get('players_alive');
        }
        if (count($playersAlive) > 1) {
            for ($i = 0; $i < count($playersAlive); $i++) {
                $warrior = $playersAlive[$i];
                $this->summary .= $warrior->plague();
                $playersAlive = $warrior->maybeSuccumbs($playersAlive);
                $this->summary .= !in_array($warrior, $playersAlive) ?
                    $warrior->getName() . ' a succombé ' : '';
                $method = $warrior->getRandomMethod();
                $target = $warrior->searchRandomTarget($playersAlive);
                $initialHealth = $target->getHealth();
                $warrior->$method($target); ////////////ATTTAAAACCKK
                $damages = $initialHealth - $target->getHealth();
                if ($method == 'heal_action') {
                    $this->summary .=
                        $warrior->getName() . ' s\'est soigné de 3 points de vie';
                } else {
                    $this->summary .=
                        $warrior->getName() . ' a attaqué '
                        . $target->getName() . ' avec ' . $method . ' et lui a infligé '
                        . $damages . ' dégats ';
                    $playersAlive = $target->maybeSuccumbs($playersAlive);
                    $this->summary .= !in_array($target, $playersAlive) ?
                        $target->getName() . ' a succombé ' : '';
                }
            }
        } else {
            $this->winner = true;
            $this->summary .= "Le vainqueur est " . $playersAlive[0]->getName()
                . ' il lui reste ' . $playersAlive[0]->getHealth() . ' PV';
            session_unset();
        }
        $this->session->set('players_alive', $playersAlive);
        // After a turn, data is sent via ajax response to view
        $data = [
            'summary' => $this->summary,
            'status' => $this->session->get('players_alive'),
            'winner' => $this->winner
        ];

        return $this->json($data);
    }
}
