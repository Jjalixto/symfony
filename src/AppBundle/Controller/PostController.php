<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostController extends Controller
{   

    /**
     * @Route("/create", name="create_post_route")
     */
    public function createPostAction(Request $request)
    {
        $post = new Post;
        $form = $this->createFormBuilder($post)
        ->add('title',TextType::class,array('attr' => array('class' => 'form-control')))
        ->add('description',TextareaType::class,array('attr' => array('class' => 'form-control')))
        ->add('category',TextType::class,array('attr' => array('class' => 'form-control')))
        ->add('save',SubmitType::class,array('label' => 'Create Post', 'attr' => array(
            'class' => 'btn btn-primary',
            'style' => 'margin-top:10px' 
            )))
        ->getForm(); 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $title = $form['title']->getData();
            $description = $form['description']->getData();
            $category = $form['category']->getData();
            
            $post->setTitle($title);
            $post->setDescription($description);
            $post->setCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('message','Post created successfully!');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('pages/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/view/{id}", name="view_post_route")
     */
    public function viewPostAction($id)
    {   
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        // echo '<pre>';
        //     print_r($posts);
        // echo '</pre>';
        // exit();
        return $this->render('pages/view.html.twig',['post' => $post]);
    }

     /**
     * @Route("/edit/{id}", name="edit_post_route")
     */
    public function editPostAction(Request $request, $id)
    {   
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        $post -> setTitle($post->getTitle());
        $post -> setDescription($post->getDescription());
        $post -> setCategory($post->getCategory());

        $form = $this->createFormBuilder($post)
        ->add('title',TextType::class,array('attr' => array('class' => 'form-control')))
        ->add('description',TextareaType::class,array('attr' => array('class' => 'form-control')))
        ->add('category',TextType::class,array('attr' => array('class' => 'form-control')))
        ->add('save',SubmitType::class,array('label' => 'Updated Post', 'attr' => array(
            'class' => 'btn btn-primary',
            'style' => 'margin-top:10px' 
            )))
        ->getForm(); 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $title = $form['title']->getData();
            $description = $form['description']->getData();
            $category = $form['category']->getData();
            $em = $this->getDoctrine()->getManager();
            $post = $em->getRepository('AppBundle:Post')->find($id);

            $post->setTitle($title);
            $post->setDescription($description);
            $post->setCategory($category);

            $em -> flush();
            $this->addFlash('message','Post Updated successfully!');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('pages/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/delete/{id}", name="delete_post_route")
     */
    public function deletePostAction($id)
    {   
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->find($id);
        $em -> remove($post);
        $em -> flush();
        $this->addFlash('message','Post Deleted successfully!');

        return $this->redirectToRoute('homepage');
    }
}
