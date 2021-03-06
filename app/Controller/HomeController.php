<?php
/**
 * Created by PhpStorm.
 * User: isak
 * Date: 6/21/17
 * Time: 10:34 AM
 */

namespace Bet\App\Controller;


use Bet\App\Exception\CustomException;
use Bet\App\Manager;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends BaseController
{
    public function homeAction(Request $request, Response $response)
    {
        try {
            // TODO: Use the one from Manager
            $time = $this->getLastBetEnd();
        } catch (CustomException $ce) {
            $error = $ce->getMessage();
        }

        $lastBet = Manager\Bet::getLastBet();

        return $this->view->render($response, 'home.html.twig', [
            'time' => !empty($time) ? $time->getTimestamp() : null,
            'dateNow' => (new \DateTime($lastBet['datenow']))->getTimestamp(),
            'error' => $error ?? null,
            'bet' => $lastBet
        ]);
    }

    /**
     * @return \DateTime
     * @throws CustomException
     */
    private function getLastBetEnd()
    {
        $lastBet = Manager\Bet::getLastBet();

        if (empty($lastBet)) {
            throw new CustomException('Il n\'y a pas de pari en cours.');
        }

        return Manager\Bet::getDateEnd($lastBet['id']);
    }
}