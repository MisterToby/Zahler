<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\Loan;
use Zahler\ZahlerBundle\Form\LoanType;

/**
 * Loan controller.
 *
 */
class LoanController extends Controller
{

    /**
     * Lists all Loan entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ZahlerBundle:Loan')->findAll();

        return $this->render('ZahlerBundle:Loan:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Loan entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Loan();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('loan_show', array('id' => $entity->getId())));
        }

        return $this->render('ZahlerBundle:Loan:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Loan entity.
     *
     * @param Loan $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Loan $entity)
    {
        $form = $this->createForm(new LoanType(), $entity, array(
            'action' => $this->generateUrl('loan_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Loan entity.
     *
     */
    public function newAction()
    {
        $entity = new Loan();
        $form   = $this->createCreateForm($entity);

        return $this->render('ZahlerBundle:Loan:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Loan entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZahlerBundle:Loan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Loan entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZahlerBundle:Loan:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Loan entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZahlerBundle:Loan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Loan entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZahlerBundle:Loan:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Loan entity.
    *
    * @param Loan $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Loan $entity)
    {
        $form = $this->createForm(new LoanType(), $entity, array(
            'action' => $this->generateUrl('loan_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Loan entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZahlerBundle:Loan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Loan entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('loan_edit', array('id' => $id)));
        }

        return $this->render('ZahlerBundle:Loan:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Loan entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ZahlerBundle:Loan')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Loan entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('loan'));
    }

    /**
     * Creates a form to delete a Loan entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('loan_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
