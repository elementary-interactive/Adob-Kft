<?php

namespace App\Models\Columns;

enum ADOBProductsImportColumns: string {
    case PRODUCT_ID                 = 'cikkszám';
    case PRODUCT_NAME               = 'megnevezés';
    case BRAND                      = 'márka';
    case PRICE                      = 'webár';
    case DESCRIPTION                = 'leírás';
    case PACKAGING                  = 'csomagolás';
    case EAN                        = 'ean';
    case PRODUCT_NUMBER             = 'termékszám';
    case ON_SALE                    = 'akciós';
    case MAIN_CATEGORY              = 'main kat';
    case COMMAND                    = 'web';
    case SUB_CATEGORY               = 'alkat';
    case IMAGES                     = 'képek';
}

/** 
 * @deprecated
 * case DESCRIPTION_UPDATE         = 'webleir';
 * case DESCRIPTION_TO_CATEGORY    = 'webkatleir';
*/
    