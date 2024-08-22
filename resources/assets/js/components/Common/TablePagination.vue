<template>
    <div class="d-flex justify-content-between align-items-center flex-wrap" v-if="hasPagination">
        <div class="d-flex align-items-center">

            <select class="form-control form-control-sm font-weight-bold mr-4" @change="changeLimit($event)" style="width: 75px;">
                <option value="10" :selected="pagination.per_page === '10'">10</option>
                <option value="20" :selected="pagination.per_page === '20'">20</option>
                <option value="50" :selected="pagination.per_page === '50'">50</option>
                <option value="100" :selected="pagination.per_page === '100'">100</option>
            </select>

            <span class="text-muted">Displaying {{ getItemCount() }} of {{ pagination.total }} records</span>
        </div>

        <nav v-if="!limitOnly">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item" @click="goToPage(pagination.prev_page_url)"><a class="page-link" href="#"><i class="fa fa-chevron-left"></i></a></li>
                
                <li class="page-item" 
                    v-for="(count, index) in 5"
                    :key="index"
                    v-if="!pagination.total"><a class="page-link" href="#">{{count}}</a></li>

                <li class="page-item"
                    :class="{ 'btn-hover-primary active' : range == pagination.current_page }"
                    v-for="(range, index) in pagination.range"
                    :key="index"
                    @click="goToPage(range)"><a class="page-link" href="#">{{range}}</a></li>

                <li class="page-item" @click="goToPage(pagination.next_page_url)"><a class="page-link" href="#"><i class="fa fa-chevron-right"></i></a></li>
            </ul>
        </nav>
    </div>
</template>

<script>

export default {

    props: [ 'pagination','limitOnly' ],

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
