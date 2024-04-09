<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

class FormController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        $employees = $em->getRepository(Employee::class)->findAll();
        return $this->render('form/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $employee = $em->getRepository(Employee::class)->find($id);

        if (!$employee) {
            // Optionally, handle the case where the entity does not exist
            // You can redirect to an error page or display a flash message
            // and redirect back to a listing page, for example.
            // For simplicity, let's redirect back to the listing page
            return $this->redirectToRoute('home');
            $this->addFlash('warning', 'Er is iets mis gegaan');
        }

        $em->remove($employee);
        $em->flush();
        $this->addFlash('danger', 'Employee verwijderd');
        // After successful deletion, you may want to redirect
        // the user to another page or display a success message
        // For simplicity, let's redirect back to the listing page
        return $this->redirectToRoute('home');

        return $this->render('form/index.html.twig', [
            'employees' => $employees,
        ]);
    }


    #[Route('/update/{id}', name: 'update')]
    public function form(EmployeeRepository $repository, Request $request, EntityManagerInterface $em, int $id): Response
    {
        $employee = $em->getRepository(Employee::class)->find($id);
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $employee = $form->getData();

            $em->persist($employee);
            $em->flush();

            $this->addFlash('success', 'Employee aangepast');

            return $this->redirectToRoute('home');
        }

        return $this->render('form/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/form', name: 'insert')]
    public function update(EmployeeRepository $repository, Request $request, EntityManagerInterface $em): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $employee = $form->getData();

            $em->persist($employee);
            $em->flush();

            $this->addFlash('success', 'Employee toegevoegd');

            return $this->redirectToRoute('home');
        }

        return $this->render('form/form.html.twig', [
            'form' => $form,
        ]);
    }
}
