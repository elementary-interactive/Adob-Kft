<?php

namespace App\Models;

enum Columns: string {
    case PRODUCT_ID                 = 'cikkszam';
    case PRODUCT_NAME               = 'megnevezes';
    case BRAND                      = 'marka';
    case PRICE                      = 'webar';
    case DESCRIPTION                = 'leiras';
    case PACKAGING                  = 'csomagolas';
    case EAN                        = 'ean';
    case PRODUCT_NUMBER             = 'termekszam';
    case ON_SALE                    = 'akcios';
    case MAIN_CATEGORY              = 'main_kat';
    case COMMAND                    = 'web';
    case SUB_CATEGORY               = 'alkat';
    
}

/** 
 * @deprecated
 * case DESCRIPTION_UPDATE         = 'webleir';
 * case DESCRIPTION_TO_CATEGORY    = 'webkatleir';
*/
    