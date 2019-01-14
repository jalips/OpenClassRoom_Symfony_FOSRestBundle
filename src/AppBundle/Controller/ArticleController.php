<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function showAction(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *    path = "/articles",
     *    name = "app_article_create"
     * )
     * @Rest\View(StatusCode = 201)
     */
    public function createAction(Request $request)
    {
        $data = $this->get('jms_serializer')->deserialize($request->getContent(), 'array', 'json');
        $article = new Article;
        $form = $this->get('form.factory')->create(ArticleType::class, $article);
        $form->submit($data);

        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();

        return $this->view(
            $article,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_article_show', ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]
        );
    }

}