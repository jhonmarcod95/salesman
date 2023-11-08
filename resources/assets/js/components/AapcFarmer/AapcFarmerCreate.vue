<template>
  <div>
    <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
    <!-- Page content -->
    <div class="container-fluid mt--7">
      <!-- Table -->
      <div class="row mt-5">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center text-center">
                <div class="col">
                  <h2 class="mb-0">
                    <template v-if="form.activity_type_id != ''">
                      {{ activity_types.find(item => item.id === form.activity_type_id)['name'] }}
                    </template>
                    <template v-else>
                      Select Activity Type
                    </template>
                  </h2>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="classification"
                      >Activity</label
                    >
                    <select
                      class="form-control"
                      v-model="form.activity_type_id"
                    >
                      <option
                        v-for="(activity, c) in activity_types"
                        v-bind:key="c"
                        :value="activity.id"
                      >
                        {{ activity.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.activity_type_id"
                      >{{ errors.activity_type_id[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="classification"
                      >Region</label
                    >
                    <multiselect
                      v-model="form.region_name"
                      :options="getProvinceNames"
                      :multiple="false"
                      track-by="id"
                      placeholder="Select Region/Province"
                      id="selected_region"
                    >
                    </multiselect>
                    <!-- <select class="form-control" v-model="form.region_name">
                      <option
                        v-for="(province, c) in getProvinceNames"
                        v-bind:key="c"
                        :value="province"
                      >
                        {{ province }}
                      </option>
                    </select> -->
                    <span class="text-danger small" v-if="errors.region_name">{{
                      errors.region_name[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >City</label
                    >
                    <multiselect
                      v-model="form.city"
                      :options="municipalities"
                      :multiple="false"
                      track-by="id"
                      :custom-label="customMunicipalities"
                      placeholder="Select Municipality"
                      id="selected_municipality"
                      :disabled="form.region_name === null"
                    >
                    </multiselect>
                    <span class="text-danger small" v-if="errors.city">{{
                      errors.city[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Venue/Barangay</label
                    >
                    <multiselect
                      v-model="form.barangay"
                      :options="barangays"
                      :multiple="false"
                      track-by="id"
                      placeholder="Select Barangay"
                      id="selected_barangay"
                      :disabled="form.city === null"
                    >
                    </multiselect>
                    <!-- <input
                      type="text"
                      id="input-baranggay"
                      placeholder="Barangay/Venue"
                      class="form-control form-control-alternative"
                      v-model="form.barangay"
                    /> -->
                    <span class="text-danger small" v-if="errors.barangay">{{
                      errors.barangay[0]
                    }}</span>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Date Conducted</label
                    >
                    <input
                      type="date"
                      id="input-date-conducted"
                      placeholder="Select Date Conducted"
                      class="form-control form-control-alternative"
                      v-model="form.date_conducted"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.date_conducted"
                      >{{ errors.date_conducted[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <label class="form-control-label" for="input-username"
                    >Target Crops</label
                  >
                  <ul class="list-group list-group-flush">
                    <li
                      v-for="(crop, b) in crops"
                      :key="b"
                      class="list-group-item"
                    >
                      <div class="custom-control custom-checkbox mt-2">
                        <input
                          type="checkbox"
                          :value="crop.id"
                          v-model="form.selected_crops"
                          class="custom-control-input"
                          :id="`aapc_target_crop${crop.id}`"
                        />
                        <label
                          class="custom-control-label"
                          :for="`aapc_target_crop${crop.id}`"
                        >
                          {{ crop.name }}
                        </label>
                      </div>
                    </li>
                  </ul>
                  <span
                    class="text-danger small"
                    v-if="errors.selected_crops"
                    >{{ errors.selected_crops[0] }}</span
                  >
                </div>
              </div>
            </div>

            <div v-if="form.selected_crops.includes(4)" class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Specify lowland vegetable</label
                    >
                    <select
                      class="form-control"
                      v-model="form.vegetable_lowland"
                    >
                      <option
                        v-for="(low, l) in vegetableLowland"
                        :key="`low-${l}`"
                        :value="low.id"
                      >
                        {{ low.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.vegetable_lowland"
                      >{{ errors.vegetable_lowland[0] }}</span
                    >
                  </div>
                </div>
                <div class="col" v-if="form.vegetable_lowland === 8">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Other:</label
                    >
                    <input
                      type="text"
                      id="input-lowland-others"
                      placeholder="Specify vegetable"
                      class="form-control form-control-alternative"
                      v-model="form.lowland_others"
                    />
                    <!-- <span
                      class="text-danger small"
                      v-if="errors.vegetable_highland"
                      >{{ errors.vegetable_highland[0] }}</span
                    > -->
                  </div>
                </div>
              </div>
            </div>

            <div v-if="form.selected_crops.includes(5)" class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Specify highland vegetable</label
                    >
                    <select
                      class="form-control"
                      v-model="form.vegetable_highland"
                    >
                      <option
                        v-for="(high, h) in vegetableHighland"
                        :key="`high-${h}`"
                        :value="high.id"
                      >
                        {{ high.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.vegetable_highland"
                      >{{ errors.vegetable_highland[0] }}</span
                    >
                  </div>
                </div>
                <div class="col" v-if="form.vegetable_highland === 12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Other:</label
                    >
                    <input
                      type="text"
                      id="input-highland-others"
                      placeholder="Specify vegetable"
                      class="form-control form-control-alternative"
                      v-model="form.highland_others"
                    />
                    <!-- <span
                      class="text-danger small"
                      v-if="errors.vegetable_highland"
                      >{{ errors.vegetable_highland[0] }}</span
                    > -->
                  </div>
                </div>
              </div>
            </div>

            <div v-if="form.selected_crops.includes(1)" class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Select rice category</label
                    >
                    <select class="form-control" v-model="form.rice_type">
                      <option
                        v-for="(rice, r) in riceTypes"
                        :key="`rice-${r}`"
                        :value="rice.id"
                      >
                        {{ rice.name }}
                      </option>
                    </select>
                    <span class="text-danger small" v-if="errors.rice_type">{{
                      errors.rice_type[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <hr class="ml-3 mr-3" style="border: 1px dashed #94a3b8" />

            <div class="row align-items-center text-center mb-3 p-3">
              <div class="col">
                <h2 class="mb-0">Farmer Database</h2>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >First Name</label
                    >
                    <input
                      type="text"
                      id="input-first-name"
                      placeholder="First Name"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_first_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_first_name"
                      >{{ errors.farmer_first_name[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Last Name</label
                    >
                    <input
                      type="text"
                      id="input-last-name"
                      placeholder="Last Name"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_last_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_last_name"
                      >{{ errors.farmer_last_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-contact-number"
                      >Contact Number</label
                    >
                    <!-- <input
                      type="text"
                      id="input-contact-number"
                      placeholder="Contact Number"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_contact_number"
                    /> -->
                    <masked-input
                      id="input-contact-number"
                      class="form-control form-control-alternative"
                      mask="\+\63 (111)-1111-111"
                      placeholder="Phone"
                      @input="form.farmer_contact_number = arguments[1]"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_contact_number"
                      >{{ errors.farmer_contact_number[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-username"
                      >Address</label
                    >
                    <input
                      type="text"
                      id="input-farmer-address"
                      placeholder="Address"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_address"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_address"
                      >{{ errors.farmer_address[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="farmer_region"
                      >Region</label
                    >
                    <multiselect
                      v-model="form.farmer_region_name"
                      :options="getProvinceNames"
                      :multiple="false"
                      track-by="id"
                      placeholder="Select Region/Province"
                      id="selected_region_farmer"
                    >
                    </multiselect>
                    <!-- <select
                      class="form-control"
                      v-model="form.farmer_region_id"
                    >
                      <option
                        v-for="(region, c) in regions"
                        v-bind:key="c"
                        :value="region.id"
                      >
                        {{ region.name }}
                      </option>
                    </select> -->
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_region_name"
                      >{{ errors.farmer_region_name[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label class="form-control-label" for="input-farmer-city"
                      >City</label
                    >
                    <multiselect
                      v-model="form.farmer_city"
                      :options="farmer_municipalities"
                      :multiple="false"
                      track-by="id"
                      :custom-label="customFarmerMunicipalities"
                      placeholder="Select Municipality"
                      id="selected_farmer_municipality"
                      :disabled="form.farmer_region_name === null"
                    >
                    </multiselect>
                    <!-- <input
                      type="text"
                      id="input-city"
                      placeholder="Farmer City"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_city"
                    /> -->
                    <span class="text-danger small" v-if="errors.farmer_city">{{
                      errors.farmer_city[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-zip-code"
                      >Postal / Zip Code</label
                    >
                    <input
                      type="text"
                      id="input-zip-code"
                      placeholder="Zip Code"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_zip_code"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_zip_code"
                      >{{ errors.farmer_zip_code[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div> -->

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <div class="d-flex justify-content-between">
                      <label
                        class="form-control-label"
                        for="input-crops-cultivated"
                        >Crop/s Cultivated</label
                      >
                      <div>
                        <button
                          @click="addCultivatedCrops()"
                          class="btn btn-sm btn-primary mb-3"
                        >
                          Add
                        </button>
                      </div>
                    </div>

                    <div class="row pl-2 pr-2 mb-3">
                      <div class="col">
                        <span>Crop Name:</span> <br />
                        <select
                          class="form-control form-control-alternative"
                          v-model="form.farmer_crop_cultivated"
                        >
                          <option
                            v-for="(crop, c) in cultivated_crops"
                            v-bind:key="c"
                            :value="crop.name"
                          >
                            {{ crop.name }}
                          </option>
                        </select>
                        <span
                          class="text-danger small"
                          v-if="errors.farmer_crop_cultivated"
                          >{{ errors.farmer_crop_cultivated[0] }}</span
                        >
                      </div>
                    </div>
                    <div class="row pl-2 pr-2">
                      <div class="col">
                        <span>Planting Season From:</span>
                        <input
                          type="date"
                          id="input-date-start-cul"
                          class="form-control form-control-alternative"
                          v-model="form.plant_season_start"
                        />
                      </div>
                      <div class="col">
                        <span>Planting Season To:</span>
                        <input
                          type="date"
                          id="input-date-end-cul"
                          class="form-control form-control-alternative"
                          v-model="form.plant_season_end"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="farmer_cultivated_crops.length > 0"
              v-for="(cultivated, c) in farmer_cultivated_crops"
              :key="c"
              class="mb-3"
            >
              <div class="row pl-2 pr-2">
                <div class="col-10">
                  <span>Crop Name:</span>
                  <div class="form-group">
                    <select class="form-control" v-model="cultivated.value">
                      <option
                        v-for="(crop, c) in cultivated_crops"
                        v-bind:key="c"
                        :value="crop.name"
                      >
                        {{ crop.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-2">
                  <div>
                    <button
                      @click="removeCultivatedCrops(c)"
                      class="btn btn-block btn-danger mt-4"
                    >
                      <i class="fa fa-minus" aria-hidden="true"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="row pl-2 pr-2">
                <div class="col">
                  <span>Planting season from:</span>
                  <input
                    type="date"
                    id="input-date-start-cul"
                    class="form-control form-control-alternative"
                    v-model="cultivated.plant_season_start"
                  />
                </div>
                <div class="col">
                  <span>Planting season to:</span>
                  <input
                    type="date"
                    id="input-date-end-cul"
                    class="form-control form-control-alternative"
                    v-model="cultivated.plant_season_end"
                  />
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-farmer-hectares"
                      >Land Holding in Hectares</label
                    >
                    <input
                      type="number"
                      id="input-farmer-hectares"
                      placeholder="Land Hectares"
                      class="form-control form-control-alternative"
                      v-model="form.farmer_hectares"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.farmer_hectares"
                      >{{ errors.farmer_hectares[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <hr class="ml-3 mr-3" style="border: 1px dashed #94a3b8" />

            <div class="row align-items-center text-center p-3">
              <div class="col">
                <h2 class="mb-0">Brands Used Most Often (BUMO)</h2>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <h3 class="mb-0">Brands Used for Weeds</h3>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-weeds-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-weeds-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="form.bumo_weeds_brand_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.bumo_weeds_brand_name"
                      >{{ errors.bumo_weeds_brand_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <div class="d-flex justify-content-between">
                  <h3 class="mb-0">Brands Used for Insects</h3>
                  <button
                    @click="addBumoInsects()"
                    class="btn btn-sm btn-primary mb-3"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-brand-name"
                      >Select Type of Insect</label
                    >
                    <select
                      class="form-control"
                      v-model="form.bumo_insect_type_id"
                    >
                      <option
                        v-for="(insect, c) in insectTypes"
                        v-bind:key="c"
                        :value="insect.id"
                      >
                        {{ insect.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.bumo_insect_type_id"
                      >{{ errors.bumo_insect_type_id[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-insect-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-insect-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="form.bumo_insect_brand_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.bumo_insect_brand_name"
                      >{{ errors.bumo_insect_brand_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="bumo_for_insects.length > 0"
              v-for="(bumo_insects, c) in bumo_for_insects"
              :key="`insect_${c}`"
              class="mb-3"
            >
              <div
                class="d-flex justify-content-between align-items-center pl-2 pr-2"
              >
                <div class="form-group" style="width: 40%; margin-right: 10px">
                  <label class="form-control-label" for="input-bumo-brand-name"
                    >Select Type of Insect</label
                  >
                  <select
                    class="form-control"
                    v-model="bumo_insects.bumo_insect_type_id"
                  >
                    <option
                      v-for="(insect, c) in insectTypes"
                      v-bind:key="c"
                      :value="insect.id"
                    >
                      {{ insect.name }}
                    </option>
                  </select>
                  <!-- <span
                      class="text-danger small"
                      v-if="errors.bumo_insect_type_id"
                      >{{ errors.bumo_insect_type_id[0] }}</span
                    > -->
                </div>
                <div style="width: 40%; margin-right: 10px">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-insect-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-insect-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="bumo_insects.bumo_insect_brand_name"
                    />
                    <!-- <span
                      class="text-danger small"
                      v-if="errors.bumo_insect_brand_name"
                      >{{ errors.bumo_insect_brand_name[0] }}</span
                    > -->
                  </div>
                </div>
                <div style="width: 100px" class="align-items-center">
                  <button
                    @click="removeBumoInsects(`insect_${c}`)"
                    class="btn btn-danger"
                  >
                    <i class="fa fa-minus" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <div class="d-flex justify-content-between">
                  <h3 class="mb-0">Brands Used for Diseases</h3>
                  <button
                    @click="addBumoDiseases()"
                    class="btn btn-sm btn-primary mb-3"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-disease-type"
                      >Select type of disease</label
                    >
                    <select
                      class="form-control"
                      v-model="form.bumo_disease_type_id"
                    >
                      <option
                        v-for="(disease, c) in diseaseTypes"
                        v-bind:key="c"
                        :value="disease.id"
                      >
                        {{ disease.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.bumo_disease_type_id"
                      >{{ errors.bumo_disease_type_id[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-diseases-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-diseases-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="form.bumo_disesse_brand_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.bumo_disesse_brand_name"
                      >{{ errors.bumo_disesse_brand_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="bumo_for_diseases.length > 0"
              v-for="(bumo_disease, c) in bumo_for_diseases"
              :key="`disease_${c}`"
              class="mb-3"
            >
              <div
                class="d-flex justify-content-between align-items-center pl-2 pr-2"
              >
                <div style="width: 40%; margin-right: 10px">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-brand-name"
                      >Select type of disease</label
                    >
                    <select
                      class="form-control"
                      v-model="bumo_disease.bumo_disease_type_id"
                    >
                      <option
                        v-for="(disease, c) in diseaseTypes"
                        v-bind:key="c"
                        :value="disease.id"
                      >
                        {{ disease.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div style="width: 40%; margin-right: 10px">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-insect-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-insect-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="bumo_disease.bumo_disesse_brand_name"
                    />
                  </div>
                </div>
                <div style="width: 100px">
                  <button
                    @click="removeBumoDiseases(`disease_${c}`)"
                    class="btn btn-danger"
                  >
                    <i class="fa fa-minus" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>

            <hr class="ml-3 mr-3" style="border: 1px dashed #94a3b8" />

            <div class="row align-items-center text-center p-3">
              <div class="col">
                <h2 class="mb-0">Store Address/Suking Tindahan</h2>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-store-name"
                      >Store Name / Trader Name</label
                    >
                    <input
                      type="text"
                      id="input-store-name"
                      placeholder="Address"
                      class="form-control form-control-alternative"
                      v-model="form.store_name"
                    />
                    <span class="text-danger small" v-if="errors.store_name">{{
                      errors.store_name[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-store-address"
                      >Complete Address</label
                    >
                    <input
                      type="text"
                      id="input-store-address"
                      placeholder="Address"
                      class="form-control form-control-alternative"
                      v-model="form.store_address"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.store_address"
                      >{{ errors.store_address[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-store-city"
                      >City</label
                    >
                    <input
                      type="text"
                      id="input-store-city"
                      placeholder="City"
                      class="form-control form-control-alternative"
                      v-model="form.store_city"
                    />
                    <span class="text-danger small" v-if="errors.store_city">{{
                      errors.store_city[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="form-control-label" for="input-store-zip"
                      >Postal / Zip Code</label
                    >
                    <input
                      type="text"
                      id="input-store-zip"
                      placeholder="Zip Code"
                      class="form-control form-control-alternative"
                      v-model="form.store_zip_code"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.store_zip_code"
                      >{{ errors.store_zip_code[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div> -->

            <hr class="ml-3 mr-3" style="border: 1px dashed #94a3b8" />

            <div class="row align-items-center text-center p-3">
              <div class="col">
                <h2 class="mb-0">Brands to Consider by Farmer</h2>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <h3 class="mb-0">Brands Used for Weeds</h3>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-weeds-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-weeds-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="form.c_bumo_weeds_brand_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.c_bumo_weeds_brand_name"
                      >{{ errors.c_bumo_weeds_brand_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <div class="d-flex justify-content-between">
                  <h3 class="mb-0">Brands to Consider for Insects</h3>
                  <button
                    @click="addConsBumoInsects()"
                    class="btn btn-sm btn-primary mb-3"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-brand-name"
                      >Select Type of Insect</label
                    >
                    <select
                      class="form-control"
                      v-model="form.c_bumo_insect_type_id"
                    >
                      <option
                        v-for="(insect, c) in insectTypes"
                        v-bind:key="c"
                        :value="insect.id"
                      >
                        {{ insect.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.c_bumo_insect_type_id"
                      >{{ errors.c_bumo_insect_type_id[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-insect-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-insect-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="form.c_bumo_insect_brand_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.c_bumo_insect_brand_name"
                      >{{ errors.c_bumo_insect_brand_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="cons_bumo_for_insects.length > 0"
              v-for="(bumo_insects, c) in cons_bumo_for_insects"
              :key="`cons_insect_${c}`"
              class="mb-3"
            >
              <div
                class="d-flex justify-content-between align-items-center pl-2 pr-2"
              >
                <div class="form-group" style="width: 40%; margin-right: 10px">
                  <label class="form-control-label" for="input-bumo-brand-name"
                    >Select Type of Insect</label
                  >
                  <select
                    class="form-control"
                    v-model="bumo_insects.bumo_insect_type_id"
                  >
                    <option
                      v-for="(insect, c) in insectTypes"
                      v-bind:key="c"
                      :value="insect.id"
                    >
                      {{ insect.name }}
                    </option>
                  </select>
                </div>
                <div style="width: 40%; margin-right: 10px">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-insect-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-insect-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="bumo_insects.bumo_insect_brand_name"
                    />
                  </div>
                </div>
                <div style="width: 100px" class="align-items-center">
                  <button
                    @click="removeConsBumoInsects(`cons_insect_${c}`)"
                    class="btn btn-danger"
                  >
                    <i class="fa fa-minus" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <div class="d-flex justify-content-between">
                  <h3 class="mb-0">Brands to Consider for Diseases</h3>
                  <button
                    @click="addConsBumoDiseases()"
                    class="btn btn-sm btn-primary mb-3"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-disease-type"
                      >Select type of disease</label
                    >
                    <select
                      class="form-control"
                      v-model="form.c_bumo_disease_type_id"
                    >
                      <option
                        v-for="(disease, c) in diseaseTypes"
                        v-bind:key="c"
                        :value="disease.id"
                      >
                        {{ disease.name }}
                      </option>
                    </select>
                    <span
                      class="text-danger small"
                      v-if="errors.c_bumo_disease_type_id"
                      >{{ errors.c_bumo_disease_type_id[0] }}</span
                    >
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-diseases-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-diseases-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="form.c_bumo_disesse_brand_name"
                    />
                    <span
                      class="text-danger small"
                      v-if="errors.c_bumo_disesse_brand_name"
                      >{{ errors.c_bumo_disesse_brand_name[0] }}</span
                    >
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="cons_bumo_for_diseases.length > 0"
              v-for="(bumo_disease, c) in cons_bumo_for_diseases"
              :key="`cons_disease_${c}`"
              class="mb-3"
            >
              <div
                class="d-flex justify-content-between align-items-center pl-2 pr-2"
              >
                <div style="width: 40%; margin-right: 10px">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-brand-name"
                      >Select type of disease</label
                    >
                    <select
                      class="form-control"
                      v-model="bumo_disease.bumo_disease_type_id"
                    >
                      <option
                        v-for="(disease, c) in diseaseTypes"
                        v-bind:key="c"
                        :value="disease.id"
                      >
                        {{ disease.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div style="width: 40%; margin-right: 10px">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-insect-brand-name"
                      >Insert Brand name</label
                    >
                    <input
                      type="text"
                      id="input-bumo-insect-brand-name"
                      placeholder="Insert brand name"
                      class="form-control form-control-alternative"
                      v-model="bumo_disease.bumo_disesse_brand_name"
                    />
                  </div>
                </div>
                <div style="width: 100px">
                  <button
                    @click="removeConsBumoDiseases(`cons_disease_${c}`)"
                    class="btn btn-danger"
                  >
                    <i class="fa fa-minus" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label
                      class="form-control-label"
                      for="input-bumo-diseases-brand-name"
                      >Remarks by MDT and FA (Field Agronomist)</label
                    >
                    <textarea
                      name=""
                      rows="5"
                      class="form-control"
                      v-model="form.remarks"
                    ></textarea>
                    <span class="text-danger small" v-if="errors.remarks">{{
                      errors.remarks[0]
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row align-items-center my-3 p-3">
              <div class="col">
                <h3 class="mb-0">Agrisolutions Product/s to Recommend</h3>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <div v-for="(recommend, r) in recommendations" :key="r">
                    <label
                      class="form-control-label mb-3"
                      for="input-username"
                      >{{ recommend[0]["brand_type"] }}</label
                    >
                    <ul class="list-group list-group-flush">
                      <li
                        v-for="(item, b) in recommend"
                        :key="b"
                        class="list-group-item"
                      >
                        <div class="custom-control custom-checkbox mt-2">
                          <input
                            type="checkbox"
                            :value="item.id"
                            v-model="form.selected_recommendations"
                            class="custom-control-input"
                            :id="`aapc_recommendations_${item.id}`"
                          />
                          <label
                            class="custom-control-label"
                            :for="`aapc_recommendations_${item.id}`"
                          >
                            {{ item.name }}
                          </label>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row pl-2 pr-2">
                <div class="col-lg-12">
                  <button
                    :disabled="submitLoading === true"
                    @click="confirmSubmission()"
                    class="btn btn-primary btn-block"
                  >
                    Submit
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- end form -->
        </div>
      </div>
    </div>
  </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
import Swal from "sweetalert2";
import MaskedInput from "vue-masked-input";
import jsonData from "../../services/ph_places.json";
import Multiselect from "vue-multiselect";

export default {
  components: {
    MaskedInput,
    Multiselect,
  },
  data() {
    return {
      places: jsonData,
      submitLoading: false,
      errors: [],
      activity_types: [],
      cultivated_crops: [],
      regions: [],
      crops: [],
      recommendations: [],
      vegetables: [],
      insectTypes: [],
      diseaseTypes: [],
      farmer_cultivated_crops: [],
      bumo_for_insects: [],
      bumo_for_diseases: [],
      cons_bumo_for_insects: [],
      cons_bumo_for_diseases: [],
      municipalities: [],
      farmer_municipalities: [],
      barangays: [],
      old_region_name: null,
      old_city: null,
      old_farmer_region_name: null,
      old_farmer_city: null,
      form: {
        region_name: null,
        city: null,
        barangay: null,
        activity_type_id: "",
        region_id: "",
        date_conducted: "",
        selected_crops: [],
        selected_recommendations: [],
        plant_season_end: "",
        plant_season_start: "",
        vegetable_id: "",
        farmer_first_name: "",
        farmer_last_name: "",
        farmer_contact_number: "",
        farmer_address: "",
        farmer_city: null,
        farmer_region_id: "",
        farmer_region_name: null,
        farmer_crop_cultivated: "",
        farmer_all_cultivated_crops: [],
        bumo_for_diseases_all: [],
        bumo_for_insects_all: [],
        cons_bumo_for_diseases_all: [],
        cons_bumo_for_insects_all: [],
        other_vegetables: [],
        vegetable_lowland: "",
        vegetable_highland: "",
        rice_type: "",
        lowland_others: "",
        highland_others: "",
        rice_others: "",
        farmer_hectares: "",
        store_name: "",
        store_address: "",
        store_city: "",
        store_state: "",
        bumo_weeds_brand_name: "",
        bumo_insect_type_id: "",
        bumo_insect_brand_name: "",
        bumo_disease_type_id: "",
        bumo_disesse_brand_name: "",
        c_bumo_weeds_brand_name: "",
        c_bumo_insect_type_id: "",
        c_bumo_insect_brand_name: "",
        c_bumo_disease_type_id: "",
        c_bumo_disesse_brand_name: "",
        remarks: "",
      },
    };
  },

  mounted() {
    this.fetchCultivatedCropName();
    this.fetchActivityType();
    this.fetchRegions();
    this.fetchCrops();
    this.fetchRecommendations();
    this.fetchVegetables();
    this.fetchInsectTypes();
    this.fetchDiseaseTypes();
  },

  computed: {
    riceTypes() {
      return this.vegetables.filter((item) => item.vegetable_type === 3);
    },
    vegetableHighland() {
      return this.vegetables.filter((item) => item.vegetable_type === 2);
    },
    vegetableLowland() {
      return this.vegetables.filter((item) => item.vegetable_type === 1);
    },
    getProvices() {
      return this.places.map((item) => item.provinces);
    },
    getRegionNames() {
      return this.places.map((item) => {
        return item.regionName;
      });
    },
    getProvinceNames() {
      return this.places
        .map((item) => {
          return item.provinces.map((province) => {
            return province.name;
          });
        })
        .flat()
        .sort();
    },
    getMunicipalities() {
      return this.places
        .map((item) => {
          return item.provinces.map((province) => {
            return province.municipalities;
          });
        })
        .flat(2);
    },
  },

  watch: {
    form: {
      handler(val, oldValue) {
        // do stuff

        console.log("check region name: ", val.region_name);
        console.log("check city: ", val.city);
        console.log("check farmer region: ", val.farmer_region_name);
        console.log("check farmer city: ", val.farmer_city);

        if (this.old_region_name === null) {
          this.old_region_name = val.region_name;
        }

        if (this.old_farmer_region_name === null) {
          this.old_farmer_region_name = val.farmer_region_name;
        }

        if (this.form.farmer_city) {
          if (this.old_farmer_city === null) {
            this.old_farmer_city = val.farmer_city.name;
          }
        }

        if (this.form.city) {
          if (this.old_city === null) {
            this.old_city = val.city.name;
          }
          if (val.city.name != this.old_city) {
            this.old_city = val.city.name;
            this.barangays = [];
            this.form.barangay = null;
          }
        }

        if (val.region_name != this.old_region_name) {
          this.old_region_name = val.region_name;
          this.municipalities = [];
          this.barangays = [];
          this.form.city = null;
          this.form.barangay = null;
        }

        if (val.farmer_region_name != this.old_farmer_region_name) {
          this.old_farmer_region_name = val.farmer_region_name;
          this.farmer_municipalities = [];
          this.from.farmer_city = null;
        }

        if (val.farmer_region_name) {
          const farmerUpdateMunicipalities = this.getMunicipalities.filter(
            (item) => item.provinceName === val.farmer_region_name
          );
          this.farmer_municipalities = farmerUpdateMunicipalities;
        }

        if (val.region_name) {
          const updateMunicipalities = this.getMunicipalities.filter(
            (item) => item.provinceName === val.region_name
          );
          this.municipalities = updateMunicipalities;
        }

        if (val.city) {
          if (val.city != null) {
            this.barangays = val.city.barangays;
          }
        }
      },
      deep: true,
    },
  },

  methods: {
    customRegionName(region) {
      return `${region.name}`;
    },
    customFarmerMunicipalities(municipality) {
      return `${municipality.name}`;
    },
    customMunicipalities(municipality) {
      return `${municipality.name}`;
    },
    customBarangays(barangay) {
      return `${barangay.name}`;
    },
    fetchMunicipalities(province) {
      return this.getMunicipalities.find(
        (item) => item.provinceName === province
      );
    },
    addCultivatedCrops() {
      this.farmer_cultivated_crops.push({
        value: "",
        plant_season_start: "",
        plant_season_end: "",
      });
    },
    removeCultivatedCrops(index) {
      this.farmer_cultivated_crops.splice(index, 1);
    },

    addBumoInsects() {
      this.bumo_for_insects.push({
        bumo_insect_type_id: "",
        bumo_insect_brand_name: "",
      });
    },
    removeBumoInsects(index) {
      this.bumo_for_insects.splice(index, 1);
    },

    addBumoDiseases() {
      this.bumo_for_diseases.push({
        bumo_disease_type_id: "",
        bumo_disesse_brand_name: "",
      });
    },
    removeBumoDiseases(index) {
      this.bumo_for_diseases.splice(index, 1);
    },

    addConsBumoInsects() {
      this.cons_bumo_for_insects.push({
        bumo_insect_type_id: "",
        bumo_insect_brand_name: "",
      });
    },
    removeConsBumoInsects(index) {
      this.cons_bumo_for_insects.splice(index, 1);
    },

    addConsBumoDiseases() {
      this.cons_bumo_for_diseases.push({
        bumo_disease_type_id: "",
        bumo_disesse_brand_name: "",
      });
    },
    removeConsBumoDiseases(index) {
      this.cons_bumo_for_diseases.splice(index, 1);
    },

    showLoading() {
      Swal.fire({
        title: "Processing...",
        allowEscapeKey: false,
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        onOpen: () => {
          Swal.showLoading();
        },
      });
    },

    confirmSubmission() {
      Swal.fire({
        title: "Are you want to proceed this action?",
        text: "Submit this form.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Proceed",
      }).then((result) => {
        if (result.isConfirmed) {
          this.showLoading();
          this.handleSubmit();
        }
      });
    },

    handleSubmit() {
      this.submitLoading = true;

      this.form.farmer_all_cultivated_crops = [
        ...this.farmer_cultivated_crops,
        {
          value: this.form.farmer_crop_cultivated,
          plant_season_start: this.form.plant_season_start,
          plant_season_end: this.form.plant_season_end,
        },
      ];

      this.form.bumo_for_diseases_all = [
        ...this.bumo_for_diseases,
        {
          bumo_disease_type_id: this.form.bumo_disease_type_id,
          bumo_disesse_brand_name: this.form.bumo_disesse_brand_name,
        },
      ];
      this.form.bumo_for_insects_all = [
        ...this.bumo_for_insects,
        {
          bumo_insect_type_id: this.form.bumo_insect_type_id,
          bumo_insect_brand_name: this.form.bumo_insect_brand_name,
        },
      ];

      this.form.cons_bumo_for_diseases_all = [
        ...this.cons_bumo_for_diseases,
        {
          bumo_disease_type_id: this.form.c_bumo_disease_type_id,
          bumo_disesse_brand_name: this.form.c_bumo_disesse_brand_name,
        },
      ];
      this.form.cons_bumo_for_insects_all = [
        ...this.cons_bumo_for_insects,
        {
          bumo_insect_type_id: this.form.c_bumo_insect_type_id,
          bumo_insect_brand_name: this.form.c_bumo_insect_brand_name,
        },
      ];

      if (
        this.form.vegetable_lowland != "" &&
        this.form.lowland_others === ""
      ) {
        const low_other = this.vegetableLowland.find(
          (item) => item.id === this.form.vegetable_lowland
        );
        this.form.lowland_others = low_other.name;
      }
      if (
        this.form.vegetable_highland != "" &&
        this.form.highland_others === ""
      ) {
        const high_other = this.vegetableHighland.find(
          (item) => item.id === this.form.vegetable_highland
        );
        this.form.highland_others = high_other.name;
      }
      if (this.form.rice_type != "" && 
          this.form.rice_others === ""
      ) {
        const rice_other = this.riceTypes.find(
          (item) => item.id === this.form.rice_type
        );
        this.form.rice_others = rice_other.name;
      }

      axios
        .post(`/api/aapc-farmer-survey`, this.form)
        .then((res) => {
          if (res.status === 200 || res.status === 201) {
            Swal.close();
            Swal.fire({
              title: "Success!",
              text: "Form entry successfully created!",
              icon: "success",
              confirmButtonText: "Okay",
            });
            this.submitLoading = false;
            window.location.href = "/aapc-farmer";
          }
        })
        .catch((err) => {
          Swal.close();
          if (err.response.status === 422) {
            this.errors = err.response.data.errors;
          } else {
            Swal.fire({
              title: "Oopps!",
              text: `${err.message}`,
              icon: "warning",
              confirmButtonText: "Okay",
            });
            this.submitLoading = false;
          }
        });
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
    fetchActivityType() {
      axios
        .get(`/api/aapc-activity-types`)
        .then((res) => {
          this.activity_types = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchRegions() {
      axios
        .get(`/api/aapc-regions`)
        .then((res) => {
          this.regions = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchCrops() {
      axios
        .get(`/api/aapc-crops`)
        .then((res) => {
          this.crops = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchRecommendations() {
      axios
        .get(`/api/aapc-recommendations`)
        .then((res) => {
          this.recommendations = res.data;
          console.log("check recommendations: ", res.data);
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchInsectTypes() {
      axios
        .get(`/api/aapc-insect-types`)
        .then((res) => {
          this.insectTypes = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchDiseaseTypes() {
      axios
        .get(`/api/aapc-disease-types`)
        .then((res) => {
          this.diseaseTypes = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
    fetchVegetables() {
      axios
        .get(`/api/aapc-vegetable`)
        .then((res) => {
          this.vegetables = res.data;
        })
        .catch((err) => {
          console.log("err: ", err.message);
        });
    },
  },
};
</script>
