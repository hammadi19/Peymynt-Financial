
define(['lodash','base','product','description','quantity','price','tax','amount'],
    function (_,Base,Product,Description, Quantity, Price , Tax , Amount) {

        function FormRow(defaultData,parent){
            this.initialize(defaultData,parent);
        }

        FormRow.prototype.constructor = FormRow;

        FormRow.prototype.initialize = function(defaultData,parent){
            this.root = parent;
            this.default_data   = defaultData;
            this.row_fields     = {};
            this.parent         = parent;
            this.row_identity   = this.identity();
            this._html          = '';
        }

        FormRow.prototype.getIdentity = function () {
            return this.row_identity;
        }

        FormRow.prototype.buildFields = function(){
            //var product = new Product(this.default_data['default_products']);
        }

        FormRow.prototype.buildTemplate = function(){
            var source = document.getElementById("abcRowTpl").innerHTML;
            var template = Handlebars.compile(source);
            this._html = $('<tr id="'+ this.getIdentity()+ '"></tr>').html(template({}));
        }

        FormRow.prototype.getTemplate = function(){
            return this._html;
            //var source = document.getElementById("rowTpl").innerHTML;
            //var template = Handlebars.compile(source);
            //return template({row_no:rowNo});
        }

        FormRow.prototype.bindEvents = function(){
            var product = new Product(this.default_data["default_products"], this);
            product.bindEvents();
            var tax = new Tax(this.default_data["tax_list"],this);
            tax.bindEvents();
            var price = new Price({},this);
            price.bindEvents();
            var quantity = new Quantity({},this);
            quantity.bindEvents();
            var description = new Description({},this);
            var amount = new Amount({},this);
            this.row_fields = {
                product : product,
                description: description,
                price: price,
                quantity: quantity,
                amount: amount,
                tax: tax
            }

            $(this._html).find(".remove-button").on('click', $.proxy(this, 'removeRowHandler'));
        }

        FormRow.prototype.removeRowHandler = function(evt) {
            evt.preventDefault();
            this.parent.removeRow(this.getIdentity());
        }

        FormRow.prototype.calculate = function () {
            var rowPrice = this.row_fields.price.getValue() * this.row_fields.quantity.getValue();
            this.row_fields.amount.setValue(_.padEnd(this.parent.currency_symbol + rowPrice),2,'00');
            this.parent.calculateStatusBoard();
        }

        FormRow.prototype.getJson = function () {
            return {
                'product': this.row_fields.product.getValue(),
                'price': this.row_fields.price.getValue(),
                'quantity' : this.row_fields.quantity.getValue(),
                'taxes': this.row_fields.tax.getTransformValue(),
                'description': this.row_fields.description.getValue()
            };
        }

        FormRow.prototype.destroy = function(){
            for(var key in this.row_fields){
                var object = this.row_fields[key];
                object.destroy();
            }
        }

        FormRow.prototype.identity = function(){
            return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        }

        FormRow.prototype.isProductEmpty = function () {
          return !this.row_fields.product.getValue();
        };

        FormRow.prototype.isProductEmptyAndChangeColor = function () {
            if(!this.row_fields.product.getValue()){
                this.row_fields.product.selector.parent().addClass('product_select2_list');
            }
        };

        return (FormRow);

    });//@



/*
define(['product','description','quantity','price','tax','amount'],
    function (Product,Description, Quantity, Price , Tax , Amount) {

        function FormRow(defaultData){
            this.initialize(defaultData);
        }

        FormRow.prototype.constructor = FormRow;

        FormRow.prototype.initialize = function(defaultData){
            this.default_data = JSON.parse(defaultData);
            this.row_fields = {};
            this._html = '';
        }

        FormRow.prototype.buildFields = function(){
            var product = new Product(this.default_data['default_products']);
        }

        FormRow.prototype.buildTemplate = function(){
            var _html = $('<tr></tr>'), source = document.getElementById("abcRowTpl").innerHTML;
            var template = Handlebars.compile(source);
            this._html = $('<tr></tr>').html(template({}));
        }


        FormRow.prototype.getTemplate = function(rowNo){
            return this._html;
            var source = document.getElementById("rowTpl").innerHTML;
            var template = Handlebars.compile(source);
            return template({row_no:rowNo});

        }

        FormRow.prototype.bindEvents = function(){
            var product = new Product(this.default_data["default_products"], this);
            product.bindEvents();
            var tax = new Tax(this.default_data["tax_list"]);
            tax.bindEvents();
            var price = new Price({},this);
            price.bindEvents();
            var quantity = new Quantity({},this);
            quantity.bindEvents();
            var description = new Description();
            var amount = new Amount();
            this.row_fields = {
                product : product,
                description: description,
                price: price
            }
        }

        FormRow.prototype.destroy = function(){
        }

        return (FormRow);

    });//@

*/