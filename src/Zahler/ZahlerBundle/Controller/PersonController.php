<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\Person;
use Zahler\ZahlerBundle\Form\PersonType;

use \DateTime;

/**
 * Person controller.
 *
 */
class PersonController extends Controller {
    public function retrieveAction(Request $request) {
        $get = $request -> query -> all();
        $em = $this -> getDoctrine() -> getManager();

        $qb = $em -> createQueryBuilder();
        $qb -> select('COUNT(per)');
        $qb -> from('ZahlerBundle:Person', 'per');

        $query = $qb -> getQuery();
        $count = $query -> getSingleScalarResult();

        $sql = "SELECT *
        FROM person
        ORDER BY per_name, id ASC
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

    public function reportAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $qb = $em -> createQueryBuilder();
        $qb -> select('tra');
        $qb -> from('ZahlerBundle:Transaction', 'tra');
        $qb -> leftJoin('tra.loans', 'loa');
        $qb -> leftJoin('loa.loaPer', 'per');
        $qb -> leftJoin('tra.payments', 'pay');
        $qb -> leftJoin('pay.payLoa', 'pay_loa');
        $qb -> leftJoin('pay_loa.loaPer', 'pay_loa_per');
        $qb -> where("per.id = $id OR pay_loa_per.id = $id");
        $qb -> orderBy('tra.traDate', 'ASC');
        $qb -> addOrderBy('tra.id', 'ASC');

        $query = $qb -> getQuery();
        $entities = $query -> getResult();

        $totalLoans = 0;
        $totalPayments = 0;
        $currentDatetime = new DateTime('now');
        $totalInterest = 0;
        foreach ($entities as $entity) {
            $interval = date_diff($entity -> getTraDate(), $currentDatetime);
            $numberOfDays = intval($interval -> format('%a'));
            $interest = 0;
            if (count($entity -> loans) > 0) {
                $annualInterestRate = $entity -> loans[0] -> getLoaInterestRate() / 100;
                $compoundInterestRate = pow($annualInterestRate + 1, $numberOfDays / 365) - 1;
                $interest = $compoundInterestRate * $entity -> getTraAmount();

                $totalLoans += $entity -> getTraAmount();
            }
            if (count($entity -> payments) > 0) {
                $annualInterestRate = $entity -> payments[0] -> getPayLoa() -> getLoaInterestRate() / 100;
                $compoundInterestRate = pow($annualInterestRate + 1, $numberOfDays / 365) - 1;
                $interest = $compoundInterestRate * $entity -> getTraAmount() * -1;

                $totalPayments += $entity -> getTraAmount();
            }

            $totalInterest += $interest;
            $entity -> setInterest($interest);
        }

        $balance = $totalLoans - $totalPayments;
        $totalDue = $balance + $totalInterest;

        return $this -> render('ZahlerBundle:Person:report.html.twig', array('entities' => $entities, 'totalLoans' => $totalLoans, 'totalPayments' => $totalPayments, 'totalInterest' => $totalInterest, 'balance' => $balance, 'totalDue' => $totalDue));
    }

    /**
     * Lists all Person entities.
     *
     */
    public function indexAction() {
        $em = $this -> getDoctrine() -> getManager();

        $entities = $em -> getRepository('ZahlerBundle:Person') -> findAll();

        return $this -> render('ZahlerBundle:Person:index.html.twig', array('entities' => $entities, ));
    }

    /**
     * Creates a new Person entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Person();
        $form = $this -> createCreateForm($entity);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();
            $em -> persist($entity);
            $em -> flush();

            return $this -> redirect($this -> generateUrl('person_show', array('id' => $entity -> getId())));
        }

        return $this -> render('ZahlerBundle:Person:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Creates a form to create a Person entity.
     *
     * @param Person $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Person $entity) {
        $form = $this -> createForm(new PersonType(), $entity, array('action' => $this -> generateUrl('person_create'), 'method' => 'POST', ));

        $form -> add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Person entity.
     *
     */
    public function newAction() {
        $entity = new Person();
        $form = $this -> createCreateForm($entity);

        return $this -> render('ZahlerBundle:Person:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Finds and displays a Person entity.
     *
     */
    public function showAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Person') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Person entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Person:show.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Displays a form to edit an existing Person entity.
     *
     */
    public function editAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Person') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Person entity.');
        }

        $editForm = $this -> createEditForm($entity);
        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Person:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Creates a form to edit a Person entity.
     *
     * @param Person $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Person $entity) {
        $form = $this -> createForm(new PersonType(), $entity, array('action' => $this -> generateUrl('person_update', array('id' => $entity -> getId())), 'method' => 'PUT', ));

        $form -> add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Person entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Person') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Person entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);
        $editForm = $this -> createEditForm($entity);
        $editForm -> handleRequest($request);

        if ($editForm -> isValid()) {
            $em -> flush();

            return $this -> redirect($this -> generateUrl('person_edit', array('id' => $id)));
        }

        return $this -> render('ZahlerBundle:Person:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Deletes a Person entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this -> createDeleteForm($id);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();
            $entity = $em -> getRepository('ZahlerBundle:Person') -> find($id);

            if (!$entity) {
                throw $this -> createNotFoundException('Unable to find Person entity.');
            }

            $em -> remove($entity);
            $em -> flush();
        }

        return $this -> redirect($this -> generateUrl('person'));
    }

    /**
     * Creates a form to delete a Person entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this -> createFormBuilder() -> setAction($this -> generateUrl('person_delete', array('id' => $id))) -> setMethod('DELETE') -> add('submit', 'submit', array('label' => 'Delete')) -> getForm();
    }

}
