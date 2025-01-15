<?php

declare(strict_types=1);
ob_start();
require_once "./db_setup.php";
ob_end_clean();

/*==================================================================================================*/
/*                                      IPay Interface                                              */
/*==================================================================================================*/
interface IPay {
    public function pay($paymentId): bool;
}

/*==================================================================================================*/
/*                                    Paypal Concrete Class                                         */
/*==================================================================================================*/
class PayByPaypal implements IPay {
    private string $email;
    private string $password;
    public string $paymentType = 'paypal';

    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function pay($paymentId): bool {
        global $configs;
        global $conn;
        $paypalData = [
            'paymentId' => $paymentId,
            'paypalEmail' => $this->email,
            'paypalPassword' => $this->password
        ];
        $columns = implode(", ", array_keys($paypalData));
        $values = implode("', '", array_values($paypalData));
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_PAYPAL_TABLE ($columns) VALUES ('$values')";
        return $conn->run_query($query);
    }
}

/*==================================================================================================*/
/*                                 Credit Card Concrete Class                                       */
/*==================================================================================================*/
class PayByCreditCard implements IPay{
    private string $number;
    private string $cvv;
    private string $expiryDate;
    public string $paymentType = 'creditCard';

    public function __construct(string $number, string $cvv, string $expiryDate) {
        $this->number = $number;
        $this->cvv = $cvv;
        $this->expiryDate = $expiryDate;
    }

    public function pay($paymentId): bool {
        global $configs;
        global $conn;
        $paypalData = [
            'paymentId' => $paymentId,
            'cardNumber' => $this->number,
            'cvv' => $this->cvv,
            'expiryDate' => $this->expiryDate
        ];
        $columns = implode(", ", array_keys($paypalData));
        $values = implode("', '", array_values($paypalData));
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_CREDIT_TABLE ($columns) VALUES ('$values')";
        return $conn->run_query($query);
    }
}

/*==================================================================================================*/
/*                               Payment Context Concrete Class                                     */
/*==================================================================================================*/
class PaymentContext {
    private IPay $strategy; 

    public function setStrategy(IPay $strategy): void {
        $this->strategy = $strategy;
    }

    public function doPayment(float $amount, string $description): bool {
        global $configs;
        global $conn;
        $paymentData = [
            'userId' => $_SESSION['USER_ID'],
            'amount' => $amount,
            'description' => $description,
            'paymentType' => $this->strategy->paymentType
        ];
        $columns = implode(", ", array_keys($paymentData));
        $values = implode("', '", array_values($paymentData));
        $query = "INSERT INTO $configs->DB_NAME.$configs->DB_PAYMENTS_TABLE ($columns) VALUES ('$values')";
        if($conn->run_query($query)){
            $paymentId = Database::getInstance()->getConnection()->insert_id;
            return $this->strategy->pay($paymentId);
        }
    }
}

?>