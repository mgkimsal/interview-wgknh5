<?php

namespace Collegeplannerpro\InterviewReport;

class Repository
{
    public function __construct(private \mysqli $db) {}

    public function allInvoices(): \mysqli_result
    {

        return $this->db->query(<<<SQL
            SELECT i.*, c.first_name, c.last_name,
            temptable.total_payments AS amount_paid,
            total - temptable.total_payments AS balance
            FROM invoices i
            left join
                (SELECT p.invoice_id, SUM(amount) as total_payments 
                 FROM payments p group by p.invoice_id) temptable
                on temptable.invoice_id=i.invoice_id
            NATURAL JOIN contacts c
            ORDER BY i.issued_at
SQL
        );
    }

    public function invoicePayments($invoiceId): \mysqli_result
    {
        return $this->db->query(
            "SELECT * FROM payments WHERE invoice_id = $invoiceId ORDER BY paid_at ASC"
        );
    }

    public function allInvoicePaymentsByInvoiceId(): array
    {
        $rows = $this->db->query(
            "SELECT * FROM payments ORDER BY paid_at ASC"
        );
        $paymentsById = [];
        while($r = $rows->fetch_assoc()) {
            if(!array_key_exists($r['invoice_id'], $paymentsById)) {
                $paymentsById[$r['invoice_id']] = [];
            }
            $paymentsById[$r['invoice_id']][] = $r;
        }

        return $paymentsById;
    }


    public function contactDetails(int $contactId): ?array
    {
        return $this->db->query(
            "SELECT * FROM contacts WHERE contact_id = $contactId LIMIT 1"
        )->fetch_assoc() ?: null;
    }
}
