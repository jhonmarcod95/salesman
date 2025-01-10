<template>
    <div class="input-group mb-0">
        <input type="text" class="form-control" placeholder="Input description..." v-model="description" autofocus/>
        <div class="input-group-append">
            <button class="btn btn-sm btn-primary" type="button" @click="submit()">Submit</button>
            <button class="btn btn-sm btn-secondary text-gray" type="button" @click="close()">Cancel</button>
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