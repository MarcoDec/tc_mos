<?php

namespace App\Entity\Interfaces;

interface BarCodeInterface {
    /** @var string Préfixe du code barre des composants */
    public const COMPONENT_BAR_CODE_TABLE_NUMBER = '01';

    /** @var string Préfixe du code barre des employés */
    public const EMPLOYEE_BAR_CODE_TABLE_NUMBER = '03';

    /** @var string Préfixe du code barre des machines */
    public const ENGINE_BAR_CODE_TABLE_NUMBER = '04';

    /** @var string Préfix du code barre des OF */
    public const MANUFACTURING_ORDER_BAR_CODE_PREFIX = '06';

    /** @var string Préfixe du code barre des produits */
    public const PRODUCT_BAR_CODE_TABLE_NUMBER = '02';

    /** @var string Préfix du code barre des stocks */
    public const STOCK_BAR_CODE_PREFIX = '05';

    /**
     * Retourne le numéro de la table à mettre en préfixe du code barre.
     *
     * Aujourd'hui, sont prévus :<ul>
     * <li>01 – composants</li>
     * <li>02 – produits</li>
     * <li>03 – employés</li>
     * <li>04 – machines</li>
     * <li>05 – stocks</li>
     * </ul>
     *
     * Cette méthode doit retourner l'une des constantes définies dans l'interface.
     *
     * Attention ! Le résultat doit être sur exactement deux digits !
     *
     * @return string numéro de table
     */
    public static function getBarCodeTableNumber(): string;

    /**
     * Retourne le code barre de l'entité.
     *
     * @return string [getBarCodeTableNumber]-[barcode]
     *
     * @see BarCodeInterface::getBarCodeTableNumber
     */
    public function getBarCode(): string;
}
