

define([],
    function () {

        function Quantity(defaultData,parent){
            this.initialize(defaultData,parent);
        }

        Quantity.prototype.constructor = Quantity;

        Quantity.prototype.initialize = function(defaultData, parent){
            this.default_data = defaultData;
            this.parent = parent;
            this.selector = $("#" + parent.getIdentity() + " .product_quantity");
            this.selector.val(1);
            this.plain_value = 1;
        }

        Quantity.prototype.getValue = function(){
            return this.plain_value;
        }

        Quantity.prototype.setValue = function(value){
            this.selector.val(value);
        }

        Quantity.prototype.bindEvents = function(){
            this.selector.on('keyup', $.proxy(this, 'quantityHandler'));
        }

        Quantity.prototype.unBindEvents = function(){
            this.selector.off('keyup', $.proxy(this, 'quantityHandler'));
        }

        Quantity.prototype.quantityHandler = function(evt){
            this.plain_value = this.selector.val();
            this.parent.calculate();
        }


        return (Quantity);

    });//@