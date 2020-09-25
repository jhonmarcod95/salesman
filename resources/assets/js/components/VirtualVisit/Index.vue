<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Import Virtual Visit Schedules</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-12">
                                        <button type="submit" @click="showExcelImport" class="btn btn-primary mb-2">Import Sessions (Excel)</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>

         <import :showModal="showModal"
                @modalCallBack="showModal = $event">
        </import>

    </div>
</template>

<script>

import UploadExcel from './Import'

export default {

    components: {
        import: UploadExcel
    },

    data() {
        return {
            showModal: false
        }
    },

    methods: {
         showExcelImport() {
            console.log('trigger show excel import diaglog')
            this.showModal = true;
          },
    }

}
</script>
