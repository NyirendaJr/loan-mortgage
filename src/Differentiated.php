<?php

namespace Mortgage;

use Cassandra\Collection;
use Mortgage\Support\EffectiveRate;
use Mortgage\Contracts\RepaymentSchedule;

class Differentiated extends Mortgage
{
    /**
     * This object will produce
     * a repayment schedule for each month
     *
     * @var object
     */
    private $repaymentSchedule;

    /**
     * This object will calculate
     * the annual effective rate
     *
     * @var object
     */
    private $effectiveRate;

    /**
     * Initialize the default value and inject schedule with effective Rate
     *
     * @param RepaymentSchedule $repaymentSchedule
     * @param EffectiveRate $effectiveRate
     */
    function __construct(RepaymentSchedule $repaymentSchedule, EffectiveRate $effectiveRate)
    {
        parent::__construct(config('mortgage.loanTerm'), config('mortgage.loanAmount'), config('mortgage.interestRate'));

        $this->repaymentSchedule = $repaymentSchedule->toCompute($this);

        $this->effectiveRate     = $effectiveRate;
    }

    /**
     * Displays the repayment schedule for the entire period
     *
     * @return Collection
     */
    public function showRepaymentSchedule() : Collection
    {
        return collect($this->repaymentSchedule['repaymentScheduleResult']);
    }

    /**
     * Total amount in percent
     *
     * @return integer
     */
    public function getPercentAmount() : int
    {
       return $this->repaymentSchedule['totalPercentDept'];
    }

    /**
     * Effective rate in percent
     *
     * @return integer
     */
    public function effectiveRate() : int
    {
        return $this->effectiveRate->toCompute($this->repaymentSchedule['deptValues']);
    }

    /**
     * Total amount with percent
     * in the other words it [ percent dept + loan amount]
     *
     * @return integer
     */
    public function getTotalamount() : int
    {
        return $this->repaymentSchedule['totalPercentDept'] + $this->loanAmount;
    }

    /**
     * mortgageType
     *
     * @return string
     */
    public function mortgageType() : string
    {
        return 'Differentiated Payment';
    }
}



