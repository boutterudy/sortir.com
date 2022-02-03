<?php

namespace App\Controller\Admin;

use App\Form\FileUploadType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadCsvController extends AbstractController
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Route ("/admin/importer", name="upload_csv")
     */
    public function uploadFile(Request $request, FileUploader $fileUploader)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['upload_file']->getData();
            if($file){
                $fileName = $fileUploader->upload($file);
                if($fileName !== null)
                {
                    $this->runUpload();
                }else{
                    $this->addFlash('error', 'Problème à la lecture du fichier');
                }
            }
        }
        return $this->render('admin/upload_csv.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function runUpload()
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'app:csv:import',
        ]);
        $output = new NullOutput();
        $app->run($input, $output);
        $this->addFlash('success', 'Les données ont été chargées en base');

        return new Response('');
    }

}