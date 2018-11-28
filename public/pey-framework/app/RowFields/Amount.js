



define([],
    function () {

        function Amount(defaultData,parent){
            this.initialize(defaultData,parent);
        }

        Amount.prototype.constructor = Amount;

        Amount.prototype.initialize = function(defaultData,parent){
            this.default_data = defaultData;
            this.selector = $("#" + parent.getIdentity() + " .product_amount");
        }

        Amount.prototype.setValue = function(value){
            this.plain_value = value;
            this.selector.html(value);
        }

        Amount.prototype.getPlainValue = function(){
            return this.plain_value;
        }



        return (Amount);

    });//@