<?php

interface ConstantList {
    public static function isValid($value);
    public static function numConstants();
}

final class Table implements ConstantList {
    // Hardcode in wpdb->prefix since can't assign variables to consts
    const Applicants = 'wp_je_worker_applicants';
    const Workers    = 'wp_je_workers';

    private function Table() {}

    public static function isValid($value) {
        switch ($value) {
            case Applicants:
            case Workers:
                return true;
        }
        return false;
    }
    
    public static function numConstants() {
        return 2;
    }
}

/* Maybe remove this in favour of a configurable list of worker types.
*/
final class WorkerType implements ConstantList {
    const Bar          = 0;
    const FoodAndDrink = 1;
    const Recycling    = 2;
    const Security     = 3;
    const StageManager = 4;
    const Steward      = 5;
    const Supervisor   = 6;

    private function WorkerType() {}

    public static function isValid($value) {
        switch ($value) {
            case Bar:
            case FoodAndDrink:
            case Recycling:
            case Security:
            case StageManager:
            case Steward:
            case Supervisor:
                return true;
        }
        return false;
    }
    
    public static function numConstants() {
        return 8;
    }
}

final class Shift implements ConstantList {
    const FirstHalf  = 0;
    const SecondHalf = 1;
    const FullNight  = 2;

    private function Shift() {}

    public static function isValid($value) {
        switch ($value) {
            case FirstHalf:
            case SecondHalf:
            case FullNight:
                return true;
        }
        return false;
    }
    
    public static function numConstants() {
        return 3;
    }
}

final class College implements ConstantList {
    const None          = '-';
    const Christs       = 'Christ\'s';
    const Clare         = 'Clare';
    const Corpus        = 'Corpus Christi';
    const Churchill     = 'Churchill';
    const Downing       = 'Downing';
    const Emma          = 'Emmanuel';
    const Fitz          = 'Fitzwilliam';
    const Girton        = 'Girton';
    const Caius         = 'Gonville and Caius';
    const Homerton      = 'Homerton';
    const HughesHall    = 'Hughes Hall';
    const Robinson      = 'Robinson';
    const Jesus         = 'Jesus';
    const Kings         = 'King\'s';
    const LucyCavendish = 'Lucy Cavendish';
    const Magdelene     = 'Magdalene';
    const MurrayEdwards = 'Murray Edwards';
    const Newnham       = 'Newnham';
    const Pembroke      = 'Pembroke';
    const Peterhouse    = 'Peterhouse';
    const Queens        = 'Queens\''; 
    const Selwyn        = 'Selwyn';
    const Sidney        = 'Sidney Sussex';
    const Catz          = 'St Catharine\'s';
    const StEdmunds     = 'St Edmund\'s';
    const Johns         = 'St John\'s';
    const Trinity       = 'Trinity';
    const TitHall       = 'Trinity Hall';
    const Wolfson       = 'Wolfson';

    private function College() {}

    public static function isValid($value) {
        switch ($value) {
            case None:
            case Christs:
            case Clare:
            case Corpus:
            case Churchill:
            case Downing:
            case Emma:
            case Fitz:
            case Girton:
            case Caius:
            case Homerton:
            case HughesHall:
            case Robinson:
            case Jesus:
            case Kings:
            case LucyCavendish:
            case Magdelene:
            case MurrayEdwards:
            case Newnham:
            case Pembroke:
            case Peterhouse:
            case Queens:
            case Selwyn:
            case Sidney:
            case Catz:
            case StEdmunds:
            case Johns:
            case Trinity:
            case TitHall:
            case Wolfson:
                return true;
        }
        return false;
    }
    
    public static function numConstants() {
        return 30;
    }
}

?>
