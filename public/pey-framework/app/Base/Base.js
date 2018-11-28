



    define(function () {


        /**
         * Vanilla JS Event Handler
         * @type {{bind: Function, unbind: Function, stop: Function}}
         */
            /*
        var _EventHandler = {
            bind:function(el, ev, fn){
                if(window.addEventListener){ // modern browsers including IE9+
                    el.addEventListener(ev, fn, false);
                } else if(window.attachEvent) { // IE8 and below
                    el.attachEvent('on' + ev, fn);
                } else {
                    el['on' + ev] = fn;
                }
            },

            unbind:function(el, ev, fn){
                if(window.removeEventListener){
                    el.removeEventListener(ev, fn, false);
                } else if(window.detachEvent) {
                    el.detachEvent('on' + ev, fn);
                } else {
                    elem['on' + ev] = null;
                }
            },

            stop:function(ev) {
                var e = ev || window.event;
                e.cancelBubble = true;
                if (e.stopPropagation) e.stopPropagation();
            }
        }
        */



        var FormUtil = function(){

            /**
             * Get function values
             *
             * @param formArray
             * @param fieldName
             * @returns {Array}
             * @private
             */
            var _getFieldValues = function(formArray , fieldName){
                var valuesArray = [];
                var foundObjects = formArray.filter(function(objElement){
                    if(objElement.name == fieldName){
                        return objElement.value;
                    }
                });
                for(var index in foundObjects){
                    var obj = foundObjects[index];
                    valuesArray.push((obj.value == null) ? '' : obj.value)
                }
                return valuesArray;
            }

            return {
                getFieldValues : _getFieldValues
            }
        }




        var ObjectUtil = function(){

            /**
             * Extended Object Literal Module
             * @param _choice_list
             * @param _default_param
             * @returns {*}
             * @private
             */
            var _persistArrayObjects = function( _choice_list ,_default_param){
                var data_array = _choice_list;
                if(_default_param.length > 0){
                    data_array = _choice_list.map(function(obj){
                        // remove default
                        if(obj.selected != undefined){
                            delete obj['selected'];
                        }
                        if(_default_param.indexOf(obj.key) != -1){
                            return _.extend(obj, {selected: true});
                        }
                        return obj;
                    });
                }
                return data_array;
            }

            /**
             * get_class(object)
             *
             * @param obj
             * @returns {*}
             */
            var _getClass = function(obj) {
                if (obj && obj.constructor && obj.constructor.toString) {
                    var arr = obj.constructor.toString().match(
                        /function\s*(\w+)/);

                    if (arr && arr.length == 2) {
                        return arr[1];
                    }
                }
                return undefined;
            }

            var _nestedMerge = function(){
                var o = {}
                for (var i = arguments.length - 1; i >= 0; i --) {
                    var s = arguments[i]
                    for (var k in s) o[k] = s[k]
                }
                return o
            }

            return {
                persistArrayObjects : _persistArrayObjects,
                getClass : _getClass,
                nestedMerge : _nestedMerge
            }
        }



        var ArrayUtil = function(){

            /**
             * Find array item by key name
             *
             * @param arr
             * @param key
             * @param value
             * @returns {*}
             */
            var findByKey = function(arr, key, value){
                var results = arr.filter(function(e){
                    return e[key] == value;
                });
                return (results.length) ? results[0]:null;
            }

            return {findByKey : findByKey}
        }


        var StringUtil = function(){

            var objectToAttribute = function (obj){
                var attributeArray = [];
                for(var index in obj){
                    attributeArray.push(index+'="'+ obj[index] + '"');
                }
                return attributeArray.join(" ");
            }

            var ID = function () {
                console.log(" Ai am callllle");
                return '_' + Math.random().toString(36).substr(2, 9);
            };

            return {
                objectToAttribute : objectToAttribute,
                randomId: ID
            };
        }

        var _Function = function(){

            var isCallable = function(functionString){
                return (window[functionString] != undefined) ? true : false;
            };

            return {
                isCallable : isCallable
            }
        }


        var Remote = function ( url , data , options ){


            //App.settingOptions.remote.data_post_uri
            var posting = $.post( url , data, options );


            /*
            var _post = function(){

                var posting = $.post( App.settingOptions.remote.data_post_uri ,{ steps: stepArray , flags: flagArray} );

                posting.done(function( data ) {
                    console.log(data);
                    //var jsonResponse = JSON.parse(data);
                    //console.log(jsonResponse);
                });

                posting.fail(function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus);
                    console.log(jqXHR);
                    //('Error : ' + jqXHR + textStatus + errorThrown);
                });
            }
            */

            return {
                post : posting
            }

        }


        var _Binary = function(){

            function dataURItoBlob(dataURI) {
                // convert base64 to raw binary data held in a string
                var byteString = atob(dataURI.split(',')[1]);

                // separate out the mime component
                var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

                // write the bytes of the string to an ArrayBuffer
                var arrayBuffer = new ArrayBuffer(byteString.length);
                var _ia = new Uint8Array(arrayBuffer);
                for (var i = 0; i < byteString.length; i++) {
                    _ia[i] = byteString.charCodeAt(i);
                }

                var dataView = new DataView(arrayBuffer);
                //var blob = new Blob([dataView], { type: mimeString });
                var blob = new Blob([dataView.buffer], { type: mimeString });
                return blob;
            }


            return {
                dataURItoBlob : dataURItoBlob
            }
        }



        /**
         *  Global returns
         */
        return {
            ObjectUtil      : ObjectUtil,
            FormUtil        : FormUtil,
            ArrayUtil       : ArrayUtil,
            StringUtil      : StringUtil,
            //EventHandler    : _EventHandler,
            Func            : _Function,
            Remote          : Remote,
            Binary          : _Binary
        }
    });