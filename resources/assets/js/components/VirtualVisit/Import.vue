<template>
  <div>
    <div
      class="modal fade"
      id="confirm-dialog"
      tabindex="-1"
      role="dialog"
      aria-labelledby="confirm-dialog-Label"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="confirm-dialog-Label">Upload Excel</h3>
          </div>
          <div class="modal-body">

            <div class="row">

                <div v-show="!uploading" class="col">
                  <div class="form-row">
                    <div class="col-md-12">
                      <label class="form-control-label"  for="input-file-import">Upload Excel File</label>
                      <input type="file" class="form-control" :class="{ ' is-invalid' : error.message }" id="input-file-import" name="file_import" ref="import_file"  @change="onFileChange">
                      <div v-if="error.message" class="invalid-feedback">
                        {{ error.message }}
                      </div>
                    </div>
                  </div>
                </div>

                <div v-show="uploading" class="col-lg-12">
                    <div id="wave" class="text-center h4 mb-3 mt-2">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                    <p class="pt-2 text-center">Uploading Excel to database...</p>
                </div>

            </div>

         </div>
          <div class="modal-footer">
            <div class="row w-100 text-right ml-3">
              <div class="col">
                <button type="button" class="btn btn-secondary btn-fill" @click="proceedAction">Proceed</button>
                <button type="button" class="btn btn-primary btn-fill" @click="closeDialog">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {

  props: {
    showModal: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      error: {},
      import_file: '',
      uploading: false,
    }
  },

  watch: {
    showModal(){
        if(this.showModal ===  true) {
            $("#confirm-dialog").modal({
                backdrop: "static",
                keyboard: false
            });
        } else {
            $("#confirm-dialog").modal("hide");
        }
    }
  },

  methods: {

    onFileChange(e) {
        this.import_file = e.target.files[0];
    },

    proceedAction() {
        this.uploading = true;
        let formData = new FormData();
        formData.append('import_file', this.import_file);

        axios.post('/api/virtual-schedule/import', formData, {
          headers: { 'content-type': 'multipart/form-data' }
        })
        .then(response => {
            console.log('check response: ', response.status)
            console.log('check data: ', response.data)
            if(response.status === 200) {
              this.uploading = false
              $("#confirm-dialog").modal("hide");
              this.import_file = ''
              this.$router.go()
              // this.$emit("modalCallBack", false);
              // this.$emit("proceedAction", true);
            }
        })
        .catch(error => {
            this.uploading = false
            this.error = error.response.data
            console.log('check error: ', this.error)
        });
    },


    removeImage: function (e) {
        this.import_file = '';
    },

      closeDialog() {
          console.log('check modal close')
          $("#confirm-dialog").modal("hide");
          this.$emit('modalCallBack', false);
          this.$emit('proceedAction', false);
          return this.$router.go()
      },
  }
};
</script>

<style scoped>
.confirm-dialog-body {
  line-height: 2.5em;
  /* text-indent: 50px; */
}
</style>


