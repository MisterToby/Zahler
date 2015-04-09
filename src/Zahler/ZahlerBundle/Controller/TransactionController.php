<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\Transaction;
use Zahler\ZahlerBundle\Form\TransactionType;
use Doctrine\ORM\Query\ResultSetMapping;

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
        $em = $this -> getDoctrine() -> getManager();

        $account = $em -> find('ZahlerBundle:Account', $accId);

        $array = array();
        $array['prefijo_url'] = $this -> get('router') -> generate('root');
        $array['accId'] = $accId;
        $array['accName'] = $account -> __toString();
        return $this -> render('ZahlerBundle:Transaction:index_js.html.twig', $array);
    }

    public function queryAction() {
        $em = $this -> getDoctrine() -> getManager();
        $request = Request::createFromGlobals();
        $get = $request -> query -> all();

        $rsm = new ResultSetMapping();
        $rsm -> addEntityResult('ZahlerBundle:Transaction', 'tra');
        $rsm -> addFieldResult('tra', 'id', 'id');
        $rsm -> addFieldResult('tra', 'tra_date', 'traDate');
        $rsm -> addFieldResult('tra', 'tra_description', 'traDescription');
        $rsm -> addFieldResult('tra', 'tra_amount', 'traAmount');
        $rsm -> addJoinedEntityResult('ZahlerBundle:Account', 'accDebit', 'tra', 'traAccDebit');
        $rsm -> addFieldResult('accDebit', 'accdebitid', 'id');
        $rsm -> addFieldResult('accDebit', 'accdebitaccname', 'accName');
        $rsm -> addJoinedEntityResult('ZahlerBundle:Account', 'accCredit', 'tra', 'traAccCredit');
        $rsm -> addFieldResult('accCredit', 'acccreditid', 'id');
        $rsm -> addFieldResult('accCredit', 'acccreditaccname', 'accName');

        $sql = "SELECT transaction.*,
        accDebit.id AS accdebitid,
        accDebit.acc_name AS accdebitaccname, 
        accCredit.id AS acccreditid,
        accCredit.acc_name AS acccreditaccname 
        FROM
        (
        SELECT tra_description, MAX(tra_date) AS max_date, MAX(id) AS max_id
        FROM transaction
        WHERE (tra_acc_id_debit = {$get['account_id']} OR tra_acc_id_credit = {$get['account_id']})
        GROUP BY tra_description
        ) AS grouped_transaction,
        transaction,
        account AS accDebit,
        account AS accCredit
        WHERE grouped_transaction.tra_description = public.transaction.tra_description AND
        grouped_transaction.max_date = public.transaction.tra_date AND
        grouped_transaction.max_id = public.transaction.id AND
        grouped_transaction.tra_description ILIKE '%{$get['query']}%' AND
        accDebit.id = transaction.tra_acc_id_debit AND
        accCredit.id = transaction.tra_acc_id_credit AND
        (accDebit.id = {$get['account_id']} OR accCredit.id = {$get['account_id']})
        ORDER BY grouped_transaction.tra_description;";
        $query = $em -> createNativeQuery($sql, $rsm);

        $records = $query -> getArrayResult();

        return new Response(json_encode($records));
    }

    public function retrieveAction($accId) {
        $em = $this -> getDoctrine() -> getManager();
        $request = Request::createFromGlobals();
        $get = $request -> query -> all();

        // $entities = $em->getRepository('ZahlerBundle:Transaction')->findAll();

        $qb = $em -> createQueryBuilder();
        $qb -> select('COUNT(tra)');
        $qb -> from('ZahlerBundle:Transaction', 'tra');
        $qb -> join('tra.traAccDebit', 'accDebit');
        $qb -> join('tra.traAccCredit', 'accCredit');
        $qb -> where("tra.traAccCredit = $accId OR tra.traAccDebit = $accId");

        $query = $qb -> getQuery();
        $total = $query -> getSingleScalarResult();

        $qb -> select('tra, accDebit, accCredit');
        $qb -> orderBy('tra.traDate');
        $qb -> addOrderBy('tra.id');
        $qb -> setFirstResult($get['start']);
        $qb -> setMaxResults($get['limit']);

        $query = $qb -> getQuery();
        $entities = $query -> getArrayResult();

        $balance = 0;

        if (isset($entities[0])) {
            $entity = $entities[0];

            $sql = "SELECT workoutbalancebeforetransaction($accId, {$entity['id']}) AS balance;";

            $connection = $em -> getConnection();

            $array = $connection -> query($sql) -> fetchAll();

            $balance = $array[0]['balance'];
        }

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

        $array = array();
        $array['rows'] = $entities;
        $array['total'] = $total;

        return new Response(json_encode($array));
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
    private function createEditForm(Transaction $entity) {
        $form = $this -> createForm(new TransactionType(), $entity, array('action' => $this -> generateUrl('transaction_update', array('id' => $entity -> getId())), 'method' => 'PUT', ));

        $form -> add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Transaction entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Transaction') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Transaction entity.');
        }

        $editForm = $this -> createEditForm($entity);
        $editForm -> handleRequest($request);

        if ($editForm -> isValid()) {
            $em -> flush();

            return new Response('Ok');
        }

        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Transaction:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Deletes a Transaction entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        // $form = $this -> createDeleteForm($id);
        // $form -> handleRequest($request);

        // if ($form -> isValid()) {
        $em = $this -> getDoctrine() -> getManager();
        $entity = $em -> getRepository('ZahlerBundle:Transaction') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Transaction entity.');
        }

        $em -> remove($entity);
        $em -> flush();
        // }

        return new Response('Ok');
    }

    /**
     * Creates a form to delete a Transaction entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        $form = $this -> createFormBuilder() -> setAction($this -> generateUrl('transaction_delete', array('id' => $id))) -> setMethod('DELETE') -> add('submit', 'submit', array('label' => 'Delete')) -> getForm();

        return $form;
    }

}
