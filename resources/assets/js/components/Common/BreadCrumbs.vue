<template>
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex flex-column">
                    <!--begin::Title-->
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ breadcrumbs.title }}</h2>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <div class="d-flex align-items-center font-weight-bold my-2">
                        <!--begin::Item-->
                        <a href="#" class="opacity-75 hover-opacity-100">
                            <i class="flaticon2-shelter text-white icon-1x"></i>
                        </a>
                        <!--end::Item-->

                        <!--begin::Breadcrumbs-->
                        <span v-for="(item,index) in breadcrumbs.items" :key="index">
                            <!--begin::Item-->
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a class="text-white text-hover-white opacity-75 hover-opacity-100">{{ item }}</a>
                            <!--end::Item-->
                        </span>
                        <!--end::Item-->

                    </div>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Heading-->
            </div>
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Slot-->
                <slot></slot>
                <!--end::Slot-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
</template>

<script>
    export default {
        name: 'AppBreadcrumbs',

        props: ['breadcrumbs']
    }
</script>
