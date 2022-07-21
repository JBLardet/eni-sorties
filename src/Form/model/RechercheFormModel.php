<?php

namespace App\Form\model;

class RechercheFormModel
{
    public $campus;
    public $rechercheParNom;
    public $dateMin;
    public $dateMax;
    public $organisateur;
    public $participant;
    public $nonParticipant;
    public $sortiesPassees;

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getRechercheParNom()
    {
        return $this->rechercheParNom;
    }

    /**
     * @param mixed $rechercheParNom
     */
    public function setRechercheParNom($rechercheParNom): void
    {
        $this->rechercheParNom = $rechercheParNom;
    }

    /**
     * @return mixed
     */
    public function getDateMin()
    {
        return $this->dateMin;
    }

    /**
     * @param mixed $dateMin
     */
    public function setDateMin($dateMin): void
    {
        $this->dateMin = $dateMin;
    }

    /**
     * @return mixed
     */
    public function getDateMax()
    {
        return $this->dateMax;
    }

    /**
     * @param mixed $dateMax
     */
    public function setDateMax($dateMax): void
    {
        $this->dateMax = $dateMax;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return mixed
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param mixed $participant
     */
    public function setParticipant($participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return mixed
     */
    public function getNonParticipant()
    {
        return $this->nonParticipant;
    }

    /**
     * @param mixed $nonParticipant
     */
    public function setNonParticipant($nonParticipant): void
    {
        $this->nonParticipant = $nonParticipant;
    }

    /**
     * @return mixed
     */
    public function getSortiesPassees()
    {
        return $this->sortiesPassees;
    }

    /**
     * @param mixed $sortiesPassees
     */
    public function setSortiesPassees($sortiesPassees): void
    {
        $this->sortiesPassees = $sortiesPassees;
    }




}