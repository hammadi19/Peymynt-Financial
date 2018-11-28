<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *  PosterTaskController
 * @Route("/secure/poster/task")
 */
class PosterTaskController extends Controller
{
    /**
     * @Route("/list", name="app_poster_task.list")
     * @Template()
     */
    public function taskList()
    {
        $contentManager = $this->get('app_content_manager');
        $userProfileInfo = $contentManager->userProfileInfo();

        $bridge = $this->get('app_rest_bridge');
        $resource = $this->getParameter('rest_endpoints')['user_task_list_endpoint'];
        $responseArray = $bridge->get($resource);

        return array(
            "data" => $responseArray["data"],
            "userProfileInfo" => $userProfileInfo
        );
    }


    /**
     * @Route("/create", name="app_poster_task.create")
     * @Template()
     */
    public function addTask(Request $request)
    {
        $errors = array();
        $success = null;

        $contentManager = $this->get('app_content_manager');
        $userProfileInfo = $contentManager->userProfileInfo();

        $bridge = $this->get('app_rest_bridge');
        $resource = $this->getParameter('rest_endpoints')['user_task_create_endpoint'];
        $resource_task_category_list = $this->getParameter('rest_endpoints')['task_categy_list_endpoint'];


        if ($request->request->get('createTaskForm')) {
            $formValues = $request->request->get('createTaskForm');
            $task_title = $formValues['task_title'];
            $task_cat = $formValues['task_cat'];
            $task_location = $formValues['task_location'];
            $task_price = $formValues['task_price'];
            $task_detail = $formValues['task_detail'];

            if (empty($task_title)) {
                array_push($errors, 'Task title field cannot be empty');
            }
            if (empty($task_cat)) {
                array_push($errors, 'Task category must be selected');
            }
            if (empty($task_location)) {
                array_push($errors, 'Task location field cannot be empty');
            }
            if (empty($task_price)) {
                array_push($errors, 'Task budget field cannot be empty');
            }

            if(0 === count($errors)) {
                $params = array(
                    'title' => $task_title,
                    'category_id' => $task_cat,
                    'location' => $task_location,
                    'description' => $task_detail,
                    'task_price' => $task_price

                );
                $responseArray = $bridge->post($resource, $params);
                if(200 == $responseArray['code']){
                    $success = $responseArray['message'];
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $success);
                    return $this->redirectToRoute('app_poster_task.create');
                }else{
                    array_push($errors,$responseArray['message']);
                }
            }

        }



        $task_cat_list = $bridge->get($resource_task_category_list);
//echo "<pre>";
//print_r($task_cat_list);
//exit;
        return array(
            "success"=>$success,
            "errors" => $errors,
            "task_category_list" => $task_cat_list["data"],
            "userProfileInfo" => $userProfileInfo
        );
    }
}
