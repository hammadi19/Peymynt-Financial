

define([],
    function () {

        function Description(defaultData,parent){
            this.initialize(defaultData,parent);
        }

        Description.prototype.constructor = Description;

        Description.prototype.initialize = function(defaultData,parent){
            this.default_data = defaultData;
            this.selector = $("#" + parent.getIdentity() + " .product_description");
        }

        Description.prototype.setValue = function(value){
            this.selector.val(value);
        }

        Description.prototype.getValue = function(){
            return this.selector.val();
        }

        return (Description);

    });//@

