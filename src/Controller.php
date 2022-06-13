<?php

namespace Collegeplannerpro\InterviewReport;

use Psr\Container\ContainerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Controller
{
    private \Twig\Environment $twig;
    private Repository $repository;

    public function __construct(ContainerInterface $container) {
        $this->twig = $container->get('twig');
        $this->repository = $container->get('repository');
    }

    public function home(Request $request, Response $response): Response
    {
        $response->getBody()->write($this->twig->render('home.html.twig'));
        return $response;
    }

    public function paymentsReport(Request $request, Response $response): Response
    {
        $invoiceResult = $this->repository->allInvoices();
        $invoices = [];

        // to make faster, let's do one query to grab all invoice payments, and group them by invoice id?
        $paymentsById = $this->repository->allInvoicePaymentsByInvoiceId();

        while ($invoice = $invoiceResult->fetch_assoc()) {
            // if we have no payments for this invoice_id, just return an empty array
            $invoice['payments'] = $paymentsById[$invoice['invoice_id']] ?? [];
            $invoices[] = $invoice;
        }

        $content = $this->twig->render('paymentsReport.html.twig', [
            'invoices' => $invoices,
        ]);

        $response->getBody()->write($content);
        return $response;
    }
}
