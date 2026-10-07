<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\User\DTO\CreateUserDTO;
use App\Application\User\DTO\UpdateUserDTO;
use App\Application\User\UseCase\CreateUserUseCase;
use App\Application\User\UseCase\DeleteUserUseCase;
use App\Application\User\UseCase\GetUserUseCase;
use App\Application\User\UseCase\ListUsersUseCase;
use App\Application\User\UseCase\UpdateUserUseCase;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(ListUsersUseCase $listUsersUseCase): Response
    {
        $users = $listUsersUseCase->execute();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CreateUserUseCase $createUserUseCase): Response
    {
        if ($request->isMethod('POST')) {
            $name = (string) $request->request->get('name');
            $email = (string) $request->request->get('email');
            $password = (string) $request->request->get('password');

            try {
                $dto = new CreateUserDTO($name, $email, $password);
                $createUserUseCase->execute($dto);

                $this->addFlash('success', 'Usuário cadastrado com sucesso!');
                return $this->redirectToRoute('user_index');
            } catch (DomainException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('user/create.html.twig');
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        string $id,
        Request $request,
        GetUserUseCase $getUserUseCase,
        UpdateUserUseCase $updateUserUseCase
    ): Response {
        try {
            $user = $getUserUseCase->execute($id);
        } catch (DomainException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('user_index');
        }

        if ($request->isMethod('POST')) {
            $name = (string) $request->request->get('name');
            $email = (string) $request->request->get('email');

            try {
                $dto = new UpdateUserDTO($id, $name, $email);
                $updateUserUseCase->execute($dto);

                $this->addFlash('success', 'Usuário atualizado com sucesso!');
                return $this->redirectToRoute('user_index');
            } catch (DomainException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(string $id, DeleteUserUseCase $deleteUserUseCase): Response
    {
        try {
            $deleteUserUseCase->execute($id);
            $this->addFlash('success', 'Usuário excluído com sucesso!');
        } catch (DomainException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('user_index');
    }
}