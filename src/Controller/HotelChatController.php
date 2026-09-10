<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HotelChatController extends AbstractController
{
    #[Route('/hotel/chat', name: 'hotel_chat')]
    public function index(): Response
    {
        return $this->render('hotel_chat/index.html.twig');
    }
}
