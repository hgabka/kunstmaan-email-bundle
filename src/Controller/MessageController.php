<?php

namespace Hgabka\KunstmaanEmailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    /**
     * The webversion action.
     *
     * @Route("/webversion", name="hgabka_kunstmaan_email_message_webversion")
     * @param Request $request
     * @return Response
     */
    public function webversionAction(Request $request)
    {
    }

    /**
     * The unsubscribe action.
     *
     * @Route("/unsubscribe", name="hgabka_kunstmaan_email_message_unsubscribe")
     * @param Request $request
     * @return Response
     */
    public function unsubscribeAction(Request $request)
    {
    }
}
