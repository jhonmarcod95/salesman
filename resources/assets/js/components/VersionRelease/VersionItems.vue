<template>
    <div>
        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
            <div class="font-size-lg font-weight-bold">
                <i class="fas fa-info-circle mr-2" v-if="title == 'New'"></i>
                <i class="fas fa-sync-alt mr-2" v-if="title == 'Enhancement'"></i>
                <i class="fas fa-wrench mr-2" v-if="title == 'Fixes'"></i>
                {{ title }}
            </div>
            <a href="javascript:;" @click="toggleInput()" v-if="isAdministrator">Add Item<i class="fas fa-plus ml-2"></i></a>
        </div>
        <ul class="my-5">
            <li class="mb-2" v-for="(item, index) in items" :key="index">
                <InputForm v-if="(inputOpen && selectedItem.id == item.id) && isAdministrator" :data="item.description" @submit="submit" @close="toggleInput()"/>
                <span v-else>
                    <span>{{ item.description }}</span>
                    <a href="javascript:;" @click="toggleInput(item)" v-if="isAdministrator"><i class="fas fa-edit icon-xs"></i></a>
                </span>
            </li>
            <li v-if="(inputOpen && inputMode == 'add') && isAdministrator">
                <InputForm @submit="submit" @close="toggleInput()"/>
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
        toggleInput(data = null) {
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
            axios.post('submit-item',data)
            .then(res => {
                this.toggleInput();
                this.$emit('submitSuccess');
                toastr.success('Data submitted successfuly.', 'Success');
            })
        }
    }
}
</script>