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
                  <a href="/aapc-farmer/create" class="btn btn-sm btn-primary"
                    >Add Survey</a
                  >
                  <!-- <a class="btn btn-outline-primary mb-2" data-toggle="modal" data-target="#questionaireModal">New</a> -->
                  <!-- <button type="submit" @click="switchView = !switchView" class="btn btn-outline-primary mb-2">{{ switchView === false ? 'Switch to Graph' : 'Switch to Table' }}</button> -->
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="start_date" class="form-control-label"
                      >Farmer Name</label
                    >
                    <input
                      type="text"
                      class="form-control form-control-alternative"
                      v-model="farmername"
                    />
                    <span class="text-danger" v-if="errors.farmername">
                      {{ errors.farmername[0] }}
                    </span>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="form-control-label" for="role">Cultivated Crops</label>
                    <select
                      class="form-control"
                      v-model="selected_cultivated_crops"
                    >
                      <option value=""> Select Cultivated Crop </option>
                      <option
                        v-for="(cultivated, c) in cultivated_crops"
                        v-bind:key="c"
                        :value="cultivated.id"
                      >
                        {{ cultivated.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger"
                      v-if="errors.selected_cultivated_crops"
                      >{{ errors.selected_cultivated_crops[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="start_date" class="form-control-label"
                      >Province</label
                    >
                    <input
                      type="text"
                      class="form-control form-control-alternative"
                      v-model="region_id"
                    />
                    <span class="text-danger" v-if="errors.region_id">
                      {{ errors.region_id[0] }}
                    </span>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="end_date" class="form-control-label"
                      >City</label
                    >
                    <input
                      type="text"
                      id="end_date"
                      class="form-control form-control-alternative"
                      v-model="city"
                    />
                    <span class="text-danger" v-if="errors.city">
                      {{ errors.city[0] }}
                    </span>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="end_date" class="form-control-label"
                      >Store Name</label
                    >
                    <input
                      type="text"
                      id="end_date"
                      class="form-control form-control-alternative"
                      v-model="store_name"
                    />
                    <span class="text-danger" v-if="errors.store_name">
                      {{ errors.store_name[0] }}
                    </span>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="end_date" class="form-control-label"
                      >&nbsp;</label
                    >
                    <button
                      type="button"
                      class="btn btn-primary btn-lg btn-block"
                      :class="{ ' disabled': loading === true }"
                      :disabled="loading === true"
                      @click="fetchFarmerMeetings()"
                    >
                      Filter
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Acitivity Type</th>
                    <th scope="col">TSR</th>
                    <th scope="col">Date Conducted</th>
                    <th scope="col">Location</th>
                    <th scope="col">Target Crops</th>
                    <th scope="col">Farmer Name</th>
                    <th scope="col">Farmer Address</th>
                    <th scope="col">Farmer Cultivated Crops</th>
                    <th scope="col">Store Name</th>
                    <th scope="col">Brands for Insects</th>
                    <th scope="col">Brands for Diseases</th>
                  </tr>
                </thead>

                <tbody v-if="loading === true" class="list">
                  <tr>
                    <td colspan="11">
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
                  <template v-if="loading === false">
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
                    <td>
                      <span v-if="survey.activity_type">
                        {{ survey.activity_type.name }}
                      </span>
                      <span v-else>
                        N/A
                      </span>
                    </td>
                    <td>{{ survey.user.name }}</td>
                    <td>
                      {{ survey.date_conducted }}
                    </td>
                    <td>
                      <span v-if="survey.region">
                      {{ survey.region.name }}
                      </span>
                      <span v-else>
                        {{ survey.region_name }}, 
                      </span><br/>
                      <span>
                        {{ survey.city }} 
                      </span><br/>
                      <span>
                        {{ survey.venue }}
                      </span>
                    </td>
                    <td>
                      <span v-for="(crop, b) in survey.farmer_crops" :key="b">
                        <span>{{ crop.name }}</span> {{ crop.pivot.others }}
                        <br />
                      </span>
                    </td>
                    <td>
                      {{ survey.farmer.first_name }}
                      {{ survey.farmer.last_name }}
                    </td>
                    <td>
                      {{ survey.farmer.address }}
                    </td>
                    <td>
                      <template
                        v-if="survey.farmer.cultivated_crops.length > 0"
                      >
                        <span
                          v-for="(cultivated, c) in survey.farmer
                            .cultivated_crops"
                          :key="c"
                        >
                          <span>{{ cultivated.crop_name }}</span> <br />
                        </span>
                      </template>
                      <template v-else>
                        <span class="text-muted">N/A</span>
                      </template>
                    </td>
                    <td>
                      <span v-if="survey.tindahan">
                        {{ survey.tindahan.name }}
                      </span>
                      <span v-else>
                        N/A
                      </span>
                    </td>
                    <td>
                      <template v-if="survey.bumo_insects.length > 0">
                        <span
                          v-for="(insect, c) in survey.bumo_insects"
                          :key="c"
                        >
                          <span>{{ insect.insect_brand_name }}</span> <br />
                        </span>
                      </template>
                      <template v-else>
                        <span class="text-muted">N/A</span>
                      </template>
                    </td>
                    <td>
                      <template v-if="survey.bumo_diseases.length > 0">
                        <span
                          v-for="(disease, c) in survey.bumo_diseases"
                          :key="c"
                        >
                          <span>{{ disease.disease_brand_name }}</span> <br />
                        </span>
                      </template>
                      <template v-else>
                        <span class="text-muted">N/A</span>
                      </template>
                    </td>
                  </tr>
                  </template>
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
      farmername: "",
      selected_cultivated_crops: "",
      region_id: "",
      city: "",
      store_name: "",
      cultivated_crops: [],
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
    this.fetchCultivatedCropName();
  },
  methods: {
    newSurvey() {
      return window.location.origin + "/aapc-farmer/create";
    },
    fetchCultivatedCropName() {
      axios
        .get(`/api/aapc-cultivated-crops`)
        .then((res) => {
          this.cultivated_crops = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchFarmerMeetings() {
      this.loading = true;
      axios.post(`/api/aapc-farmer-survey-list`,{
        farmername: this.farmername,
        cultivated_crops: this.selected_cultivated_crops,
        region_id: this.region_id,
        city: this.city,
        store_name: this.store_name,
      }).then((res) => {
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
