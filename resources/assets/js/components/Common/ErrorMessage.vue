<template>
    <div class="alert fill-danger fade show mb-0 border-danger text-danger" role="alert">
        <div class="alert-text">
            <strong>
                <i class="flaticon-warning text-danger icon-md mr-2"></i>
                Error:
            </strong>
            The given data was invalid!
            <ul class="mb-0">
                <li v-for="(error, e) in formErrors" :key="e">{{ error[0] }}</li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    props: ['formErrors'],
}
</script>   