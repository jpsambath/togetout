<?php


namespace App\DBAL\Types;


class EtatEnum extends AbstractEnumType
{
    public const CREE = 'Créée';
    public const OUVERTE = 'Ouverte';
    public const CLOTUREE = 'Cloturée';
    public const ACTIVITE_EN_COURS = 'Activité en cours';
    public const PASSEE = 'Passée';
    public const ANNULEE = 'Annulée';

    protected static $choices = [
        self::CREE => 'Créée',
        self::OUVERTE => 'Ouverte',
        self::CLOTUREE => 'Cloturée',
        self::ACTIVITE_EN_COURS => 'Activité en cours',
        self::PASSEE => 'Passée',
        self::ANNULEE => 'Annulée'
    ];
}