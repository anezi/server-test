<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Extra\Route("/", name="homepage")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        return $this->getInfo($request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    private function getInfo(Request $request)
    {
        if ($request->get('sleep')) {
            sleep($request->get('sleep'));
        }

        $files = [];

        /** @var UploadedFile $file */
        foreach ($request->files->all() as $key => $file) {
            $files[ $key ] = [
                'originalName' => $file->getClientOriginalName(),
                'mimeType'     => $file->getMimeType(),
                'size'         => $file->getSize()
            ];
        }

        $tmpPutFile = tempnam(sys_get_temp_dir(), 'php_curl_');
        file_put_contents($tmpPutFile, $request->getContent());

        $response = new JsonResponse([
            'userAgent'         => $request->headers->get('USER-AGENT'),
            'referer'           => $request->server->get('HTTP_REFERER'),
            'requestMethod'     => $request->getMethod(),
            'get'               => $request->query->all(),
            'post'              => $request->request->all(),
            'files'             => $files,
            'cookies'           => $request->cookies->all(),
            'content'           => [
                'mimeContentType' => mime_content_type($tmpPutFile),
                'fileSize'        => filesize($tmpPutFile)
            ],
            'httpAuthorisation' => $request->server->get('HTTP_AUTHORIZATION'),
            'http_user'         => $request->getUser(),
        ]);

        $response->headers->set('X-REQUEST-METHOD', $request->getMethod());

        $cookie = new Cookie('server-cookie', 'contentOfServerCookie');
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Extra\Route("/http/basic", name="http_basic")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function httpBasicAction(Request $request)
    {
        return $this->getInfo($request);
    }

    /**
     * @Extra\Route("/http/digest", name="http_digest")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function httpDigestAction(Request $request)
    {
        return $this->getInfo($request);
    }
}
