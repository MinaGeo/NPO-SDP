<?php
require_once 'DonationTemplate.php';
require_once 'DonationModel.php';
require_once './models/notifications/NotificationToMailAdapter.php';

class MonetaryDonationProcessor extends DonationTemplate
{
    private Donation $donation;
    private IPay $paymentStrategy;

    public function __construct(Donation $donation, IPay $paymentStrategy)
    {
        $this->donation = $donation;
        $this->paymentStrategy = $paymentStrategy;
    }

    protected function processPayment()
    {
        $paymentContext = new PaymentContext();
        $paymentContext->setStrategy($this->paymentStrategy);
        $paymentContext->doPayment($this->donation->getDonationAmount(), "Monetary Donation Payment");
        echo json_encode(['success' => true, 'Popup' => false]);
    }

    protected function sendReceipt(string $donatorName)
    {
        $email = new NotificationToMailAdapter();
        $email->sendNotification("Monetary Donation!", $donatorName . ": has donated " . $this->donation->getDonationAmount());
    }
}
