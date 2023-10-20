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
                  <h3 class="mb-0">Farmer Quality Meetings</h3>
                </div>
                <div class="col-4 text-right">
                  <!-- <a class="btn btn-outline-primary mb-2" data-toggle="modal" data-target="#questionaireModal">New</a> -->
                  <!-- <button type="submit" @click="switchView = !switchView" class="btn btn-outline-primary mb-2">{{ switchView === false ? 'Switch to Graph' : 'Switch to Table' }}</button> -->
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">TSR</th>
                    <th scope="col">Date Conducted</th>
                    <th scope="col">Region</th>
                    <th scope="col">Target Crops</th>
                    <th scope="col">Farmer Name</th>
                    <th scope="col">Farmer Address</th>
                  </tr>
                </thead>

                <tbody v-if="loading === true" class="list">
                  <tr>
                    <td colspan="8">
                      <div
                        class="center-align py-3"
                        style="
                          display: flex;
                          align-items: center;
                          justify-content: center;
                        "
                      >
                        <svg
                          class="spinner"
                          width="65px"
                          height="65px"
                          viewBox="0 0 66 66"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <circle
                            class="path"
                            fill="none"
                            stroke-width="6"
                            stroke-linecap="round"
                            cx="33"
                            cy="33"
                            r="30"
                          ></circle>
                        </svg>
                      </div>
                    </td>
                  </tr>
                </tbody>

                <tbody class="list">
                  <tr v-for="(survey, s) in surveys.data" v-bind:key="s">
                    <td class="text-right">
                      <div class="dropdown">
                        <a
                          class="btn btn-sm btn-icon-only text-light"
                          href="#"
                          role="button"
                          data-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          <i class="fas fa-ellipsis-v"></i>
                        </a>
                        <div
                          class="dropdown-menu dropdown-menu-right dropdown-menu-arrow"
                        >
                          <a class="dropdown-item" href="javascript:void(0)"
                            >View Details</a
                          >
                        </div>
                      </div>
                    </td>
                    <td>{{ survey.user.name }}</td>
                    <td>
                      {{ survey.date_conducted }}
                    </td>
                    <td>
                      {{ survey.region.name }}
                    </td>
                    <td>
                      <span v-for="(crop, b) in survey.farmer_crops" :key="b">
                        <span>{{ crop.name }}</span> <br />
                      </span>
                    </td>
                    <td>
                      {{ survey.farmer.first_name }} {{ survey.farmer.last_name }}
                    </td>
                    <td>
                      {{ survey.farmer.address }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer py-4">
              <div class="row mt-3">
                <div class="col-6">
                  <button
                    :disabled="surveys.current_page === 1"
                    class="btn btn-default btn-sm"
                    v-on:click="prevPage()"
                  >
                    Previous
                  </button>
                  <span class="text-dark"
                    >Page {{ surveys.current_page }} of
                    {{ surveys.last_page }}</span
                  >
                  <button
                    :disabled="surveys.current_page == surveys.last_page"
                    class="btn btn-default btn-sm"
                    v-on:click="nextPage()"
                  >
                    Next
                  </button>
                </div>
                <div class="col-6 text-right">
                  <span>{{ surveys.total }} Farmer Meetings</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
  </div>
</template>

<script>
import moment from "moment";

export default {
  props: ["userRole"],
  data() {
    return {
      searchString: "",
      loading: false,
      errors: [],
      surveys: [],
      currentPage: 0,
      itemsPerPage: 5,
    };
  },
  created() {
    this.fetchFarmerMeetings();
  },
  methods: {
    fetchFarmerMeetings() {
      this.loading = true;
      axios.get(`/api/aapc-farmer-survey`).then((res) => {
        this.surveys = res.data;
        this.loading = false;
      });
    },
    nextPage() {
      this.loading = true;
      axios.get(`${this.surveys.next_page_url}`).then((response) => {
        this.surveys = response.data;
        this.loading = false;
      });
    },

    prevPage() {
      this.loading = true;
      axios.get(`${this.surveys.prev_page_url}`).then((response) => {
        this.surveys = response.data;
        this.loading = false;
      });
    },

    setPage(pageNumber) {
      this.currentPage = pageNumber;
    },

    resetStartRow() {
      this.currentPage = 0;
    },

    showPreviousLink() {
      return this.currentPage == 0 ? false : true;
    },

    showNextLink() {
      return this.currentPage == this.totalPages - 1 ? false : true;
    },
  },
};
</script>

<style>
.disabled {
  cursor: not-allowed;
}
.modal {
  background-color: rgba(0, 0, 0, 0.9);
}
/* The Close Button */
.closed {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}
.closed:hover,
.closed:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}
</style>
