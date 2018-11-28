


define([],
    function () {

        function Tax(defaultData,parent){
            this.initialize(defaultData,parent);
        }

        Tax.prototype.constructor = Tax;

        Tax.prototype.initialize = function(defaultData,parent){
            this.default_data = defaultData;
            this.plain_tax_array = {};
            this.parent = parent;
            this.selector = $("#" + parent.getIdentity() + " .tax_list");
        }

        Tax.prototype.bindEvents = function(){
            this.selector.select2({allowClear: false,data: this.default_data});
            var that = this;
            this.selector.on('select2:select', function (e) {
                var data = e.params.data;
                that.plain_tax_array[data.id] = {"tax_rate" : data.tax_rate,"abbreviation":data.abbreviation };
                that.parent.parent.calculateStatusBoard();
            });
            this.selector.on('select2:unselect', function (e) {
                var data = e.params.data;
                delete that.plain_tax_array[data.id];
                that.parent.parent.calculateStatusBoard();
            });
        }

        Tax.prototype.getValue = function(){
            return this.plain_tax_array;
        }

        Tax.prototype.setValue = function(value){
            var that = this;
            that.selector.val(value).trigger('change');
            if(Array.isArray(value)){
                value.forEach(function (el) {
                    that.plain_tax_array[parseInt(el)] = that.default_data.find(x => x.id === parseInt(el));
                    that.parent.parent.calculateStatusBoard();
                })
            }
            // else{
            //     that.plain_tax_array[parseInt(el)] = that.default_data.find(x => x.id === parseInt(el));
            //     that.parent.parent.calculateStatusBoard();
            // }
        }

        // To Save as JSON
        Tax.prototype.getTransformValue = function(){
            return Object.keys(this.plain_tax_array);
        }

        return (Tax);

    });//@