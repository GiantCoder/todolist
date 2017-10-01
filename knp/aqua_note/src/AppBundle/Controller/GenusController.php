<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GenusNote;
use AppBundle\Services\MarkdownTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Genus;
use Symfony\Component\HttpException;

class GenusController extends Controller
{

    /**
     * @Route("/genus/new", name=")
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1,100));
        $genus->setSubFamily('Octopie'.rand(1,100));
        $genus->setSpeciesCount(rand(1,100));
        $genus->setFunFact('Yay!');

        $genusNote = new GenusNote();
        $genusNote->setUsername('Aquaweaver');
        $genusNote->setUserAvatarFilename('ryan.jpeg');
        $genusNote->setNote('I counted 8 legs...');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->persist($genusNote);
        $em->flush();

        return new Response('<html><body>Genus created!</body></html>');
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName)
    {

        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository('AppBundle:Genus')
            ->findOneBy(['name' => $genusName]);

        if (!$genus) {
            throw $this->createNotFoundException('No genus found');
        }

        $transformer = new MarkdownTransformer();
        $funFact = $transformer->parse($genus->getFunFact());

        $this->get('logger')
            ->info('Showing genus: '.$genusName);

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesforGenus($genus);

        return $this->render('genus/show.html.twig', [
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNoteCount' -> count($recentNotes)
        ]);

    }

     /** @Route("/genus")
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAllPublishedOrderBySize();

        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses
        ]);
    }

    /**
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {

        $notes = [];

        foreach ($genus->getNotes() as $note)
        {
            $notes[] = [
            'id' => $note->getId(),
            'username' => $note->getUsername(),
            'avatarUri' => '/images'.$note->getUserAvatarFilename(),
            'note' => $note->getNote(),
            'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }

        $data = [
            'notes' => $notes,
        ];

        return new JsonResponse($data);
    }

}