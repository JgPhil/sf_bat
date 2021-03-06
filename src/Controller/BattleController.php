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
        if (count($playersAlive) > 1) { // Plague after each turn 
            foreach ($playersAlive as $warrior) {
                $this->summary .= $warrior->plague();
                $playersAlive = $warrior->maybeSuccumbs($playersAlive);
                $this->summary .= !in_array($warrior, $playersAlive) ?
                    $warrior->getName() . " a succombé \n" : "";
            }
            if (count($playersAlive) > 1) {
                foreach ($playersAlive as $warrior) {
                    $method = $warrior->getRandomMethod();
                    $target = $warrior->searchRandomTarget($playersAlive);
                    $beforeHealth = $target->getHealth();
                    $warrior->$method($target); // Attack

                    $playersAlive = $target->maybeSuccumbs($playersAlive); // removing the deads from the array
                    $damages = $beforeHealth - $target->getHealth();
                    if ($method == 'heal_action') {
                        $this->summary .=
                            $warrior->getName() . " s'est soignée de 3 points de vie\n";
                    } else {
                        $this->summary .=
                            $warrior->getName() . ' a attaqué '
                            . $target->getName() . ' avec ' . preg_replace('/_action/', '', $method)
                            . ' et lui a infligé ' . $damages . " dégats\n";
                        $this->summary .= !in_array($target, $playersAlive) ?
                            $target->getName() . " a succombé \n" : "";
                    }
                }
            }
            if (count($playersAlive) == 1) {
                $this->winner = true;
            }
        } else {
            session_unset();
        }
        $this->session->set('players_alive', $playersAlive); //actualize the players in session
        $this->summary .= "------------------------\n";
        // After a turn, data is sent via ajax response to view
        $data = [
            'summary' => $this->summary,
            'status' => $this->session->get('players_alive'),
            'winner' => $this->winner
        ];

        return $this->json($data);
    }
}
