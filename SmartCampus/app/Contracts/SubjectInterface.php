<?php

namespace App\Contracts;

interface SubjectInterface
{
    public function attach(ObserverInterface $observer): void;
    public function detach(ObserverInterface $observer): void;
    public function notifyObservers(array $targetStudentIds): void;
}