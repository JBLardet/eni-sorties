<?php


namespace App\Form\model;

use Symfony\Component\Form\FormTypeInterface;

class AnnulationFormModel
{
    public $motif;

    /**
     * @return mixed
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * @param mixed $motif
     */
    public function setMotif($motif): void
    {
        $this->motif = $motif;
    }

}
