<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function uploadAction(Request $request)
    {
        $rootDir = $this->get('kernel')->getRootDir() . '/../web';
        $tmpDir = '/' . ltrim($this->get('service_container')->getParameter('snowcap_admin.multiupload.tmp_dir'), '/');
        $dstDir = $rootDir . $tmpDir;

        if (null === $files = $request->files->get('files')) {
            throw new \Exception('No file provided in the request');
        }

        $file = reset($files);
        $filename = uniqid(mt_rand(), true) . '.' . $file->getClientOriginalExtension();
        $file->move($dstDir, $filename);

        $fileUrl = rtrim($tmpDir, '/') . '/' . $filename;

        return new JsonResponse(array('url' => $fileUrl), 200, array(
            'Content-Type' => 'text/plain', // IE compatibility issue
        ));
    }
}
