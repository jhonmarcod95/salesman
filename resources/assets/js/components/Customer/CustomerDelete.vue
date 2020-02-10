
<template>
    <button class="dropdown-item" @click="deleteCustomer">
        Delete
    </button>
</template>

<script>
    export default {
        props: ['customerId'],
        methods: {
            deleteCustomer(){
                if(confirm("Do you really want to delete this customer?")){
                        axios.delete(`/customers/${this.customerId}`)
                        .then(response => {
                            alert('Customer successfully deleted');
                            location.reload();
                        })
                        .catch(error => {
                            var errors = error.response.data.errors;
                            console.log(errors);
                            alert('Something went wrong! Please try again.');
                        })
                        
                }
               
            }
        },
    }
</script>
