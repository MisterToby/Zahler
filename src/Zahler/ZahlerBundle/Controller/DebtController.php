<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\Debt;
use Zahler\ZahlerBundle\Form\DebtType;
use Zahler\ZahlerBundle\Entity\Transaction;

use \DateTime;

/**
 * Debt controller.
 *
 */
class DebtController extends Controller {
    public function retrieveAction(Request $request) {
        $get = $request -> query -> all();
        $em = $this -> getDoctrine() -> getManager();

        $qb = $em -> createQueryBuilder();
        $qb -> select('COUNT(deb)');
        $qb -> from('ZahlerBundle:Debt', 'deb');
        $qb -> where("deb.debPer = {$get['person_id']}");

        $query = $qb -> getQuery();
        $count = $query -> getSingleScalarResult();

        $sql = "SELECT debt.id AS deb_id, *
        FROM debt, transaction
        WHERE deb_tra_id = transaction.id AND
        deb_per_id = {$get['person_id']}
        ORDER BY tra_date, debt.id ASC
        OFFSET {$get['start']}
        LIMIT {$get['limit']}";

        $connection = $em -> getConnection();
        $result = $connection -> query($sql);
        $records = $result -> fetchAll();

        $array = array();
        $array['rows'] = $records;
        $array['count'] = $count;

        return new Response(json_encode($array));
    }

    /**
     * Lists all Debt entities.
     *
     */
    public function indexAction() {
        $em = $this -> getDoctrine() -> getManager();

        $entities = $em -> getRepository('ZahlerBundle:Debt') -> findAll();

        return $this -> render('ZahlerBundle:Debt:index.html.twig', array('entities' => $entities, ));
    }

    /**
     * Creates a new Debt entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Debt();
        $form = $this -> createCreateForm($entity);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();

            $transaction = new Transaction();
            $transaction -> setTraAccCredit($em -> getReference('ZahlerBundle:Account', Debt::DEBT_ACCOUNT));
            $transaction -> setTraAccDebit($entity -> getDestinationAccount());
            $transaction -> setTraAmount($entity -> getAmount());
            $transaction -> setTraDate($entity -> getDate());
            $transaction -> setTraDescription('Loan from ' . $entity -> getDebPer() -> getPerName());

            $em -> persist($transaction);

            $entity -> setDebTra($transaction);

            $em -> persist($entity);
            $em -> flush();

            return $this -> redirect($this -> generateUrl('debt_show', array('id' => $entity -> getId())));
        }

        return $this -> render('ZahlerBundle:Debt:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Creates a form to create a Debt entity.
     *
     * @param Debt $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Debt $entity) {
        $form = $this -> createForm(new DebtType(), $entity, array('action' => $this -> generateUrl('debt_create'), 'method' => 'POST', ));

        $form -> add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Debt entity.
     *
     */
    public function newAction() {
        $entity = new Debt();
        $entity -> setDate(new DateTime('now'));
        $form = $this -> createCreateForm($entity);

        return $this -> render('ZahlerBundle:Debt:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Finds and displays a Debt entity.
     *
     */
    public function showAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Debt') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Debt entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Debt:show.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Displays a form to edit an existing Debt entity.
     *
     */
    public function editAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Debt') -> find($id);

        $entity -> setDate($entity -> getDebTra() -> getTraDate());
        $entity -> setDestinationAccount($entity -> getDebTra() -> getTraAccDebit());
        $entity -> setAmount($entity -> getDebTra() -> getTraAmount());

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Debt entity.');
        }

        $editForm = $this -> createEditForm($entity);
        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Debt:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Creates a form to edit a Debt entity.
     *
     * @param Debt $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Debt $entity) {
        $form = $this -> createForm(new DebtType(), $entity, array('action' => $this -> generateUrl('debt_update', array('id' => $entity -> getId())), 'method' => 'PUT', ));

        $form -> add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Debt entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Debt') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Debt entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);
        $editForm = $this -> createEditForm($entity);
        $editForm -> handleRequest($request);

        if ($editForm -> isValid()) {
            $transaction = $entity -> getDebTra();
            $transaction -> setTraAccDebit($entity -> getDestinationAccount());
            $transaction -> setTraAmount($entity -> getAmount());
            $transaction -> setTraDate($entity -> getDate());
            $transaction -> setTraDescription('Loan from ' . $entity -> getDebPer() -> getPerName());

            $em -> flush();

            // return $this -> redirect($this -> generateUrl('debt_edit', array('id' => $id)));
            return new Response('Ok');
        }

        return $this -> render('ZahlerBundle:Debt:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Deletes a Debt entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this -> createDeleteForm($id);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();
            $entity = $em -> getRepository('ZahlerBundle:Debt') -> find($id);

            if (!$entity) {
                throw $this -> createNotFoundException('Unable to find Debt entity.');
            }

            $em -> remove($entity);
            $em -> remove($entity -> getDebTra());
            $em -> flush();
        }

        return $this -> redirect($this -> generateUrl('debt'));
    }

    /**
     * Creates a form to delete a Debt entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this -> createFormBuilder() -> setAction($this -> generateUrl('debt_delete', array('id' => $id))) -> setMethod('DELETE') -> add('submit', 'submit', array('label' => 'Delete')) -> getForm();
    }

}
