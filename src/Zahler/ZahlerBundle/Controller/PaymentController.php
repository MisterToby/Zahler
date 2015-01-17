<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\Payment;
use Zahler\ZahlerBundle\Form\PaymentType;
use Zahler\ZahlerBundle\Entity\Transaction;
use Zahler\ZahlerBundle\Entity\Loan;
use \DateTime;

/**
 * Payment controller.
 *
 */
class PaymentController extends Controller {

    /**
     * Lists all Payment entities.
     *
     */
    public function indexAction() {
        $em = $this -> getDoctrine() -> getManager();

        $entities = $em -> getRepository('ZahlerBundle:Payment') -> findAll();

        return $this -> render('ZahlerBundle:Payment:index.html.twig', array('entities' => $entities, ));
    }

    /**
     * Creates a new Payment entity.
     *
     */
    public function createAction(Request $request) {
        $em = $this -> getDoctrine() -> getManager();
        $entity = new Payment();
        $form = $this -> createCreateForm($entity);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $transaction = new Transaction();
            $transaction -> setTraAccCredit($em -> getReference('ZahlerBundle:Account', Loan::LOAN_ACCOUNT));
            $transaction -> setTraAccDebit($entity -> getDestinationAccount());
            $transaction -> setTraAmount($entity -> getAmount());
            $transaction -> setTraDate($entity -> getDate());
            $transaction -> setTraDescription('Payment from ' . $entity -> getPayLoa() -> getLoaPer() -> getPerName());

            $em -> persist($transaction);

            $entity -> setPayTra($transaction);

            if ($entity -> getInterest() > 0) {
                $interestTransaction = new Transaction();
                $interestTransaction -> setTraAccCredit($em -> getReference('ZahlerBundle:Account', Payment::INTEREST_ACCOUNT));
                $interestTransaction -> setTraAccDebit($entity -> getDestinationAccount());
                $interestTransaction -> setTraAmount($entity -> getInterest());
                $interestTransaction -> setTraDate($entity -> getDate());
                $interestTransaction -> setTraDescription('Interest from ' . $entity -> getPayLoa() -> getLoaPer() -> getPerName());

                $em -> persist($interestTransaction);
                $entity -> setPayTraInterest($interestTransaction);
            }

            $em -> persist($entity);
            $em -> flush();

            return $this -> redirect($this -> generateUrl('payment_show', array('id' => $entity -> getId())));
        }

        return $this -> render('ZahlerBundle:Payment:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Creates a form to create a Payment entity.
     *
     * @param Payment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Payment $entity) {
        $form = $this -> createForm(new PaymentType(), $entity, array('action' => $this -> generateUrl('payment_create'), 'method' => 'POST', ));

        $form -> add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Payment entity.
     *
     */
    public function newAction() {
        $entity = new Payment();
        $entity -> setDate(new DateTime('now'));
        $entity -> setInterest(0);
        $form = $this -> createCreateForm($entity);

        return $this -> render('ZahlerBundle:Payment:new.html.twig', array('entity' => $entity, 'form' => $form -> createView(), ));
    }

    /**
     * Finds and displays a Payment entity.
     *
     */
    public function showAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Payment') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Payment entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Payment:show.html.twig', array('entity' => $entity, 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Displays a form to edit an existing Payment entity.
     *
     */
    public function editAction($id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Payment') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Payment entity.');
        }

        $entity -> setDate($entity -> getPayTra() -> getTraDate());
        $entity -> setDestinationAccount($entity -> getPayTra() -> getTraAccDebit());
        $entity -> setAmount($entity -> getPayTra() -> getTraAmount());
        if ($entity -> getPayTraInterest() != NULL) {
            $entity -> setInterest($entity -> getPayTraInterest() -> getTraAmount());
        } else {
            $entity -> setInterest(0);
        }

        $editForm = $this -> createEditForm($entity);
        $deleteForm = $this -> createDeleteForm($id);

        return $this -> render('ZahlerBundle:Payment:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Creates a form to edit a Payment entity.
     *
     * @param Payment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Payment $entity) {
        $form = $this -> createForm(new PaymentType(), $entity, array('action' => $this -> generateUrl('payment_update', array('id' => $entity -> getId())), 'method' => 'PUT', ));

        $form -> add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Payment entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this -> getDoctrine() -> getManager();

        $entity = $em -> getRepository('ZahlerBundle:Payment') -> find($id);

        if (!$entity) {
            throw $this -> createNotFoundException('Unable to find Payment entity.');
        }

        $deleteForm = $this -> createDeleteForm($id);
        $editForm = $this -> createEditForm($entity);
        $editForm -> handleRequest($request);

        if ($editForm -> isValid()) {
            $transaction = $entity -> getPayTra();
            $transaction -> setTraAccCredit($em -> getReference('ZahlerBundle:Account', Loan::LOAN_ACCOUNT));
            $transaction -> setTraAccDebit($entity -> getDestinationAccount());
            $transaction -> setTraAmount($entity -> getAmount());
            $transaction -> setTraDate($entity -> getDate());
            $transaction -> setTraDescription('Payment from ' . $entity -> getPayLoa() -> getLoaPer() -> getPerName());

            if ($entity -> getInterest() > 0) {
                $interestTransaction = $entity -> getPayTraInterest();
                if ($interestTransaction == NULL) {
                    $interestTransaction = new Transaction();
                }
                $interestTransaction -> setTraAccCredit($em -> getReference('ZahlerBundle:Account', Payment::INTEREST_ACCOUNT));
                $interestTransaction -> setTraAccDebit($entity -> getDestinationAccount());
                $interestTransaction -> setTraAmount($entity -> getInterest());
                $interestTransaction -> setTraDate($entity -> getDate());
                $interestTransaction -> setTraDescription('Interest from ' . $entity -> getPayLoa() -> getLoaPer() -> getPerName());

                $em -> persist($interestTransaction);
                $entity -> setPayTraInterest($interestTransaction);
            } else {
                $interestTransaction = $entity -> getPayTraInterest();
                if ($interestTransaction != NULL) {
                    $entity -> setPayTraInterest(NULL);
                    $em -> remove($interestTransaction);
                }
            }

            $em -> flush();

            return $this -> redirect($this -> generateUrl('payment_edit', array('id' => $id)));
        }

        return $this -> render('ZahlerBundle:Payment:edit.html.twig', array('entity' => $entity, 'edit_form' => $editForm -> createView(), 'delete_form' => $deleteForm -> createView(), ));
    }

    /**
     * Deletes a Payment entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this -> createDeleteForm($id);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();
            $entity = $em -> getRepository('ZahlerBundle:Payment') -> find($id);

            if (!$entity) {
                throw $this -> createNotFoundException('Unable to find Payment entity.');
            }

            $em -> remove($entity);
            $em -> remove($entity -> getPayTra());
            $em -> remove($entity -> getPayTraInterest());
            $em -> flush();
        }

        return $this -> redirect($this -> generateUrl('payment'));
    }

    /**
     * Creates a form to delete a Payment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this -> createFormBuilder() -> setAction($this -> generateUrl('payment_delete', array('id' => $id))) -> setMethod('DELETE') -> add('submit', 'submit', array('label' => 'Delete')) -> getForm();
    }

}
