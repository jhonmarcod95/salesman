<template>
	<div>
		
		<app-breadcrumbs :breadcrumbs="breadcrumbs">
			<div class="btn btn-white font-weight-bold p-3 mr-3" 
				v-if="isAdministrator"
				@click="showModal('form_modal')">
				Add New Version
			</div>
		</app-breadcrumbs>

		<div class="container">
			<div class="d-flex flex-row">
				<!--begin::Aside-->
				<div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px min-h-550px" id="kt_profile_aside">
					<!--begin::Profile Card-->
					<div class="card card-custom card-stretch">
						<div class="card-header border-0 pt-10 pl-15">
							<h3 class="card-title font-weight-bolder text-dark">Version Release</h3>
						</div>
						<!--begin::Body-->
						<div class="card-body pt-4 position-relative">
							<!--begin::Block UI spinner-->
							<table-spinner v-if="isProcessing && items.length"/>
							<!--end::Block UI spinner-->

							<!--begin::Nav-->
							<div class="navi navi-bold navi-hover navi-active navi-link-rounded">
								<div class="navi-item mb-4" v-for="(item, index) in items" :key="index">
									<a class="navi-link py-4 cursor-pointer" :class="{'active':selectedVersion.id == item.id}" @click="viewVersion(item)">
										<span class="navi-icon mr-2">
											<span class="svg-icon">
												<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<polygon points="0 0 24 0 24 24 0 24"></polygon>
														<path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
														<path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
													</g>
												</svg>
												<!--end::Svg Icon-->
											</span>
										</span>
										<span class="navi-text font-size-lg">{{ `Vsn ${item.version}` }}</span>
										<span class="navi-label" v-if="index == items.length - 1 && pagination.current_page == 1">
											<span class="label label-light-success label-inline font-weight-bold">new</span>
										</span>
									</a>
								</div> 

								<div v-if="!items.length">
									<span class="spinner spinner-primary mr-10"></span>
									<span>Loading Data...</span>
								</div>
							</div>
							<!--end::Nav-->
						</div>
						<!--end::Body-->
						<div class="card-footer py-2">
							<!--begin::Pagination-->
							<table-pagination-basic v-if="items.length > 0" :pagination="pagination" v-on:updatePage="goToPage" v-on:doChangeLimit="changePageCount"/>
							<!--end::Pagination-->
						</div>
					</div>
					<!--end::Profile Card-->
				</div>
				<!--end::Aside-->
				<!--begin::Content-->
				<div class="flex-row-fluid ml-8 col-8">
					<!--begin::Advance Table: Widget 7-->
					<div class="card card-custom card-stretch px-8">
						<!--begin::Header-->
						<div class="card-header border-0 pt-10">
							<h3 class="card-title align-items-start flex-column">
								<span class="card-label font-weight-bold font-size-h4 text-dark-75" >Vsn {{ selectedVersion.version || "0000.00.00" }}</span>
								<span class="text-muted mt-3 font-weight-bold font-size-sm">Release Date: {{ selectedVersion.release_date || "0000-00-00"}}</span>
							</h3>
						</div>
						<!--end::Header-->
						<!--begin::Body-->
						<div class="card-body">
							<div v-if="!isEmpty(selectedVersion)">
								<div class="mb-10" v-if="!isEmpty(selectedVersion.release_note.new)">
									<VersionItems title="New" :items="selectedVersion.release_note.new" @submitSuccess="submitSuccess" :isAdministrator="isAdministrator"/>
								</div>
								<div class="mb-10" v-if="!isEmpty(selectedVersion.release_note.updates)">
									<VersionItems title="Enhancement" :items="selectedVersion.release_note.updates" @submitSuccess="submitSuccess" :isAdministrator="isAdministrator"/>
								</div>
								<div class="mb-10" v-if="!isEmpty(selectedVersion.release_note.fixes)">
									<VersionItems title="Fixes" :items="selectedVersion.release_note.fixes" @submitSuccess="submitSuccess" :isAdministrator="isAdministrator"/>
								</div>
							</div>
							<div v-else>
								<span class="spinner spinner-primary mr-10"></span>
								<span>Loading Data...</span>
							</div>
						</div>
						<!--end::Body-->
					</div>
					<!--end::Advance Table Widget 7-->
				</div>
				<!--end::Content-->
			</div>
		</div>

		<!-- begin:Add Modal -->
		<form-modal
			:data="data"
			:formErrors="errors"
			:request-processing="requestProcessing"
			:formAction="formAction"
			@submit="submit"
			@formClose="closeModal()"/>
		<!-- end:Add Modal -->
	</div>
</template>

<script>
	import FormModal from './FormModal.vue';
	import listFormMixins from '../../list-form-mixins.vue';
	import VersionItems from './VersionItems.vue';

	export default {
		name: "VersionRelease",

		props: ['user-roles','authenticated'],
		mixins: [listFormMixins],
		components: {FormModal,VersionItems},

		data() {
			return {
				endpoint: '/version-release',
				breadcrumbs: {
                    title: 'Version Release',
                    items: [
						'Master Data',
                        'Version Release'
                    ]
                },
				data: {},
				items: [],
				page_limit: 5,
				selectedVersion: {}
			}
		},
		created() {
			this.fetchList();
		},
		methods: {
			submit(data) {
				if(this.formAction == 'add') {
					this.submitData(`${this.endpoint}/store`, data);
				} else {
					this.submitData(`${this.endpoint}/update/${data.id}`, data);
				}
			},
			listFetched(selectedIndex = null) {
				let index = 0
				if(!_.isEmpty(this.selectedVersion)) {
					let selectedIndex = _.findIndex(this.items, ['id', this.selectedVersion.id]);
					index = selectedIndex >= 0 ? selectedIndex : 0
				}
				this.viewVersion(this.items[index])
			},
			viewVersion(version) {
				this.selectedVersion = version;
			},
			isEmpty(data) {
				return _.isEmpty(data)
			},
			submitSuccess() {
				// let index = !_.isEmpty(this.selectedVersion) ? _.findIndex(this.items, ['id', this.selectedVersion.id]) : 0
				// const selectedIndex = _.findIndex(this.items, ['id', this.selectedVersion.id]);
				this.fetchList()
			}
		},
		computed: {
			isAdministrator() {
				return this.authenticated && (this.userRoles == "It" || this.userRoles == "Admin");
				// return true;
			}
		}
	}
</script>