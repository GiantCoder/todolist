<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Security\Http\Session;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class TodoController extends Controller
{

    /**
     * @Route("/todos", name="todo_list")
     */
    public function listAction(Request $request)
    {
        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findAll();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos
        ]);

    }

    /**
     * @Route("/todo/new", name="todo_new")
     */
    public function newAction(Request $request)
    {
        $now = new \DateTime('now');

        $todo = new Todo();

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('category', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('priority', ChoiceType::class, ['choices' => ['High' => 'High', 'Medium' => 'Medium', 'Low' => 'Low'], 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('dueDate', DateTimeType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('Save Todo', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', ]])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                // Get Form data
                $name = $form['name']->getData();
                $category = $form['category']->getData();
                $description = $form['description']->getData();
                $dueDate = $form['dueDate']->getData();
                $priority = $form['priority']->getData();

                $todo->setName($name);
                $todo->setCategory($category);
                $todo->setDescription($description);
                $todo->setDueDate($dueDate);
                $todo->setCreateDate($now);
                $todo->setPriority($priority);

                $em = $this->getDoctrine()->getManager();
                $em->persist($todo);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Todo created!'
                );

                return $this->redirectToRoute('todo_list');
            }

        return $this->render("todo/create.html.twig",
            ['form' => $form->createView()]);
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);
        $em->flush();
        $flashbag = $this->get('session')->getFlashBag();

        $flashbag->add('success', 'Todo created!');


        return $this->render('todo/create.html.twig');
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setDueDate($todo->getDueDate());
        $todo->setCreateDate($todo->getCreateDate());
        $todo->setPriority($todo->getPriority());

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('category', TextType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('priority', ChoiceType::class, ['choices' => ['High' => 'High', 'Medium' => 'Medium', 'Low' => 'Low'], 'attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('dueDate', DateTimeType::class, ['attr' => ['class' => 'form-control', 'style' => 'margin-bottom: 15px']])
            ->add('Update Todo', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', ]])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Get Form data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $dueDate = $form['dueDate']->getData();
            $priority = $form['priority']->getData();

            $now = new \DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setDueDate($dueDate);
            $todo->setCreateDate($now);
            $todo->setPriority($priority);

            $em->flush();

            $this->addFlash(
                'warning',
                'Todo updated!'
            );

            return $this->redirectToRoute('todo_list');
        }

        return $this->render("todo/edit.html.twig",
            ['form' => $form->createView()]);
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

        return $this->render('todo/details.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);
        $em->remove($todo);
        $em->flush();

        $this->addFlash(
            'danger',
            'Todo deleted!'
        );

        return $this->redirectToRoute('todo_list');
    }

}