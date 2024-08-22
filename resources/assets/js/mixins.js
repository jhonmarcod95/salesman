import moment from 'moment';
Vue.mixin({
    methods: {
        isEmpty(data) {
            return _.isEmpty(data);
        },
        getSelectOptions(props, endpoint, allOption, key = 'name', label = 'All', allOptionValue = null) {
            let vm = this;
            axios.get(endpoint)
                .then( response => {
                    let all = { id: allOptionValue }
                    all[key] = label

                    vm[props] = response.data;

                    //Add all option at the last item
                    if (allOption) {
                        vm[props].push(all)
                    }
                })
                .catch(err => {
                    this.fetchError = true;
                });
        },
        sumTotal(nums) {
            return nums.length > 0 ? nums.reduce((a, b) => a + b) : 0;
        }
    },
    computed:{
        publicPath(){
            return window.location.origin;
        }
    },
    filters: {
        _date(date) {
            if (date)
                return moment(date).format('lll');
        },
        _date_only(date){
            return moment(date).format('LL');
        },
        _amount(amount){
            return amount ? Number(parseFloat(amount).toFixed(2)).toLocaleString('en', { minimumFractionDigits: 2 }) : 0;
        },
        _null(data){
            if (_.isEmpty(data))
                return "-"
            else
                return data
        },
        _uppercase(str) {
            if (str)
                return _.upperCase(str)
        },
        _fromNow(value) {
            if (value)

                if (moment().diff(value, 'days') >= 6) {
                    return moment(value).format('LL');
                }
                return moment(value).fromNow()
        },
    }
})
