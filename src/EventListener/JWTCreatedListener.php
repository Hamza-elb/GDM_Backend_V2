<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        echo "The User is :";
        echo $user;
        dd($user);
        // Récupérer les données actuelles
        $payload = $event->getData();

        // Ajouter l'ID et l'email de l'utilisateur
        $payload['id'] = $user->getId();
        $payload['email'] = $user->getEmail();

        // Mettre à jour les données du token
        $event->setData($payload);
    }
}