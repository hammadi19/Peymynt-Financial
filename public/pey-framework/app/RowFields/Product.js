

define([],
    function () {

        function Product(defaultData, parent){
            this.initialize(defaultData,parent);
        }

        Product.prototype.constructor = Product;

        Product.prototype.initialize = function(defaultData,parent){
            this.default_data = defaultData;
            this.parent = parent;
            this.selected_value = 0;
            this.selector = $("#" + parent.getIdentity() + " .product_list");
        }

        Product.prototype.bindEvents = function(){
            this.selector.select2({placeholder: "Choose",data: this.default_data});
            var parent = this.parent,
                that = this;
            this.selector.on('select2:select', function (e) {
                that.selector.parent().removeClass('product_select2_list');
                var data = e.params.data;
                that.selected_value = data.id;
                parent.row_fields.description.setValue(data.description);
                parent.row_fields.price.setValue(data.price);
                if (data.tax_id) {
                    parent.row_fields.tax.setValue(data.tax_id);
                }
                parent.calculate();
            });
        }

        Product.prototype.getValue = function() {
            return this.selected_value;
        }

        Product.prototype.setValue = function(value) {
            this.selector.val(value).trigger('change');
            this.selected_value = value;
        }

        Product.prototype.destroy = function(){
        }

        return (Product);

    });//@

