



define(['lodash'],
    function (_) {

        function TaxPanel(defaultData, parent){
            this.initialize(defaultData, parent);
        }

        TaxPanel.prototype.constructor = TaxPanel;

        TaxPanel.prototype.initialize = function(defaultData, parent){
            this.parent = parent;
            this.exchange_rate = 1;
        }


        TaxPanel.prototype.reset = function(){
            // reset on start of every loop
            this.total_amount = 0;
            this.tax_beg = {};
        }

        TaxPanel.prototype.calculateRowTax = function(rowAmount,taxesArray){
            this.total_amount += rowAmount;
            if (_.size(taxesArray) > 0) {
                var taxAmount = {};
                for (var index in taxesArray) {
                    var dueTax = (rowAmount * taxesArray[index].tax_rate) / 100;
                    var taxAbb = taxesArray[index].abbreviation;
                    taxAmount[taxesArray[index].abbreviation] = dueTax;
                    this.total_amount += dueTax;
                    if (this.tax_beg.hasOwnProperty(taxAbb)) {
                        // update reference
                        this.tax_beg[taxAbb] += dueTax;
                    } else {
                        // create reference
                        this.tax_beg[taxAbb] = dueTax;
                    }
                }
            }else {
                this.tax_beg = [];
            }
        }

        TaxPanel.prototype.buildTaxBoard = function(){
            if(_.size(this.tax_beg) > 0){
                var _html = "";
                for(var taxAbb in this.tax_beg){
                    _html += "<tr>";
                    _html += "<td>" + taxAbb + "</td>";
                    _html += "<td>" + this.tax_beg[taxAbb] + "</td>";
                    _html += "</tr>";
                }
                $("#taxPanel").html(_html);
            }else{
                $("#taxPanel").html("");
            }
        }

        TaxPanel.prototype.getTemplate = function(){
            //$('<tr id="'+ this.getIdentity()+ '"></tr>');
        };


        /*
        TaxPanel.prototype.calculate =  function(){
            for(var key in this.tax_bucket){
                var item = this.tax_bucket[key];

            }
            console.log(this.tax_bucket);
        }
        */

        TaxPanel.prototype.getTotal = function() {
            return this.total_amount;
        }


        return (TaxPanel);

    });//@