<script type="text/babel">
    import AutoNumeric from 'autonumeric/src/main';
    
    const defaultOptions = {};
    
    export default {
        //TODO If an html value attribute is set in the source, then the 'linked' component sharing the same v-model are not updated with the value nor formatted on load (it takes precedence over the changes made by other inputs, and always keep `value` to the initial value)
        name: 'InputNumeric',
        /**
         * Allow the vue-autonumeric component to generate other (allowed) html tags, and not only the `<input>` one like currently.
         * This sets its 'contenteditable' attribute to `true` to make it interactive.
         *
         * @param {function} createElement
         * @returns {*}
         */
        render(createElement) {
            const isInput = this.tag === 'input';
            let attributes;
            if (isInput) {
                attributes = {
                    type           : 'text',
                    placeholder    : this.placeholder,
                    class          : 'autonumerical'
                };
            } else {
                attributes = {
                    contenteditable: this.hasContentEditable,
                };
            }
            return createElement(this.tag, {
                attrs: attributes,
                ref  : 'autoNumericElement',
                on   : {
                    'autoNumeric:rawValueModified': this.updateVModel,
                },
            });
        },
        props: {
            value: {
                required: false,
                validator(val) {
                    return typeof val === 'number' || typeof val === 'string' || val === '' || val === null;
                },
            },
            options: {
                type    : [Object, String, Array],
                required: false,
                default() {
                    return defaultOptions;
                },
            },
            /**
             * If set to `true`, whenever the `options` prop changes, the AutoNumeric settings are first reset to the AutoNumeric defaults options.
             * This is set to `true` by default so that it allows for users to pass predefined option names and be sure that no previous settings would be kept, resulting in an unusual result (ie. when switching from 'integer' to 'euro', the decimalPlaces would still be `0`).
             */
            resetOnOptions: {
                type    : Boolean,
                required: false,
                default : true,
            },
            placeholder: { // The <input> placeholder text. This is only used if the generated element is an <input>.
                type    : String,
                required: false,
            },
            tag: {
                type    : String,
                required: false,
                default : 'input',
            },
        },
        data() {
            return {
                // Store the reference to the AutoNumeric object
                anElement         : null,
                initialOptions    : null, // Store the options that were first used when initializing the component
                hasContentEditable: true, // Store if the element should be set as `contenteditable` on initialization
            };
        },
        created() {
            // Manage the options
            /*
             * Currently, the allowed format for the `options` property are :
             * - a string (predefined options)
             * - an object (the option object)
             * - an array of strings and/or objects
             */
            if (Array.isArray(this.options)) {
                // This allows the user to use multiple options (strings or objects) in an array, and overwrite the previous one with the next option element ; this is useful to tune the wanted format
                let optionObjects = {};
                this.options.forEach(optionElement => {
                    this.initialOptions = this.manageOptionElement(optionElement);
                    optionObjects       = Object.assign(optionObjects, this.initialOptions); // Merge each options objects one after the other
                });
                this.initialOptions = optionObjects;
            } else {
                this.initialOptions = this.manageOptionElement(this.options);
            }
            // Define if the generated non-input element should have the contenteditable attribute set to `true`
            this.hasContentEditable = !this.initialOptions.readOnly;
        },
        mounted() {
            // Initialize the autoNumeric element
            this.anElement = new AutoNumeric(this.$refs.autoNumericElement, this.initialOptions);
            if (this.value !== null && this.value !== '') {
                
                if(!isNaN(this.value)) this.anElement.set(Number(this.value));
                // The `v-model` must be updated with that default value on startup
                this.updateVModel(); //FIXME Send the `event.timeStamp` info here
            }
            
            $(this.anElement.domElement).addClass('el-numeric el-input__inner')
            $(this.anElement.domElement).attr("autocomplete", "off");
        },
        computed: {
            /**
             * This computed property is created in order to be able to watch the changes to both `value` and `options` at the same time.
             * This is important since if both are changed at the same time, `options` needs to be updated *before* `value` (the order here is important, and is respected in the `anInfo` watcher).
             *
             * cf. https://github.com/vuejs/vue/issues/7723#issuecomment-369344926
             *
             * @returns {Object}
             */
            anInfo() {
                return {
                    value  : this.value,
                    options: this.options,
                };
            },
        },
        methods: {
            /**
             * Update the v-model value and make the parent aware of that change.
             *
             * @param {Event} event This is needed if we want to use the `event.timeStamp` attribute
             */
            updateVModel(event) {
                if (this.anElement !== null) {
                    this.$emit('input', this.anElement.getNumber(), event);
                }
            },
            /**
             * Return an option object, whatever the type of `optionElement`.
             * If `optionElement` is as string, then we retrieve the predefined option object, if it's an object, we use it as is.
             *
             * @param {object|string} optionElement
             * @returns {object}
             */
            manageOptionElement(optionElement) {
                let options;
                if (typeof optionElement === 'string' || optionElement instanceof String) {
                    options = AutoNumeric.getPredefinedOptions()[optionElement];
                    if (options === void(0) || options === null) {
                        // If the given `optionElement` does not exist, we switch back to the default options
                        console.warn(`The given pre-defined options [${optionElement}] is not recognized by AutoNumeric.\nSwitching back to the default options.`);
                        options = defaultOptions; // Default value
                    }
                } else { // A `settings` object
                    options = optionElement;
                }
                return options;
            },
        },
        watch: {
            anInfo(newValue, oldValue) {
                // 1) First, check if the options have changed, if that's the case, update those first
                // Compare the new and old options, and only update if they are different
                if (oldValue.options && JSON.stringify(newValue.options) !== JSON.stringify(oldValue.options)) { //TODO Find a better way (without external libraries) to compare the two options objects. Also, the comparison is moot when comparing 'euro' with the actual euro object.
                    if (this.resetOnOptions) { // This is needed when using predefined options that do not override previously used options
                        this.anElement.options.reset();
                    }
                    let optionsToUse;
                    if (Array.isArray(newValue.options)) { // Manage the new options if they are passed in an array
                        optionsToUse = AutoNumeric.mergeOptions(newValue.options);
                    } else {
                        optionsToUse = AutoNumeric._getOptionObject(newValue.options);
                    }
                    this.anElement.update(optionsToUse);
                }
                // 2) Then check if the value has changed, if it's defined
                if (newValue.value !== void(0) &&
                    // Make sure this is only called when the value is set by an external script, and not from a user input
                    this.anElement.getNumber() !== newValue.value &&
                    // Compare the 'newValue' with the current 'oldValue' so we do not `set` it again if it's not needed
                    newValue.value !== oldValue.value) {
                    // The modification comes from a script, so we need to reformat the new value `newValue.value`
                    this.anElement.set(newValue.value);
                }
            },
        },
    };
</script>

<style>
.el-form-item .el-numeric.el-input__inner,{
    height: 40px;
    line-height: 40px;
}
.el-form-item--small .el-numeric.el-input__inner{
    height: 32px;
    line-height: 32px;
}
.el-form-item--mini .el-numeric.el-input__inner{
    height: 28px;
    line-height: 28px;
}
</style>