<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Doctor;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email'] ?? '']);

        if (!$user || !$hasher->isPasswordValid($user, $data['password'] ?? '')) {
            return new JsonResponse(['error' => 'Identifiants invalides'], 401);
        }

        $token = Uuid::v4()->toRfc4122();


        return new JsonResponse([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ]);
    }

    #[Route('/api/doctors/{id}', name: 'delete_doctor', methods: ['DELETE'])]
    public function deleteDoctor(Request $request, Doctor $doctor, EntityManagerInterface $em): JsonResponse
    {
        $token = $request->headers->get('Authorization');

        if (!$token) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }

        // Ici on ne vérifie pas la validité, on suppose que tout token est "connecté"
        $em->remove($doctor);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
