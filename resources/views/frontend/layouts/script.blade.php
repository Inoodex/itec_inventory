    
    <script type="text/javascript" src="{{ asset('frontend') }}/js/frontend6d8b.js?ver=5.38.8" id="cr-frontend-js-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/colcade6d8b.js?ver=5.38.8" id="cr-colcade-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/wd_shortcoded01b.js?ver=160119.0505" id="wd_shortcode-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/bootstrap5e0d.js?ver=240118" id="bootstrap-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery.imagesloaded.min5e0d.js?ver=240118" id="jquery.imagesloaded-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/bootstrap-typeahead5861.js?ver=14.9" id="solr_auto_js1-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/url.min5861.js?ver=14.9" id="urljs-js"></script>
 
    <script type="text/javascript" src="{{ asset('frontend') }}/js/autocomplete_solr5861.js?ver=14.9" id="autocomplete-js"></script>

    <script type="text/javascript" src="{{ asset('frontend') }}/js/solr-autocomplete6a64.js?v=1.0.1&amp;ver=240118" id="wafi-autocomplete-js"></script>

    <script type="text/javascript" src="{{ asset('frontend') }}/js/public.minae29.js?ver=2.7.4" id="tinvwl-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/TweenMax5e0d.js?ver=240118" id="TweenMax-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery.touchSwipe5e0d.js?ver=240118" id="jquery.touchSwipe-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/include-script5e0d.js?ver=240118" id="include-script-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery.prettyPhoto.min5e0d.js?ver=240118" id="jquery.prettyPhoto_-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery.prettyPhoto.init.min5e0d.js?ver=240118" id="jquery.prettyPhoto.init-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/custom_cart_display705f.js?ver=202101" id="custom-cart-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery.bpopup.min5e0d.js?ver=240118" id="bpopup-js"></script>

    <script type="text/javascript" src="{{ asset('frontend') }}/js/wafic70c.js?ver=2407231" id="wafiscript-js"></script>
    <script type="text/javascript" src="{{ asset('frontend') }}/js/oswadmarket542c.js?ver=202106" id="wafimobilemenu-js"></script>

    <script type="text/javascript" src="{{ asset('frontend') }}/js/wafi-add-to-cart6892.js?ver=202102" id="wafi-add-to-cart-js"></script>





    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery/ui/core.min3f14.js?ver=1.13.2" id="jquery-ui-core-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery/ui/menu.min3f14.js?ver=1.13.2" id="jquery-ui-menu-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/vendor/wp-polyfill-inert.min0226.js?ver=3.1.2" id="wp-polyfill-inert-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/vendor/regenerator-runtime.min8fa4.js?ver=0.13.11" id="regenerator-runtime-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/vendor/wp-polyfill.min2c7c.js?ver=3.15.0" id="wp-polyfill-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/dom-ready.minded6.js?ver=392bdd43726760d1f3ca" id="wp-dom-ready-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/hooks.min2ebd.js?ver=c6aec9a8d4e5a5d543a1" id="wp-hooks-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/i18n.minf92f.js?ver=7701b0c3857f914212ef" id="wp-i18n-js"></script> -->

    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/dist/a11y.min866e.js?ver=7032343a947cfccf5608" id="wp-a11y-js"></script> -->
   
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/jquery/ui/autocomplete.min3f14.js?ver=1.13.2" id="jquery-ui-autocomplete-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/ajax-solr-master/core/Core5e0d.js?ver=240118" id="ajax-solr-core-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/ajax-solr-master/core/AbstractManager5e0d.js?ver=240118" id="ajax-solr-abstract-manager-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/ajax-solr-master/managers/Manager.jquery5e0d.js?ver=240118" id="ajax-solr-manager-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/ajax-solr-master/core/Parameter5e0d.js?ver=240118" id="ajax-solr-parameter-js"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/js/ajax-solr-master/core/ParameterStore5e0d.js?ver=240118" id="ajax-solr-parameterstore-js"></script> -->
 
    <!-- <script type="text/javascript" src="{{ asset('frontend') }}/jsss/frontend6b25.js?ver=2.1.4" id="aepc-pixel-events-js"></script> -->

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


    <script>
        document.addEventListener('click', function(event) {
            const freeSearchResultHolder = document.getElementById('freeSearchResultHolder');
            if (freeSearchResultHolder && !freeSearchResultHolder.contains(event.target)) {
                freeSearchResultHolder.style.display = 'none';
            }
        });
        function Validator(data, rules) {
            const errors = {};
            const addError = (field, message) => {if (!errors[field]) {errors[field] = message;/*errors[field] = [];*/}/*errors[field].push(message);*/};
            const isNumeric = (value) => !isNaN(value);
            const isInteger = (value) => Number.isInteger(Number(value));
            const isString = (value) => typeof value === 'string';
            const isEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            const isURL = (value) => /^(https?:\/\/)?([\w-]+(\.[\w-]+)+)([\/#?]?.*)$/.test(value);
            const isUUID = (value) => /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(value);
            const isDate = (value) => !isNaN(Date.parse(value));
            const isBoolean = (value) => value === true || value === false || value === 'true' || value === 'false';
            const isArray = (value) => Array.isArray(value);
            const isJSON = (value) => {try {JSON.parse(value);return true;} catch {return false;}};
            const isAlpha = (value) => /^[A-Za-z]+$/.test(value);
            const isAlphaNum = (value) => /^[A-Za-z0-9]+$/.test(value);
            const isAlphaDash = (value) => /^[A-Za-z0-9_-]+$/.test(value);
            const isIP = (value) => /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(value);
            const isTimezone = (value) => {try {Intl.DateTimeFormat(undefined, { timeZone: value });return true;} catch {return false;}};

            for (const field in rules) {
                const value = data[field];
                const fieldRules = rules[field].split('|');
                let isNullable = fieldRules.includes('nullable');
                if (isNullable && (value === null || value === undefined || value === '')) {continue;}
                fieldRules.forEach(rule => {
                    let [ruleName, ruleValue] = rule.split(':');
                    switch (ruleName) {
                        case 'required':if (!value || value === '') {addError(field, `${field} is required.`);}break;
                        case 'nullable':if (value === null || value === undefined || value === '') return;break;
                        case 'numeric':if (!isNumeric(value)) {addError(field, `${field} must be a number.`);}break;
                        case 'integer':if (!isInteger(value)) {addError(field, `${field} must be an integer.`);}break;
                        case 'string':if (!isString(value)) {addError(field, `${field} must be a string.`);}break;
                        case 'min':if (isString(value) && value.length < ruleValue) {addError(field, `${field} must be at least ${ruleValue} characters.`);
                        } else if (isNumeric(value) && value < ruleValue) {addError(field, `${field} must be at least ${ruleValue}.`);}break;
                        case 'max':if (isString(value) && value.length > ruleValue) {addError(field, `${field} may not be greater than ${ruleValue} characters.`);
                        } else if (isNumeric(value) && value > ruleValue) {addError(field, `${field} may not be greater than ${ruleValue}.`);}break;
                        case 'email':if (!isEmail(value)) {addError(field, `${field} must be a valid email address.`);}break;
                        case 'url':if (!isURL(value)) {addError(field, `${field} must be a valid URL.`);}break;
                        case 'confirmed':const confirmationField = `${field}_confirmation`;
                        if (data[confirmationField] !== value) {addError(field, `${field} confirmation does not match.`);}break;
                        case 'date':if (!isDate(value)) {addError(field, `${field} must be a valid date.`); }break;
                        case 'after':if (isDate(value) && Date.parse(value) <= Date.parse(ruleValue)) {addError(field, `${field} must be a date after ${ruleValue}.`);}break;
                        case 'before':if (isDate(value) && Date.parse(value) >= Date.parse(ruleValue)) {
                        addError(field, `${field} must be a date before ${ruleValue}.`);}break;
                        case 'boolean':if (!isBoolean(value)) {addError(field, `${field} must be true or false.`);}break;
                        case 'array':if (!isArray(value)) {addError(field, `${field} must be an array.`);}break;
                        case 'json':if (!isJSON(value)) {addError(field, `${field} must be a valid JSON string.`);}break;
                        case 'alpha':if (!isAlpha(value)) { addError(field, `${field} must only contain letters.`);}break;
                        case 'alpha_num':if (!isAlphaNum(value)) {addError(field, `${field} must only contain letters and numbers.`);}break;
                        case 'alpha_dash':if (!isAlphaDash(value)) {addError(field, `${field} may contain only letters, numbers, dashes, and underscores.`);}break;
                        case 'ip':if (!isIP(value)) {addError(field, `${field} must be a valid IP address.`);}break;
                        case 'uuid':if (!isUUID(value)) {addError(field, `${field} must be a valid UUID.`);}break;
                        case 'timezone':if (!isTimezone(value)) {addError(field, `${field} must be a valid timezone.`);}break;
                        case 'same':if (value !== data[ruleValue]) {addError(field, `${field} must match ${ruleValue}.`);}break;
                        case 'in':const allowedValues = ruleValue.split(',');
                        if (!allowedValues.includes(String(value))) {addError(field, `${field} must be one of the following values: ${allowedValues.join(', ')}.`);}break;
                        case 'nullable_if':const [dependentField, dependentValue] = ruleValue.split(',');
                        if (data[dependentField] === dependentValue && value === '') {addError(field, `${field} must be present when ${dependentField} is ${dependentValue}.`);}break;
                        default:break;
                    }
                });
            }

            return {isValid:Object.keys(errors).length === 0 ? true : false, errors:errors};
        }

        function implode(tag, data) {
            if (Array.isArray(data)) {return data.join(tag);
            } else if (typeof data === 'object' && data !== null) {return Object.values(data).join(tag);
            } else {return '';}
        }

        function removeCartItem(key, fun=null){
            fetch('{{route('remove_cart_item')}}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                body: JSON.stringify({ key })
            }).then(res => res.json())
            .then(data => {
                document.getElementById("mini_cart_value").innerHTML = `${data.currectPrice} ৳`;
                document.getElementById("mini_cart_view").innerHTML = data.cart;
                if (fun) fun();
            });
        }

        function updateQty(){
                
        }

        function freeSearchProduct(key){
            key = key.trim();
            if(key==null || key==""){
                document.getElementById("freeSearchResultHolder").style.display = "none";
                document.getElementById("freeSearchResultHolderMobole").style.display = "none";
                return;
            }
            fetch('{{route('free_search_product')}}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                body: JSON.stringify({ key })
            }).then(res => res.json())
            .then(data => {
                if(data.totalItem==0){
                    console.log("000000000000");
                    document.getElementById("freeSearchResultHolder").style.display = "none";
                    document.getElementById("freeSearchResultHolderMobole").style.display = "none";
                }else{
                    document.getElementById("freeSearchResultHolder").style.display = "";
                    document.getElementById("freeSearchResultHolderMobole").style.display = "";
                }
                document.getElementById("freeSearchResultHolder").innerHTML = data.html;
                document.getElementById("freeSearchResultHolderMobole").innerHTML = data.html;
            });
        }
    </script>

