<template>
    <div>
        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
            <div class="font-size-lg font-weight-bold">
                <i class="flaticon-interface-9 mr-2" v-if="title == 'New'"></i>
                <i class="flaticon2-refresh mr-2" v-if="title == 'Enhancement'"></i>
                <i class="flaticon2-gear mr-2" v-if="title == 'Fixes'"></i>
                {{ title }}
            </div>
            <a href="javascript:;" @click="toggelInput()" v-if="isAdministrator">Add Item</a>
        </div>
        <ul class="my-5">
            <li class="mb-2" v-for="(item, index) in items" :key="index">
                <InputForm v-if="(inputOpen && selectedItem.id == item.id) && isAdministrator" :data="item.description" @submit="submit" @close="toggelInput()"/>
                <span v-else>
                    <span>{{ item.description }}</span>
                    <a href="javascript:;" @click="toggelInput(item)" v-if="isAdministrator"><i class="flaticon2-pen font-size-sm"></i></a>
                </span>
            </li>
            <li v-if="(inputOpen && inputMode == 'add') && isAdministrator">
                <InputForm @submit="submit" @close="toggelInput()"/>
            </li>
        </ul>
    </div>
</template>
<script>
import InputForm from './InputForm.vue';
export default {
    props: {
        title: String,
        items: Array,
        isAdministrator: Boolean
    },
    components: {InputForm},
    data() {
        return {
            inputOpen:false,
            inputMode: 'add',
            selectedItem: {}
        }
    },
    methods: {
        toggelInput(data = null) {
            this.inputOpen = !this.inputOpen
            this.inputMode = 'add';
            this.selectedItem = {
                version_release_id: this.items[0]['version_release_id'],
                type: this.items[0]['type']
            };
            if(!_.isEmpty(data)) {
                this.inputOpen = true
                this.inputMode = 'edit';
                this.selectedItem = data;
            }
        },
        submit(description) {
            let data = {
                id: this.selectedItem.id,
                version_release_id: this.selectedItem.version_release_id,
                type: this.selectedItem.type,
                description,
            }
            axios.post('/master-data/version-release/submit-item',data)
            .then(res => {
                this.toggelInput();
                this.$emit('submitSuccess');
                toastr.success('Data submitted successfuly.', 'Success');
            })
        }
    }
}
</script>