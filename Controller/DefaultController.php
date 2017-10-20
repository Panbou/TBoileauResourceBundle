<?php

namespace TBoileau\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/_resource/upload")
     */
    public function uploadAction(Request $request)
    {
        try {
            $file = $request->files->get("file");
            $dir = $request->request->get("dir");
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(__DIR__ . '/../../../../web/' . $dir, $fileName);
            return $this->json(["error" => false, "resource" => $dir."/".$fileName]);
        }catch(\Exception $e) {
            return $this->json(["error" => true, "message" => "Une erreur est survenue lors de l'upload de votre fichier."]);
        }
    }
    /**
     * @Route("/_resource/crop")
     */
    public function cropAction(Request $request)
    {
        try {
            $data = $request->request->all();
            $file = __DIR__."/../../../../web/".$data["file"];
            $imagick = new \Imagick($file);

            $file = new File($data["file"]);

            $imagick->cropImage($data["width"], $data["height"], $data["x"], $data["y"]);
            if(isset($data["maxWidth"]) && isset($data["maxHeight"])) {
                if($data["width"] > $data["maxWidth"] || $data["height"] > $data["maxHeight"]) {
                    $imagick->resizeImage($data["maxWidth"], $data["maxHeight"], \Imagick::FILTER_LANCZOS, 1);
                }
            }
            if(isset($data["minWidth"]) && isset($data["minHeight"])) {
                if ($data["width"] < $data["minWidth"] || $data["height"] < $data["minHeight"]) {
                    $imagick->resizeImage($data["minWidth"], $data["minHeight"], \Imagick::FILTER_LANCZOS, 1);
                }
            }

            $imagick->writeImage($file);

            return $this->json(["error" => false, "resource" => $data["file"]]);
        }catch(\Exception $e) {
            return $this->json(["error" => true, "message" => "Une erreur est survenue lors de la modification de votre image."]);
        }
    }
}
