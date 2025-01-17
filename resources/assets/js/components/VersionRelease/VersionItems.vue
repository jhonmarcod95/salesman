<template>
    <div>
        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
            <div class="font-size-lg font-weight-bold">
                <div v-if="type == 'new'"><i class="fas fa-info-circle mr-2"></i>New Features</div>
                <div v-else-if="type == 'updates'"><i class="fas fa-cogs mr-2"></i>Updates</div>
                <div v-else-if="type == 'fixes'"><i class="fas fa-wrench mr-2"></i>Fixes</div>
            </div>
            <a href="javascript:;" @click="toggleInput()" v-if="isAdministrator">Add Item<i class="fas fa-plus ml-2"></i></a>
        </div>
        <ul class="mt-3">
            <div class="mb-4 text-gray" v-if="items==null">No changes</div>
            <li class="mb-4" v-else v-for="(item, index) in items" :key="index">
                <InputForm v-if="(inputOpen && selectedItem.id == item.id) && isAdministrator"
                :data="item.description" @submit="submit" @close="toggleInput()"/>
                <span v-else>
                    <span>{{ item.description }}</span>
                    <a href="javascript:;" class="text-primary" @click="toggleInput(item)" v-if="isAdministrator">
                        <i class="fas fa-edit icon-xs"></i></a>
                    <a href="javascript:;" class="text-danger" @click="deleteItem(item)" v-if="isAdministrator">
                        <i class="fas fa-trash icon-xs"></i></a>
                </span>
            </li>
            <li v-if="(inputOpen && inputMode == 'add') && isAdministrator">
                <InputForm @submit="submit" @close="toggleInput()"/>
            </li>
        </ul>
    </div>
</template>
<script>
import Swal from 'sweetalert2';
import InputForm from './InputForm.vue';

export default {
    props: {
        type: String,
        version_release_id: Number,
        items: Array,
        lastItem: Boolean, //true if version release has only 1 note
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
            this.inputOpen = !this.inputOpen;
            this.inputMode = 'add';
            this.selectedItem = {
                version_release_id: this.version_release_id,
                type: this.type
            };
            if(!_.isEmpty(data)) {
                this.inputOpen = true;
                this.inputMode = 'edit';
                this.selectedItem = data;
            }
        },
        submit(description) {
            let data = {
                id: this.selectedItem.id,
                version_release_id: this.version_release_id,
                type: this.type,
                description,
            }
            axios.post('submit-item',data)
            .then(res => {
                this.toggleInput();
                this.$emit('submitSuccess');
                // toastr.success('Data submitted successfuly.', 'Success');
            })
        },
        deleteItem(data = null){
            if(_.isEmpty(data)) return;
            
            this.selectedItem = data;
            if (this.lastItem) this.deleteVersion();
            else this.deleteNote();
        },
        deleteNote() {
            Swal.fire({
              title: "Delete from " + this.selectedItem.type + "?",
              text: "'" + this.selectedItem.description + "'",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#e24444",
              cancelButtonColor: "#666666",
              confirmButtonText: "Delete",
            }).then((result) => {
                if (result.isConfirmed) {
                  axios.delete(`delete-item/${this.selectedItem.id}`);
                  Swal.fire({
                    title: "Note deleted!",
                    icon: "success",
                    confirmButtonColor: "666666",
                    confirmButtonText: "Close",
                  }).then((result) => {
                      if (result.isConfirmed) window.location.reload();
                  });
                }
            });
        },
        deleteVersion() {
            Swal.fire({
              title: "Deleting last item!",
              text: "This is the last item on the list. Deleting this will erase the entire version. Proceed?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#e24444",
              cancelButtonColor: "#666666",
              confirmButtonText: "Delete",
            }).then((result) => {
                if (result.isConfirmed) {
                  axios.delete(`delete-item/${this.selectedItem.id}`);
                  axios.delete(`delete/${this.selectedItem.version_release_id}`);
                  Swal.fire({
                    title: "Version deleted!",
                    icon: "success",
                    confirmButtonColor: "666666",
                    confirmButtonText: "Close",
                  }).then((result) => {
                      if (result.isConfirmed) window.location.reload();
                  });
                }
            });
        }

    },
    watch:{
        version_release_id:function(){
            this.inputOpen=false; //close input tabs when clicking a new version release
        }
    }
    
}
</script>