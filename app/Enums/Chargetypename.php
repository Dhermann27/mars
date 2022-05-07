<?php


namespace App\Enums;


enum Chargetypename: int
{
    const Fees = 1000;
    const Deposit = 1003;
    const Staffcredit = 1021;
    const Workshopfee = 1002;
    const Earlyarrival = 1014;
    const Donation = 1008;
    const PayPalServiceCharge = 1023;
    const PayPalPayment = 1005;
    const CheckPayment = 1006;
    const CreditCardPayment = 1007;
}
