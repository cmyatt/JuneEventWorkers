<?php

global $wpdb;

interface ConstantList {
    public static function isValid($value);
}

final class Table implements ConstantList {
    const Applicants = $wpdb->prefix . 'je_worker_applicants';
    const Workers    = $wpdb->prefix . 'je_workers';

    private Table() {}

    public static function isValid($value) {
        switch ($value) {
            case Applicants:
            case Workers:
                return true;
        }
        return false;
    }
}

/* Maybe remove this in favour of a configurable list of worker types.
*/
final class WorkerType implements ConstantList {
    const None         = 0;
    const Bar          = 1;
    const FoodAndDrink = 2;
    const Recycling    = 3;
    const Security     = 4;
    const StageManager = 5;
    const Steward      = 6;
    const Supervisor   = 7;

    private WorkerType() {}

    public static function isValid($value) {
        switch ($value) {
            case None:
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
}

final class Shift implements ConstantList {
    const FirstHalf  = 1;
    const SecondHalf = 2;

    private Shift() {}

    public static function isValid($value) {
        switch ($value) {
            case FirstHalf:
            case SecondHalf:
                return true;
        }
        return false;
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

    private College() {}

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
}

?>
