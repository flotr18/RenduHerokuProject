<?php

namespace App\Controller;

use App\Form\EmployeeType;
use App\Entity\Employees;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{


    /**
     * @Route("/", name="test")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function index(Request $request, EntityManagerInterface $entityManager)
    {

        $employee = new Employees();


        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $employee = $form->getData();

            $entityManager->persist($employee);
            $entityManager->flush();
        }

        try {
            $employeesRepo = $this->getDoctrine()
                ->getRepository(Employees::class)
                ->findAll();

            if (!$employeesRepo) {
                throw $this->createNotFoundException(
                    'No event found'
                );
            }

        } catch (\Exception $e){
            $this->redirectToRoute('test');
        }

        return $this->render('test/index.html.twig', [
            'employees' => $employeesRepo,
            'formEmployee' => $form->createView()

        ]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     * @Route("/delete/{id}", name="delete")
     */

   public function remove($id, EntityManagerInterface $entityManager){

       $employee = $this->getDoctrine()->getRepository(Employees::class)->find($id);

       $entityManager->remove($employee);
       $entityManager->flush();

       return $this->redirectToRoute("test");

   }

    /**
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/update/{id}", name="update")
     */

   public function update($id, Request $request, EntityManagerInterface $entityManager){
       $employee = $this->getDoctrine()
           ->getRepository(Employees::class)->find($id);

       $form = $this->createForm(EmployeeType::class, $employee);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()){

           $newEmployee = $form->getData();

           $entityManager->persist($newEmployee);
           $entityManager->flush();

           return $this->redirectToRoute('test');
       }

       return $this->render('test/update.html.twig', [
           'employee' => $employee,
           'formUpdate' => $form->createView()
       ]);
   }
}
