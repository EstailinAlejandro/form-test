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

    #[Route('/delete', name: 'delete')]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $employees = $em->getRepository(Employee::class)->find($id);
        $em->remove($employees);
        return $this->render('form/index.html.twig', [
            'employees' => $employees,
        ]);
    }


    #[Route('/form', name: 'insert')]
    public function form(EmployeeRepository $repository, Request $request, EntityManagerInterface $em): Response
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
