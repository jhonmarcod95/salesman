<template>
    <div class="d-flex justify-content-between align-items-center flex-wrap" v-if="hasPagination">
        <div class="d-flex align-items-center py-3">
            <select class="form-control form-control-sm font-weight-bold mr-1" @change="changeLimit($event)" style="width: 55px;">
                <option value="10" :selected="pagination.per_page === '10'">10</option>
                <option value="20" :selected="pagination.per_page === '20'">20</option>
                <option value="50" :selected="pagination.per_page === '50'">50</option>
                <option value="100" :selected="pagination.per_page === '100'">100</option>
            </select>
        </div>
        <div class="d-flex flex-wrap py-2">
            <a class="btn btn-icon btn-sm bg-green mr-2 my-1 text-white" @click="goToPage(pagination.first_page_url)"><i class="fas fa-angle-double-left icon-xs"></i></a>
            <a class="btn btn-sm bg-green mr-2 my-1 text-white" @click="goToPage(pagination.prev_page_url)"><i class="fas fa-angle-left icon-xs"></i> Prev</a>

            <a class="btn btn-sm bg-green mr-2 my-1 text-white" @click="goToPage(pagination.next_page_url)">Next <i class="fas fa-angle-right icon-xs"></i></a>
            <a class="btn btn-icon btn-sm bg-green mr-2 my-1 text-white" @click="goToPage(pagination.last_page_url)"><i class="fas fa-angle-double-right icon-xs"></i></a>
        </div>
    </div>
</template>

<script>

export default {

    props: [ 'pagination' ],

    methods: {
      goToPage(page){
          if (typeof page === 'string') {
              page = page.split('page=').slice(1).join("page=")
          }
          this.$emit('updatePage', page);

          this.pagination.current_page = page;
      },

      changeLimit(event) {
          this.$emit('doChangeLimit', event.target.value);
      },

      getItemCount() {

          let total = this.pagination.per_page;

          if (this.pagination.per_page >= this.pagination.total) {
              total = this.pagination.total;
          }

          return total;
      }
    },

    computed: {
        hasPagination: function () {
            return !_.isEmpty(this.pagination);
        }
    }
}

</script>
