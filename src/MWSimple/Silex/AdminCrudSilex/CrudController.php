<?php
namespace MWSimple\Silex\AdminCrudSilex;

use Symfony\Component\HttpFoundation\Request;
use Controller\FormController;

class CrudController
{
    private $app;
    private $options;
    private $class;

    public function __construct($app, $options)
    {
        $this->app = $app;
        $this->options = $options;
        $className = $this->options['entityRepo'];
        $this->class = "\Entity$className";
    }

    public function indexAction()
    {
        $em = $this->app["orm.em"];

        $entities = $em->getRepository('Entity'.$this->options['entityRepo'])->findBy(array(),array('id' => 'DESC'));

        return $this->app['twig']->render($this->options['dirTemplate'].'index.html.twig', array(
            'entities' => $entities,
            'options' => $this->options
        ));
    }

    public function newAction()
    {
        $entity = new $this->class();
        $form = FormController::createForm($this->options['entityName'], $this->app, $entity);
        // display the form
        return $this->app['twig']->render($this->options['dirTemplate'].'new.html.twig', array(
            'form' => $form->createView(),
            'options' => $this->options
        ));
    }

    public function createAction(Request $request)
    {
        $entity = new $this->class();
        $form = FormController::createForm($this->options['entityName'], $this->app, $entity);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->app["orm.em"];
                $em->persist($entity);
                $em->flush();

                return $this->app->redirect($this->app['url_generator']->generate(
                    $this->options['entityRoute'].'_show', array('id' => $entity->getId())
                ));
            }
        }
        // display the form
        return $this->app['twig']->render($this->options['dirTemplate'].'new.html.twig', array(
            'form' => $form->createView(),
            'options' => $this->options
        ));
    }

    public function showAction($id)
    {
        $em = $this->app["orm.em"];

        $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

        if (!$entity) {
            $this->app->abort(404, $this->options['entityName']." $id does not exist.");
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->app['twig']->render($this->options['dirTemplate'].'show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'options' => $this->options
        ));
    }

    public function editAction($id)
    {
        $em = $this->app["orm.em"];

        $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

        if (!$entity) {
            $this->app->abort(404, $this->options['entityName']." $id does not exist.");
        }

        $editForm = FormController::createForm($this->options['entityName'], $this->app, $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->app['twig']->render($this->options['dirTemplate'].'edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'options' => $this->options
        ));
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->app["orm.em"];

        $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

        if (!$entity) {
            $this->app->abort(404, $this->options['entityName']." $id does not exist.");
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = FormController::createForm($this->options['entityName'], $this->app, $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->app->redirect($this->app['url_generator']->generate(
                $this->options['entityRoute'].'_edit', array('id' => $id)
            ));
        }

        return $this->app['twig']->render($this->options['dirTemplate'].'edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'options' => $this->options
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->app["orm.em"];
            $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

            if (!$entity) {
                $this->app->abort(404, $this->options['entityName']." $id does not exist.");
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->app->redirect($this->app['url_generator']->generate(
            $this->options['entityRoute']
        ));
    }

    private function createDeleteForm($id)
    {
        return $this->app['form.factory']->createBuilder('form', array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
