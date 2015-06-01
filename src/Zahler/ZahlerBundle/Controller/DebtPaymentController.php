<?php

namespace Zahler\ZahlerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zahler\ZahlerBundle\Entity\DebtPayment;
use Zahler\ZahlerBundle\Form\DebtPaymentType;
use Zahler\ZahlerBundle\Entity\Debt;
use Zahler\ZahlerBundle\Entity\Transaction;

use \DateTime;

/**
 * DebtPayment controller.
 *
 */
class DebtPaymentController extends Controller
{
    public function retrieveAction(Request $request) {
        $get = $request -> query -> all();
        $em = $this -> getDoctrine() -> getManager();

        $qb = $em -> createQueryBuilder();
        $qb -> select('COUNT(dep)');
        $qb -> from('ZahlerBundle:DebtPayment', 'dep');
        $qb -> where("dep.depDeb = {$get['deb_id']}");

        $query = $qb -> getQuery();
        $count = $query -> getSingleScalarResult();

        $sql = "SELECT debt_payment.id AS pay_id, *
        FROM debt_payment, transaction
        WHERE dep_tra_id = transaction.id AND
        dep_deb_id = {$get['deb_id']}
        ORDER BY tra_date, debt_payment.id ASC
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
     * Lists all DebtPayment entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ZahlerBundle:DebtPayment')->findAll();

        return $this->render('ZahlerBundle:DebtPayment:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new DebtPayment entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DebtPayment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $transaction = new Transaction();
            $transaction -> setTraAccCredit($entity -> getSourceAccount());
            $transaction -> setTraAccDebit($em -> getReference('ZahlerBundle:Account', Debt::DEBT_ACCOUNT));
            $transaction -> setTraAmount($entity -> getAmount());
            $transaction -> setTraDate($entity -> getDate());
            $transaction -> setTraDescription('Payment to ' . $entity -> getDepDeb() -> getDebPer() -> getPerName());

            $em -> persist($transaction);

            $entity -> setDepTra($transaction);

            if ($entity -> getInterest() > 0) {
                $interestTransaction = new Transaction();
                $interestTransaction -> setTraAccCredit($entity -> getSourceAccount());
                $interestTransaction -> setTraAccDebit($em -> getReference('ZahlerBundle:Account', DebtPayment::INTEREST_ACCOUNT));
                $interestTransaction -> setTraAmount($entity -> getInterest());
                $interestTransaction -> setTraDate($entity -> getDate());
                $interestTransaction -> setTraDescription('Interest to ' . $entity -> getDepDeb() -> getDebPer() -> getPerName());

                $em -> persist($interestTransaction);
                $entity -> setDepTraInterest($interestTransaction);
            }
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('debtpayment_show', array('id' => $entity->getId())));
        }

        return $this->render('ZahlerBundle:DebtPayment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DebtPayment entity.
     *
     * @param DebtPayment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DebtPayment $entity)
    {
        $form = $this->createForm(new DebtPaymentType(), $entity, array(
            'action' => $this->generateUrl('debtpayment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new DebtPayment entity.
     *
     */
    public function newAction()
    {
        $entity = new DebtPayment();
        $entity->setDate(new DateTime('now'));
        $form   = $this->createCreateForm($entity);

        return $this->render('ZahlerBundle:DebtPayment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DebtPayment entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZahlerBundle:DebtPayment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DebtPayment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZahlerBundle:DebtPayment:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DebtPayment entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZahlerBundle:DebtPayment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DebtPayment entity.');
        }
        
        $entity -> setDate($entity -> getDepTra() -> getTraDate());
        $entity -> setSourceAccount($entity -> getDepTra() -> getTraAccCredit());
        $entity -> setAmount($entity -> getDepTra() -> getTraAmount());
        if ($entity -> getDepTraInterest() != NULL) {
            $entity -> setInterest($entity -> getDepTraInterest() -> getTraAmount());
        } else {
            $entity -> setInterest(0);
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZahlerBundle:DebtPayment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a DebtPayment entity.
    *
    * @param DebtPayment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DebtPayment $entity)
    {
        $form = $this->createForm(new DebtPaymentType(), $entity, array(
            'action' => $this->generateUrl('debtpayment_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing DebtPayment entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZahlerBundle:DebtPayment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DebtPayment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $transaction = $entity -> getDepTra();
            $transaction -> setTraAccCredit($entity -> getSourceAccount());
            $transaction -> setTraAccDebit($em -> getReference('ZahlerBundle:Account', Debt::DEBT_ACCOUNT));
            $transaction -> setTraAmount($entity -> getAmount());
            $transaction -> setTraDate($entity -> getDate());
            $transaction -> setTraDescription('Payment to ' . $entity -> getDepDeb() -> getDebPer() -> getPerName());

            if ($entity -> getInterest() > 0) {
                $interestTransaction = $entity -> getDepTraInterest();
                if ($interestTransaction == NULL) {
                    $interestTransaction = new Transaction();
                }
                $interestTransaction -> setTraAccCredit($entity -> getSourceAccount());
                $interestTransaction -> setTraAccDebit($em -> getReference('ZahlerBundle:Account', DebtPayment::INTEREST_ACCOUNT));
                $interestTransaction -> setTraAmount($entity -> getInterest());
                $interestTransaction -> setTraDate($entity -> getDate());
                $interestTransaction -> setTraDescription('Interest to ' . $entity -> getDepDeb() -> getDebPer() -> getPerName());

                $em -> persist($interestTransaction);
                $entity -> setDepTraInterest($interestTransaction);
            } else {
                $interestTransaction = $entity -> getPayTraInterest();
                if ($interestTransaction != NULL) {
                    $entity -> setDepTraInterest(NULL);
                    $em -> remove($interestTransaction);
                }
            }
            
            $em->flush();

            return $this->redirect($this->generateUrl('debtpayment_edit', array('id' => $id)));
        }

        return $this->render('ZahlerBundle:DebtPayment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a DebtPayment entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ZahlerBundle:DebtPayment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DebtPayment entity.');
            }

            $em->remove($entity);
            $em -> remove($entity -> getDepTra());
            $em -> remove($entity -> getDepTraInterest());
            $em->flush();
        }

        return $this->redirect($this->generateUrl('debtpayment'));
    }

    /**
     * Creates a form to delete a DebtPayment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('debtpayment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
