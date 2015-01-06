<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\Transaction;
use Zahler\ZahlerBundle\Form\TransactionType;

use \DateTime;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller {
    /**
     * Lists all Transaction entities.
     *
     */
    public function index_jsAction($accId) {
        $array = array();
        $array['prefijo_url'] = $this -> get('router') -> generate('root');
        $array['accId'] = $accId;
        return $this -> render('ZahlerBundle:Transaction:index_js.html.twig', $array);
    }

    public function retrieveAction($accId) {
        $em = $this -> getDoctrine() -> getManager();

        // $entities = $em->getRepository('ZahlerBundle:Transaction')->findAll();

        $qb = $em -> createQueryBuilder();
        $qb -> select('tra, accDebit, accCredit');
        $qb -> from('ZahlerBundle:Transaction', 'tra');
        $qb -> join('tra.traAccDebit', 'accDebit');
        $qb -> join('tra.traAccCredit', 'accCredit');
        $qb -> where("tra.traAccCredit = $accId OR tra.traAccDebit = $accId");
        $qb -> orderBy('tra.id');

        $query = $qb -> getQuery();
        $entities = $query -> getArrayResult();

        $balance = 0;
        foreach ($entities as $key => $entity) {
            if ($entity['traAccDebit']['id'] == $accId) {
                $balance += $entity['traAmount'];
                $entities[$key]['account_id'] = $entity['traAccCredit']['id'];
                $entities[$key]['debitAmount'] = $entity['traAmount'];
                $entities[$key]['creditAmount'] = 0;
            } else {
                $balance -= $entity['traAmount'];
                $entities[$key]['account_id'] = $entity['traAccDebit']['id'];
                $entities[$key]['debitAmount'] = 0;
                $entities[$key]['creditAmount'] = $entity['traAmount'];
            }
            $entities[$key]['balance'] = $balance;
            $entities[$key]['date'] = $entity['traDate'] -> format('Y-m-d');
        }

        return new Response(json_encode($entities));
    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function indexAction($accId) {
        $em = $this -> getDoctrine() -> getManager();

        // $entities = $em->getRepository('ZahlerBundle:Transaction')->findAll();

        $qb = $em -> createQueryBuilder();
        $qb -> select('tra');
        $qb -> from('ZahlerBundle:Transaction', 'tra');
        $qb -> where("tra.traAccCredit = $accId OR tra.traAccDebit = $accId");
        $qb -> orderBy('tra.id');

        $query = $qb -> getQuery();
        $entities = $query -> getResult();

        $balance = 0;
        foreach ($entities as $entity) {
            if ($entity -> getTraAccDebit() -> getId() == $accId) {
                $balance += $entity -> getTraAmount();
                $entity -> setAccount($entity -> getTraAccCredit());
                $entity -> setDebitAmount($entity -> getTraAmount());
                $entity -> setCreditAmount(0);
            } else {
                $balance -= $entity -> getTraAmount();
                $entity -> setAccount($entity -> getTraAccDebit());
                $entity -> setDebitAmount(0);
                $entity -> setCreditAmount($entity -> getTraAmount());
            }
            $entity -> balance = $balance;

            $deleteForm = $this -> createDeleteForm($entity -> getId(), $accId);
            $entity -> deleteFormView = $deleteForm -> createView();
        }

        $entity = new Transaction();
        $entity -> setCreditAmount(0);
        $entity -> setDebitAmount(0);
        $entity -> setTraDate(new DateTime('now'));
        $form = $this -> createCreateForm($entity, $accId);

        return $this -> render('ZahlerBundle:Transaction:index.html.twig', array('entities' => $entities, 'entity' => $entity, 'form' => $form -> createView(), 'accId' => $accId));
    }

    /**
     * Creates a new Transaction entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Transaction();
        $form = $this -> createCreateForm($entity);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();

            $em -> persist($entity);
            $em -> flush();

            return new Response('Ok');
        }

        return $this -> render('ZahlerBundle:Transaction:new.html.twig', array('entity' => $entity, 'form' => $form -> createView()));
    }

    /**
     * Creates a form to create a Transaction entity.
     *
     * @param Transaction $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Transaction $entity) {
        $form = $this -> createForm(new TransactionType(), $entity, array('action' => $this -> generateUrl('transaction_create'), 'method' => 'POST', ));

        $form -> add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Transaction entity.
     *
     */
    public function newAction() {
        $entity = new Transaction();
        $form = $this -> createCreateForm($entity);

        return $this -> render('ZahlerBundle:Transaction:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Finds and displays a Transaction entity.
     *
     */
    public function showAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Transaction') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Transaction entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Transaction:show.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Displays a form to edit an existing Transaction entity.
     *
     */
    public function editAction($id, $accId) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Transaction') -> find($id);

        if ($entity -> getTraAccDebit() -> getId() == $accId) {
            $entity -> setDebitAmount($entity -> getTraAmount());
            $entity -> setCreditAmount(0);
            $entity -> setAccount($entity -> getTraAccCredit());
        } else {
            $entity -> setCreditAmount($entity -> getTraAmount());
            $entity -> setDebitAmount(0);
            $entity -> setAccount($entity -> getTraAccDebit());
        }

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Transaction entity.');
        }

        $editForm = $this -> createEditForm($entity, $accId);
        $deleteForm = $this -> createDeleteForm($id, $accId);

        return $this -> render('ZahlerBundle:Transaction:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), 'accId' => $accId));
    }

    /**
     * Creates a form to edit a Transaction entity.
     *
     * @param Transaction $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Transaction $entity, $accId) {
        $form = $this -> createForm(new TransactionType(), $entity, array('action' => $this -> generateUrl('transaction_update', array('id' => $entity -> getId(), 'accId' => $accId)), 'method' => 'PUT', ));

        $form -> add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Transaction entity.
     *
     */
    public function updateAction(Request $request, $id, $accId) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Transaction') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Transaction entity.');
        }

        $deleteForm = $this -> createDeleteForm($id, $accId);
        $editForm = $this -> createEditForm($entity, $accId);
        $editForm -> handleRequest($request);

        if ($editForm -> isValid()) {
            $entity -> adjust($em -> getReference('ZahlerBundle:Account', $accId));

            $em -> flush();

            return $this -> redirect($this -> generateUrl('transaction_edit', array('id' => $id, 'accId' => $accId)));
        }

        return $this -> render('ZahlerBundle:Transaction:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Deletes a Transaction entity.
     *
     */
    public function deleteAction(Request $request, $id, $accId) {
        $form = $this -> createDeleteForm($id, $accId);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();
            $entity = $em -> getRepository('ZahlerBundle:Transaction') -> find($id);

            if (!$entity) {
                throw $this -> createNotFoundException('Unable to find Transaction entity.');
            }

            $em -> remove($entity);
            $em -> flush();
        }

        return $this -> redirect($this -> generateUrl('transaction', array('accId' => $accId)));
    }

    /**
     * Creates a form to delete a Transaction entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $accId) {
        return $this -> createFormBuilder() -> setAction($this -> generateUrl('transaction_delete', array('id' => $id, 'accId' => $accId))) -> setMethod('DELETE') -> add('submit', 'submit', array('label' => 'Delete')) -> getForm();
    }

}
