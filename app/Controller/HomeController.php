<?php
/**
 * Created by PhpStorm.
 * User: isak
 * Date: 6/21/17
 * Time: 10:34 AM
 */

namespace Bet\App\Controller;


use Bet\App\Exception\CustomException;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends BaseController
{
    public function homeAction(Request $request, Response $response)
    {
        try {
            $time = $this->getDateEnd();
        } catch(CustomException $ce) {
            $error = $ce->getMessage();
        }

        return $this->view->render($response, 'home.html.twig', [
            'time' => !empty($time) ? $time->getTimestamp() : null,
            'error' => $error ?? null,
        ]);
    }

    /**
     * @return \DateTime
     * @throws CustomException
     */
    private function getDateEnd()
    {
        $lastBet = $this->database->select()
            ->from('bet')
            ->orderBy('dateCreated', 'DESC')
            ->limit(1, 0);

        $lastBet = $lastBet->execute()->fetch();

        if (empty($lastBet)) {
            throw new CustomException('Il n\'y a pas de pari en cours.');
        }

        $dateCreated = new \DateTime($lastBet['dateCreated']);
        $interval = new \DateInterval('PT' . $lastBet['pariDurationMinute'] . 'M');

        $dateEnd = $dateCreated->add($interval);

        if($dateEnd < new \DateTime()) {
            throw new CustomException('Il n\'y a pas de pari en cours.');
        }

        return $dateEnd->setTimezone(new \DateTimeZone('UTC'));
    }
}