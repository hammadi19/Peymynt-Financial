
'use strict';

requirejs.config({
    "baseUrl": "../../pey-framework",
    "paths": {
        "lodash" : "../assets/vendor/lodash.min",
        "ui_framework" : "app/UIFramework",
        "base" : "app/Base/Base",
        "data_bucket" : "app/Base/DataBucket",
        "form_row": "app/FormRow",
        "product" : "app/RowFields/Product",
        "description" : "app/RowFields/Description",
        "price" : "app/RowFields/Price",
        "quantity" : "app/RowFields/Quantity",
        "tax" : "app/RowFields/Tax",
        "amount" : "app/RowFields/Amount",
        "tax_panel" : "app/Stat/TaxPanel",
        "app"  : "App"
    }
});
