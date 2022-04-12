<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderDocument;
use App\Entity\OrderState;
use App\Form\AdminType\AdminChangeStateOrderType;
use App\Form\AdminType\OrderDocumentType;
use App\Repository\OrderRepository;
use App\Repository\StateRepository;
use App\Services\OrderManager;
use App\Services\SetupService;
use App\Services\UserNotifierService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route("/admin/commandes")]
class AdminOrderController extends AbstractController
{

    public function __construct(
    )
    {
    }

    #[Route("", name: "admin.order.index")]
    public function index(Request $request, OrderRepository $orderRepository, StateRepository $stateRepository): Response
    {
        $filter = $request->get("filter");

        $orders = $orderRepository->findAll();

        if(!is_null($filter)) {
            $filterState = $stateRepository->findOneBySlug($filter);
            if (is_null($filterState)) {
                return $this->redirectToRoute('admin.order.index', [], Response::HTTP_SEE_OTHER);
            }
            else{
                $orders = $orderRepository->findByState($filterState);
            }
        }



        return $this->render('admin/orders/index.html.twig', [
            'menu' => 'admin.order',
            'orders' => $orders,
            'states' => $stateRepository->findAll(),
            'filter' => $filter
        ]);
    }

    #[Route("/pdf/1/{id}", name: "admin.order.document.1")]
    public function generateOrderDocument1(Order $order)
    {

        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Ubuntu');

        $pdf = new Dompdf($pdfOptions);

        $svg = '<svg width="310" height="500" viewBox="0 0 310 500" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_5_2)">
<path d="M194.29 362.34L271.27 438.83L210.48 500L82.91 373.26L0 290.88H121.01C121.57 290.88 122.12 290.88 122.66 290.86" fill="#060400"/>
<path d="M122.66 290.86C178.41 289.98 223.32 244.51 223.32 188.56C223.32 132.04 177.51 86.23 121.01 86.23H104.56V0.72C107.28 0.47 110.02 0.3 112.78 0.19C115.5 0.05 118.26 0 121.01 0C225.15 0 309.57 84.42 309.57 188.56C309.57 266.7 262.02 333.73 194.28 362.34L122.66 290.86Z" fill="#FABA15"/>
</g>
<defs>
<clipPath id="clip0_5_2">
<rect width="309.58" height="500" fill="white"/>
</clipPath>
</defs>
</svg>
';

        $logo = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '">';

        $content = $this->renderView('pdf/bdc_1.html.twig', [
            'order' => $order,
            'logo' => $logo
        ]);

        $pdf->loadHtml($content);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $pdf->stream("Bon de commande ". $order->getInitialZeroId() . ".pdf", [
            "Attachment" => true
        ]);

    }

    #[Route("/pdf/2/{id}", name: "admin.order.document.2")]
    public function generateOrderDocument2(Order $order)
    {

        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Ubuntu');

        $pdf = new Dompdf($pdfOptions);

        $content = $this->renderView('pdf/bdc_2.html.twig', [
            'order' => $order
        ]);

        $pdf->loadHtml($content);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $pdf->stream("Bon de récupération_". $order->getInitialZeroId() . ".pdf", [
            "Attachment" => true
        ]);

    }

    #[Route("/{id}/order-document-form", name: "admin.order.upload.document", methods: ["POST", "GET"])]
    public function newOrderDocument(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        $document = new OrderDocument();
        $form = $this->createForm(OrderDocumentType::class, $document, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.upload.document', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $document->setCreatedBy($this->getUser());
            $document->setDocumentOrder($order);
            $slugger = new AsciiSlugger();
            $document->setSlug($slugger->slug($document->getTitle() . "-" . $order->getInitialZeroId()));

            $entityManager->persist($document);
            $entityManager->flush();

            return $this->redirectToRoute("admin.order.show", ["id" => $order->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('admin/orders/document_form.html.twig', [
            'form' => $form
        ]);

    }

    #[Route("/change-state/{slug}/{id}", name: "admin.order.change_state")]
    public function changeState(Order $order, string $slug, Request $request, OrderManager $orderManager, EntityManagerInterface $entityManager, UserNotifierService $userNotifier): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.change_state', ['slug' => $slug, 'id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($orderManager->changeState($slug, $orderState, $order) !== false){
                $entityManager->persist($orderState);
                $entityManager->flush();

                if($orderState->getState()->getSlug() === "error"){
                    $userNotifier->clientOrderError($order->getId());
                }
                else{
                    $userNotifier->clientOrderStatusChange($order->getId());
                }

            }
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Changement de statut de la commande"
        ]);
    }

    #[Route("/{id}", name: "admin.order.show")]
    public function show(Order $order, StateRepository $stateRepository): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'menu' => 'admin.order',
            'order' => $order,
            'states' => $stateRepository->findAll()
        ]);
    }

}