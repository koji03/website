<template>
    <div style="text-align:center">
        <div class="c-error u-margin--error" 
        v-for="error in this.errorMessage" :key="error">{{ error }}<br></div>
    </div> 
</template>
<script>
import { mapState } from 'vuex'
export default {
    computed:{
        ...mapState({
            errorMessage: state => state.autoActionStart.errorMessage,
        }),
    },
}
</script>