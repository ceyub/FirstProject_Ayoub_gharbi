<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            
        ]);
    }


#[Route(path:'/hello',name:'hello')]
public function hello():Response{

    return new Response('hello');
}



#[Route(path:'/contact/{tel}',name:'contact')]

public function contact($tel):Response{
    return $this->render(view:'home/contact.html.twig',parameters:['telephone'=>$tel]);
}


#[Route(path:'/show',name:'show')]
public function show():Response{

    return new Response('bienvenue');
}


#[Route(path:'/afficher',name:'afficher')]

public function afficher():Response{
    return $this->render(view:'home/apropos.html.twig');
}






}



