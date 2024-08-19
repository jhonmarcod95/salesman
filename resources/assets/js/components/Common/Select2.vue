<template>
    <multiselect
        :placeholder="selectPlaceholder"
        :multiple="multiple"
        :allow-empty="multipleSelect"
        :disabled="disabled"
        :max-height="200"
        tagPosition="top"
        :trackBy="selectTrackBy"
        :showLabels="false"
        :searchable="true"
        :max="selectMax"
        :label="selectLabel"
        v-model="selectValue"
        :options="options"
        @input="formatValue()"
    >
        <template slot="option" slot-scope="props" v-if="withDetail">
            <div class="option__desc">
                <span class="option__title">
                    <div>
                        <b>{{ props.option[selectLabel] }}</b> 
                        <span v-if="props.option.from_courier">| GUARD</span>
                    </div>
                </span>
                <span v-if="selectType == 'user'">
                    <div class="font-size-sm">
                        <span v-if="!isEmpty(props.option.company)">
                            {{ props.option.company.name }}
                        </span>
                        <span v-else>**No Company Match</span>
                    </div> 
                    <div class="font-size-sm">
                        <span v-if="!isEmpty(props.option.department)">
                            {{ props.option.department.name }}
                        </span>
                        <span v-else>**No Department Match</span>
                    </div>
                </span>
                <span v-if="selectType == 'visitor'">
                    <div class="font-size-sm">
                        <span v-if="props.option.from_courier">
                            COURIER: 
                            <span v-if="!isEmpty(props.option.courier)">{{ props.option.courier.description | _uppercase }}</span>
                            <span v-else>{{ props.option.courier_name | _uppercase }}</span>
                        </span> 
                        <span v-else>{{ props.option.company_name | _uppercase }}</span>
                        (<em>{{ props.option.transaction_id }}</em>)
                    </div> 
                </span>
                <span v-if="selectType == 'pms_job_order'">
                    <div class="font-size-sm">
                        <span>{{ props.option.user | _uppercase }}</span> | 
                        <span>PLATE NO: {{ props.option.plate_number | _uppercase }}</span> 
                        <span v-if="!isEmpty(props.option.service_quotation_number)">| {{ props.option.service_quotation_number | _uppercase }}</span> 
                    </div> 
                </span>
            </div>
        </template>
    </multiselect>
</template>

<script>
    export default {

        props: ['value','options', 'multiple', 'disabled', 'max', 'placeholder', 'label', 'trackKey', 'withDetail', 'selectType'],

        data() {
            return {
                selectTrackBy: '',
                selectLabel: '',
                selectMax: this.max ? this.max : null,
                selectValue: this.checkValue(),
                multipleSelect: this.multiple ? this.multiple : false,
                selectPlaceholder: this.placeholder,
                customDetail: {}
            }
        },

        methods: {
            checkOptions() {
                if (_.every(this.options, _.isObject)) {
                    this.selectTrackBy = this.trackKey ? this.trackKey : 'id'
                    this.selectLabel = this.label ? this.label : 'name'
                }
            },

            checkValue() {
                this.checkOptions()
                if (_.every(this.options, _.isObject)) {
                    if (Array.isArray(this.value)) {
                        let data = [];
                        _.each(this.options, (option) => {
                            _.each(this.value, (value) => {
                                if (option[this.selectTrackBy] === value) {
                                    data.push({
                                        id: option[this.selectTrackBy],
                                        name: option[this.selectLabel]
                                    });
                                }
                            })
                        })  
                        return data;
                    }
                    return _.filter(this.options, (item) => {
                        return item.id === this.value;
                    })
                }
                return this.value;
            },

            formatValue() {
                let returnValue = this.selectValue
                if (_.isObject(this.selectValue)) {
                    returnValue = this.selectValue[this.selectTrackBy]
                }

                if (this.multipleSelect) {
                    returnValue = _.map(this.selectValue, (item) => {
                        return item[this.selectTrackBy]
                    });
                }

                this.$emit('input', returnValue)
            }
        },

        created() {
            this.checkOptions()
        },

        watch: {
            value: {
                handler(val) {
                    if (_.isUndefined(val)) {
                        this.selectValue = null;
                    }
                }
            },
            options: {
                handler(val) {
                    if (!_.isUndefined(val)) {
                        if (this.value) {
                            this.selectValue = this.checkValue();
                        }
                        else {
                            this.selectValue = null
                        }
                    }
                }
            },
            placeholder:function(val) {
                this.selectPlaceholder = val;
            }
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

