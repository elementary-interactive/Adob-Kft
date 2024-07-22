<?php

namespace App\Models\Columns;

enum ADOBProductsExportColumns: string
{
    case PRODUCT_ID                 = 'cikkszam';
    case PRODUCT_NAME               = 'terméknév';
    case BRAND_NAME                 = 'márka';
    case PRODUCT_EAN                = 'ean';
    case PRODUCT_PRICE              = 'webár';
    case PRODUCT_MAIN_CATEGORY      = 'fő kategória';
    case PRODUCT_CATEGORIES         = 'kategóriák';
    case PRODUCT_URL                = 'link';
    case IMAGE_COUNT                = 'Kép darabszám';
    case IMAGE_SIZES                = 'Kép méretek';
    case IMAGE_SIZE_SUM             = 'Kép méret összesen';
    case PRODUCT_STATUS             = 'státusz (1: aktív 0:inaktív)';
    case IMAGE_LINKS                = 'Kép linkek';
    case PRODUCT_DESCRIPTION        = 'Termék leírás';
}

/** 
 * @deprecated
 * case DESCRIPTION_UPDATE         = 'webleir';
 * case DESCRIPTION_TO_CATEGORY    = 'webkatleir';
 */
