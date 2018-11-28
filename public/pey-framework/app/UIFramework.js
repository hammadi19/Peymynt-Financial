

define(['lodash','form_row','base','data_bucket','tax_panel'],
    function (_,FormRow, Base, DataBucket, TaxPanel) {

        // blank constructor
        var UIFramework = function(){};

        var currency_symbols = {
            "c377" : "AED",
            "c1" : "؋",
            "c5" : "Lek",
            "c20" : "AMD",
            "c101" : "ƒ",
            "c12" : "AOA",
            "c18" : "$",
            "c24" : "$",
            "c22" : "ƒ",
            "c399" : "₼",
            "c51" : "KM",
            "c35" : "$",
            "c33" : "BDT",
            "c62" : "лв",
            "c31" : "BHD",
            "c65" : "BIF",
            "c44" : "$",
            "c60" : "$",
            "c48" : '$b',
            "c57" : "R$",
            "c29" : "$",
            "c46" : "BTN",
            "c53" : "P",
            "c37" : "Br",
            "c40" : "BZ$",
            "c71" : "$",
            "c90" : "CDF",
            "c213" : "CHF",
            "c79" : "$",
            "c81" : "¥",
            "c85" : "$",
            "c94" : "₡",
            "c99" : "₱",
            "c73" : "CVE",
            "c104" : "Kč",
            "c108" : "DJF",
            "c106" : "kr",
            "c111" : "RD$",
            "c7" : "DZD",
            "c400" : "EEK",
            "c114" : "£",
            "c118" : "ERN",
            "c121" : "ETB",
            "c3" : "€",
            "c126" : "$",
            "c123" : "FKP",
            "c151" : "£",
            "c137" : "GEL",
            "c140" : "¢",
            "c142" : "£",
            "c135" : "GMD",
            "c153" : "GNF",
            "c149" : "Q",
            "c401" : "GWP",
            "c156" : "$",
            "c164" : "$",
            "c162" : "L",
            "c97" : "kn",
            "c158" : "HTG",
            "c166" : "Ft",
            "c172" : "Rp",
            "c180" : "₪",
            "c170" : "INR",
            "c176" : "IQD",
            "c174" : "﷼",
            "c168" : "kr",
            "c183" : "J$",
            "c188" : "JOD",
            "c185" : "¥",
            "c192" : "KES",
            "c200" : "лв",
            "c67" : "៛",
            "c87" : "KMF",
            "c195" : "KRW",
            "c198" : "KWD",
            "c75" : "$",
            "c190" : "лв",
            "c202" : "₭",
            "c205" : "£",
            "c337" : "₨",
            "c209" : "$",
            "c207" : "LSL",
            "c402" : "LTL",
            "c403" : "LVL",
            "c211" : "LYD",
            "c248" : "MAD",
            "c241" : "MDL",
            "c221" : "MGA",
            "c219" : "ден",
            "c252" : "MMK",
            "c244" : "₮",
            "c217" : "MOP",
            "c404" : "MRO",
            "c233" : "MRU",
            "c235" : "₨",
            "c227" : "MVR",
            "c223" : "MWK",
            "c238" : "$",
            "c225" : "RM",
            "c250" : "MT",
            "c254" : "$",
            "c265" : "₦",
            "c262" : "C$",
            "c55" : "kr",
            "c257" : "₨",
            "c92" : "$",
            "c271" : "﷼",
            "c276" : "B/.",
            "c282" : "S/.",
            "c278" : "PGK",
            "c284" : "₱",
            "c273" : "₨",
            "c287" : "zł",
            "c280" : "Gs",
            "c291" : "﷼",
            "c294" : "lei",
            "c316" : "Дин.",
            "c296" : "₽",
            "c298" : "RWF",
            "c313" : "﷼",
            "c327" : "$",
            "c318" : "₨",
            "c334" : "SDG",
            "c346" : "kr",
            "c322" : "$",
            "c301" : "£",
            "c320" : "SLL",
            "c329" : "S",
            "c341" : "$",
            "c405" : "SSP",
            "c311" : "STD",
            "c406" : "$",
            "c349" : "£",
            "c344" : "SZL",
            "c357" : "฿",
            "c353" : "TJS",
            "c27" : "TMM",
            "c366" : "TND",
            "c362" : "TOP",
            "c368" : "TRY",
            "c364" : "TT$",
            "c351" : "NT$",
            "c355" : "TZS",
            "c375" : "₴",
            "c373" : "UGX",
            "c9" : "$",
            "c382" : '$U',
            "c407" : "лв",
            "c387" : "Bs",
            "c389" : "₫",
            "c385" : "VUV",
            "c308" : "WST",
            "c42" : "XAF",
            "c14" : "$",
            "c69" : "XOF",
            "c131" : "XPF",
            "c395" : "﷼",
            "c331" : "R",
            "c408" : "ZMK",
            "c409" : "ZMW",
            "c398" : "Z$"
        };
        UIFramework.prototype.setParameters = function(options){;
            this.initialize(options);
        }

        UIFramework.prototype.initialize = function(options){
            this.default_data = JSON.parse(options);

            // start object bucket
            this.bucket = new DataBucket();
            this.tax_panel = new TaxPanel({}, this);
            this.currency = this.default_data['business_currency'];
            this.business_currency = this.default_data['business_currency'];
            this.currency_symbol = currency_symbols[this.default_data['business_currency']];
            this.exchange_rate = 1;
        }

        UIFramework.prototype.updateParameters = function(options){
            this.default_data = options;
        };

        UIFramework.prototype.addRow = function(){
            var rowObject = new FormRow(this.default_data,this);
            rowObject.buildTemplate();
            var html = rowObject.getTemplate();
            $(html).appendTo($("#RowContainer"));
            rowObject.bindEvents();
            this.bucket.add(rowObject.getIdentity(), rowObject);
            rowObject.calculate();
        }

        UIFramework.prototype.addRowWithData = function(data){
            var rowObject = new FormRow(this.default_data,this);
            rowObject.buildTemplate();
            var html = rowObject.getTemplate();
            $(html).appendTo($("#RowContainer"));
            rowObject.bindEvents();
            rowObject.row_fields.quantity.setValue(data.quantity);
            rowObject.row_fields.description.setValue(data.description);
            rowObject.row_fields.price.setValue(data.price);
            rowObject.row_fields.product.setValue(data.product_id.toString());
            rowObject.row_fields.tax.setValue(data.taxes);
            this.bucket.add(rowObject.getIdentity(), rowObject);
            rowObject.calculate();
        }


        UIFramework.prototype.removeRow = function(rowId){
            this.bucket.remove(rowId);
            $("#"+rowId).remove();
            this.calculateStatusBoard();
        }

        UIFramework.prototype.calculateStatusBoard = function(){
            //gather all objects right here
            //var rowObjects = this.bucket.getAllNodes();
            var dataArray = this.bucket.getAll(), subTotal = 0;
            this.tax_panel.reset();
            for(var key in dataArray){
                var rowAmount = dataArray[key].row_fields.price.getValue() * dataArray[key].row_fields.quantity.getValue();
                subTotal += rowAmount;
                this.tax_panel.calculateRowTax(rowAmount,dataArray[key].row_fields.tax.getValue());
            }
            this.tax_panel.buildTaxBoard();
            this.calculateTotal(subTotal);
        }

        UIFramework.prototype.getExcelFormData = function(){
            var dataArray = this.bucket.getAll();
            var transformArray = [];
            for(var key in dataArray){
                var rowObject = dataArray[key];
                rowObject.isProductEmptyAndChangeColor();
                transformArray.push(rowObject.getJson());
            }
            return transformArray;
        }

        UIFramework.prototype.checkIsAllProductSet = function(){
            var dataArray = this.bucket.getAll();
            var set = true;
            for(var key in dataArray){
                var rowObject = dataArray[key];
                if(rowObject.isProductEmpty()){
                    set = false;
                    break;
                }
            }
            return set;
        }
        
        UIFramework.prototype.calculateTotal = function(subTotal){
            $("#subTotalAmount").html(this.currency_symbol + (Math.round(subTotal * 100) /100).toFixed(2));
            var total = this.tax_panel.getTotal();
            $("#totalAmount").html(this.currency_symbol + (Math.round(total * 100) / 100).toFixed(2));
            $("#totalAmountExchangeRate").html(currency_symbols[this.business_currency] + (Math.round(total * this.exchange_rate * 100)/100).toFixed(2));
        }

        UIFramework.prototype.getTotalFixed = function(){
            var total = this.tax_panel.getTotal();
            return Math.round((total * 100) / 100).toFixed(2);
        }

        UIFramework.prototype.getSubTotalFixed = function(){
            var dataArray = this.bucket.getAll(), subTotal = 0;
            for(var key in dataArray){
                subTotal += dataArray[key].row_fields.price.getValue() * dataArray[key].row_fields.quantity.getValue();
            }
            return Math.round((subTotal * 100) / 100).toFixed(2);
        }

        UIFramework.prototype.getCalculationData = function(){
            return [{'name' : 'total_amount', 'value' : this.getTotalFixed()}, {'name' : 'sub_total', 'value' : this.getSubTotalFixed()}];
        }


        UIFramework.prototype.updateExchangeCurrency = function (exchange_rate) {
            this.exchange_rate = exchange_rate;
            this.calculateStatusBoard();
        }

        UIFramework.prototype.fixRows = function(){
            var dataArray = this.bucket.getAll();
            for(var key in dataArray){
                dataArray[key].calculate();
            }
        }

        UIFramework.prototype.updateCurrency = function (currency) {
            this.currency = currency;
            this.currency_symbol = currency_symbols[currency];
            this.fixRows();
        }

        return (UIFramework);
    });