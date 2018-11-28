

define([],
    function () {

        function Price(defaultData, parent){
            this.initialize(defaultData, parent);
        }

        Price.prototype.constructor = Price;

        Price.prototype.initialize = function(defaultData, parent){
            this.default_data = defaultData;
            this.parent = parent;
            this.selector = $("#" + parent.getIdentity() + " .product_price");
            this.selector.val(0);
            this.plain_value = 0;
        }

        Price.prototype.setValue = function(value){
            this.plain_value = value;
            this.selector.val(value);
        }

        Price.prototype.getValue = function(){
            return this.plain_value;
        }

        Price.prototype.bindEvents = function(){
            this.selector.on('keyup', $.proxy(this, 'priceHandler'));
        }

        Price.prototype.unBindEvents = function(){
            this.selector.off('keyup', $.proxy(this, 'priceHandler'));
        }

        Price.prototype.priceHandler = function(evt){
            this.plain_value = this.selector.val();
            this.parent.calculate();
        }

        return (Price);

    });//@