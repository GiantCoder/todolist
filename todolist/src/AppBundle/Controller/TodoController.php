<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function newAction()
    {
        $now = new \DateTime();

        $todo = new Todo();
        $todo->setName('Task'.rand(1,100));
        $todo->setCategory('Work');
        $todo->setDueDate(new \DateTime());
        $todo->setCreateDate($now);
        $todo->setDescription('Lorem ipsum etc');
        $todo->setPriority('High');

        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);
        $em->flush();

        return new Response('<html><body>Todo created!</body></html>');
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        return $this->render('todo/create.html.twig');
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        return $this->render('todo/edit.html.twig');
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        return $this->render('todo/details.html.twig');
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
        return $this->render('Task deleted');
    }


}