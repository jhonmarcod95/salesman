<template>
    <div class="input-group mb-0">
        <input type="text" class="form-control" placeholder="Input description..." v-model="description" autofocus/>
        <div class="input-group-append">
            <button class="btn btn-primary" type="button" @click="submit()">Submit</button>
            <button class="btn btn-secondary" type="button" @click="close()"><i class="flaticon2-cross icon-sm"></i></button>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            data: String
        },
        data() {
            return {
                description: null
            }
        },
        created() {
            this.description = this.data
        },
        methods: {
            submit() {
                this.$emit('submit', this.description);
            },
            close() {
                this.$emit('close')
            }
        }
    }
</script>