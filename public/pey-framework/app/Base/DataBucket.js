




define(['base','lodash'],
    function ( Base , _) {


        /**
         * Handle core level data
         * @constructor
         */
        function DataBucket(){
            //this.objectBucket   = [];
            this.objectBucket   = {};
            this.last_index     = 1;
        }

        DataBucket.prototype = {
            constructor: DataBucket,
            add : function (randomKey,_row_object){
                this.objectBucket[randomKey] = _row_object;
            },
            getAll : function(){
                return this.objectBucket;
            },
            remove: function(rowId){
                delete this.objectBucket[rowId];
            }
            /*
             getNextNodeId  : function(){
             return this.last_index;
             },
             addNode : function (_index,_row_object) {
             this.objectBucket.push({
             index: _index,
             row_object: _row_object
             });
             this.last_index++;
             },
             removeNode: function (_index) {
             var index = this.getIndex(keyIndex);
             this.objectBucket.splice(index, 1);
             delete this.objectBucket[index];
             },
             getNode: function (_index) {

             },
             getIndex        : function(_index){
             return _.findIndex(this.objectBucket, function(e, index){ return (e['index'] == _index) ? index : null; });
             },
             getAllNodes: function () {
             return _.find(this.objectBucket, 'row_object');
             }
             */
        };




        /**
         *
         * @type {{load: Function, add: Function, remove: Function, removes: Function, removeAll: Function, sort: Function}}
         */

        /*
         DataBucket.prototype = {
         constructor     : DataBucket,
         load            : function(jsonString){},
         add             : function( _index , _stepKey , _formArray , _type ,_step_schema , _flags, _remote_action){
         // now insert it
         this.objectBucket.push({
         index : _index,
         step_key : _stepKey,
         type     : _type,
         data     : _formArray,
         step_schema : _step_schema,
         flags : _flags,
         remote_action: _remote_action
         });
         },
         remove          : function(keyIndex){
         var index = this.getIndex(keyIndex);
         this.objectBucket.splice(index, 1);
         delete this.objectBucket[index];
         },
         removes         : function(limitIndex){
         for(var i=limitIndex; i < this.count(); i++){
         var index = this.getIndex(i);
         delete this.objectBucket[index];
         }
         },
         removeAll       : function(){
         this.objectBucket = [];
         },
         sort            : function(){
         this.objectBucket.sort(function (a, b) {return a.index - b.index});
         },
         find            : function(){},
         getIndex        : function(_index){
         return _.findIndex(this.objectBucket, function(e, index){ return (e['index'] == _index) ? index : null; });
         },
         getIndexByKey        : function(_stepKey){
         return _.findIndex(this.objectBucket, function(e, index){ return (e['step_key'] == _stepKey) ? index : null; });
         },
         getByStepIndex: function(_stepKey){
         return Base.ArrayUtil().findByKey(this.objectBucket, 'index' , _stepKey);
         },
         ifDuplicateRemoveIt : function(_stepKey){
         var alreadyIndex = this.getIndexByKey(_stepKey);
         if(alreadyIndex != -1){
         //this.destroyElementByIndex(alreadyIndex);
         this.objectBucket.splice(alreadyIndex, 1);
         }
         },
         getByStepKey: function(_stepKey){
         return Base.ArrayUtil().findByKey(this.objectBucket, 'step_key' , _stepKey);
         },
         get             : function(){
         // sort it first [by ASC]
         this.sort();
         return this.objectBucket;
         },
         count           : function(){
         return this.objectBucket.length;
         },
         getArray        : function(){
         return this.objectBucket;
         },
         getLast         : function(){
         return this.objectBucket[(this.count()-1)];
         },
         removeWithObject : function(stepIndex){
         var targetIndex = this.getIndex(stepIndex);
         var step_schema = this.getArray()[targetIndex];
         if(step_schema){
         step_schema.step_object.destroy();
         delete(step_schema.step_object);
         this.objectBucket.splice( targetIndex , 1 );
         }
         console.info("Selected Objects Removed");
         },
         cloneShadowObject : function () {
         console.log("Object successfully cloned");
         },
         getSerializeDb : function(){
         var serializeArray = [];
         for(var index in this.get()){
         var stepRow = this.get()[index];
         stepRow.step_object.destroy();
         delete(stepRow.step_object);
         serializeArray.push(stepRow);
         }
         return serializeArray;
         },
         destroyElementByIndex : function(index){
         var stepRow = this.get()[index];
         stepRow.step_object.destroy();
         },

         debug : function(){
         var arrayKey = [];
         for(var index in this.get()) {
         var stepRow = this.get()[index];
         arrayKey.push(stepRow.step_key);
         }
         var str = arrayKey.join(",");
         console.log( str);
         },
         debugStepData : function(){
         for(var index in this.get()) {
         var stepRow = this.get()[index];
         console.log(stepRow.step_key +" : "+ stepRow.step_object.getStepData());
         }
         }
         };
         */

        return (DataBucket);

    });//@