<template>
	<div>
        <div class="header bg-green pb-6 pt-5 pt-md-6 mb--8"></div>
		<app-breadcrumbs :breadcrumbs="breadcrumbs">
			<div class="btn btn-white font-weight-bold p-3 mr-3 container-fluid" 
				v-if="isAdministrator"
				@click="showModal('form_modal')">
				Add New Version
			</div>
		</app-breadcrumbs>

		<div class="container-fluid">
			<div class="d-flex flex-row">
				<!--begin::Aside-->
				<div class="flex-row offcanvas-mobile w-600px w-xl-500px min-h-550px" id="kt_profile_aside">
					<!--begin::Profile Card-->
					<div class="card card-custom card-stretch shadow-sm">
						<div class="card-header border-0">
							<h3 class="card-title font-weight-bolder text-dark">Version Release</h3>
						</div>
						<!--begin::Body-->
						<div class="card-body pt-4 position-relative">
							<!--begin::Block UI spinner-->
							<table-spinner v-if="isProcessing"/>
							<!--end::Block UI spinner-->

							<!--begin::Nav-->
							<div class="navi navi-bold navi-hover navi-active navi-link-rounded">
								<div v-if="!isProcessing && isEmpty(items)" class="navi-item text-muted">No data available</div>
								<div v-else class="navi-item mb-1" v-for="(item, index) in items" :key="index">
									<a class="btn active border-0 navi-link py-1 pr-0" @click="viewVersion(item)">
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
										<span class="navi-label" v-if="index == 0 && pagination.current_page == 1">
											<span class="label p-1 label-inline text-white bg-green rounded">new</span>
										</span>
									</a>
									<a href="javascript:;" class="text-danger" @click="deleteVersion(item)" v-if="isAdministrator">
										<i class="fas fa-trash icon-xs"></i></a>
								</div> 

								<div v-if="isProcessing && isEmpty(items)">
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
				<div class="flex-row-fluid col-9">
					<!--begin::Advance Table: Widget 7-->
					<div class="card card-custom card-stretch px-8 shadow-sm">
						<!--begin::Header-->
						<div class="card-header border-0 pt-10">
							<h3 class="card-title align-items-start flex-column" v-if="!isEmpty(selectedVersion)">
								<span class="card-label font-weight-bolder font-size-h4 text-dark" >Vsn {{ selectedVersion.version }}</span>
								<span class="text-muted mt-3 font-weight-bold font-size-sm">Release Date: {{ selectedVersion.release_date }}</span>
							</h3>
							<h2 v-else>No data available</h2>
						</div>
						<!--end::Header-->
						<!--begin::Body-->
						<div class="card-body">
							<div class="mb-10" v-if="!isEmpty(selectedVersion)">
								<!--New features-->
								<VersionItems type="new" :version_release_id="selectedVersion.id"
								:items="selectedVersion.release_note.new" @submitSuccess="submitSuccess"
								:lastItem="lastItem" :isAdministrator="isAdministrator"/>
								<!--Updates-->
								<VersionItems type="updates" :version_release_id="selectedVersion.id"
								:items="selectedVersion.release_note.updates" @submitSuccess="submitSuccess"
								:lastItem="lastItem" :isAdministrator="isAdministrator"/>
								<!--Fixes-->
								<VersionItems type="fixes" :version_release_id="selectedVersion.id"
								:items="selectedVersion.release_note.fixes" @submitSuccess="submitSuccess"
								:lastItem="lastItem" :isAdministrator="isAdministrator"/>
							</div>
							<div v-if="isProcessing">
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
			<div class="card card-custom card-stretch my-2">
				<div class="card-body">
					<!--begin::Feedbacks Table-->
					<div v-if="!isEmpty(selectedVersion.feedbacks)">
						<h2>Vsn {{ selectedVersion.version || "0000.00.00" }} Feedback</h2>
						<div style="max-height: 360px" class="table-responsive my-4">
							<table class="table table-head-custom table-vertical-center mb-4">
 							    <thead>
 							    	<tr class="text-uppercase text-dark">
										<th class="col-2">Username</th>
										<th class="col-2">Email</th>
 										<th class="col-2">Date</th>
 										<th class="col-8">Feedback Notes</th>
 							    	</tr>
 							    </thead>
 							    <tbody>
 							        <tr v-for="feedback in selectedVersion.feedbacks">
										<td>{{ feedback.user.name }}</td>
										<td>{{ feedback.user.email }}</td>
 									    <td>{{ feedback.created_at.slice(0, 10) }}</td>
 									    <td>{{ feedback.feedback }}</td>
										<td>
											<a href="javascript:;" @click="deleteFeedback(feedback.id)">
												<i class="fas fa-trash font-size-sm text-danger"></i>
											</a>
										</td>
 							        </tr>
 							    </tbody>
 							</table>
						</div>
					</div>
					<!--end::Feedbacks Table-->
					<span>Comments? Suggestions? </span>
					<a href="javascript:;" @click="showModal('feedback_modal')">Send your feedback!</a>
				</div>
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
		<!-- begin:Feedback Modal -->
		<feedback-modal
			:versionReleaseId="selectedVersion.id"
			:authenticated="authenticated"
			:formAction="formAction"
			:feedbackId="feedbackId"
			@formClose="closeModal()"/>
		<!-- end:Feedback Modal -->
	</div>
</template>

<script>
	import FormModal from './FormModal.vue';
	import FeedbackModal from './FeedbackModal.vue';
	import listFormMixins from '../../list-form-mixins.vue';
	import VersionItems from './VersionItems.vue';
	import Swal from 'sweetalert2';

	export default {
		name: "VersionRelease",

		props: ['user-roles','authenticated'],
		mixins: [listFormMixins],
		components: {FormModal,FeedbackModal,VersionItems},

		data() {
			return {
				endpoint: '/version-release',
				breadcrumbs: {
                    title: 'Version Release',
                    items: [
						// 'Master Data',
                        // 'Version Release'
                    ]
                },
				data: {},
				items: [],
				page_limit: 10,
				selectedVersion: {},
				feedbackId: 0 //for feedback deletion
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
				let index = 0;
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
			},
			deleteVersion(data = null){
            	if(_.isEmpty(data)) return;
				
            	Swal.fire({
            		title: "Delete Version " + data.version + "?",
            		icon: "warning",
            		showCancelButton: true,
            		confirmButtonColor: "#e24444",
            		cancelButtonColor: "#666666",
            		confirmButtonText: "Delete",
            	}).then((result) => {
            	  	if (result.isConfirmed) {
            	  	  	axios.delete(`delete/${data.id}`);
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
        	},
			deleteFeedback(feedbackId) {
				this.formAction = 'delete';
				this.feedbackId = feedbackId;
				this.showModal('feedback_modal');
			}
		},
		computed: {
			isAdministrator() {
				return this.authenticated && (this.userRoles == "It" || this.userRoles == "Admin");
				// return true;
			},
			lastItem() {
				let new_features = this.selectedVersion.release_note.new ? this.selectedVersion.release_note.new.length : 0;
				let updates = this.selectedVersion.release_note.updates ? this.selectedVersion.release_note.updates.length : 0;
				let fixes = this.selectedVersion.release_note.fixes ? this.selectedVersion.release_note.fixes.length : 0;
				return new_features + updates + fixes <= 1;
			}
		}
	}
</script>